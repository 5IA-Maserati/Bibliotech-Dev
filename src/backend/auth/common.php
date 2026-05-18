<?php

declare(strict_types=1);

function readJson()
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        sendJsonError('JSON non valido', 400);
        exit;
    }
    return $data;
}

function sendJson($payload, int $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload);
}

function sendJsonError(string $message, int $status = 400)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode(['error' => $message]);
}

function validatePasswordRules(string $password)
{
    if (strlen($password) < 8 || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d).+$/', $password)) {
        return 'La password deve contenere almeno 8 caratteri, inclusi una maiuscola, una minuscola e un numero';
    }
    return null;
}
