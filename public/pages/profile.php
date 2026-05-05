<?php

$title = 'Il mio profilo';
$subtitle = 'Visualizza i tuoi dati e cambia la password';
$show_auth = true;

require_once dirname(__DIR__) . '/bootstrap.php';
require_once dirname(__DIR__) . '/../src/db/db.php';

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    header('Location: /pages/login.php');
    exit;
}

/** @var src\backend\libs\DatabasePDO $database */
$database = require dirname(__DIR__) . '/../src/db/db.php';
$user = $database->queryOne(
    'SELECT username, surname, email, role, created_at FROM users WHERE id = ?',
    [$userId]
);

if (!$user) {
    header('Location: /pages/login.php');
    exit;
}

$createdAt = isset($user['created_at']) && $user['created_at'] !== null
    ? date('d/m/Y H:i', strtotime($user['created_at']))
    : 'N/A';

ob_start();
include dirname(__DIR__) . '/includes/header.php';
$header = ob_get_clean() ?: '';

$tpl = __DIR__ . '/profile.html';
$html = file_get_contents($tpl);
$replacements = [
    '{{TITLE}}' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    '{{HEADER}}' => $header,
    '{{USERNAME}}' => htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'),
    '{{SURNAME}}' => htmlspecialchars($user['surname'] ?? '', ENT_QUOTES, 'UTF-8'),
    '{{EMAIL}}' => htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'),
    '{{ROLE}}' => htmlspecialchars($user['role'] ?? 'user', ENT_QUOTES, 'UTF-8'),
    '{{CREATED_AT}}' => htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8'),
];
$html = str_replace(array_keys($replacements), array_values($replacements), $html);

echo $html;
