<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
spl_autoload_register(function($cls){
    $prefixes = [
        'Config\\'     => __DIR__.'/config/',
        'Entity\\'     => __DIR__.'/entity/',
        'Controller\\' => __DIR__.'/controller/',
    ];
    foreach ($prefixes as $ns => $dir) {
        if (strpos($cls, $ns) === 0) {
            $path = $dir . str_replace('\\', '/', substr($cls, strlen($ns))) . '.php';
            if (file_exists($path)) { require_once $path; }
        }
    }
});
