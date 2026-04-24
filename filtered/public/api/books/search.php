<?php

use src\backend\libs\DatabasePDO;
use Exception;

header('Content-Type: application/json');

/** @var DatabasePDO $db */
$db = require dirname(__DIR__, 2) . '/../src/db/db.php';

$q = $_GET['q'] ?? '';
$genre = $_GET['genre'] ?? '';
$sort = $_GET['sort'] ?? 'title';

$q = is_string($q) ? $q : '';
$genre = is_string($genre) ? $genre : '';
$sort = is_string($sort) ? $sort : 'title';

if (strlen($q) < 2) {
    echo json_encode([
        'error' => 'Search term too short',
        'books' => []
    ]);
    exit;
}

try {
    $query = "
        SELECT id, title, author, publisher, publication_year, isbn, genres
        FROM books
        WHERE (title LIKE ? OR author LIKE ? OR isbn LIKE ?)
    ";

    $params = [
        "%$q%",
        "%$q%",
        "%$q%"
    ];

    if ($genre !== '') {
        $query .= " AND genres LIKE ?";
        $params[] = "%$genre%";
    }

    if ($sort === 'year-desc') {
        $query .= " ORDER BY publication_year DESC";
    } else {
        $query .= " ORDER BY title ASC";
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
