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
    if ($bookId === false || $bookId === null || $bookId <= 0) {
        header('Location: /pages/page_404.php');
        exit;
    }

    /** @var DatabasePDO $db */
    require_once __DIR__ . '/../../src/backend/libs/DatabaseSchema.php';
    $db = require __DIR__ . '/../../src/db/db.php';
    \src\backend\libs\DatabaseSchema::ensureFavoritesTableExists($db);

    $book = $db->queryOne(
        'SELECT b.*
         FROM books b
         WHERE b.id = ?',
        [$bookId]
    );

    $book_categories = [];
    try {
        $book_categories = $db->query(
            'SELECT c.name
             FROM categories c
             JOIN books_categories bc ON c.id = bc.category_id
             WHERE bc.book_id = ?',
            [$bookId]
        );
    } catch (Exception $e) {
        // Missing category tables or schema mismatch; continue without genre data.
        $book_categories = [];
    }

    if (!$book) {
        header('Location: /pages/page_404.php');
        exit;
    }

    $tpl = __DIR__ . '/books_details.html';
    $html = file_get_contents($tpl);

    if ($html === false) {
        throw new Exception('Impossibile leggere books_details.html');
    }

    $favoriteStatus = false;
    $userId = $_SESSION['user']['id'] ?? null;
    if ($userId) {
        try {
            $favoriteStatus = (bool)$db->queryOne(
                'SELECT id FROM favorites WHERE user_id = ? AND book_id = ?',
                [$userId, $bookId]
            );
        } catch (Exception $e) {
            $favoriteStatus = false;
        }
    }


    $getGoogleBooksCoverUrl = function (string $isbn): string {
        if ($isbn === '') {
            return '';
        }

        $apiUrl = 'https://www.googleapis.com/books/v1/volumes?q=isbn:' . urlencode($isbn);
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 5,
                'header' => "User-Agent: Bibliotech/1.0\r\n"
            ]
        ]);

        $response = @file_get_contents($apiUrl, false, $context);
        if ($response === false) {
            return '';
        }

        $data = json_decode($response, true);
        if (!is_array($data) || empty($data['items'][0]['volumeInfo']['imageLinks'])) {
            return '';
        }

        $imageLinks = $data['items'][0]['volumeInfo']['imageLinks'];


        return $imageLinks['extraLarge']
            ?? $imageLinks['large']
            ?? $imageLinks['medium']
            ?? $imageLinks['thumbnail']
            ?? $imageLinks['smallThumbnail']
            ?? '';
    };

    $isbnRaw = $book['isbn'] ?? '';
    $bookIsbn = $isbnRaw;
    $coverUrl = '';
    if ($bookIsbn !== '') {
        $coverUrl = $getGoogleBooksCoverUrl($bookIsbn);
    }
    if ($coverUrl === '') {
        $coverUrl = '/assets/img/common/default_cover.png';
    }

    $replacements = [
        '{{TITLE}}' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
        '{{HEADER}}' => $header,
        '{{BOOK_TITLE}}' => htmlspecialchars($book['title'] ?? 'N/D', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_AUTHOR}}' => htmlspecialchars($book['author'] ?? 'N/D', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_SUBTITLE}}' => htmlspecialchars($book['subtitle'] ?? '', ENT_QUOTES, 'UTF-8'),


        '{{BOOK_GENRE}}' => htmlspecialchars(
            implode(', ', array_column($book_categories, 'name')),
            ENT_QUOTES,
            'UTF-8'
        ),

        '{{BOOK_YEAR}}' => htmlspecialchars($book['publication_year'] ?? 'N/D', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_COPIES}}' => htmlspecialchars($book['copies_number'] ?? 'N/D', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_ISBN}}' => htmlspecialchars($bookIsbn ?: 'N/D', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_LANGUAGE}}' => 'Italiano',
        '{{BOOK_AVAILABILITY}}' => ((int)($book['available_copies'] ?? 0) > 0) ? 'Disponibile' : 'Esaurito',
        '{{BOOK_PUBLISHER}}' => htmlspecialchars($book['publisher'] ?? 'N/D', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_EDITION}}' => htmlspecialchars($book['edition'] ?? '1ª', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_DESC}}' => htmlspecialchars($book['description'] ?? 'non disponibile.', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_FORMAT}}' => htmlspecialchars($book['format'] ?? 'Cartaceo', ENT_QUOTES, 'UTF-8'),
        '{{BOOK_COVER}}' => htmlspecialchars($coverUrl, ENT_QUOTES, 'UTF-8'),
        '{{BOOK_ID}}' => htmlspecialchars((string)$bookId, ENT_QUOTES, 'UTF-8'),
        '{{FAVORITE_BUTTON_TEXT}}' => $favoriteStatus ? 'Preferito' : 'Aggiungi ai preferiti',
        '{{FAVORITE_BUTTON_DISABLED}}' => $favoriteStatus ? 'disabled' : '',
    ];

    $html = str_replace(array_keys($replacements), array_values($replacements), $html);

    echo $html;
} catch (Exception $e) {
    echo $e->getMessage();
}
