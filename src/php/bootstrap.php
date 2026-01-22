<?php

declare(strict_types=1);

// Security headers
header(
    "Content-Security-Policy: "
    . "default-src 'self'; "
    . "style-src 'self' 'unsafe-inline'; "
    . "script-src 'self'; "
    . "img-src 'self'; "
    . "font-src 'self'; "
    . "frame-src 'none'"
);


header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
