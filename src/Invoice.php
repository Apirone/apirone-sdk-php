<?php

/**
 * This file is part of the Apirone SDK.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Apirone\SDK;

use Apirone\SDK\Service\InvoiceDb;
use Apirone\SDK\Service\InvoiceQuery;
use Apirone\SDK\Model\AbstractModel;
use Apirone\SDK\Model\InvoiceDetails;
use Apirone\API\Endpoints\Service;
use Apirone\API\Endpoints\Account;
use Apirone\API\Exceptions\RuntimeException;
use Apirone\API\Exceptions\ValidationFailedException;
use Apirone\API\Exceptions\UnauthorizedException;
use Apirone\API\Exceptions\ForbiddenException;
use Apirone\API\Exceptions\InternalServerErrorException;
use Apirone\API\Exceptions\NotFoundException;
use Apirone\API\Exceptions\MethodNotAllowedException;
use Apirone\API\Log\LoggerWrapper;
use Apirone\SDK\Model\UserData;
use Apirone\SDK\Model\Settings;
use Apirone\SDK\Service\Render;
use Apirone\SDK\Service\Utils;
use DivisionByZeroError;
use ArithmeticError;
use ReflectionException;

class Invoice extends AbstractModel
{
    /**
     * Invoice settings object
     *
     * @var Settings
     */
    public static Settings $settings;

    private ?int $id = null;

    private ?int $order = null;

    private ?string $invoice = null;

    private ?string $status = null;

    private ?InvoiceDetails $details;

    private ?array $meta = null;

    private ?array $createParams;

    private function __construct() {}

    /**
     * Set settings to Invoice object
     *
     * @param Settings $settings
     * @return void
     */
    public static function settings(\Apirone\SDK\Model\Settings $settings): void
    {
        static::$settings = $settings;

        Render::$backlink = $settings->getBacklink();
        Render::$logo = $settings->getLogo();
        Render::$qrOnly = $settings->getQrOnly();
    }

    /**
     * Set DB handler & table prefix for InvoiceDb class
     *
     * @param Closure $handler
     * @param string $prefix
     * @return void
     */
    public static function db(\Closure $handler, string $prefix = ''): void
    {
        InvoiceDb::setCallback($handler);
        InvoiceDb::setPrefix($prefix);
    }

    /**
     * Set Render invoice dataUrl
     *
     * @param string $dataUrl
     * @return void
     */
    public static function dataUrl(string $dataUrl)
    {
        Render::$dataUrl = $dataUrl;
    }

    /**
     * Set log handler
     *
     * @param mixed $logger
     * @return void
     */
    public static function setLogger($logger): void
    {
        LoggerWrapper::setLogger($logger);
    }

    /**
     * Init new Invoice class
     *
     * @param string $currency
     * @param null|int $amount
     * @return static
     */
    public static function init(string $currency, ?int $amount = null): Invoice
    {
        $class = new static();

        $class->currency($currency);

        if ($amount !== null) {
            $class->createParams['amount'] = $amount;
        }

        return $class;
    }

    /**
     * Create new invoice class from fiat amount
     *
     * @param float $value
     * @param string $from
     * @param string $to
     * @param float|int $factor
     * @return static
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @throws InternalServerErrorException
     */
    public static function fromFiatAmount(float $value, string $from, string $to, float $factor = 1): Invoice
    {
        $class = new static();
        $class->currency($to);
        $cryptoAmount = Service::fiat2crypto($value * $factor, $from, $to);
        $unitFactor = $class->createParams['currency']->{'units-factor'};

        $class->createParams['amount'] = Utils::cur2min($cryptoAmount, $unitFactor);

        return $class;
    }

    /**
     * Restore invoice from JSON object
     *
     * @param mixed $json
     * @return static
     * @throws ReflectionException
     */
    public static function fromJson($json): Invoice
    {
        $class = new static();

        $class->classLoader($json);

        return $class;
    }

    /**
     * Get Invoice from database table
     *
     * @param mixed $invoice
     * @return null|Invoice
     * @throws ReflectionException
     */
    public static function getInvoice(?string $invoice): ?Invoice
    {
        $prefix = InvoiceDb::$prefix;
        $query = InvoiceQuery::selectInvoice((string)$invoice, $prefix);

        $result = InvoiceDb::execute($query);

        if (empty($result)) {
            return new static();
        }
        $row = $result[0];
        $json = new \stdClass();
        $json->id = $row['id'];
        $json->order = $row['order'];
        $json->invoice = $row['invoice'];
        $json->status = $row['status'];
        $json->details = json_decode($row['details']);
        $json->meta = json_decode($row['meta']);

        return Invoice::fromJson($json);
    }

    /**
     * Get invoices objects array for order with orderID
     *
     * @param int $order - Order ID in your system
     * @return array
     */
    public static function getOrderInvoices(int $order): array
    {
        $prefix = InvoiceDb::$prefix;
        $query = InvoiceQuery::selectOrder($order, $prefix);

        $result = InvoiceDb::execute($query);

        $invoices = [];

        if ($result === null) {
            return $invoices;
        }
        foreach($result as $data) {
            $json = new \stdClass();
            $json->id = $data['id'];
            $json->order = $data['order'];
            $json->invoice = $data['invoice'];
            $json->status = $data['status'];
            $json->details = json_decode($data['details']);
            $json->meta = json_decode($data['meta']);

            $invoices[] = Invoice::fromJson($json);
        }

        return $invoices;
    }

    /**
     * Invoice callback handler
     *
     * @param mixed $order_handler - Callback func to process your order process logic (change status, etc)
     * @return void
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    public static function callbackHandler($order_handler = null): void
    {

        $data = file_get_contents('php://input');

        $params = ($data) ? json_decode(Utils::sanitize($data)) : null;

        if (!$params) {
            $message = 'Data not received';
            LoggerWrapper::debug($message);
            Utils::send_json('Data not received', 400);

            return;
        }

        if (!property_exists($params, 'invoice') || !property_exists($params, 'status')) {
            $message = 'Wrong params received: ' . json_encode($params);
            LoggerWrapper::debug($message);
            Utils::send_json('Wrong params received: ' . json_encode($params), 400);

            return;
        }

        $invoice = Invoice::getInvoice($params->invoice);

        if (!$invoice->invoice) {
            $message = "Invoice not found: " . $params->invoice;
            LoggerWrapper::debug($message);
            Utils::send_json($message, 404);

            return;
        }

        if ($invoice->update()) {
            if ($order_handler !== null) {
                $order_handler($invoice);
            }
        }
        exit;
    }

    /**
     * Set order ID for new invoice
     *
     * @param null|int $order
     * @return $this
     */
    public function order(?int $order = null)
    {
        $this->createParams['order'] = $order;

        return $this;
    }

    /**
     * Set currency for neww invoice
     *
     * @param string $currency
     * @return static
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    public function currency(string $currency): self
    {
        $currency = Utils::currency($currency);
        $this->createParams['currency'] = $currency;

        return $this;
    }

    /**
     * Set invoice amount
     *
     * @param null|int $amount
     * @return $this
     */
    public function amount(?int $amount = null)
    {
        if (!$this->id) {
            $this->createParams['amount'] = $amount;
        }

        return $this;
    }

    /**
     * Set invoice lifetime
     *
     * @param null|int $lifetime
     * @return $this
     */
    public function lifetime(?int $lifetime = null)
    {
        if (!$this->id) {
            $this->createParams['lifetime'] = $lifetime;
        }

        return $this;
    }

    /**
     * Set invoice expire date
     *
     * @param null|string $expire
     * @return $this
     */
    public function expire(?string $expire = null)
    {
        if (!$this->id) {
            $this->createParams['expire'] = $expire;
        }

        return $this;
    }

    /**
     * Set invoice UserData object
     *
     * @param null|UserData $userData
     * @return $this
     */
    public function userData(?UserData $userData = null)
    {
        if (!$this->id) {
            $this->createParams['user-data'] = ($userData instanceof UserData) ? $userData->toJson() : $userData;
        }

        return $this;
    }

    /**
     * Set invoice linkback
     *
     * @param null|string $linkback
     * @return $this
     */
    public function linkback(?string $linkback = null)
    {
        if (!$this->id) {
            $this->createParams['linkback'] = $linkback;
        }

        return $this;
    }

    /**
     * Set callback url
     *
     * @param null|string $callbackUrl
     * @return $this
     */
    public function callbackUrl(?string $callbackUrl = null)
    {
        if (!$this->id) {
            $this->createParams['callback-url'] = $callbackUrl;
        }

        return $this;
    }

    /**
     * Create invoice from creation params
     *
     * @param string $account
     * @return $this
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @throws InternalServerErrorException
     * @throws ReflectionException
     */
    public function create(?string $account = null)
    {
        if ($this->invoice !== null || !isset($this->createParams)) {
            return $this;
        }

        $this->order = array_key_exists('order', $this->createParams) ? $this->createParams['order'] : 0;

        unset($this->createParams['order']);
        $account_id = ($account === null) ? Invoice::$settings->getAccount() : $account;

        $account = Account::init($account_id);
        $created = false;
        $options = $this->createParams;
        $options['currency'] = $this->createParams['currency']->abbr;

        try {
            $created = $account->invoiceCreate(json_encode($options));
            $this->details = InvoiceDetails::fromJson($created);
            $this->invoice = $this->details->invoice;
            $this->status = $this->details->status;
            unset($this->createParams);
        }
        catch(Exception $e) {
            throw $e;
        }
        $this->save();

        return $this;
    }

    /**
     * Save invoice into data table
     *
     * @return bool
     */
    public function save(): bool
    {
        if(!isset($this->details)) {
            return false;
        }

        $prefix = InvoiceDb::$prefix;
        $query = ($this->id === null) ? InvoiceQuery::createInvoice($this, $prefix) : InvoiceQuery::updateInvoice($this, $prefix);
        $result = InvoiceDb::execute($query);

        if ($result == true) {
            $this->id = ($this->id === null) ? 0 : $this->id;
        }

        return $result;
    }

    /**
     * Update invoice data from apirone & save if status changed
     *
     * @return bool
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @throws ReflectionException
     */
    public function update(): bool
    {
        if(isset($this->details)) {
            $this->details->update();

            if ($this->status !== $this->details->status) {
                $this->status = $this->details->status;

                return $this->save();
            }
        }

        return false;
    }

    /**
     * Render html invoice loader
     *
     * @param null|string $invoice_id
     * @return string
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    public static function renderLoader(?Invoice $invoice = null)
    {
        if(Render::isAjaxRequest()) {
            return Invoice::renderAjax();
        }

        return Render::show($invoice);
    }

    /**
     * Echo invoice data or status ajax response
     *
     * @return never
     * @throws DivisionByZeroError
     * @throws ArithmeticError
     */
    public static function renderAjax()
    {
        if (Render::isAjaxRequest()) {
            $data = file_get_contents('php://input');
            $params = ($data) ? json_decode(Utils::sanitize($data)) : null;

            if ($params) {
                $id = property_exists($params, 'invoice') ? (string) $params->invoice : '';
                $offset = property_exists($params, 'offset') ? (int) $params->offset : 0;
                header("Content-Type: text/plain");
                $invoice = Invoice::getInvoice($id);
                if ($offset) {
                    Render::setTimeZoneByOffset($offset);
                    echo $invoice->render();
                }
                else {
                    echo $invoice->id ? $invoice->details->statusNum() : 0;
                }
            }
            exit;
        }
        echo 0;
        exit;
    }

    /**
     * Render Invoice object html
     *
     * @return string
     */
    public function render()
    {
        return Render::show($this);
    }

    /**
     * Set invoice meta value
     *
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function setMeta($key, $value)
    {
        if ($this->meta === null) {
            $this->meta = [];
        }

        $this->meta[$key] = $value;

        $this->save();

        return $this;
    }

    /**
     * Get invoice meta value by key
     *
     * @param mixed $key
     * @return mixed
     */
    public function getMeta($key)
    {
        if ($this->meta == null) {
            return null;
        }

        return array_key_exists($key, $this->meta) ? $this->meta[$key] : null;
    }

    /**
     * Delete meta value from invoice
     *
     * @param mixed $key
     * @return $this
     */
    public function deleteMeta($key)
    {
        if($this->meta === null) {
            return $this;
        }
        unset($this->meta[$key]);
        if (count($this->meta) == 0) {
            $this->meta = null;
        }

        $this->save();

        return $this;
    }

    /**
     * Return public or private invoice info
     *
     * @param bool $private
     * @return Apirone\SDK\Model\stdClass
     */
    public function info($private = false)
    {
        return $this->details->info($private);
    }

    /**
     * Convert invoice object to JSON
     *
     * @return Apirone\SDK\Model\stdClass
     */
    public function toJson(): \stdClass
    {
        $json = parent::toJson();
        unset($json->{'create-params'});
        unset($json->{'template'});

        return $json;
    }

    /**
     * Invoice details parser
     *
     * @param mixed $json
     * @return InvoiceDetails
     * @throws ReflectionException
     */
    protected function parseDetails($json)
    {
        $details = InvoiceDetails::fromJson($json);

        return $details;
    }

    /**
     * Invoice meta parser
     *
     * @param mixed $value
     * @return array
     */
    protected function parseMeta($value)
    {
        return (array) $value;
    }
}
