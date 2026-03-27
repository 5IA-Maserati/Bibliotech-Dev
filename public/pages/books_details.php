<?php 

$title = 'Pagina libri';
$show_nav = false;

ob_start(); // rendering the header
include dirname(__DIR__) . '/includes/header.php';
$header = ob_get_clean() ?: '';

$tpl = __DIR__ . "/books_details.html";
$html = file_get_contents($tpl);

$replacements = [
    '{{TITLE}}' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    '{{HEADER}}' => $header,
];
$html = str_replace(array_keys($replacements), array_values($replacements), $html);
echo $html;