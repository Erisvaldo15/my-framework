<?php

namespace app\database;

use PDO;

class Connection
{
    private static ?PDO $connection = null;

    public static function connection()
    {

        if (!self::$connection) {
            self::$connection = new PDO("mysql:host=localhost;dbname=blog", "root", "erisvaldo", [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            ]);
        }

        return self::$connection;
    }
}
