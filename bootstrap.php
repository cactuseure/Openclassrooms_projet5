<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Charge les variables d'environnement à partir du fichier .env
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();