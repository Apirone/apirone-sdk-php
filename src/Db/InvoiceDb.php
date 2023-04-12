<?php

namespace Apirone\Invoice\Db;


class InvoiceDb
{
    static $handler = false;
    
    static $prefix = false;

    public static function execute($query)
    {
        if (!self::$handler) {
            return false;
        }

        return call_user_func(self::$handler, $query);
    }

    public static function setCallback($callback)
    {
        $class = new \ReflectionClass(static::class);
        $class->setStaticPropertyValue('handler', $callback);
    }

    public static function setPrefix($prefix)
    {
        $class = new \ReflectionClass(static::class);
        $class->setStaticPropertyValue('prefix', $prefix);
    }

}