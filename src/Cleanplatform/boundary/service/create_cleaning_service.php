<?php
namespace Boundary;

use Controller\CreateCleaningServiceController;

require_once __DIR__ . '/../partials/header.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'cleaner') {
    header('Location: /boundary/auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/CleanerUser.php';
require_once __DIR__ . '/../../controller/CreateCleaningServiceController.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'cleaner_id'  => $_SESSION['user']['id'],
        'name'        => trim($_POST['name']),
        'type'        => trim($_POST['type']),
        'price'       => floatval($_POST['price']),
        'description' => trim($_POST['description'])
    ];
    $ok      = (new CreateCleaningServiceController())->execute($data);
    $message = $ok ? 'Service created.' : 'Failed to create service.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Create Service</title></head>
<body>
  <h2>Create Cleaning Service</h2>
  <?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>
  <form method="post">
    <label>Name: <input name="name" required></label><br>
    <label>Type: <input name="type" required></label><br>
    <label>Price: <input type="number" step="0.01" name="price" required></label><br>
    <label>Description: <textarea name="description"></textarea></label><br>
    <button type="submit">Create</button>
  </form>
</body>
</html>
