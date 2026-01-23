<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/php/bootstrap.php';

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Scolastica</title>
    <!-- Stylesheet: main page styles -->
    <link rel="stylesheet" href="assets/style/index.css">
</head>


<body>

    <!-- Header: site logo area and login button -->
    <header class="navbar" role="banner">
        <div class="logo-area">
            <div class="logo-placeholder" aria-hidden="true">LOGO 1</div>
            <div class="logo-placeholder" aria-hidden="true">LOGO 2</div>
        </div>
        <!-- Primary action: login -->
        <a  href="pages/login.html" class="btn-accedi-text">
            Accedi all'area riservata
        </a>
    </header>

    <!-- Quick navigation: important page links -->
    <nav class="quick-nav" role="navigation" aria-label="Navigazione rapida">
        <a href="pages/booking.html" class="btn-nav">Restituisci Libro</a>
        <a href="pages/donate.html" class="btn-nav">Dona Libro</a>
        <a href="pages/search.html" class="btn-nav">Cerca</a>
    </nav>

    <!-- Main title -->
    <h1 class="main-title">BIBLIOTECA SCOLASTICA</h1>

    <!-- CSS divider replaces <hr> for visual separation -->
    <div class="css-divider" aria-hidden="true"></div>

    <!-- Main content wrapper -->
    <main class="content-wrapper" role="main">
        <!-- Library opening hours section -->
        <section class="orari-mega-container" aria-labelledby="orari-biblioteca">
            <div class="orari-label">
                <h2 id="orari-biblioteca">ORARI<br>BIBLIOTECA</h2>
            </div>

            <div class="orari-box">
                <div class="orari-grid">
                    <div class="orari-nav" aria-hidden="true"><strong>Lun - Ven:</strong> 09:00 - 13:00</div>
                    <div class="css-divider" aria-hidden="true"></div>
                    <div class="orari-nav" aria-hidden="true"><strong>Sab - Dom:</strong> Chiuso</div>
                </div>
            </div>
        </section>
    </main>

    <!-- Photo slider (decorative content) -->
    <section class="photo-slider-container" aria-label="Galleria fotografica della biblioteca">
        <div class="slider-track">
            <div class="slide-group">
                <img src="assets/img/common/library-1.jpg" alt="Copertina della biblioteca">
                <img src="assets/img/common/library-2.jpg" alt="Area lettura della biblioteca">
                <img src="assets/img/common/library-3.jpg" alt="Ingresso della biblioteca">
            </div>

            <div class="slide-group">
                <img src="assets/img/common/library-4.jpg" alt="Sezione consultazione">
                <img src="assets/img/common/library-5.jpg" alt="Scaffali della biblioteca">
                <img src="assets/img/common/library-6.jpg" alt="Seconda area scaffali">
            </div>

            <div class="slide-group">
                <img src="assets/img/common/library-7.jpg" alt="Sezione consultazione">
                <img src="assets/img/common/library-8.jpg" alt="Scaffali della biblioteca">
                <img src="assets/img/common/library-9.jpg" alt="Seconda area scaffali">
            </div>
        </div>
    </section>

    <!-- Footer: contact and location -->
    <footer class="footer" role="contentinfo">
        <div class="footer-info">
            <p><strong>Istituto Scolastico Superiore</strong></p>
            <p>Via della Scuola, 123 - 00100 Città (Prov)</p>
            <p>Tel: 0123 456789 | Email: biblioteca@scuola.edu.it</p>
        </div>
    </footer>

</body>
</html>
