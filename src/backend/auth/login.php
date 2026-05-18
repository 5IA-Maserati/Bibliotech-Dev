<?php

declare(strict_types=1);

use src\backend\libs\DatabasePDO;

require_once __DIR__ . '/../../../public/bootstrap.php';
require_once __DIR__ . '/common.php';

header('Content-Type: application/json');

/** @var DatabasePDO $db */
$db = require __DIR__ . '/../../db/db.php';

try {
    $data = readJson();

    $email = trim((string)($data['email'] ?? ''));
    $password = (string)($data['password'] ?? '');

    if ($email === '' || $password === '') {
        sendJsonError('Inserisci email e password', 400);
        exit;
    }

    $user = $db->queryOne(
        'SELECT id, passwords, role FROM users WHERE email = ?',
        [$email]
    );

    if (
        !$user ||
        !isset($user['passwords']) ||
        !password_verify($password, $user['passwords'])
    ) {
        sendJsonError('Email o password errati', 401);
        exit;
    }

    $_SESSION['user'] = [
        'id' => $user['id'],
        'role' => $user['role'] ?? 'user',
    ];

    sendJson(['success' => true, 'role' => $user['role'] ?? 'user']);
} catch (Exception $e) {
    sendJsonError('Errore server', 500);
}
