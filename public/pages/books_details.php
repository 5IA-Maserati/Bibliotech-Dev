<?php

use src\backend\libs\DatabasePDO;

$title = 'Pagina libri';
$show_nav = false;

require_once dirname(__DIR__) . '/bootstrap.php';

ob_start();
include dirname(__DIR__) . '/includes/header.php';
$header = ob_get_clean() ?: '';

$book = null;

$bookId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

try {
    //if ($bookId === false || $bookId === null || $bookId <= 0) {
        //header('Location: /pages/page_404.php');
        //exit;
    //}

    /** @var DatabasePDO $db */
    $db = require __DIR__ . '/../../src/db/db.php';

    $book = $db->queryOne(
        'SELECT b.*
         FROM books b
         WHERE b.id = ?',
        [$bookId]
    );

    //if (!$book) {
        //header('Location: /pages/page_404.php');
        //exit;
    //}

    $tpl = __DIR__ . '/books_details.html';
    $html = file_get_contents($tpl);

    if ($html === false) {
        throw new Exception('Impossibile leggere books_details.html');
    }

    $replacements = [
        '{{TITLE}}' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
        '{{HEADER}}' => $header,
        '{{BOOK_TITLE}}' => htmlspecialchars($book['title'] ?? 'N/D', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_AUTHOR}}' => htmlspecialchars($book['author'] ?? 'N/D', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_SUBTITLE}}' => htmlspecialchars($book['subtitle'] ?? '', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_GENRE}}' => htmlspecialchars($book['category'] ?? 'N/D', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_YEAR}}' => htmlspecialchars($book['publication_year'] ?? 'N/D', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_COPIES}}' => htmlspecialchars($book['copies_number'] ?? 'N/D', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_ISBN}}' => htmlspecialchars($book['isbn'] ?? 'N/D', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_LANGUAGE}}' => 'Italiano',
        '{{BOOK_AVAILABILITY}}' => ((int)($book['available_copies'] ?? 0) > 0) ? 'Disponibile' : 'Esaurito',
        '{{BOOK_PUBLISHER}}' => htmlspecialchars($book['publisher'] ?? 'N/D', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_EDITION}}' => htmlspecialchars($book['edition'] ?? '1ª', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_DESC}}' => htmlspecialchars($book['description'] ?? 'non disponibile.', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_FORMAT}}' => htmlspecialchars($book['format'] ?? 'Cartaceo', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_COVER}}' => htmlspecialchars($book['cover_url'] ?? 'N/D', ENT_QUOTES, 'UTF-8'),
    ];

    $html = str_replace(array_keys($replacements), array_values($replacements), $html);

    echo $html;
} catch (Exception $e) {
    //header('Location: /pages/page_404.php');
    //exit;
    echo $e->getMessage();
}
