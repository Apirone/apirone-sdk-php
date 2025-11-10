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

use Apirone\SDK\Service\Db;
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
use Apirone\SDK\Service\Logger;
use Apirone\SDK\Model\UserData;
use Apirone\SDK\Model\Settings;
use Apirone\SDK\Service\Render;
use Apirone\SDK\Service\Utils;
use DivisionByZeroError;
use ReflectionException;

class Invoice extends AbstractModel
{
    /**
     * Invoice settings object
     *
     * @var Settings
     */
    public static Settings $settings;

    /**
     * Minimum interval for checking invoice status
     *
     * @var int
     */
    public static int $checkInterval = 0;

    /**
     * Invoice record Id - auto increment
     * @var null|int
     */
    private ?int $id = null;

    /**
     * Last updated time
     * @var mixed
     */
    private $time;

    /**
     * Order ID in the external system
     * @var null|int
     */
    private ?int $order = null;

    /**
     * Invoice ID
     * @var null|string
     */
    private ?string $invoice = null;

    /**
     * Invoice status
     * @var null|string
     */
    private ?string $status = null;

    /**
     * Apirone invoice data object
     *
     * @var null|\Apirone\SDK\Model\InvoiceDetails
     */
    private ?InvoiceDetails $details = null;

    /**
     * Additional invoice properties 'key->value'storage
     *
     * @var null|\stdClass
     */
    private ?\stdClass $meta = null;

    /**
     * Parameter storage for invoice creation
     * @var null|array
     */
    private ?array $createParams = null;

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
    }

    /**
     * Set invoice update timer
     *
     * @param int $interval
     * @return void
     */
    public static function checkInterval(int $interval = 0): void
    {
        static::$checkInterval = $interval;
    }

    /**
     * Init new Invoice class
     *
     * @param string $currency
     * @param null|int $amount
     * @return static
     */
    public static function init(string $currency, $amount = null): Invoice
    {
        $class = new static();

        $class->currency($currency);

        if ($amount !== null) {
            $class->amount($amount);
        }

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
    public static function get(?string $invoice): ?Invoice
    {
        $result = Db::getInvoice($invoice);
        if (empty($result)) {
            return new static();
        }
        $row = $result[0];
        $json = new \stdClass();
        $json->id = $row['id'];
        $json->time = strtotime($row['time']);
        $json->order = $row['order'];
        $json->invoice = $row['invoice'];
        $json->status = $row['status'];
        $json->details = json_decode($row['details']);
        $json->meta = $row['meta'] !== NULL ? json_decode($row['meta']) : null;

        return Invoice::fromJson($json);
    }

    /**
     * Get invoices objects array for order with orderID
     *
     * @param int $order - Order ID in your system
     * @return array
     */
    public static function getByOrder(int $order): array
    {
        $result = Db::getOrderInvoices($order);

        $invoices = [];

        if ($result === null) {
            return $invoices;
        }
        foreach($result as $row) {
            $json = new \stdClass();
            $json->id = $row['id'];
            $json->order = $row['order'];
            $json->invoice = $row['invoice'];
            $json->status = $row['status'];
            $json->details = json_decode($row['details']);
            $json->meta = $row['meta'] !== NULL ? json_decode($row['meta']) : null;

            $invoice = Invoice::fromJson($json);
            if ($invoice->details->isExpired() == true && $invoice->status !="expired") {
                $invoice->update();
            }

            $invoices[] = $invoice;
        }


        return $invoices;
    }

    /**
     * Invoice callback handler
     *
     * @return void
     * @throws ReflectionException
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     */
    public static function callbackHandler(?callable $paymentProcessing = null, ?callable $callbackChecker = null): void
    {

        $data = file_get_contents('php://input');
        $params = ($data) ? json_decode(Utils::sanitize($data)) : null;

        if (!$params) {
            $message = 'Data not received';
            Logger::debug($message);
            Utils::sendJson('Data not received', 400);

            return;
        }

        if (!property_exists($params, 'invoice') || !property_exists($params, 'status')) {
            $message = 'Wrong params received: ' . json_encode($params);
            Logger::debug($message);
            Utils::sendJson('Wrong params received: ' . json_encode($params), 400);

            return;
        }

        $invoice = Invoice::get($params->invoice);

        if (!$invoice->invoice) {
            $message = "Invoice not found: " . $params->invoice;
            Logger::debug($message);
            Utils::sendJson($message, 404);

            return;
        }

        if (is_callable($callbackChecker)) {
            call_user_func($callbackChecker, $invoice);
        }

        if ($invoice->update() && is_callable($paymentProcessing)) {
            call_user_func($paymentProcessing, $invoice);
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
     * Set currency for new invoice
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
     * @param $amount
     * @return $this
     */
    public function amount($amount = null)
    {
        if (!$this->id) {
            if ($amount < 9223372036854775807) {
                $this->createParams['amount'] = (int) $amount;
            }
            else {
                $this->createParams['amount'] = (string) $amount;
            }
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
        Db::checkHandler();

        if ($this->invoice !== null || !isset($this->createParams)) {
            return $this;
        }

        $this->order = array_key_exists('order', $this->createParams) ? $this->createParams['order'] : 0;

        unset($this->createParams['order']);
        $account_id = ($account === null) ? Invoice::$settings->account : $account;

        $account = Account::init($account_id);
        $created = false;
        $options = $this->createParams;
        $options['currency'] = $this->createParams['currency']->abbr;

        $created = $account->invoiceCreate(json_encode($options));
        $this->details = InvoiceDetails::fromJson($created);
        $this->invoice = $this->details->invoice;
        $this->status = $this->details->status;
        unset($this->createParams);

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

        $result = Db::saveInvoice($this);

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
        if(!isset($this->details)) {
            return false;
        }

        if (Invoice::$checkInterval > 0) {
            $interval = Invoice::$checkInterval <= 5 ? 5 : Invoice::$checkInterval;

            if (time() - $this->time < $interval) {
                return false;
            }
        }

        $historyCount = count($this->details->history);
        $this->details->update();

        if ($historyCount == count($this->details->history)) {
            return false;
        }

        $this->status = $this->details->status;

        return $this->save();
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

        foreach ($json as $key => $val) {
            if (!in_array($key, ['id', 'order', 'invoice', 'details', 'status', 'meta'])) {
                unset($json->{$key});
            }
        }

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
}
