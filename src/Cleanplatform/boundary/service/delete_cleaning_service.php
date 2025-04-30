<?php
// boundary/service/delete_cleaning_service.php
namespace Boundary;

use Controller\DeleteCleaningServiceController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/CleanerUser.php';
require_once __DIR__ . '/../../controller/DeleteCleaningServiceController.php';

$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=intval($_POST['id']);
    $message=(new DeleteCleaningServiceController())->execute($id)?'Deleted':'Failed';
}
?><html><body>
<h2>Delete Service</h2>
<?php if($message):?><p><?=htmlspecialchars($message)?></p><?php endif;?>
<form method="post"><input name="id" placeholder="Service ID" required><button>Delete</button></form>
</body></html>
