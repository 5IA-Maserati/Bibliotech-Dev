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

    <!-- CSS principale -->
    <link rel="stylesheet" href="/assets/style/index.css">
</head>
<body>

    <!-- Header: site logo area and login button -->
    <header class="navbar" role="banner">
        <div class="logo-area">
            <div class="logo-placeholder" aria-hidden="true">LOGO 1</div>
            <div class="logo-placeholder" aria-hidden="true">LOGO 2</div>
        </div>

        <a href="pages/login.html" class="btn-accedi-text">
            Accedi all'area riservata
        </a>
    </header>

    <!-- Quick navigation -->
    <nav class="quick-nav" role="navigation" aria-label="Navigazione rapida">
        <a href="pages/booking.html" class="btn-nav">Restituisci Libro</a>
        <a href="pages/search.html" class="btn-nav">Cerca</a>
    </nav>

    <h1 class="main-title">BIBLIOTECA SCOLASTICA</h1>

    <div class="css-divider" aria-hidden="true"></div>

    <main class="content-wrapper" role="main">
        <section class="orari-mega-container" aria-labelledby="orari-biblioteca">
            <div class="orari-label">
                <h2 id="orari-biblioteca">ORARI<br>BIBLIOTECA</h2>
            </div>

            <div class="orari-box">
                <div class="orari-grid">
                    <div class="orari-nav" aria-hidden="true">
                        <strong>Lun - Ven:</strong> 09:00 - 13:00
                    </div>
                    <div class="css-divider" aria-hidden="true"></div>
                    <div class="orari-nav" aria-hidden="true">
                        <strong>Sab - Dom:</strong> Chiuso
                    </div>
                </div>
            </div>
        </section>
    </main>

    <section class="photo-slider-container" aria-label="Galleria fotografica della biblioteca">
        <div class="slider-track">
            <div class="slide-group">
                <img src="assets/img/common/img-1.jpg" alt="Copertina della biblioteca">
                <img src="assets/img/common/img-2.jpg" alt="Area lettura della biblioteca">
                <img src="assets/img/common/img-3.jpg" alt="Ingresso della biblioteca">
            </div>
        </div>
    </section>

    <footer class="footer" role="contentinfo">
        <div class="footer-info">
            <p><strong>Istituto Scolastico Superiore</strong></p>
            <p>Via della Scuola, 123 - 00100 Città (Prov)</p>
            <p>Tel: 0123 456789 | Email: biblioteca@scuola.edu.it</p>
        </div>
    </footer>

</body>
</html>
