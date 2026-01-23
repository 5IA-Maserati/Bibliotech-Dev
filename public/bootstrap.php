<?php
declare(strict_types=1);

/**
 * -------------------------------------------------
 * Security headers
 * MUST be sent before any output
 * -------------------------------------------------
 */
header(
    "Content-Security-Policy: "
    . "default-src 'self'; "
    . "style-src 'self'; "
    . "script-src 'self'; "
    . "img-src 'self'; "
    . "font-src 'self'; "
    . "frame-src 'none'"
);

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');

/**
 * -------------------------------------------------
 * Encoding & locale safety
 * -------------------------------------------------
 */
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}
