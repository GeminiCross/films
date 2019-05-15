<?php

namespace Films\Modules;

use PDO;

class Db extends PDO
{
    public static $dsn;

    public static $db_username;

    public static $db_password;

    public static $db_charset;

    private static $connect;

    /*
    возвращает соединение с базой данных
    если соединения нет, создаёт новое
    (некое подобие синглтон, но т.к. класс наследуется от PDO, конструктор приватным сделать не получится)
    */

    public static function getConnect() : Db {
        if (static::$connect == null) {
            static::$connect = new Db();
            static::$connect->exec('SET NAMES ' . static::$db_charset);
        }
        return static::$connect;
    }

    public function __construct()
    {
        static::getConfig();
        parent::__construct(static::$dsn, static::$db_username, static::$db_password);
    }

    /*загружает конфигурацию базы данных и присваивает её статическим свойствам класса*/

    public static function getConfig() {
        $db_config = require 'config/dbconfig.php';
        static::$dsn = $db_config['db_type'] . ":host=" . $db_config['db_host'] . ";dbname=" . $db_config['db_name'];
        static::$db_username = $db_config['username'];
        static::$db_password = $db_config['password'];
        static::$db_charset = $db_config['charset'];
        return 0;
    }
}