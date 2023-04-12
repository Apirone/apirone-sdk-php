<?php

namespace Apirone\Invoice\Db;

use Apirone\Invoice\Db\InvoiceDb;
use Apirone\Invoice\Db\InvoiceQuery;
use Apirone\Invoice\Db\SettingsQuery;

class Setup
{
    public static function install(
        string $prefix = '',
        string $charset = 'utf8',
        string $collate = 'utf8_general_ci'
    ) {
        $res = false;
        $query = InvoiceQuery::createInvoicesTable($prefix, $charset, $collate);
        $res = InvoiceDb::execute($query);

        $query = SettingsQuery::createSettingsTable($prefix, $charset, $collate);
        $res = InvoiceDb::execute($query);

        return $res;
    }

    public static function uninstall(string $prefix = '')
    {
        $res = false;
        $query = InvoiceQuery::dropInvoicesTable($prefix);
        $res = InvoiceDb::execute($query);

        $query = SettingsQuery::dropSettingsTable($prefix);
        $res = InvoiceDb::execute($query);

        return $res;
    }
}