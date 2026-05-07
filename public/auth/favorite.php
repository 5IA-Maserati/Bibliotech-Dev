<?php

use src\backend\libs\DatabasePDO;

header('Content-Type: application/json');

require_once dirname(__DIR__) . '/../bootstrap.php';
require_once dirname(__DIR__) . '/../../src/db/db.php';

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'Devi effettuare il login per aggiungere un preferito.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$bookId = isset($input['book_id']) ? filter_var($input['book_id'], FILTER_VALIDATE_INT) : false;

if ($bookId === false || $bookId === null) {
    http_response_code(400);
    echo json_encode(['error' => 'ID libro non valido.']);
    exit;
}

/** @var DatabasePDO $database */
$database = require dirname(__DIR__) . '/../../src/db/db.php';

$book = $database->queryOne('SELECT id FROM books WHERE id = ?', [$bookId]);
if (!$book) {
    http_response_code(404);
    echo json_encode(['error' => 'Libro non trovato.']);
    exit;
}

try {
    $favorite = $database->queryOne(
        'SELECT id FROM favorites WHERE user_id = ? AND book_id = ?',
        [$userId, $bookId]
    );

    if ($favorite) {
        echo json_encode(['success' => true, 'message' => 'Libro già nei preferiti.']);
        exit;
    }

    $database->execute(
        'INSERT INTO favorites (user_id, book_id, created_at) VALUES (?, ?, NOW())',
        [$userId, $bookId]
    );

    echo json_encode(['success' => true, 'message' => 'Libro aggiunto ai preferiti.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore nel database: ' . $e->getMessage()]);
}
