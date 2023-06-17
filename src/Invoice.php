<?php
/**
 * This file is part of the Apirone Invoice library.
 *
 * (c) Alex Zaytseff <alex.zaytseff@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Apirone\Invoice;

use Apirone\Invoice\Db\InvoiceDb;
use Apirone\Invoice\Db\InvoiceQuery;
use Apirone\Invoice\Model\AbstractModel;
use Apirone\Invoice\Model\InvoiceDetails;
use Apirone\API\Endpoints\Service;
use Apirone\API\Endpoints\Account;
use Apirone\API\Exceptions\RuntimeException;
use Apirone\API\Exceptions\ValidationFailedException;
use Apirone\API\Exceptions\UnauthorizedException;
use Apirone\API\Exceptions\ForbiddenException;
use Apirone\API\Exceptions\InternalServerErrorException;
use Apirone\API\Exceptions\NotFoundException;
use Apirone\API\Exceptions\MethodNotAllowedException;
use Apirone\API\Http\ErrorDispatcher;
use Apirone\Invoice\Model\UserData;
use Apirone\Invoice\Model\Settings;
use Apirone\Invoice\Utils;
use Closure;
use ReflectionException;

class Invoice extends AbstractModel{

    public static Settings $settings;

    private ?int $id = null;

    private ?int $order = null;

    private ?string $invoice = null;

    private ?string $status = null;

    private ?InvoiceDetails $details;

    private ?array $meta = null;

    private ?array $createParams;

    private function __construct()
    {
    }

    /**
     * Set settings to Iinvoice object
     *
     * @param Settings $settings 
     * @return void 
     */
    public static function config(\Apirone\Invoice\Model\Settings $settings): void
    {
        static::$settings = $settings;
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
     * Set log handler
     *
     * @param Closure $handler 
     * @return void 
     */
    public static function log(\Closure $handler): void
    {
        ErrorDispatcher::setCallback($handler);
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

        // $class->createParams['currency'] = $currency;
        $class->currency($currency);

        if ($amount !== null ) {
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
     * Get Invoce from data table
     *
     * @param mixed $invoice 
     * @return null|Invoice 
     * @throws ReflectionException 
     */
    public static function getInvoice($invoice): ?Invoice
    {
        $prefix = InvoiceDb::$prefix;
        $query = InvoiceQuery::selectInvoice($invoice, $prefix);

        $result = InvoiceDb::execute($query);

        if (empty($result)) {
            return null;
        }
        $row = $result[0];
        $json = new \stdClass;
        $json->id = $row['id'];
        $json->order = $row['order'];
        $json->invoice = $row['invoice'];
        $json->status = $row['status'];
        $json->details = json_decode($row['details']);
        $json->meta = json_decode($row['meta']);

        return Invoice::fromJson($json);
    }

    public static function getOrderInvoices($order): array
    {
        $prefix = InvoiceDb::$prefix;
        $query = InvoiceQuery::selectOrder($order, $prefix);

        $result = InvoiceDb::execute($query);
        
        $invoices = [];
        
        if ($result === null) {
            return $invoices;
        }
        foreach($result as $data) {
            $json = new \stdClass;
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
     * Invoce callback handler
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
            Utils::send_json('Data not received', 400);
            return;
        }

        if (!property_exists($params, 'invoice') || !property_exists($params, 'status')) {
            Utils::send_json('Wrong params received: ' . json_encode($params), 400); 
            return;
        }

        $invoice = Invoice::getInvoice($params->invoice);

        if (!$invoice) {
            Utils::send_json("Invoice not found: " . $params->invoice, 404);
            return;
        }

		if($invoice->update()) {
            if($order_handler !== null) {
                $order_handler($invoice);
            }
		}
		exit;
    }

    public function order(?int $order = null)
    {
        $this->createParams['order'] = $order;

        return $this;
    }

    /**
     * Set currency
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
     * Set amount
     * 
     * @param null|int $amount 
     * @return $this 
     */
    public function amount(?int $amount = null)
    {
        $this->createParams['amount'] = $amount;

        return $this;
    }

    /**
     * set lifetime
     * 
     * @param null|int $lifetime 
     * @return $this 
     */
    public function lifetime(?int $lifetime = null)
    {
        $this->createParams['lifetime'] = $lifetime;

        return $this;
    }

    /**
     * Set expire
     * 
     * @param null|string $expire 
     * @return $this 
     */
    public function expire(?string $expire = null)
    {
        $this->createParams['expire'] = $expire;

        return $this;
    }

    /**
     * Set UserData object
     *
     * @param null|UserData $userData 
     * @return $this 
     */
    public function userData(?UserData $userData = null)
    {
        $this->createParams['user-data'] = ($userData instanceof UserData) ? $userData->toJson() : $userData;

        return $this;
    }

    /**
     * Set linkback
     * 
     * @param null|string $linkback 
     * @return $this 
     */
    public function linkback(?string $linkback = null)
    {
        $this->createParams['linkback'] = $linkback;

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
        $this->createParams['callback-url'] = $callbackUrl;

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
        catch(Exception $e)
        {
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
        $this->details->update();

        if ($this->status !== $this->details->status) {
            $this->status = $this->details->status;
            return $this->save();
        }

        return false;
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
        if ($this->meta === null)
            $this->meta = [];
        
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
        return array_key_exists('$key', $this->meta) ? $this->meta[$key] : null;
    }

    /**
     * Delete meta valie from invoice
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
     * @return Apirone\Invoice\Model\stdClass 
     */
    public function info($private = false)
    {
        return $this->details->info($private);
    }

    /**
     * convert invoice to JSON
     * 
     * @return Apirone\Invoice\Model\stdClass 
     */
    public function toJson(): \stdClass
    {
        $json = parent::toJson();
        unset($json->{'create-params'});
        unset($json->{'template'});

        return $json;
    }

    protected function parseDetails($json)
    {
        $details = InvoiceDetails::fromJson($json);

        return $details;
    }

    protected function parseMeta($value)
    {
        return (array) $value;
    }

}
