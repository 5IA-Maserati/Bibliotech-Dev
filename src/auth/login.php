<?php

declare(strict_types=1);

session_start();

header('Content-Type: application/json');

use public\libs\PDO;

require_once __DIR__ . '/../db/db.php';

/** @var PDO $pdo */

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

$user = $stmt->fetch();

if ($user === false || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Email o password errati']);
    exit;
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];

echo json_encode([
    'success' => true,
    'role' => $user['role'],
]);
