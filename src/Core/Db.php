<?php

namespace App\Core;

use PDO;

/**
 * Classe de gestion de la base de données.
 */
class Db
{
    private static ?PDO $instance = null;

    private function __construct() {}

    /**
     * Obtient une instance de la connexion à la base de données.
     *
     * @return PDO|null Instance de la connexion PDO ou null en cas d'erreur.
     */
    public static function getInstance(): ?PDO
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'];
            $dbname =  $_ENV['DB_NAME'];
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASSWORD'];

            $dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;

            self::$instance = new PDO($dsn, $username, $password);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$instance;
    }
}