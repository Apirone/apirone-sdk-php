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

/**
 * @property-read string $date
 * @property-read string $status
 * @property-read string $txid
 * @property-read int    $amount
 */
class HistoryItem extends AbstractModel
{
    private ?string $date = null;

    private ?string $status = null;

    private ?string $txid = null;

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
    public function toArray(array $skip = []): array
    {
        $item = parent::toArray();

        if ($item['status'] == 'created' || $item['status'] == 'expired') {
            unset($item['txid'], $item['amount']);
        }

        return $item;
    }


    /**
     * Get the value of date
     *
     * @deprecated Use $class->date
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get the value of status
     *
     * @deprecated Use $class->status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get the value of txid
     *
     * @deprecated Use $class->txid
     */
    public function getTxid()
    {
        return $this->txid;
    }

    /**
     * Get the value of amount
     *
     * @deprecated Use $class->amount
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
