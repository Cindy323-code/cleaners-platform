<?php
// boundary/service/view_cleaning_services.php
namespace Boundary;

use Controller\ViewCleaningServicesController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/CleanerUser.php';
require_once __DIR__ . '/../../controller/ViewCleaningServicesController.php';

if(!isset($_SESSION['role'])||$_SESSION['role']!=='cleaner'){header('Location: ../auth/login.php');exit;}
$cleanerId=$_SESSION['user']['id'];
$list=(new ViewCleaningServicesController())->execute($cleanerId);
?>
<html><body>
<h2>Your Services</h2>
<ul><?php foreach($list as$l):?><li><?=htmlspecialchars($l['name'])." (".$l['type'].') $'.htmlspecialchars($l['price'])?></li><?php endforeach;?></ul>
</body></html>
