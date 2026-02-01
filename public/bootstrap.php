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

/**
 * -------------------------------------------------
 * Session security settings
 * -------------------------------------------------
 */
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => isset($_SERVER['HTTPS'])
]);

/**
 * -------------------------------------------------
 * Session initialization
 * -------------------------------------------------
 */
if (session_status() === 1) {   // 1 equals PHP_SESSION_NONE, which means that no session is active and...
    session_start();            // ...we can start a new one. (I've done this because of the linter...)
}
/* Session codes
 * 0 = disabled
 * 1 = none
 * 2 = active
 */
