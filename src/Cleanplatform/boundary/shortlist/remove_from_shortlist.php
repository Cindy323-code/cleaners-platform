<?php
// boundary/shortlist/remove_from_shortlist.php
namespace Boundary;

use Controller\RemoveFromShortlistController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/HomeOwnerUser.php';
require_once __DIR__ . '/../../controller/RemoveFromShortlistController.php';

// Check if user is logged in as homeowner
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'homeowner') {
    header('Location: /Cleanplatform/boundary/auth/login.php');
    exit;
}

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $homeownerId = $_SESSION['user']['id'];
    $shortlistId = isset($_POST['shortlist_id']) ? intval($_POST['shortlist_id']) : 0;
    
    if ($shortlistId) {
        $result = (new RemoveFromShortlistController())->execute($homeownerId, $shortlistId);
        if ($result) {
            $message = 'Service removed from your shortlist successfully';
            $success = true;
        } else {
            $message = 'Failed to remove service from shortlist';
        }
    } else {
        $message = 'Invalid shortlist ID';
    }
}

// Redirect back to the shortlist page
header('Location: /Cleanplatform/boundary/shortlist/view_shortlist.php' . ($message ? '?message=' . urlencode($message) . '&success=' . ($success ? '1' : '0') : ''));
exit;
