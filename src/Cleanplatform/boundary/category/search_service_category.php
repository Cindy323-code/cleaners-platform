<?php
// boundary/category/search_service_category.php
namespace Boundary;

use Controller\SearchServiceCategoryController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/PlatformManager.php';
require_once __DIR__ . '/../../controller/SearchServiceCategoryController.php';

$results=[];
if($_SERVER['REQUEST_METHOD']==='GET'&&isset($_GET['q'])){
    $results=(new SearchServiceCategoryController())->execute(trim($_GET['q']));
}
?>
<html><body>
<h2>Search Categories</h2>
<form><input name="q" placeholder="Keyword"><button>Search</button></form>
<ul><?php foreach($results as$r):?><li><?=htmlspecialchars($r['name'])?></li><?php endforeach;?></ul>
</body></html>