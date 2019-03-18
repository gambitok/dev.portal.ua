<?php

require_once __DIR__ . '/mysql_class.php';

class DbSingleton
{
    private static $instanceDb;
    private static $instanceTokoDb;

    public static function getDb()
    {
        if (self::$instanceDb === null) {
            self::$instanceDb = new db();
            self::$instanceDb->connect();
        }

        return self::$instanceDb;
    }

    public static function getTokoDb()
    {
        if (self::$instanceTokoDb === null) {
            self::$instanceTokoDb = new dbt();
            self::$instanceTokoDb->connect();
        }

        return self::$instanceTokoDb;
    }
}