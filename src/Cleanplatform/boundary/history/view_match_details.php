<?php
// boundary/history/view_match_details.php
namespace Boundary;

// Add redirect to maintain compatibility with existing links
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    header("Location: /Cleanplatform/boundary/history/search_confirmed_matches.php?view_id=$id");
    exit;
} else {
    // Redirect to the search page if no ID provided
    header("Location: /Cleanplatform/boundary/history/search_confirmed_matches.php");
    exit;
}
?> 