<?php

$title = 'Il mio profilo';
$subtitle = 'Visualizza i tuoi dati e cambia la password';
$show_auth = true;

require_once dirname(__DIR__) . '/bootstrap.php';
require_once dirname(__DIR__) . '/../src/db/db.php';

$userId = $_SESSION['user']['id'] ?? null;
if (!$userId) {
    header('Location: /pages/login.php');
    exit;
}

/** @var src\backend\libs\DatabasePDO $database */
$database = require dirname(__DIR__) . '/../src/db/db.php';
$user = $database->queryOne(
    'SELECT username, surname, email, role, created_at FROM users WHERE id = ?',
    [$userId]
);

if (!$user) {
    header('Location: /pages/login.php');
    exit;
}

$createdAt = isset($user['created_at']) && $user['created_at'] !== null
    ? date('d/m/Y H:i', strtotime($user['created_at']))
    : 'N/A';

function formatDate(?string $date): string
{
    if (!$date) {
        return 'N/A';
    }

    $timestamp = strtotime($date);
    return $timestamp ? date('d/m/Y', $timestamp) : 'N/A';
}

function renderBookList(array $books, string $emptyMessage, bool $showBorrowed = false, bool $showDue = false, bool $showReturned = false): string
{
    if (empty($books)) {
        return '<p class="empty-state">' . htmlspecialchars($emptyMessage, ENT_QUOTES, 'UTF-8') . '</p>';
    }

    $html = '<div class="book-list">';
    foreach ($books as $book) {
        $title = htmlspecialchars($book['title'] ?? 'Titolo sconosciuto', ENT_QUOTES, 'UTF-8');
        $author = htmlspecialchars($book['author'] ?? 'Autore sconosciuto', ENT_QUOTES, 'UTF-8');
        $year = htmlspecialchars($book['publication_year'] ?? 'N/D', ENT_QUOTES, 'UTF-8');
        $borrowedAt = formatDate($book['borrowed_at'] ?? null);
        $dueAt = formatDate($book['due_at'] ?? null);
        $returnedAt = formatDate($book['returned_at'] ?? null);

        $html .= '<div class="book-card-small">';
        $html .= '<h4>' . $title . '</h4>';
        $html .= '<p class="book-meta">' . $author . ' · ' . $year . '</p>';

        if ($showBorrowed) {
            $html .= '<p class="book-date"><strong>Preso in prestito:</strong> ' . $borrowedAt . '</p>';
        }
        if ($showDue) {
            $html .= '<p class="book-date"><strong>Scadenza:</strong> ' . $dueAt . '</p>';
        }
        if ($showReturned) {
            $html .= '<p class="book-date"><strong>Restituito:</strong> ' . $returnedAt . '</p>';
        }

        $html .= '</div>';
    }
    $html .= '</div>';

    return $html;
}

$avatarInitial = strtoupper(substr($user['username'] ?? 'U', 0, 1));
$currentBorrowed = [];
$borrowHistory = [];
$favorites = [];
$totalBorrowed = 0;
$currentBorrowedCount = 0;
$favoriteCount = 0;
$mostReadGenres = [];

try {
    $result = $database->queryOne(
        'SELECT COUNT(*) AS total FROM loans WHERE user_id = ?',
        [$userId]
    );
    $totalBorrowed = (int)($result['total'] ?? 0);
} catch (Exception $e) {
    $totalBorrowed = 0;
}

try {
    $currentBorrowed = $database->query(
        'SELECT b.id, b.title, b.author, b.publication_year, l.loan_date as borrowed_at, l.return_date as due_at
         FROM loans l
         JOIN books b ON b.id = l.book_id
         WHERE l.user_id = ?
           AND l.return_date IS NULL
         ORDER BY l.return_date ASC
         LIMIT 10',
        [$userId]
    );
    $currentBorrowedCount = count($currentBorrowed);
} catch (Exception $e) {
    $currentBorrowed = [];
    $currentBorrowedCount = 0;
}

