<?php
// Security headers
header("Content-Security-Policy: default-src 'self'; style-src 'self'; img-src 'self'; script-src 'self'; frame-src 'none';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Scolastica</title>

    <!-- CSS principale -->
    <link rel="stylesheet" href="/assets/style/index.css">
</head>