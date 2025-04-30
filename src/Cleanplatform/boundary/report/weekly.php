<?php
// boundary/report/weekly.php
namespace Boundary;

use Controller\GenerateWeeklyReportController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/PlatformManager.php';
require_once __DIR__ . '/../../controller/GenerateWeeklyReportController.php';

$data=[];
if($_SERVER['REQUEST_METHOD']==='GET'&&isset($_GET['start'],$_GET['end'])){
    $data=(new GenerateWeeklyReportController())->execute($_GET['start'],$_GET['end']);
}
?>
<html><body>
<h2>Weekly Report</h2>
<form>
  From: <input type="date" name="start" required>
  To:   <input type="date" name="end"   required>
  <button>Generate</button>
</form>
<?php if($data):?><ul><?php foreach($data as$k=>$v):?><li><?=htmlspecialchars($k)?>: <?=htmlspecialchars($v)?></li><?php endforeach;?></ul><?php endif;?>
</body></html>
