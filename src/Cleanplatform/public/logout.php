<?php
// public/logout.php

require_once __DIR__ . '/../bootstrap.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$_SESSION = [];
session_unset();
session_destroy();
header('Location: /Cleanplatform/boundary/auth/login.php');
exit;
