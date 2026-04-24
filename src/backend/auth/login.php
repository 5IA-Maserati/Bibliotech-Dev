<?php

declare(strict_types=1);

use src\backend\libs\DatabasePDO;
use RuntimeException;

require_once __DIR__ . '/../../../public/bootstrap.php';

header('Content-Type: application/json');

/** @var DatabasePDO $db */
$db = require __DIR__ . '/../../db/db.php';

try {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['error' => 'JSON non valido']);
        exit;
    }

    $email = trim((string)($data['email'] ?? ''));
    $password = (string)($data['password'] ?? '');

    if ($email === '' || $password === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Inserisci email e password']);
        exit;
    }

    $user = $db->queryOne(
        'SELECT id, password, role FROM users WHERE email = ?',
        [$email]
    );

    if (
        !$user ||
        !isset($user['password']) ||
        !password_verify($password, $user['password'])
    ) {
        http_response_code(401);
        echo json_encode(['error' => 'Email o password errati']);
        exit;
    }

    $_SESSION['user'] = [
        'id' => $user['id'],
        'role' => $user['role'],
    ];

    echo json_encode([
        'success' => true,
        'role' => $user['role'],
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore server']);
}
