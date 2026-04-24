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
    // If no search query, return all books
    if (strlen($q) < 2) {
        $query = "
            SELECT id, title, author, publisher, publication_year, isbn
            FROM books
        ";
        $params = [];

        if ($genre !== '') {
            // Genre filter not supported without genres column
        }

        if ($sort === 'year-desc') {
            $query .= " ORDER BY publication_year DESC";
        } else {
            $query .= " ORDER BY title ASC";
        }

        $query .= " LIMIT 50"; // TODO: This should be increased for a real application

        $books = $db->query($query, $params);

        echo json_encode([
            'success' => true,
            'books' => $books
        ]);
        exit;
    }

    $query = "
        SELECT id, title, author, publisher, publication_year, isbn
        FROM books
        WHERE (title LIKE ? OR author LIKE ? OR isbn LIKE ?)
    ";

    $params = [
        "%$q%",
        "%$q%",
        "%$q%"
    ];

    if ($genre !== '') {
        // Genre filter not supported without genres column
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
