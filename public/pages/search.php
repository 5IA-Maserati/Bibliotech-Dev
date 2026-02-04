<?php

$title = 'Catalogo';
$show_nav = false;
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="Catalogo Bibliotecario">
  <title>Catalogo Bibliotecario</title>
  <link rel="stylesheet" href="../assets/style/search.css">
  <link rel="stylesheet" href="../assets/style/validation.css">
</head>
<body>



  <nav class="action-bar">
    <div class="nav-buttons">
      <a href="../index.php" class="btn-nav">Home</a>
      <a href="donate.php" class="btn-nav donate">Dona un libro</a>
    </div>
  </nav>

  <main class="container">
    <section class="search-section">
      <h2 class="catalogo-title">Catalogo</h2>
      <div class="search-bar">
        <input id="q" type="text" placeholder="Cerca per titolo, autore o ISBN" aria-label="Cerca libri">
        <button class="btn-search">Cerca</button>
      </div>
    </section>

    <div class="filters-dropdown">
      <select id="genre">
        <option value="">Tutti i generi</option>
        <option>Romanzo</option>
        <option>Saggistica</option>
        <option>Storia</option>
        <option>Fantascienza</option>
      </select>
      <select id="sort">
        <option value="title">Ordina: Titolo</option>
        <option value="year-desc">Anno (più recente)</option>
      </select>
    </div>

    <div id="results" class="book-grid" aria-live="polite">
        </div>

    <footer>
      <p>Mostrate <span id="count">0</span> voci. Prototipo Bibliotecario 2026.</p>
    </footer>
  </main>

  <script src="../assets/js/form-validator.js"></script>
  <script src="../assets/js/search.js"></script>
</body>
</html>
