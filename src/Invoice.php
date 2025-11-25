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
use Apirone\SDK\Model\AbstractModel;
use Apirone\SDK\Model\InvoiceDetails;
use Apirone\API\Endpoints\Account;
use Apirone\SDK\Service\Logger;
use Apirone\SDK\Model\UserData;
use Apirone\SDK\Model\Settings;
use Apirone\SDK\Service\Utils;

/** @package Apirone\SDK */
class Invoice extends AbstractModel
{
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
     * Init new Invoice class
     *
     * @param string $account
     * @param string $currency
     * @return static
     */
    public static function init(string $account, string $currency)
    {
        $class = new static();

        $class->createParams['account'] = $account;
        $class->createParams['currency'] = $currency;

        return $class;
    }

    /**
     * Restore invoice from JSON object
     *
     * @param mixed $json
     * @return static
     */
    public static function fromJson($json)
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
     */
    public static function get(?string $invoice)
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
        $json->meta = $row['meta'] !== null ? json_decode($row['meta']) : null;

        return Invoice::fromJson($json);
    }

    /**
     * Get invoices objects array for order with orderID
     *
     * @param int $order - Order ID in your system
     * @return array
     */
    public static function getByOrder(int $order)
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
            $json->meta = $row['meta'] !== null ? json_decode($row['meta']) : null;

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
     * @param null|callable $paymentProcessing
     * @param null|callable $callbackChecker
     * @return void
     */
    public static function callbackHandler(?callable $paymentProcessing = null, ?callable $callbackChecker = null)
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
     * @param null|string $account
     * @return $this
     */
    public function create()
    {
        Db::checkHandler();

        if ($this->invoice !== null || !isset($this->createParams)) {
            return $this;
        }

        $account = $this->createParams['account'];
        $this->order = $this->createParams['order'] ?? 0;

        unset($this->createParams['order'], $this->createParams['account']);

        $invoice = Account::init($account)->invoiceCreate(json_encode($this->createParams));

        $this->details = InvoiceDetails::fromJson($invoice);
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
    public function save()
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
     * @param int $checkInterval default 0
     * @return false|bool
     */
    public function update($checkInterval = 0)
    {
        if(!isset($this->details)) {
            return false;
        }

        if ($checkInterval > 0) {
            $interval = $checkInterval <= 5 ? 5 : $checkInterval;

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
     * @return stdClass
     */
    public function info($private = false)
    {
        return $this->details->info($private);
    }

    /**
     * Convert invoice object to JSON
     *
     * @return stdClass
     */
    public function toJson()
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
     */
    protected function parseDetails($json)
    {
        $details = InvoiceDetails::fromJson($json);

        return $details;
    }
}
