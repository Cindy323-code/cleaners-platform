<?php
// boundary/profile/search_user_profile.php
namespace Boundary;

use Controller\SearchUserProfileController;

require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/UserProfile.php';
require_once __DIR__ . '/../../controller/SearchUserProfileController.php';

$results=[];
if ($_SERVER['REQUEST_METHOD']==='GET' && isset($_GET['q'])){
    $criteria=['fullName'=>trim($_GET['q'])];
    $results=(new SearchUserProfileController())->execute($criteria);
}
?>
<html><body>
<h2>Search Profiles</h2>
<form method="get">
  <input name="q" placeholder="Name"><button>Search</button>
</form>
<?php if($results):?><ul><?php foreach($results as $r):?><li><?=htmlspecialchars($r['fullName'])?></li><?php endforeach;?></ul><?php endif;?>
</body></html>
