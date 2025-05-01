<?php
// boundary/admin/search_user_account.php
namespace Boundary;
require_once __DIR__ . '/../partials/header.php';
use Controller\SearchUserAccountController;

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/AdminUser.php';
require_once __DIR__ . '/../../controller/SearchUserAccountController.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php'); exit;
}

// 获取搜索参数
$keyword = trim($_GET['keyword'] ?? '');
$role = $_GET['role'] ?? '';
$status = $_GET['status'] ?? '';

// 设置默认值，如果没有任何参数，则执行空搜索（返回所有用户）
$hasSearch = (!empty($keyword) || !empty($role) || !empty($status));
$results = [];

// 执行搜索
$controller = new SearchUserAccountController();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $results = $controller->execute($keyword, $role, $status);
}
?>

<h2>Search User Accounts</h2>

<!-- 高级搜索表单 -->
<div class="card">
  <div class="card-title">Search Filters</div>
  <form method="get" class="search-form">
    <div class="form-row">
      <div class="form-group">
        <label for="keyword">Keyword:</label>
        <input type="text" id="keyword" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Username or email...">
      </div>
      
      <div class="form-group">
        <label for="role">Role:</label>
        <select id="role" name="role">
          <option value="">-- All Roles --</option>
          <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Administrator</option>
          <option value="cleaner" <?= $role === 'cleaner' ? 'selected' : '' ?>>Cleaner</option>
          <option value="homeowner" <?= $role === 'homeowner' ? 'selected' : '' ?>>Homeowner</option>
          <option value="manager" <?= $role === 'manager' ? 'selected' : '' ?>>Manager</option>
        </select>
      </div>
      
      <div class="form-group">
        <label for="status">Status:</label>
        <select id="status" name="status">
          <option value="">-- All Statuses --</option>
          <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
        </select>
      </div>
    </div>
    
    <div class="form-actions">
      <button type="submit" class="btn">Search</button>
      <a href="<?= $_SERVER['PHP_SELF'] ?>" class="btn btn-small">Reset</a>
    </div>
  </form>
</div>

<!-- 搜索结果 -->
<div class="card">
  <div class="card-title">
    Search Results
    <?php if ($hasSearch): ?>
      <span class="results-count">(<?= count($results) ?> users found)</span>
    <?php endif; ?>
  </div>
  
  <?php if (empty($results)): ?>
    <p class="no-results">No users found matching your criteria.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Username</th>
          <th>Role</th>
          <th>Email</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($results as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['user']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td>
              <span class="status-badge status-<?= htmlspecialchars(strtolower($row['status'])) ?>">
                <?= htmlspecialchars($row['status']) ?>
              </span>
            </td>
            <td>
              <a href="../admin/view_user_account.php?username=<?= htmlspecialchars($row['user']) ?>" class="btn btn-small">View</a>
              <a href="../admin/update_user_account.php?username=<?= htmlspecialchars($row['user']) ?>" class="btn btn-small">Update</a>
              <?php if ($row['status'] !== 'suspended'): ?>
                <a href="../admin/suspend_user_account.php?username=<?= htmlspecialchars($row['user']) ?>" class="btn btn-small">Suspend</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<style>
  .form-row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -10px;
  }
  
  .form-row .form-group {
    flex: 1;
    min-width: 200px;
    padding: 0 10px;
    margin-bottom: 15px;
  }
  
  .form-actions {
    margin-top: 10px;
  }
  
  .results-count {
    font-size: 14px;
    font-weight: normal;
    color: #666;
    margin-left: 10px;
  }
  
  .no-results {
    padding: 20px;
    text-align: center;
    color: #666;
    font-style: italic;
  }
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
