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
use Apirone\API\Endpoints\Service;
use Apirone\API\Exceptions\RuntimeException;
use Apirone\API\Exceptions\ValidationFailedException;
use Apirone\API\Exceptions\UnauthorizedException;
use Apirone\API\Exceptions\ForbiddenException;
use Apirone\API\Exceptions\NotFoundException;
use Apirone\API\Exceptions\MethodNotAllowedException;
use Apirone\SDK\Invoice;
use Apirone\SDK\Model\AbstractModel;
use Apirone\SDK\Model\UserData;
use Apirone\SDK\Model\HistoryItem;
use Apirone\SDK\Tools\Utils;
use ReflectionException;

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
    private ?string $created;

    /**
     * @var null|string Currency type
     */
    private ?string $currency;

    /**
     * @var null|string The generated cryptocurrency address to receive a payment from a customer
     */
    private ?string $address;

    /**
     * @var null|string Invoice expiration time in ISO-8601 format, for example, 2022-02-22T09:00:30
     */
    private ?string $expire;

    /**
     * @var null|int Amount in the selected currency
     */
    private ?int $amount;

    /**
     * @var null|UserData Some additional information about the invoice
     */
    private ?UserData $userData = null;

    /**
     * @var null|string Invoice status
     */
    private ?string $status;

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
     * Create a new instance
     *
     * @return static
     */
    public static function init()
    {
        $class = new static();

        return $class;
    }

    /**
     * Restore instance from JSON
     *
     * @param mixed $json
     * @return $this
     * @throws ReflectionException
     */
    public static function fromJson($json)
    {
        $class = new static();

        return $class->classLoader($json);
    }

    /**
     * Get invoice info from apirone and create instance from it
     *
     * @return $this
     * @throws RuntimeException
     * @throws ValidationFailedException
     * @throws UnauthorizedException
     * @throws ForbiddenException
     * @throws NotFoundException
     * @throws MethodNotAllowedException
     * @throws ReflectionException
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
     * @throws ReflectionException
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
     * @throws ReflectionException
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
     * Return invoice public or private invoice info
     *
     * @param bool $private
     * @return Apirone\SDK\Model\stdClass
     */
    public function info($private = false)
    {
        $info = $this->toJson();
        if (!$private) {
            unset($info->{'callback-url'}, $info->account);
        }
        unset($info->{'create-params'});

        if ($info->{'user-data'} !== null) {
            foreach ($info->{'user-data'} as $key => $value) {
                if ($value === null) {
                    unset($info->{'user-data'}->{$key});
                }
            }
        }

        return $info;
    }

    /**
     * Get the value of currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Get the value of address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Get the value of expire
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * Get the value of amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get the value of userData
     */
    public function getUserData()
    {
        return $this->userData;
    }

    /**
     * Get the value of status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get the value of history
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Get the value of linkback
     */
    public function getLinkback()
    {
        return $this->linkback;
    }

    /**
     * Get the value of callbackUrl
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * Get the value of invoiceUrl
     */
    public function getInvoiceUrl()
    {
        return $this->invoiceUrl;
    }

    /**
     * Is invoice expired
     *
     * @return bool
     */
    public function isExpired(): bool
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

    /**
     * Returns the number of seconds until an invoice expires
     * If the invoice has status paid, overpaid,completed or expired - returns -1
     *
     * @return int
     */
    public function timeToExpire()
    {
        if ($this->expire == null || !in_array($this->status, ['created', 'partpaid'])) {
            return -1;
        }

        $expire = strtotime($this->expire . ' UTC');
        $now = time();

        return ($expire > $now) ? $expire - $now : -1;
    }

    /**
     * Show linkback if set and invoice status is paid or overpaid
     *
     * @return bool
     */
    public function showLinkback()
    {
        if ($this->linkback && in_array($this->status, ['paid', 'overpaid'])) {
            return true;
        }

        return false;
    }

    /**
     * Return count of history items.
     * In case when invoice completed or expired return zero value
     *
     * @return int|int<0, max>
     */
    public function statusNum()
    {
        switch ($this->status) {
            case 'completed':
            case 'expired':
                return 0;
            default:
                return count($this->history);
        }
    }
}
