<?php

require_once dirname(__DIR__, 2) . '/bootstrap.php';

use src\backend\libs\DatabasePDO;

header('Content-Type: application/json');

$bookId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$bookId || $bookId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID libro non valido']);
    exit;
}

try {
    /** @var DatabasePDO $db */
    $db = require dirname(__DIR__, 3) . '/src/db/db.php';

    // Get current book details
    $currentBook = $db->queryOne(
        'SELECT author FROM books WHERE id = ?',
        [$bookId]
    );

    if (!$currentBook) {
        http_response_code(404);
        echo json_encode(['error' => 'Libro non trovato']);
        exit;
    }

    $author = $currentBook['author'];

    // Get book categories
    $categories = $db->query(
        'SELECT category_id FROM books_categories WHERE book_id = ?',
        [$bookId]
    );

    $categoryIds = array_column($categories, 'category_id');

    $relatedBooks = [];

    // Get books by same author (limit to 3)
    if ($author) {
        $authorBooks = $db->query(
            'SELECT b.id, b.title, b.author, b.publication_year, b.isbn
             FROM books b
             WHERE b.author = ? AND b.id != ?
             ORDER BY b.publication_year DESC
             LIMIT 3',
            [$author, $bookId]
        );
        $relatedBooks = array_merge($relatedBooks, $authorBooks);
    }

    // Get books by same genre (limit to 3)
    if (!empty($categoryIds)) {
        $placeholders = str_repeat('?,', count($categoryIds) - 1) . '?';
        $params = array_merge($categoryIds, [$bookId]);

        $genreBooks = $db->query(
            "SELECT DISTINCT b.id, b.title, b.author, b.publication_year, b.isbn
             FROM books b
             JOIN books_categories bc ON b.id = bc.book_id
             WHERE bc.category_id IN ($placeholders) AND b.id != ?
             AND b.id NOT IN (
                 SELECT id FROM books WHERE author = ?
             )
             ORDER BY b.publication_year DESC
             LIMIT 3",
            array_merge($params, [$author ?? ''])
        );
        $relatedBooks = array_merge($relatedBooks, $genreBooks);
    }

    // If we don't have enough books, fill with random books
    if (count($relatedBooks) < 6) {
        $existingIds = array_column($relatedBooks, 'id');
        $existingIds[] = $bookId;

        $placeholders = str_repeat('?,', count($existingIds) - 1) . '?';
        $randomBooks = $db->query(
            "SELECT b.id, b.title, b.author, b.publication_year, b.isbn
             FROM books b
             WHERE b.id NOT IN ($placeholders)
             ORDER BY RAND()
             LIMIT " . (6 - count($relatedBooks)),
            $existingIds
        );
        $relatedBooks = array_merge($relatedBooks, $randomBooks);
    }

    // Limit to 6 books total
    $relatedBooks = array_slice($relatedBooks, 0, 6);

    foreach ($relatedBooks as &$relatedBook) {
        if (!isset($relatedBook['isbn']) || $relatedBook['isbn'] === null) {
            $relatedBook['isbn'] = '';
        }
    }
    unset($relatedBook);

    echo json_encode([
        'success' => true,
        'books' => $relatedBooks
    ]);
} catch (Exception $e) {
    error_log('Related books error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Errore interno del server']);
}
