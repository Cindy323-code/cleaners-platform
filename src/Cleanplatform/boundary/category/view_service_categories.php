<?php
// boundary/category/view_service_categories.php
namespace Boundary;

use Controller\ViewServiceCategoriesController;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/PlatformManager.php';
require_once __DIR__ . '/../../controller/ViewServiceCategoriesController.php';

$list=(new ViewServiceCategoriesController())->execute();
?>
<html><body>
<h2>Service Categories</h2>
<ul><?php foreach($list as$c):?><li><?=htmlspecialchars($c['name'])?></li><?php endforeach;?></ul>
</body></html>
