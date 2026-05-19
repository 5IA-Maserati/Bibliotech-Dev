<?php

declare(strict_types=1);

use src\backend\libs\DatabasePDO;

require_once __DIR__ . '/../../../public/bootstrap.php';

header('Content-Type: application/json');

/** @var DatabasePDO $db */
$db = require __DIR__ . '/../../db/db.php';

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorizzato']);
    exit;
}

try {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['error' => 'JSON non valido']);
        exit;
    }

    $currentPassword = (string)($data['current_password'] ?? '');
    $newPassword = (string)($data['password'] ?? '');
    $confirmPassword = (string)($data['confirm_password'] ?? '');

    if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Compila tutti i campi richiesti']);
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        http_response_code(400);
        echo json_encode(['error' => 'Le password non coincidono']);
        exit;
    }

    if (strlen($newPassword) < 8 || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', $newPassword)) {
    http_response_code(400);
    echo json_encode([
        'error' => 'La password deve contenere almeno 8 caratteri, '
            . 'inclusi una maiuscola, una minuscola e un numero'
    ]);
    exit;
    }

    $user = $db->queryOne('SELECT passwords FROM users WHERE id = ?', [$userId]);

    if (!$user || !isset($user['passwords']) || !password_verify($currentPassword, $user['passwords'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Password attuale errata']);
        exit;
    }

    $db->execute(
        'UPDATE users SET passwords = ? WHERE id = ?',
        [password_hash($newPassword, PASSWORD_DEFAULT), $userId]
    );

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore server, riprova più tardi']);
}
