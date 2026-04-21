<?php

declare(strict_types=1);

use src\point_backend\libs\PDO;
use RuntimeException;

require_once __DIR__ . '/../../../public/bootstrap.php';

header('Content-Type: application/json');

/** @var PDO $pdo */
$pdo = require __DIR__ . '/../../db/db.php';

try {
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode(['error' => 'JSON non valido']);
        exit;
    }

    $email = isset($data['email']) ? trim((string) $data['email']) : '';
    $password = isset($data['password']) ? (string) $data['password'] : '';

    if ($email === '' || $password === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Inserisci email e password']);
        exit;
    }

    $stmt = $pdo->prepare(
        'SELECT id, password, role FROM users WHERE email = :email'
    );

    $stmt->execute(['email' => $email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (
        $user === false ||
        !isset($user['password']) ||
        !password_verify($password, (string) $user['password'])
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
    header('Location: /pages/page_404.php');
    exit;
}
