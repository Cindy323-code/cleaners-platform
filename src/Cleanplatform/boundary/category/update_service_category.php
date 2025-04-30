<?php
// boundary/category/update_service_category.php
namespace Boundary;

use Controller\UpdateServiceCategoryController;
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/PlatformManager.php';
require_once __DIR__ . '/../../controller/UpdateServiceCategoryController.php';

$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=intval($_POST['id']);
    $fields=[];
    if($_POST['name']) $fields['name']=trim($_POST['name']);
    if($_POST['description']) $fields['description']=trim($_POST['description']);
    $message=(new UpdateServiceCategoryController())->execute($id,$fields)?'Updated':'Failed';
}
?>
<html><body>
<h2>Update Service Category</h2>
<?php if($message):?><p><?=htmlspecialchars($message)?></p><?php endif;?>
<form method="post">
  <input name="id" placeholder="Category ID" required><br>
  <input name="name" placeholder="Name"><br>
  <textarea name="description" placeholder="Description"></textarea><br>
  <button>Update</button>
</form>
</body></html>
