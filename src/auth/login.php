<?php

declare(strict_types=1);
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/src/db/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Inserisci email e password']);
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
$stmt->execute(['email' => $email]);

$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Email o password errati']);
    exit;
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];

echo json_encode([
    'success' => true,
    'role' => $user['role']
]);