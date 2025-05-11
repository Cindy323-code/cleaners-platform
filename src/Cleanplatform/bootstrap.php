<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Define Base URL
if (isset($_SERVER['HTTP_HOST'])) { // Ensure it's a web request
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $projectRootSegment = '';

    if (isset($_SERVER['DOCUMENT_ROOT'])) {
        // realpath to resolve any symbolic links and get a canonical path
        // str_replace to normalize directory separators
        $docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
        // __DIR__ is the directory of bootstrap.php (e.g., d:/xampp/htdocs/github/cleaners-platform/src/Cleanplatform)
        $scriptPhysicalDir = str_replace('\\', '/', __DIR__); 

        // Check if the project directory is a subdirectory of the document root
        if (strpos($scriptPhysicalDir, $docRoot) === 0) {
            $projectRootSegment = substr($scriptPhysicalDir, strlen($docRoot));
        } else {
            // Fallback or error: Project directory is not under document root.
            error_log("CleanersPlatform Error: Project directory {$scriptPhysicalDir} not under DOCUMENT_ROOT {$docRoot}. BASE_URL may be incorrect. Attempting SCRIPT_NAME heuristic.");
            // Attempt to derive from SCRIPT_NAME as a fallback, common for aliased setups
            // This heuristic assumes SCRIPT_NAME contains a recognizable part of the project path.
            // For example, if SCRIPT_NAME is /Cleanplatform/public/index.php and bootstrap.php is in src/Cleanplatform,
            // then dirname(dirname(SCRIPT_NAME)) might give /Cleanplatform (if src/Cleanplatform is aliased to /Cleanplatform)
            if (isset($_SERVER['SCRIPT_NAME'])) {
                // This specific heuristic tries to find a segment like '/Cleanplatform' if used in the URL structure
                // It's less reliable than DOC_ROOT based calculation.
                if (preg_match('/^(\/[^\/]+\/github\/cleaners-platform\/src\/Cleanplatform)/i', $_SERVER['SCRIPT_NAME'], $matches) || preg_match('/^(\/Cleanplatform)/i', $_SERVER['SCRIPT_NAME'], $matches) ) {
                    $projectRootSegment = $matches[1];
                } else {
                     // A more general approach: path to the directory containing the 'public' folder in the URL
                    $projectRootSegment = dirname(dirname($_SERVER['SCRIPT_NAME']));
                    if ($projectRootSegment === '/' || $projectRootSegment === '.') {
                        $projectRootSegment = ''; // Avoid making it just '/' if it's at the root
                    }
                    error_log("CleanersPlatform Warning: BASE_URL SCRIPT_NAME heuristic used, segment: " . $projectRootSegment);
                }
            } else {
                 $projectRootSegment = ''; // SCRIPT_NAME not available
            }
        }
    } else {
        error_log("CleanersPlatform Error: SERVER_DOCUMENT_ROOT not set. Cannot determine BASE_URL reliably.");
        $projectRootSegment = ''; // Default to empty, or a pre-configured value for CLI/exotic setups.
    }
    
    // Ensure $projectRootSegment starts with a slash if it's not empty and not already starting with one.
    if (!empty($projectRootSegment) && $projectRootSegment[0] !== '/') {
        $projectRootSegment = '/' . $projectRootSegment;
    }
    // Remove trailing slash from $projectRootSegment if any, except if it's just "/"
    if (strlen($projectRootSegment) > 1 && substr($projectRootSegment, -1) === '/') {
        $projectRootSegment = rtrim($projectRootSegment, '/');
    }

    define('BASE_URL', $protocol . $host . $projectRootSegment);
} else {
    // Not a web request (e.g. CLI), BASE_URL might not be applicable or needs a different definition.
    define('BASE_URL', ''); // Define as empty or a sensible default for CLI scripts.
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
