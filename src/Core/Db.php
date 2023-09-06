<?php

namespace App\Core;

use PDO;

class Db
{
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getInstance(): ?PDO
    {

        if (self::$instance === null) {
            $dsn = 'mysql:host=' . $_ENV["DB_HOST"] . ';dbname=' . $_ENV["DB_NAME"];
            $username = $_ENV["DB_USER"];
            $password = $_ENV["DB_PASSWORD"];

            self::$instance = new PDO($dsn, $username, $password);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}