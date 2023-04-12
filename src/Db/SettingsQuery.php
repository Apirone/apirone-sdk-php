<?php

namespace Apirone\Invoice\Db;

class SettingsQuery
{
    const TABLE_SETTINGS = 'apirone_settings';

    /**
     * Return create settings table SQL query
     * 
     * @param string $prefix 
     * @param string $charset 
     * @param string $collate 
     * @return string 
     */
    public static function createSettingsTable (
        string $prefix = '',
        string $charset = 'utf8',
        string $collate = 'utf8_general_ci'
    ): string {
        $table = $prefix . self::TABLE_SETTINGS;

        $charset_collate = '';
        if ( ! empty( $charset ) ) {
            $charset_collate = "DEFAULT CHARACTER SET $charset";
        }
        if ( ! empty( $collate ) ) {
                $charset_collate .= " COLLATE $collate";
        }

        $query = "CREATE TABLE IF NOT EXISTS `$table` (
            `id` int NOT NULL AUTO_INCREMENT,
            `key` varchar(128) NOT NULL,
            `value` text NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `key` (`key`)
        ) ENGINE=InnoDB $charset_collate;";

        return $query;
    }

    /**
     * Return drop settings table SQL query
     * @param string $prefix 
     * @return string 
     */
    public static function dropSettingsTable(string $prefix = '')
    {
        $table = $prefix . self::TABLE_SETTINGS;

        $query = "DROP TABLE IF EXISTS `$table`;";

        return $query;
    }

}