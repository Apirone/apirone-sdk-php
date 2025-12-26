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

use Apirone\SDK\Invoice;
use Apirone\SDK\Service\Db\HandlerNotSetException;
use Apirone\SDK\Service\Logger;

/**
 * @package Apirone\SDK\Service
 *
 * @method public adapter(string $adapter)
 * @method public handler(Callable $handler)
 * @method public prefix(string $prefix)
 * @method public table(string $table)
 */
class Db
{
    /**
     * Callback handler for database execution
     *
     * @var callable
     */
    public static $handler = null;

    /**
     * Database adepter
     *
     * @var string
     */
    private static $adapter = 'mysql';

    /**
     * Database table name
     *
     * @var string
     */
    public static $table = 'apirone_invoice';

    /**
     * Database table prefix
     *
     * @var string
     */
    public static $prefix = '';

    private function __construct() {}

    public function __get($name)
    {
        if (\property_exists($this, $name)) {
            $class = new \ReflectionClass(static::class);

            return $class->getStaticProperties()[$name];
        }
        else {
            $adapter = new \ReflectionClass(static::adapterClass());
            if (\property_exists(static::adapterClass(), $name)) {
                return $adapter->getStaticProperties()[$name];
            }
            else {
                $trace = \debug_backtrace();
                \trigger_error(
                    'Undefined property ' . $name .
                    ' in class ' . static::class .
                    ' or adapter class ' .  $adapter->name .
                    ' in ' . $trace[0]['file'] .
                    ' on line ' . $trace[0]['line'],
                    \E_USER_NOTICE
                );
            }
        }

        return null;
    }

    public function __call($name, $args = [])
    {
        return static::__callStatic($name, $args);
    }

    public static function __callStatic($name, $args = [])
    {
        if (\property_exists(static::class, $name)) {
            $class = new \ReflectionClass(static::class);
            $class->setStaticPropertyValue($name, $args[0] ?? $class->getDefaultProperties()[$name]);
        }
        else {
            $adapter = new \ReflectionClass(static::adapterClass());
            if (\property_exists(static::adapterClass(), $name)) {
                $adapter->setStaticPropertyValue($name, $args[0] ?? $adapter->getDefaultProperties()[$name]);
            }
            else {
                $trace = \debug_backtrace();
                \trigger_error(
                    'Undefined property ' . $name .
                    ' in class ' . $trace[0]['file'] .
                    ' or adapter class ' .  $adapter->name,
                    \E_USER_NOTICE
                );
            }
        }

        return new static();
    }

    /**
     * Returns adapter class namespace
     *
     * @return string
     */
    private static function adapterClass()
    {
        $adapters = ['mysql','sqlite','postgres'];
        $class = in_array(static::$adapter, $adapters) ? sprintf('Apirone\SDK\Service\Db\%s', ucfirst(static::$adapter)) : static::$adapter;

        return $class;
    }
    /**
     * Returns the full name of the invoice table.
     *
     * @return string
     */
    public static function tableName()
    {
        return static::$prefix . static::$table;
    }

    /**
     * Throws an exception if the handler is not set.
     *
     * @return void
     */
    public static function checkHandler()
    {
        if (!is_callable(static::$handler)) {
            throw new HandlerNotSetException('Db handler not set');
        }
    }

    /**
     * Database exec function
     *
     * @param mixed $query
     * @return mixed
     */
    public static function execute($query)
    {
        static::checkHandler();

        $result = call_user_func(static::$handler, $query);
        Logger::debug("Db::execute()", ['query' => $query, 'result' => $result]);

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
    public static function install()
    {
        return Db::execute(DB::adapterClass()::createTable());
    }

    /**
     * Uninstall invoice table from database
     *
     * @return mixed
     */
    public static function uninstall()
    {
        return Db::execute(DB::adapterClass()::dropTable());
    }

    /**
     * Save created or update existed
     *
     * @param Invoice $invoice
     * @return mixed
     */
    public static function saveInvoice(Invoice $invoice)
    {
        $adapter = DB::adapterClass();
        $query = ($invoice->id === null) ? $adapter::createInvoice($invoice) : $adapter::updateInvoice($invoice);

        return Db::execute($query);
    }

    public static function getInvoice(string $invoice)
    {
        return Db::execute(DB::adapterClass()::getInvoice($invoice));
    }

    public static function getOrderInvoices(int $order)
    {
        return Db::execute(DB::adapterClass()::getOrderInvoices($order));
    }
}
