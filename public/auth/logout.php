<?php

require_once __DIR__ . '/../bootstrap.php';

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name() ?: 'PHPSESSID',
        '',
        time() - 42000,
        $params['path'] ?? '',
        $params['domain'] ?? '',
        $params['secure'] ?? false,
        $params['httponly'] ?? false
    );
}

session_destroy();
header('Location: /pages/login.php');
exit;