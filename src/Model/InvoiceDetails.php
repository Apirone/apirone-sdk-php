<?php

namespace Apirone\Invoice\Model;

use Apirone\API\Endpoints\Account;
use Apirone\API\Endpoints\Service;
use Apirone\Invoice\Model\AbstractModel;
use Apirone\Invoice\Model\UserData;
use Apirone\Invoice\Model\HistoryItem;

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

    public static function fromJson($json)
    {
        $class = new static();

        return $class->classLoader($json);
    }

    public function loadJson($json)
    {
        return $this->classLoader($json);
    }

    public function update()
    {
        $json = Account::init($this->account)->invoiceInfo($this->invoice);
        
        return $this->loadJson($json);
    }
    public function parseUserData($data)
    {
        $userData = UserData::fromJson($data);

        return $userData;
    }

    public function parseHistory($data)
    {
        $history = [];
        foreach ($data as $item) {
            $history[] = HistoryItem::fromJson($item);
        }

        return $history;
    }

}