try {
    $borrowHistory = $database->query(
        'SELECT b.id, b.title, b.author, b.publication_year, l.loan_date as borrowed_at, l.return_date as returned_at
         FROM loans l
         JOIN books b ON b.id = l.book_id
         WHERE l.user_id = ?
           AND l.return_date IS NOT NULL
         ORDER BY l.return_date DESC
         LIMIT 10',
        [$userId]
    );
} catch (Exception $e) {
    $borrowHistory = [];
}

try {
    $favorites = $database->query(
        'SELECT b.id, b.title, b.author, b.publication_year
         FROM favorites f
         JOIN books b ON b.id = f.book_id
         WHERE f.user_id = ?
         ORDER BY f.created_at DESC
         LIMIT 10',
        [$userId]
    );
    $favoriteCount = count($favorites);
} catch (Exception $e) {
    $favorites = [];
    $favoriteCount = 0;
}

try {
    $mostReadGenres = $database->query(
        'SELECT c.name, COUNT(*) AS total
         FROM loans l
         JOIN books_categories bc ON bc.book_id = l.book_id
         JOIN categories c ON c.id = bc.category_id
         WHERE l.user_id = ?
           AND l.return_date IS NOT NULL
         GROUP BY c.name
         ORDER BY total DESC
         LIMIT 3',
        [$userId]
    );
} catch (Exception $e) {
    $mostReadGenres = [];
}

$statsHtml = '<div class="stat-card"><span class="stat-value">' . $currentBorrowedCount . '</span><span class="stat-label">Attualmente in prestito</span></div>';
$statsHtml .= '<div class="stat-card"><span class="stat-value">' . $totalBorrowed . '</span><span class="stat-label">Libri presi in prestito</span></div>';
$statsHtml .= '<div class="stat-card"><span class="stat-value">' . $favoriteCount . '</span><span class="stat-label">Libri preferiti</span></div>';
$statsHtml .= '<div class="stat-card"><span class="stat-value">' . count($mostReadGenres) . '</span><span class="stat-label">Generi più letti</span></div>';

$currentBorrowedHtml = renderBookList($currentBorrowed, 'Nessun libro in prestito al momento.', true, true, false);
$borrowHistoryHtml = renderBookList($borrowHistory, 'Nessuna cronologia di prestiti disponibile.', true, false, true);
$favoritesHtml = renderBookList($favorites, 'Nessun libro preferito.', false, false, false);
$mostReadGenresText = !empty($mostReadGenres)
    ? implode(', ', array_map(static fn($genre) => htmlspecialchars($genre['name'], ENT_QUOTES, 'UTF-8'), $mostReadGenres))
    : 'Nessun genere registrato.';

ob_start();
include dirname(__DIR__) . '/includes/header.php';
$header = ob_get_clean() ?: '';

$tpl = __DIR__ . '/profile.html';
$html = file_get_contents($tpl);
$replacements = [
    '{{TITLE}}' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    '{{HEADER}}' => $header,
    '{{AVATAR_INITIAL}}' => htmlspecialchars($avatarInitial, ENT_QUOTES, 'UTF-8'),
    '{{USERNAME}}' => htmlspecialchars($user['username'] ?? '', ENT_QUOTES, 'UTF-8'),
    '{{SURNAME}}' => htmlspecialchars($user['surname'] ?? '', ENT_QUOTES, 'UTF-8'),
    '{{EMAIL}}' => htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'),
    '{{ROLE}}' => htmlspecialchars($user['role'] ?? 'user', ENT_QUOTES, 'UTF-8'),
    '{{CREATED_AT}}' => htmlspecialchars($createdAt, ENT_QUOTES, 'UTF-8'),
    '{{STATS}}' => $statsHtml,
    '{{CURRENT_BORROWED}}' => $currentBorrowedHtml,
    '{{BORROW_HISTORY}}' => $borrowHistoryHtml,
    '{{FAVORITES}}' => $favoritesHtml,
    '{{MOST_READ_GENRES}}' => htmlspecialchars($mostReadGenresText, ENT_QUOTES, 'UTF-8'),
];
$html = str_replace(array_keys($replacements), array_values($replacements), $html);

echo $html;
