<?php
// boundary/shortlist/add_to_shortlist.php
namespace Boundary;

use Controller\AddCleanerToShortlistController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/HomeOwnerUser.php';
require_once __DIR__ . '/../../controller/AddCleanerToShortlistController.php';

$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $homeId=$_SESSION['user']['id'];
    $serviceId=intval($_POST['service_id']);
    $message=(new AddCleanerToShortlistController())->execute($homeId,$serviceId)?'Added':'Failed';
}
?><html><body>
<h2>Add to Shortlist</h2>
<?php if($message):?><p><?=htmlspecialchars($message)?></p><?php endif;?>
<form method="post"><input name="service_id" placeholder="Service ID" required><button>Add</button></form>
</body></html>