<?php

declare(strict_types=1);

use PDO;

/**
 * Database connection
 *
 * @var PDO $pdo
 */

$host = 'localhost';
$db = 'bibliotech';
$user = 'root';
$password = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    /** @var PDO $pdo */
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore DB']);
    exit;
}
