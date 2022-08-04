<?php

require_once __DIR__ . '/mysql_class.php';

class DbSingleton
{
    private static $instanceDb;
    private static $instanceTokoDb;
    private static $instanceTokoCacheDb;

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

    public static function getTokoCacheDb()
    {
        if (self::$instanceTokoCacheDb === null) {
            self::$instanceTokoCacheDb = new dbc();
            self::$instanceTokoCacheDb->connect();
        }

        return self::$instanceTokoCacheDb;
    }
}