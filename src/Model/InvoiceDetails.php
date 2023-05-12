<?php

namespace Apirone\Invoice\Model;

use Apirone\API\Endpoints\Account;
use Apirone\API\Endpoints\Service;
use Apirone\Invoice\Model\AbstractModel;
use Apirone\Invoice\Model\UserData;
use Apirone\Invoice\Model\HistoryItem;
use Apirone\Invoice\Tools\Utils;

class InvoiceDetails extends AbstractModel
{

    /**
     * @var null|string Account Identifier
     */
    private ?string $account;

    // invoice	string	Invoice Identifier
    private ?string $invoice;

    // created	string	Invoice creation date. Contains the full date in ISO-8601 format, for example, 2022-02-22T09:00:30
    private ?string $created;

    // currency	string	Currency type
    private ?string $currency;

    // address	string	The generated cryptocurrency address to receive a payment from a customer
    private ?string $address;

    // expire	string	Invoice expiration time in ISO-8601 format, for example, 2022-02-22T09:00:30
    private ?string $expire;

    // amount	integer	Amount in the selected currency
    private ?int $amount;

    // user-data	object	Some additional information about the invoice
    private ?UserData $userData = null;

    // status	string	Invoice status. More information see here
    private ?string $status;

    // history	array	Invoice status change history
    private ?array $history = null;

    // linkback	string	The customer will be redirected to this URL after the payment is completed
    private ?string $linkback = null;

    // callback-url	string	Callback URL to receive data about the payment
    private ?string $callbackUrl = null;

    // invoice-url	string	Link to the invoice web view
    private ?string $invoiceUrl = null;
    
    private function __construct()
    {
    }

    public static function init() 
    {
        $class = new static();

        return $class;
    }

    public static function fromJson($json)
    {
        $class = new static();

        return $class->classLoader($json);
    }

    protected function loadJson($json)
    {
        return $this->classLoader($json);
    }

    public function update()
    {
        $json = Account::init($this->account)->invoiceInfo($this->invoice);
        
        return $this->classLoader($json);
    }

    protected function parseUserData($data)
    {
        $userData = UserData::fromJson($data);

        return $userData;
    }

    protected function parseHistory($data)
    {
        $history = [];
        foreach ($data as $item) {
            $history[] = HistoryItem::fromJson($item);
        }

        return $history;
    }

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
     * Get the value of created
     */ 
    public function getCreated()
    {
        return $this->created;
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

    public function remains($minor = false)
    {
      $remains = $this->amount;

      foreach ($this->history as $item) {
        if (property_exists($item, 'amount')) {
          $remains = $remains - $item->amount;
        }
      }
      if ($minor) {
        return $remains;
      }

      return $remains;
    }

    public function currencyInfo() {
      return Utils::currency($this->currency);
    }

    public function countdown()
    {
        $expire = strtotime($this->expire);
        $now = time();
        
        return ($expire > $now) ? $expire - $now : 0;
    }
}
