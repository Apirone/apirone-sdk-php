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

namespace Apirone\SDK\Model;

use Apirone\API\Endpoints\Account;
use Apirone\SDK\Model\AbstractModel;
use Apirone\SDK\Model\UserData;
use Apirone\SDK\Model\HistoryItem;

/**
 * Apirone invoice wrapper class
 *
 * @package Apirone\SDK\Model
 *
 * @property-read string $account
 * @property-read string $invoice
 * @property-read string $created
 * @property-read string $currency
 * @property-read string $address
 * @property-read string $expire
 * @property-read string $amount
 * @property-read string $userData
 * @property-read string $status
 * @property-read string $history
 * @property-read string $linkback
 * @property-read string $callbackUrl
 * @property-read string $invoiceUrl
 */

class InvoiceDetails extends AbstractModel
{
    /**
     * @var null|string Account Identifier
     */
    private ?string $account;

    /**
     * @var	null|string	Invoice Identifier
     */
    private ?string $invoice;

    /**
     *
     * @var null|string Invoice creation date. Contains the full date in ISO-8601 format, for example, 2022-02-22T09:00:30
     */
    private ?string $created = null;

    /**
     * @var null|string Currency type
     */
    private ?string $currency = null;

    /**
     * @var null|string The generated cryptocurrency address to receive a payment from a customer
     */
    private ?string $address = null;

    /**
     * @var null|string Invoice expiration time in ISO-8601 format, for example, 2022-02-22T09:00:30
     */
    private ?string $expire = null;

    /**
     * @var null|string Amount in the selected currency
     */
    private ?string $amount = null;

    /**
     * @var null|UserData Some additional information about the invoice
     */
    private ?UserData $userData = null;

    /**
     * @var null|string Invoice status
     */
    private ?string $status = null;

    /**
     * @var null|array Invoice status change history
     */
    private ?array $history = null;

    /**
     * @var null|string The customer will be redirected to this URL after the payment is completed
     */
    private ?string $linkback = null;

    /**
     * @var null|string Callback URL to receive data about the payment
     */
    private ?string $callbackUrl = null;

    /**
     * @var null|string Link to the invoice web view
     */
    private ?string $invoiceUrl = null;

    private function __construct() {}

    /**
     * Creates a new instance
     *
     * @return static
     */
    public static function init()
    {
        $class = new static();

        return $class;
    }

    /**
     * Restores instance from JSON
     *
     * @param mixed $json
     * @return $this
     */
    public static function fromJson($json)
    {
        $class = new static();

        return $class->classLoader($json);
    }

    /**
     * Gets invoice info from apirone and create instance from it
     *
     * @return $this
     */
    public function update()
    {
        if ($this->isExpired() && $this->status == 'expired') {
            return $this;
        }
        if ($this->status == 'completed') {
            return $this;
        }
        $json = Account::init($this->account)->invoiceInfo($this->invoice);

        return $this->classLoader($json);
    }

    /**
     * Invoice UserData parser
     *
     * @param mixed $data
     * @return UserData
     */
    protected function parseUserData($data)
    {
        $userData = UserData::fromJson($data);

        return $userData;
    }

    /**
     * Invoice history data parser
     *
     * @param mixed $data
     * @return array
     */
    protected function parseHistory($data)
    {
        $history = [];
        foreach ($data as $item) {
            $history[] = HistoryItem::fromJson($item);
        }

        return $history;
    }

    /**
     * Returns invoice public or private invoice info JSON
     *
     * @param bool $private
     * @return stdClass
     */
    public function info($private = false)
    {

        $info = $this->toJson();
        unset($info->{'create-params'});

        if (!$private) {
            unset($info->{'callback-url'}, $info->account);
        }

        return $info;
    }

    /**
     * Is invoice expired
     *
     * @return bool
     */
    public function isExpired()
    {
        if ($this->status == 'expired') {
            return true;
        }

        if (in_array($this->status, ['paid', 'overpaid', 'completed'])) {
            return false;
        }

        if ($this->expire == null || strtotime($this->expire . ' UTC') > time()) {
            return false;
        }

        return true;
    }
}
