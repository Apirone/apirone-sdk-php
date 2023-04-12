<?php

namespace Apirone\Invoice;

use Apirone\Invoice\Db\InvoiceDb;
use Apirone\Invoice\Db\InvoiceQuery;
use Apirone\Invoice\Model\AbstractModel;
use Apirone\Invoice\Model\InvoiceDetails;
use Apirone\Invoice\Model\InvoiceMeta;
use Apirone\API\Endpoints\Service;
use Apirone\API\Endpoints\Account;

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

    public static function fromFiatAmount(float $value, string $from, string $to )
    {
        $class = new static();
        $class->createParams['currency'] = $to;
        $class->createParams['amount'] = Service::fiat2crypto($value, $from, $to);
        
        $class->price($value, $from);

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

    public function currency(?string $currency = null)
    {
        $this->createParams['currency'] = $currency;

        return $this;
    }

    public function amount(?string $amount = null)
    {
        $this->createParams['amount'] = $amount;

        return $this;
    }

    public function merchant(?string $merchant = null)
    {
        $this->createParams['user-data']['merchant'] = $merchant;

        return $this;
    }

    public function url(?string $url = null)
    {
        $this->createParams['user-data']['url'] = $url;

        return $this;
    }

    public function price(?float $amount = null, ?string $currency = null)
    {
        if ($currency == null || $amount == null) {
            unset($this->createParams['user-data']['price']);
        }
        else {
            $price = new \stdClass;
            $price->currency = $currency;
            $price->amount   = $amount;

            $this->createParams['user-data']['price'] = $price;
        }
        return $this;
    }

    public function lifetime(?int $lifetime = null)
    {
        $this->createParams['lifetime'] = $lifetime;

        return $this;
    }

    public function expired(?string $expired = null)
    {
        $this->createParams['expired'] = $expired;

        return $this;
    }

    public function linkback(?string $linkback = null)
    {
        $this->createParams['linkback'] = $linkback;

        return $this;
    }

    public function callbackUrl(?string $callbackUrl = null)
    {
        $this->createParams['callbackUrl'] = $callbackUrl;

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
        try {
            $created = $account->invoiceCreate(json_encode($this->createParams));
            $this->details = InvoiceDetails::fromJson($created);
            $this->invoice = $this->details->invoice;
            $this->status = $this->details->status;
            unset($this->createParams);
        }
        catch(Exception $e)
        {
            throw $e;
        }

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