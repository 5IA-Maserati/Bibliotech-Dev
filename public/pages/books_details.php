<?php

$title = 'Pagina libri';
$show_nav = false;

ob_start(); // rendering the header
include dirname(__DIR__) . '/includes/header.php';
$header = ob_get_clean() ?: '';

$book = null;
// book id by query string: books_details.php?id=123
$bookId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($bookId === false || $bookId === null) {
    $bookId = 0;
}

if ($bookId > 0) {
    require_once __DIR__ . '/../../src/db/db.php';

    /** @var \PDO $pdo */
    $stmt = $pdo->prepare(
        'SELECT b.*, c.name AS category
         FROM books b
         LEFT JOIN categories c ON b.category_id = c.id
         WHERE b.id = :id'
    );

    if ($stmt) {
        $stmt->execute(['id' => $bookId]);
        $book = $stmt->fetch();
    }
}

if ($book === null) {
    header('Location: /pages/page_404.php');
    exit;
}

$tpl = __DIR__ . "/books_details.html";
$html = file_get_contents($tpl);

$replacements = [
    '{{TITLE}}' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    '{{HEADER}}' => $header,
    '{{BOOK_TITLE}}' => htmlspecialchars((string)($book['title'] ?? 'N/D'), ENT_QUOTES, 'UTF-8'),
    '{{BOOK_AUTHOR}}' => htmlspecialchars((string)($book['author'] ?? 'N/D'), ENT_QUOTES, 'UTF-8'),
    '{{BOOK_SUBTITLE}}' => htmlspecialchars((string)($book['subtitle'] ?? ''), ENT_QUOTES, 'UTF-8'),
    '{{BOOK_GENRE}}' => htmlspecialchars((string)($book['category'] ?? 'N/D'), ENT_QUOTES, 'UTF-8'),
    '{{BOOK_YEAR}}' => htmlspecialchars((string)($book['publication_year'] ?? 'N/D'), ENT_QUOTES, 'UTF-8'),
    '{{BOOK_COPIES}}' => htmlspecialchars((string)($book['copies_number'] ?? 'N/D'), ENT_QUOTES, 'UTF-8'),
    '{{BOOK_ISBN}}' => htmlspecialchars((string)($book['isbn'] ?? 'N/D'), ENT_QUOTES, 'UTF-8'),
    '{{BOOK_LANGUAGE}}' => 'Italiano',
    '{{BOOK_AVAILABILITY}}' => ((int)($book['available_copies'] ?? 0) > 0) ? 'Disponibile' : 'Esaurito',
    '{{BOOK_PUBLISHER}}' => htmlspecialchars((string)($book['publisher'] ?? 'N/D'), ENT_QUOTES, 'UTF-8'),
    '{{BOOK_EDITION}}' => htmlspecialchars((string)($book['edition'] ?? '1ª'), ENT_QUOTES, 'UTF-8'),
    '{{BOOK_DESC}}' => htmlspecialchars((string)($book['description'] ?? 'non disponibile.'), ENT_QUOTES, 'UTF-8'),
    '{{BOOK_FORMAT}}' => htmlspecialchars((string)($book['format'] ?? 'Cartaceo'), ENT_QUOTES, 'UTF-8'),
    '{{BOOK_COVER}}' => htmlspecialchars((string)($book['cover_url'] ?? 'N/D'), ENT_QUOTES, 'UTF-8'),
];
$html = str_replace(array_keys($replacements), array_values($replacements), $html);
echo $html;
