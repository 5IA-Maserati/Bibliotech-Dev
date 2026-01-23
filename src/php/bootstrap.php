<?php
declare(strict_types=1);

/**
 * -------------------------------------------------
 * Bootstrap guard
 * -------------------------------------------------
 */
if (!defined('PROJECT_ROOT')) {
    http_response_code(500);
    exit('PROJECT_ROOT not defined');
}

/**
 * -------------------------------------------------
 * Environment hardening
 * -------------------------------------------------
 */
ini_set('display_errors', '0');
ini_set('expose_php', '0');

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

/**
 * -------------------------------------------------
 * (Optional) Session hardening
 * -------------------------------------------------
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
        'use_strict_mode' => true,
    ]);
}
