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

use Apirone\SDK\Model\AbstractModel;
use ReflectionException;

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

    private function __construct() {}

    /**
     * Restore object from JSON
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
     * Convert object to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $item = parent::toArray();

        if ($item['status'] == 'created' || $item['status'] == 'expired') {
            unset($item['txid'], $item['amount']);
        }

        return $item;
    }


    /**
     * Get the value of date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get the value of status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get the value of txid
     */
    public function getTxid()
    {
        return $this->txid;
    }

    /**
     * Get the value of amount
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
