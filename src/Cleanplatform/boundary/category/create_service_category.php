<?php
// boundary/category/create_service_category.php
namespace Boundary;

use Controller\CreateServiceCategoryController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/PlatformManager.php';
require_once __DIR__ . '/../../controller/CreateServiceCategoryController.php';

if(!isset($_SESSION['role'])||$_SESSION['role']!=='manager'){header('Location: ../auth/login.php');exit;}

$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name=trim($_POST['name']);
    $desc=trim($_POST['description']);
    $message=(new CreateServiceCategoryController())->execute($name,$desc)?'Category created':'Failed';
}
?>
<html><body>
<h2>Create Service Category</h2>
<?php if($message):?><p><?=htmlspecialchars($message)?></p><?php endif; ?>
<form method="post">
  <input name="name" placeholder="Name" required><br>
  <textarea name="description" placeholder="Description"></textarea><br>
  <button>Create</button>
</form>
</body></html>