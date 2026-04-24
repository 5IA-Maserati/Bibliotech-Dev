<?php

require_once __DIR__ . '/../backend/libs/DatabasePDO.php';
require_once __DIR__ . '/../backend/libs/DatabasePDOException.php';

use src\backend\libs\DatabasePDO;
use src\backend\libs\DatabasePDOException;

$host = getenv('DB_HOST') ?: '127.0.0.1';
$db   = getenv('DB_NAME') ?: 'bibliotech';
$dbport = getenv('DB_PORT') ?: '3306';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'Beso2007?';
$charset = getenv('DB_CHARSET') ?: 'utf8mb4';

$dsn = "mysql:host=$host;port=$dbport;dbname=$db;charset=$charset";

try {
    $database = new DatabasePDO($dsn, $user, $pass);
} catch (DatabasePDOException $e) {
    die("Errore di connessione: " . $e->getMessage());
} catch (Exception $e) {
    die("Errore sconosciuto: " . $e->getMessage());
}

return $database;
