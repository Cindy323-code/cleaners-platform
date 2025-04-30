<?php
// boundary/report/monthly.php
namespace Boundary;

use Controller\GenerateMonthlyReportController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/PlatformManager.php';
require_once __DIR__ . '/../../controller/GenerateMonthlyReportController.php';

$data=[];
if($_SERVER['REQUEST_METHOD']==='GET'&&isset($_GET['year'],$_GET['month'])){
    $data=(new GenerateMonthlyReportController())->execute(intval($_GET['year']),intval($_GET['month']));
}
?>
<html><body>
<h2>Monthly Report</h2>
<form>
  Year:  <input type="number" name="year" required>
  Month: <input type="number" name="month" min="1" max="12" required>
  <button>Generate</button>
</form>
<?php if($data):?><ul><?php foreach($data as$k=>$v):?><li><?=htmlspecialchars($k)?>: <?=htmlspecialchars($v)?></li><?php endforeach;?></ul><?php endif;?>
</body></html>