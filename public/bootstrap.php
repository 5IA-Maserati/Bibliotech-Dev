<?php

/**
 * -------------------------------------------------
 * Bootstrap file
 *
 * Security headers and runtime safety configuration.
 * This file MUST be included before any output.
 * -------------------------------------------------
 */

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
    . "style-src 'self' 'unsafe-inline'; "
    . "script-src 'self'; "
    . "img-src 'self'; "
    . "font-src 'self'; "
    . "frame-src 'self'; "
    . "object-src 'none'; "
    . "base-uri 'self';"
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
