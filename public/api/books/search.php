<?php

header('Content-Type: application/json');

// Include database connection
require_once dirname(__DIR__, 2) . '/../src/db/db.php';

/** @var \PDO $pdo */

$q = $_GET['q'] ?? '';
$genre = $_GET['genre'] ?? '';
$sort = $_GET['sort'] ?? 'title';

if (strlen($q) < 2) {
    echo json_encode(['error' => 'Search term too short', 'books' => []]);
    exit;
}

try {
    $query = "SELECT id, title, author, publisher, publication_year, isbn, genres 
              FROM books WHERE 1=1";
    $params = [];

    // Search by title, author, or ISBN
    $query .= " AND (title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
    $searchTerm = "%$q%";
    $params = [$searchTerm, $searchTerm, $searchTerm];

    // Filter by genre if selected
    if ($genre) {
        $query .= " AND genres LIKE ?";
        $params[] = "%$genre%";
    }

    // Sorting
    if ($sort === 'year-desc') {
        $query .= " ORDER BY publication_year DESC";
    } else {
        $query .= " ORDER BY title ASC";
    }

    $query .= " LIMIT 50";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $books = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'books' => $books]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage(), 'books' => []]);
}
