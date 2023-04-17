<?php

namespace Apirone\Invoice;

use Apirone\Invoice\Db\InvoiceDb;
use Apirone\Invoice\Db\InvoiceQuery;
use Apirone\Invoice\Model\AbstractModel;
use Apirone\Invoice\Model\InvoiceDetails;
use Apirone\Invoice\Model\InvoiceMeta;
use Apirone\API\Endpoints\Service;
use Apirone\API\Endpoints\Account;
use Apirone\Invoice\Model\UserData;
use Apirone\Invoice\Utils;
use stdClass;

class Invoice extends AbstractModel{

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

    public static function init(string $currency, ?int $amount = null)
    {
        $class = new static();

        $class->createParams['currency'] = $currency;

        if ($amount !== null ) {
            $class->createParams['amount'] = $amount;
        }

        return $class;
    }

    public static function fromFiatAmount(float $value, string $from, string $to, float $factor = 1)
    {
        $class = new static();
        $class->currency($to);
        $cryptoAmount = Service::fiat2crypto($value * $factor, $from, $to);
        $unitFactor = $class->createParams['currency']->{'units-factor'};

        $class->createParams['amount'] = Utils::cur2min($cryptoAmount, $unitFactor);
        
        return $class;
    }

    public static function fromJson($json)
    {
        $class = new static();

        return $class->classLoader($json);
    }

    public static function getInvoice($invoice)
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

    public function order(?int $order = null)
    {
        $this->createParams['order'] = $order;

        return $this;
    }

    public function currency(string $currency)
    {
        $currency = Utils::currency($currency);
        $this->createParams['currency'] = $currency;

        return $this;
    }

    public function amount(?int $amount = null)
    {
        $this->createParams['amount'] = $amount;

        return $this;
    }

    public function lifetime(?int $lifetime = null)
    {
        $this->createParams['lifetime'] = $lifetime;

        return $this;
    }

    public function expire(?string $expire = null)
    {
        $this->createParams['expire'] = $expire;

        return $this;
    }

    public function userData(?UserData $userData = null)
    {
        $this->createParams['user-data'] = ($userData instanceof UserData) ? $userData->toJson() : $userData;

        return $this;
    }

    public function linkback(?string $linkback = null)
    {
        $this->createParams['linkback'] = $linkback;

        return $this;
    }

    public function callbackUrl(?string $callbackUrl = null)
    {
        $this->createParams['callback-url'] = $callbackUrl;

        return $this;
    }

    public function create(string $account)
    {
        if ($this->invoice !== null || !isset($this->createParams)) {
            return $this;
        }

        $this->order = array_key_exists('order', $this->createParams) ? $this->createParams['order'] : 0;

        unset($this->createParams['order']);

        $account = Account::init($account);
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

    public function save()
    {
        $prefix = InvoiceDb::$prefix;
        $query = ($this->id === null) ? InvoiceQuery::createInvoice($this, $prefix) : InvoiceQuery::updateInvoice($this, $prefix);
        $result = InvoiceDb::execute($query);
        
        if ($result == true) {
            $this->id = ($this->id === null) ? 0 : $this->id;
        }

        return $result;
    }

    public function update()
    {
        $this->details->update();

        if ($this->status !== $this->details->status) {
            $this->status = $this->details->status;
            return $this->save();
        }

        return false;
    }

    public function setMeta($key, $value)
    {
        if ($this->meta === null)
            $this->meta = [];
        
        $this->meta[$key] = $value;
        
        $this->save();
        
        return $this;
    }

    public function getMeta($key)
    {
        return array_key_exists('$key', $this->meta) ? $this->meta[$key] : null;
    }

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

    public function info($private = false)
    {
        return $this->details->info($private);
    }
    
    public function toJson(): stdClass
    {
        $json = parent::toJson();
        unset($json->{'create-params'});

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