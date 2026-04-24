<?php

require_once __DIR__ . '/bootstrap.php';

// Render header (capture output)
$title = 'Biblioteca Scolastica';
$subtitle = null;
ob_start();
include __DIR__ . '/includes/header.php';
$header = ob_get_clean() ?: '';

// Load template and inject header
$tpl = __DIR__ . '/index.html';
$html = file_get_contents($tpl);
$html = str_replace('{{HEADER}}', $header, $html);
echo $html;
