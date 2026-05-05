<?php

$title = 'Biblioteca Digitale';
$subtitle = 'Accedi o registrati per entrare nella piattaforma';
$show_auth = false;

// Render header (capture output)
ob_start();
include dirname(__DIR__) . '/includes/header.php';
$header = ob_get_clean() ?: '';

// Render form inputs
$type = 'email';
$id = 'email';
$label = 'Email';
$aria_label = 'Inserisci la tua email';
ob_start();
include dirname(__DIR__) . '/includes/form-input.php';
$form_input_username = ob_get_clean() ?: '';

$type = 'password';
$id = 'password';
$label = 'Password';
$aria_label = 'Inserisci la tua password';
ob_start();
include dirname(__DIR__) . '/includes/form-input.php';
$form_input_password = ob_get_clean() ?: '';

// Load template and replace placeholders
$tpl = __DIR__ . '/login.html';
$html = file_get_contents($tpl);
$replacements = [
    '{{TITLE}}' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    '{{HEADER}}' => $header,
    '{{FORM_INPUT_USERNAME}}' => $form_input_username,
    '{{FORM_INPUT_PASSWORD}}' => $form_input_password,
];
$html = str_replace(array_keys($replacements), array_values($replacements), $html);
echo $html;
