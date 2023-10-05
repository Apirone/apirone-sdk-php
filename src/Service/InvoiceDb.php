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

namespace Apirone\SDK\Service;

use Apirone\API\Log\LoggerWrapper;
use Apirone\SDK\Service\InvoiceQuery;

class InvoiceDb
{
    /**
     * Callback handler for database execution
     *
     * @var false
     */
    public static $handler = false;

    /**
     * Database table prefix
     *
     * @var false
     */
    public static $prefix = false;

    /**
     * Set database callback function
     *
     * @param mixed $callback
     * @return void
     */
    public static function setCallback($callback)
    {
        $class = new \ReflectionClass(static::class);
        $class->setStaticPropertyValue('handler', $callback);
    }

    /**
     * Set database table prefix
     *
     * @param mixed $prefix
     * @return void
     */
    public static function setPrefix($prefix)
    {
        $class = new \ReflectionClass(static::class);
        $class->setStaticPropertyValue('prefix', $prefix);
    }

    /**
     * Database exec function
     *
     * @param mixed $query
     * @return mixed
     */
    public static function execute($query)
    {
        if (!self::$handler) {
            return false;
        }

        $result = call_user_func(self::$handler, $query);
        if (LoggerWrapper::$handler) {
            LoggerWrapper::debug("InvoiceDB::execute", ['query' => $query, 'result' => $result]);
        }

        return $result;
    }

    /**
     * Install invoice table into database
     *
     * @param string $prefix
     * @param string $charset
     * @param string $collate
     * @return mixed
     */
    public static function install(
        string $prefix = '',
        string $charset = 'utf8mb4',
        string $collate = 'utf8mb4_general_ci'
    ) {
        $prefix = static::$prefix !== false ? static::$prefix : $prefix;
        $query = InvoiceQuery::createInvoicesTable($prefix, $charset, $collate);

        return InvoiceDb::execute($query);
    }

    /**
     * Uninstall invoice table from database
     *
     * @param string $prefix
     * @return mixed
     */
    public static function uninstall(string $prefix = '')
    {
        $prefix = static::$prefix !== false ? static::$prefix : $prefix;
        $query = InvoiceQuery::dropInvoicesTable($prefix);

        return InvoiceDb::execute($query);
    }
}
