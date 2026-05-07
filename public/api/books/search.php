<?php

use src\backend\libs\DatabasePDO;

header('Content-Type: application/json');

/** @var DatabasePDO $db */
$db = require dirname(__DIR__, 2) . '/../src/db/db.php';

$q = $_GET['q'] ?? '';
$genre = $_GET['genre'] ?? '';
$sort = $_GET['sort'] ?? 'title';

$q = is_string($q) ? $q : '';
$genre = is_string($genre) ? $genre : '';
$sort = is_string($sort) ? $sort : 'title';

try {
    $params = [];
    $query = "
        SELECT DISTINCT b.id, b.title, b.author, b.publisher, b.publication_year, b.isbn
        FROM books b
    ";

    if ($genre !== '') {
        $query .= "
            JOIN books_categories bc ON bc.book_id = b.id
            JOIN categories c ON c.id = bc.category_id
        ";
    }

    $whereClauses = [];
    if (strlen($q) >= 2) {
        $whereClauses[] = "(b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ?)";
        $params[] = "%$q%";
        $params[] = "%$q%";
        $params[] = "%$q%";
    }

    if ($genre !== '') {
        $whereClauses[] = "c.name = ?";
        $params[] = $genre;
    }

    if (!empty($whereClauses)) {
        $query .= ' WHERE ' . implode(' AND ', $whereClauses);
    }

    if ($sort === 'year-desc') {
        $query .= " ORDER BY b.publication_year DESC";
    } else {
        $query .= " ORDER BY b.title ASC";
    }

    $query .= " LIMIT 50";

    $books = $db->query($query, $params);

    echo json_encode([
        'success' => true,
        'books' => $books
    ]);
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'books' => []
    ]);
}
