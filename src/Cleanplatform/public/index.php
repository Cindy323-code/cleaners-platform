<?php
require_once __DIR__ . '/../bootstrap.php'; // This defines BASE_URL

// public/index.php
// Redirect to the login page using the BASE_URL
if (!defined('BASE_URL')) {
    // This should ideally not happen if bootstrap.php is included and defines it.
    error_log("Critical Error: BASE_URL not defined in public/index.php. Check bootstrap.php.");
    // Fallback to a generic error or a very basic redirect if absolutely necessary.
    die('Critical configuration error: Base URL is not defined. Please check server logs.');
}

header('Location: ' . BASE_URL . '/boundary/auth/login.php');
exit;
