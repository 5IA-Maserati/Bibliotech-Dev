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

    $name = trim((string)($data['name'] ?? ''));
    $surname = trim((string)($data['surname'] ?? ''));
    $email = trim((string)($data['email'] ?? ''));
    $password = (string)($data['password'] ?? '');

    if ($name === '' || $surname === '' || $email === '' || $password === '') {
        sendJsonError('Tutti i campi obbligatori devono essere compilati', 400);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJsonError('Email non valida', 400);
        exit;
    }

    $pwError = validatePasswordRules($password);
    if ($pwError !== null) {
        sendJsonError($pwError, 400);
        exit;
    }

    $existingUser = $db->queryOne('SELECT id FROM users WHERE email = ?', [$email]);
    if ($existingUser) {
        sendJsonError('Questo indirizzo email è già registrato', 409);
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $username = $name;

    $db->execute(
        'INSERT INTO users (username, surname, email, passwords, role) VALUES (?, ?, ?, ?, ?)',
        [$username, $surname, $email, $passwordHash, 'user']
    );

    sendJson(['success' => true]);
} catch (Exception $e) {
    sendJsonError('Errore server, riprova più tardi', 500);
}
