<?php
// boundary/history/search_confirmed_matches.php
namespace Boundary;

use Controller\SearchConfirmedMatchesController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/MatchHistory.php';
require_once __DIR__ . '/../../controller/SearchConfirmedMatchesController.php';

$results=[];
if($_SERVER['REQUEST_METHOD']==='GET'&&isset($_GET['from'],$_GET['to'])){
    $filter=['from'=>$_GET['from'],'to'=>$_GET['to']];
    $results=(new SearchConfirmedMatchesController())->execute($_SESSION['user']['id'],$filter);
}
?>
<html><body>
<h2>Confirmed Matches</h2>
<form>
  From: <input type="date" name="from">
  To:   <input type="date" name="to">
  <button>Search</button>
</form>
<ul><?php foreach($results as$r):?><li>Match ID <?=htmlspecialchars($r['id'])?></li><?php endforeach;?></ul>
</body></html>
  