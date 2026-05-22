<?php

use src\backend\libs\DatabasePDO;

$title = 'Prestito';
$show_nav = false;

require_once dirname(__DIR__) . '/bootstrap.php';
require_once dirname(__DIR__) . '/../src/backend/libs/DatabaseSchema.php';
require_once dirname(__DIR__) . '/../src/db/db.php';

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    header('Location: /pages/login.php');
    exit;
}

/** @var DatabasePDO $database */
$database = require dirname(__DIR__) . '/../src/db/db.php';
\src\backend\libs\DatabaseSchema::ensureFavoritesTableExists($database);
$user = $database->queryOne(
    'SELECT username, email FROM users WHERE id = ?',
    [$userId]
);

$bookId = filter_input(INPUT_GET, 'book_id', FILTER_VALIDATE_INT);
$message = '';
$bookTitle = 'Seleziona un libro';
$hasBookId = $bookId !== null && $bookId !== false && $bookId > 0;

if ($hasBookId) {
    $book = $database->queryOne(
        'SELECT title FROM books WHERE id = ?',
        [$bookId]
    );
    if ($book) {
        $bookTitle = $book['title'];
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $postedBookId = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
    // FILTER_SANITIZE_STRING è deprecato in PHP 8.1+. Leggiamo direttamente e castiamo a stringa.
    $dueDateRaw = $_POST['booking_date'] ?? '';
    $dueDate = is_string($dueDateRaw) ? trim($dueDateRaw) : '';

    if ($postedBookId === false || $postedBookId === null) {
        $message = '<span class="message-error">Libro non valido. Riprova.</span>';
    } elseif ($dueDate === '') {
        $message = '<span class="message-error">Devi selezionare una data di restituzione prevista.</span>';
    } else {
        // Validate return date
        $returnDateTime = DateTime::createFromFormat('Y-m-d', $dueDate);
        $today = new DateTime();
        $oneYearFromNow = new DateTime();
        $oneYearFromNow->modify('+1 year');

        if (!$returnDateTime) {
            $message = '<span class="message-error">Data di restituzione non valida.</span>';
        } elseif ($returnDateTime < $today) {
            $message = '<span class="message-error">La data di restituzione non può essere nel passato.</span>';
        } elseif ($returnDateTime > $oneYearFromNow) {
            $message = '<span class="message-error">La data di restituzione '
                . 'non può essere oltre un anno da oggi.</span>';
        } else {
            $bookId = $postedBookId;
            $book = $database->queryOne(
                'SELECT title FROM books WHERE id = ?',
                [$bookId]
            );
            $bookTitle = $book['title'] ?? $bookTitle;

            try {
                $existingLoan = $database->queryOne(
                    'SELECT id FROM loans WHERE user_id = ? AND book_id = ? AND return_date IS NULL',
                    [$userId, $bookId]
                );

                if ($existingLoan) {
                    $message = '<span class="message-error">Hai già un prestito attivo per questo libro.</span>';
                } else {
                    $success = $database->execute(
                        'INSERT INTO loans (user_id, book_id, loan_date, return_date) VALUES (?, ?, CURDATE(), ?)',
                        [$userId, $bookId, $dueDate]
                    );

                    if ($success) {
                        $message = '<span class="message-success">Prestito registrato con successo.</span>';
                    } else {
                        $message = '<span class="message-error">Impossibile completare il prestito.</span>';
                    }
                }
            } catch (Exception $e) {
                $message = '<span class="message-error">Errore database: '
                    . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
                    . '</span>';
            }
        }
    }
}

$bookSuggestions = [];
try {
    $bookSuggestions = $database->query(
        'SELECT b.id, b.title, b.author, b.publication_year
         FROM favorites f
         JOIN books b ON b.id = f.book_id
         WHERE f.user_id = ?
         ORDER BY f.created_at DESC
         LIMIT 6',
        [$userId]
    );
} catch (Exception $e) {
    $bookSuggestions = [];
}

$renderSuggestionList = function (array $books, string $emptyMessage): string {
    if (empty($books)) {
        return '<p class="empty-state">' . htmlspecialchars($emptyMessage, ENT_QUOTES, 'UTF-8') . '</p>';
    }

    $html = '<div id="suggestions-list" class="search-results">';
    foreach ($books as $book) {
        $title = htmlspecialchars($book['title'] ?? 'Titolo sconosciuto', ENT_QUOTES, 'UTF-8');
        $author = htmlspecialchars($book['author'] ?? 'Autore sconosciuto', ENT_QUOTES, 'UTF-8');
        $year = htmlspecialchars($book['publication_year'] ?? 'N/D', ENT_QUOTES, 'UTF-8');
        $bookId = (int)($book['id'] ?? 0);


        $html .= '<button type="button" class="search-result-item" data-book-id="'
            . $bookId . '" data-book-title="' . $title . '">';

        $html .= '<div class="search-result-item-title">' . $title . '</div>';
        $html .= '<div class="search-result-item-meta">' . $author . ' · ' . $year . '</div>';
        $html .= '<span class="favorite-badge">Preferito</span>';
        $html .= '</button>';
    }
    $html .= '</div>';

    return $html;
};


$bookSuggestionsHtml = $renderSuggestionList(
    $bookSuggestions,
    'Non ci sono libri visti di recente. Prova a prendere in prestito un nuovo libro dal catalogo.'
);

ob_start();
include dirname(__DIR__) . '/includes/header.php';
$header = ob_get_clean() ?: '';

$tpl = __DIR__ . '/booking.html';
$html = file_get_contents($tpl);
$replacements = [
    '{{TITLE}}' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    '{{HEADER}}' => $header,
    '{{USER_NAME}}' => htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'),
    '{{USER_EMAIL}}' => htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'),
    '{{BOOK_TITLE}}' => htmlspecialchars($bookTitle, ENT_QUOTES, 'UTF-8'),
    // Uso l'operatore ternario di fallback più il casting a stringa per evitare i conflitti di tipo con 'false'
    '{{BOOK_ID}}' => htmlspecialchars((string)($bookId ?: ''), ENT_QUOTES, 'UTF-8'),
    '{{BOOK_FORM_DISPLAY}}' => $hasBookId ? 'block' : 'hidden',
    '{{SEARCH_SECTION_DISPLAY}}' => $hasBookId ? 'hidden' : 'block',
    '{{SUGGESTIONS_SECTION_DISPLAY}}' => $hasBookId ? 'hidden' : 'block',
    '{{SUGGESTIONS}}' => $bookSuggestionsHtml,
    '{{MESSAGE}}' => $message,
];
$html = str_replace(array_keys($replacements), array_values($replacements), $html);
echo $html;