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
    . "connect-src 'self' https://www.googleapis.com https://archive.org "
    . "https://api.isbndb.com https://api.bookshare.org https://bookshare.org "
    . "https://www.bookshare.org; "
    . "img-src 'self' https://covers.openlibrary.org https://books.google.com "
    . "https://*.googleusercontent.com https://archive.org https://*.archive.org; "
    . "font-src 'self'; "
    . "frame-src 'self'; "
    . "object-src 'none'; "
    . "base-uri 'self';"
);

header('Cache-Control: no-cache');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');

/**
 * -------------------------------------------------
 * HSTS (HTTPS only)
 * -------------------------------------------------
 */
header(
    'Strict-Transport-Security: '
    . 'max-age=31536000; includeSubDomains; preload'
);

/**
 * -------------------------------------------------
 * Permissions Policy
 * Deny all unnecessary browser features
 * -------------------------------------------------
 */
header(
    'Permissions-Policy: '
    . 'accelerometer=(), '
    . 'autoplay=(), '
    . 'camera=(), '
    . 'display-capture=(), '
    . 'encrypted-media=(), '
    . 'fullscreen=(), '
    . 'geolocation=(), '
    . 'gyroscope=(), '
    . 'magnetometer=(), '
    . 'microphone=(), '
    . 'midi=(), '
    . 'payment=(), '
    . 'picture-in-picture=(), '
    . 'publickey-credentials-get=(), '
    . 'screen-wake-lock=(), '
    . 'usb=(), '
    . 'web-share=(), '
    . 'xr-spatial-tracking=()'
);

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
    'secure' => (
        !empty($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== 'off'
    )
]);

/**
 * -------------------------------------------------
 * Session initialization
 * -------------------------------------------------
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
