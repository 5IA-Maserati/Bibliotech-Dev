<?php

$title = 'Catalogo';
$show_nav = false;

// Include bootstrap (handles session)
require_once dirname(__DIR__) . '/bootstrap.php';

// Include database connection
require_once dirname(__DIR__) . '/../src/db/db.php';

// Check if user is logged in
$userId = $_SESSION['user']['id'] ?? null;

// Render header
ob_start();
include dirname(__DIR__) . '/includes/header.php';
$header = ob_get_clean() ?: '';

// Load template and replace
$tpl = __DIR__ . '/search.html';
$html = file_get_contents($tpl);
$replacements = [
    '{{TITLE}}' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    '{{HEADER}}' => $header,
];
$html = str_replace(array_keys($replacements), array_values($replacements), $html);

// Fetch all books from the database
/** @var DatabasePDO $database */
$database = require dirname(__DIR__) . '/../src/db/db.php';

$genres = [];
try {
    $genres = $database->query(
        "SELECT name FROM categories ORDER BY name ASC"
    );
} catch (Exception $e) {
    $genres = [];
}

$genreOptions = '<option value="">Tutti i generi</option>';
foreach ($genres as $genre) {
    $genreName = htmlspecialchars($genre['name'] ?? '', ENT_QUOTES, 'UTF-8');
    $genreOptions .= "<option value=\"{$genreName}\">{$genreName}</option>";
}

// Convert books to JSON for JavaScript
$html = str_replace('{{GENRE_OPTIONS}}', $genreOptions, $html);

echo $html;
