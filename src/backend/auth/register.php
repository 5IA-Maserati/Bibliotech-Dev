<?php

declare(strict_types=1);

use src\backend\libs\DatabasePDO;

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

    $name = trim((string)($data['name'] ?? ''));
    $surname = trim((string)($data['surname'] ?? ''));
    $email = trim((string)($data['email'] ?? ''));
    $password = (string)($data['password'] ?? '');

    if ($name === '' || $surname === '' || $email === '' || $password === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Tutti i campi obbligatori devono essere compilati']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email non valida']);
        exit;
    }

    if (strlen($password) < 8) {
        http_response_code(400);
        echo json_encode(['error' => 'La password deve contenere almeno 8 caratteri']);
        exit;
    }

    $existingUser = $db->queryOne('SELECT id FROM users WHERE email = ?', [$email]);
    if ($existingUser) {
        http_response_code(409);
        echo json_encode(['error' => 'Questo indirizzo email è già registrato']);
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $username = $name;

    $db->execute(
        'INSERT INTO users (username, surname, email, passwords, role) VALUES (?, ?, ?, ?, ?)',
        [$username, $surname, $email, $passwordHash, 'user']
    );

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore server, riprova più tardi']);
}
