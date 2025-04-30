<?php
// boundary/category/delete_service_category.php
namespace Boundary;

use Controller\DeleteServiceCategoryController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/PlatformManager.php';
require_once __DIR__ . '/../../controller/DeleteServiceCategoryController.php';

$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=intval($_POST['id']);
    $message=(new DeleteServiceCategoryController())->execute($id)?'Deleted':'Failed';
}
?>
<html><body>
<h2>Delete Service Category</h2>
<?php if($message):?><p><?=htmlspecialchars($message)?></p><?php endif;?>
<form method="post"><input name="id" placeholder="Category ID" required><button>Delete</button></form>
</body></html>