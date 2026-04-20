<?php

use PDO;
use Exception;

header('Content-Type: application/json');

/**
 * @var PDO $pdo
*/
$pdo = require dirname(__DIR__, 2) . '/../src/db/db.php';

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
        WHERE 1=1
    ";

    $params = [];

    $query .= " AND (title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
    $searchTerm = '%' . $q . '%';
    $params = [$searchTerm, $searchTerm, $searchTerm];

    if ($genre !== '') {
        $query .= " AND genres LIKE ?";
        $params[] = '%' . $genre . '%';
    }

    if ($sort === 'year-desc') {
        $query .= " ORDER BY publication_year DESC";
    } else {
        $query .= " ORDER BY title ASC";
    }

    $query .= " LIMIT 50";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
