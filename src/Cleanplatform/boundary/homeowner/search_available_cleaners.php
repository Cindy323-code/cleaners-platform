<?php
namespace Boundary;

use Controller\SearchAvailableCleanersController;

require_once __DIR__ . '/../partials/header.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'homeowner') {
    header('Location: /boundary/auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/HomeOwnerUser.php';
require_once __DIR__ . '/../../controller/SearchAvailableCleanersController.php';

$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type'])) {
    $criteria = ['service_type' => trim($_GET['type'])];
    $results  = (new SearchAvailableCleanersController())->execute($criteria);
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Search Cleaners</title></head>
<body>
  <h2>Search Available Cleaners</h2>
  <form method="get">
    <label>Service Type:
      <input name="type" placeholder="e.g. Deep Cleaning">
    </label>
    <button type="submit">Search</button>
  </form>

  <?php if ($results): ?>
    <h3>Results</h3>
    <table border="1">
      <tr><th>Cleaner</th><th>Service</th><th>Type</th><th>Price</th></tr>
      <?php foreach ($results as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= htmlspecialchars($row['service_name']) ?></td>
          <td><?= htmlspecialchars($row['service_type']) ?></td>
          <td>$<?= htmlspecialchars($row['price']) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php elseif (isset($_GET['type'])): ?>
    <p>No cleaners found.</p>
  <?php endif; ?>
</body>
</html>
