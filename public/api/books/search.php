<?php

use src\backend\libs\DatabasePDO;

header('Content-Type: application/json');

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
if ($page < 1) $page = 1;
if ($limit < 1 || $limit > 100) $limit = 50; // Max 100 per page

$offset = ($page - 1) * $limit;

try {
    $params = [];
    $countParams = [];
    $query = "
        SELECT DISTINCT b.id, b.title, b.author, b.publisher, b.publication_year, b.isbn
        FROM books b
    ";
    $countQuery = "
        SELECT COUNT(*) as total
        FROM books b
    ";

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
        $whereClauses[] = "(b.title LIKE ? OR b.author LIKE ? OR b.isbn LIKE ?)";
        $params[] = "%$q%";
        $params[] = "%$q%";
        $params[] = "%$q%";
        $countParams[] = "%$q%";
        $countParams[] = "%$q%";
        $countParams[] = "%$q%";
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
