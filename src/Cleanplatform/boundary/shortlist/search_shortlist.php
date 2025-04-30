<?php
// boundary/shortlist/search_shortlist.php
namespace Boundary;

use Controller\SearchShortlistController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/HomeOwnerUser.php';
require_once __DIR__ . '/../../controller/SearchShortlistController.php';

$results=[];
if($_SERVER['REQUEST_METHOD']==='GET'&&isset($_GET['q'])){
    $results=(new SearchShortlistController())->execute($_SESSION['user']['id'],trim($_GET['q']));
}
?>
<html><body>
<h2>Search Shortlist</h2>
<form><input name="q" placeholder="Keyword"><button>Search</button></form>
<ul><?php foreach($results as$r):?><li><?=htmlspecialchars($r['name'])?></li><?php endforeach;?></ul>
</body></html>
