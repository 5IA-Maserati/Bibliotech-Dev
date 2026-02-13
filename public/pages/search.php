<?php

$title = 'Catalogo';
$show_nav = false;

// Render header
ob_start();
include __DIR__ . '/../includes/header.php';
$header = ob_get_clean();

// Load template and replace
$tpl = __DIR__ . '/search.html';
$html = file_get_contents($tpl);
$replacements = [
    '{{TITLE}}' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    '{{HEADER}}' => $header,
];
$html = str_replace(array_keys($replacements), array_values($replacements), $html);
echo $html;
