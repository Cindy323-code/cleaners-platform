<?php
// boundary/service/update_cleaning_service.php
namespace Boundary;

use Controller\UpdateCleaningServiceController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/CleanerUser.php';
require_once __DIR__ . '/../../controller/UpdateCleaningServiceController.php';

$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=intval($_POST['id']);
    $fields=[];
    if($_POST['name']) $fields['name']=trim($_POST['name']);
    if($_POST['type']) $fields['type']=trim($_POST['type']);
    if($_POST['price'])$fields['price']=floatval($_POST['price']);
    if($_POST['description'])$fields['description']=trim($_POST['description']);
    $message=(new UpdateCleaningServiceController())->execute($id,$fields)?'Updated':'Failed';
}
?><html><body>
<h2>Update Service</h2>
<?php if($message):?><p><?=htmlspecialchars($message)?></p><?php endif;?>
<form method="post">
  <input name="id" placeholder="Service ID" required><br>
  <input name="name" placeholder="Name"><br>
  <input name="type" placeholder="Type"><br>
  <input name="price" placeholder="Price"><br>
  <textarea name="description" placeholder="Description"></textarea><br>
  <button type="submit">Update</button>
</form>
</body></html>