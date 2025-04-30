<?php
// boundary/history/search_service_usage_history.php
namespace Boundary;

use Controller\SearchServiceUsageHistoryController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/MatchHistory.php';
require_once __DIR__ . '/../../controller/SearchServiceUsageHistoryController.php';

$results=[];
if($_SERVER['REQUEST_METHOD']==='GET'&&isset($_GET['from'],$_GET['to'])){
    $filter=['from'=>$_GET['from'],'to'=>$_GET['to']];
    $results=(new SearchServiceUsageHistoryController())->execute($_SESSION['user']['id'],$filter);
}
?>
<html><body>
<h2>Service Usage History</h2>
<form>
  From: <input type="date" name="from">
  To:   <input type="date" name="to">
  <button>Search</button>
</form>
<ul><?php foreach($results as$r):?><li>Usage ID <?=htmlspecialchars($r['id'])?></li><?php endforeach;?></ul>
</body></html>