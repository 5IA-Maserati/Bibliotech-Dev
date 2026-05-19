<?php

use src\backend\libs\DatabasePDO;

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/bootstrap.php';

$userId = $_SESSION['user']['id'] ?? null;

/** @var DatabasePDO $db */
$db = require dirname(__DIR__, 2) . '/../src/db/db.php';

$q = $_GET['q'] ?? '';
$genre = $_GET['genre'] ?? '';
$sort = $_GET['sort'] ?? 'title';
$page = $_GET['page'] ?? 1;
$limit = $_GET['limit'] ?? 50;

$q = is_string($q) ? $q : '';
$genre = is_string($genre) ? $genre : '';
$sort = is_string($sort) ? $sort : 'title';
$page = is_numeric($page) ? (int)$page : 1;
$limit = is_numeric($limit) ? (int)$limit : 50;

// Validate parameters
if ($page < 1) {
    $page = 1;
}
if ($limit < 1 || $limit > 100) {
    $limit = 50; // Max 100 per page
}

$offset = ($page - 1) * $limit;

$escapeLikePattern = function (string $value): string {
    return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
};

try {
    $params = [];
    $countParams = [];

    $favoriteSelect = $userId !== null
        ? 'CASE WHEN f.id IS NOT NULL THEN 1 ELSE 0 END AS favorite'
        : '0 AS favorite';

    $query = "
        SELECT DISTINCT b.id, b.title, b.author, b.publisher, b.publication_year, b.isbn, {$favoriteSelect}
        FROM books b
    ";
    $countQuery = "
        SELECT COUNT(*) as total
        FROM books b
    ";

    if ($userId !== null) {
        $query .= "
            LEFT JOIN favorites f ON f.book_id = b.id AND f.user_id = ?
        ";
        $params[] = $userId;
    }

    if ($genre !== '') {
        $query .= "
            JOIN books_categories bc ON bc.book_id = b.id
            JOIN categories c ON c.id = bc.category_id
        ";
        $countQuery .= "
            JOIN books_categories bc ON bc.book_id = b.id
            JOIN categories c ON c.id = bc.category_id
        ";
    }

    $whereClauses = [];
    if (strlen($q) >= 2) {
        $escapedQuery = $escapeLikePattern($q);
        $searchPattern = "%{$escapedQuery}%";

        $whereClauses[] = "
                            (b.title LIKE ? COLLATE utf8mb4_unicode_ci 
                            OR b.author LIKE ? COLLATE utf8mb4_unicode_ci 
                            OR b.isbn LIKE ? COLLATE utf8mb4_unicode_ci)
                            ";
        $params[] = $searchPattern;
        $params[] = $searchPattern;
        $params[] = $searchPattern;
        $countParams[] = $searchPattern;
        $countParams[] = $searchPattern;
        $countParams[] = $searchPattern;
    }

    if ($genre !== '') {
        $whereClauses[] = "c.name = ?";
        $params[] = $genre;
        $countParams[] = $genre;
    }

    if (!empty($whereClauses)) {
        $query .= ' WHERE ' . implode(' AND ', $whereClauses);
        $countQuery .= ' WHERE ' . implode(' AND ', $whereClauses);
    }

    if ($sort === 'year-desc') {
        $query .= " ORDER BY b.publication_year DESC";
    } else {
        $query .= " ORDER BY b.title ASC";
    }

    $query .= " LIMIT $limit OFFSET $offset";

    // Get total count
    $totalResult = $db->queryOne($countQuery, $countParams);
    $total = $totalResult['total'] ?? 0;

    $books = $db->query($query, $params);

    foreach ($books as &$book) {
        if (!isset($book['isbn']) || $book['isbn'] === null) {
            $book['isbn'] = '';
        }
    }
    unset($book);

    echo json_encode([
        'success' => true,
        'books' => $books,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'totalPages' => ceil($total / $limit)
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'books' => [],
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => 0,
            'totalPages' => 0
        ]
    ]);
}
