<?php
// boundary/service/search_cleaning_services.php
namespace Boundary;

use Controller\SearchCleaningServicesController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/CleanerUser.php';
require_once __DIR__ . '/../../controller/SearchCleaningServicesController.php';

$results=[];
if($_SERVER['REQUEST_METHOD']==='GET'&&isset($_GET['q'])){
    $id=$_SESSION['user']['id'];
    $results=(new SearchCleaningServicesController())->execute($id,trim($_GET['q']));
}
?><html><body>
<h2>Search Services</h2>
<form><input name="q" placeholder="Keyword"><button>Search</button></form>
<ul><?php foreach($results as$r):?><li><?=htmlspecialchars($r['name'])?></li><?php endforeach;?></ul>
</body></html>