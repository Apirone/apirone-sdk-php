<?php

namespace Apirone\Invoice\Model;

use Apirone\Invoice\Model\AbstractModel;

class HistoryItem extends AbstractModel
{
    // date	string	Invoice status change date
    private ?string $date = null;

    // status	string	Invoice status
    private ?string $status = null;

    // txid	string	Identifier of the transaction in the blockchain
    private ?string $txid = null;

    // amount	integer	Paid amount
    private ?int $amount = null;

    private function __construct()
    {
    }
    
    public static function fromJson($json)
    {
        $class = new static();

        return $class->classLoader($json);
    }

    public function toArray(): array
    {
        $item = parent::toArray();

        if ($item['status'] == 'created' || $item['status'] == 'expired') {
            unset($item['txid'], $item['amount']);
        }

        return $item;
    }

}
