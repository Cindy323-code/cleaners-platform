<?php
// boundary/report/daily.php
namespace Boundary;

use Controller\GenerateDailyReportController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/PlatformManager.php';
require_once __DIR__ . '/../../controller/GenerateDailyReportController.php';

$data=[];
if($_SERVER['REQUEST_METHOD']==='GET'&&isset($_GET['date'])){
    $data=(new GenerateDailyReportController())->execute($_GET['date']);
}
?>
<html><body>
<h2>Daily Report</h2>
<form><input type="date" name="date" required><button>Generate</button></form>
<?php if($data):?><ul><?php foreach($data as$k=>$v):?><li><?=htmlspecialchars($k)?>: <?=htmlspecialchars($v)?></li><?php endforeach;?></ul><?php endif;?>
</body></html>