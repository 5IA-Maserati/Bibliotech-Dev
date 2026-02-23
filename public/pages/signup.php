<?php

$title = 'Biblioteca Digitale';
$subtitle = 'Grazie per il tuo contributo alla nostra comunità di lettori!';
$show_auth = false;

// Render header
ob_start();
include dirname(__DIR__) . '/includes/header.php';
$header = ob_get_clean() ?: '' ;

// Render all form inputs
$type = 'text';
$id = 'nome';
$label = 'Nome';
$aria_label = 'Inserisci il tuo nome';
ob_start();
include dirname(__DIR__) . '/includes/form-input.php';
$fi_nome = ob_get_clean() ?: '';

$type = 'text';
$id = 'cognome';
$label = 'Cognome';
$aria_label = 'Inserisci il tuo cognome';
ob_start();
include dirname(__DIR__) . '/includes/form-input.php';
$fi_cognome = ob_get_clean() ?: '';

$type = 'date';
$id = 'data-nascita';
$label = 'Data di nascita';
$aria_label = 'Inserisci la tua data di nascita';
ob_start();
include dirname(__DIR__) . '/includes/form-input.php';
$fi_data_nascita = ob_get_clean() ?: '';

$type = 'email';
$id = 'email';
$label = 'Email';
$aria_label = 'Inserisci la tua email';
ob_start();
include dirname(__DIR__) . '/includes/form-input.php';
$fi_email = ob_get_clean() ?: '';

$type = 'password';
$id = 'password';
$label = 'Password';
$aria_label = 'Inserisci la tua password';
ob_start();
include dirname(__DIR__) . '/includes/form-input.php';
$fi_password = ob_get_clean() ?: '';

$type = 'password';
$id = 'confirm-password';
$label = 'Conferma password';
$aria_label = 'Conferma la tua password';
ob_start();
include dirname(__DIR__) . '/includes/form-input.php';
$fi_confirm_password = ob_get_clean() ?: '';

// Load template and replace placeholders
$tpl = __DIR__ . '/signup.html';
$html = file_get_contents($tpl);
$replacements = [
    '{{TITLE}}' => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
    '{{HEADER}}' => $header,
    '{{FORM_INPUT_NOME}}' => $fi_nome,
    '{{FORM_INPUT_COGNOME}}' => $fi_cognome,
    '{{FORM_INPUT_DATA_NASCITA}}' => $fi_data_nascita,
    '{{FORM_INPUT_EMAIL}}' => $fi_email,
    '{{FORM_INPUT_PASSWORD}}' => $fi_password,
    '{{FORM_INPUT_CONFIRM_PASSWORD}}' => $fi_confirm_password,
];
$html = str_replace(array_keys($replacements), array_values($replacements), $html);
echo $html;
