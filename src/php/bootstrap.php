<?php
// Security headers
header("Content-Security-Policy: default-src 'self'; style-src 'self'; img-src 'self'; script-src 'self'; frame-src 'none';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");
?>
