<?php

namespace Apirone\Invoice\Model\UserData;

use Apirone\Invoice\Model\AbstractModel;

class OrderItem extends AbstractModel
{
    // date	string	Invoice status change date
    private ?string $item = null;

    // status	string	Invoice status
    private ?string $cost = null;

    private ?int $qty = null;

    private ?string $total = null;

    private function __construct(
        ?string $item = null,
        ?string $cost = null,
        ?int $qty = null,
        ?string $total = null
    ) {
        $this->item = $item;
        $this->cost = $cost;
        $this->qty = $qty;
        $this->total = $total;
    }

    public static function init(string $item, string $cost, int $qty, string $total)
    {
        $class = new static($item, $cost, $qty, $total);

        return $class;
    }

    public static function fromJson($json)
    {
        $class = new static();

        return $class->classLoader($json);
    }

    public function toString()
    {
        return $this->amount . $this->currency;
    }

}
