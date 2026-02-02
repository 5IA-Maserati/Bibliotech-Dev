<?php

$title = 'Biblioteca Digitale';
$subtitle = 'Accedi o registrati per entrare nella piattaforma';
include '../includes/header.php';
?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Biblioteca Digitale | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/style/login.css">
    <link rel="stylesheet" href="../assets/style/validation.css">
</head>
<body>


<div class="login-container">
    <div class="login-card">
        <h1 style="text-align: center;">Biblioteca Digitale</h1>
        <p class="subtitle">Accesso studenti e docenti</p>

        <form id="login-form">
            <?php
            $type = 'text';
            $id = 'username';
            $label = 'Username';
            $aria_label = 'Inserisci il tuo username';
            include '../includes/form-input.php';
            ?>

            <?php
            $type = 'password';
            $id = 'password';
            $label = 'Password';
            $aria_label = 'Inserisci la tua password';
            include '../includes/form-input.php';
            ?>

            <button type="submit">Accedi</button>

            <a href="signup.php" class="back-link">Registrati</a>
            <a href="../index.php" class="home-btn">Torna alla Home</a>
        </form>
    </div>
</div>

<footer>
    <p>&copy; 2026 Biblioteca Scolastica. Tutti i diritti riservati.</p>
    <p>Grazie per il tuo contributo alla nostra comunità di lettori!</p>
</footer>

<script src="../assets/js/form-validator.js"></script>
<script src="../assets/js/login.js"></script>
</body>
</html>
