<?php
namespace Boundary;

use Controller\CreateUserProfileController;

require_once __DIR__ . '/../partials/header.php';
if (!isset($_SESSION['user'])) {
    header('Location: /boundary/auth/login.php');
    exit;
}

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../entity/UserProfile.php';
require_once __DIR__ . '/../../controller/CreateUserProfileController.php';

$userId = $_SESSION['user']['id'];
$userRole = $_SESSION['role'];

// Check user role, only cleaner and homeowner can create profiles
if ($userRole !== 'cleaner' && $userRole !== 'homeowner') {
    echo "<div class='error'>Only cleaners and homeowners can create profiles</div>";
    require_once __DIR__ . '/../partials/footer.php';
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'full_name'    => trim($_POST['full_name']),
        'avatar_url'   => trim($_POST['avatar_url']),
        'bio'          => trim($_POST['bio']),
        'availability' => trim($_POST['availability']),
        'user_type'    => $userRole
    ];
    
    $controller = new CreateUserProfileController();
    $ok = $controller->execute($userId, $data);
    $message = $ok ? 'Profile created successfully.' : 'Failed to create profile.';
}
?>

<h2>Create User Profile</h2>

<div class="card">
  <div class="card-title">Personal Information</div>
  
  <?php if ($message): ?>
    <div class="<?= strpos($message, 'success') !== false ? 'success' : 'error' ?>">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>
  
  <form method="post">
    <div class="form-group">
      <label for="full_name">Full Name:</label>
      <input type="text" id="full_name" name="full_name" required>
    </div>
    
    <div class="form-group">
      <label for="avatar_url">Profile Picture URL:</label>
      <input type="url" id="avatar_url" name="avatar_url" placeholder="https://example.com/your-image.jpg">
    </div>
    
    <div class="form-group">
      <label for="bio">Bio/Description:</label>
      <textarea id="bio" name="bio" rows="4" placeholder="Tell us about yourself or your services..."></textarea>
    </div>
    
    <div class="form-group">
      <label for="availability">Availability:</label>
      <input type="text" id="availability" name="availability" placeholder="e.g. Weekdays 9am-5pm, Weekends on request">
    </div>
    
    <button type="submit" class="btn">Create Profile</button>
  </form>
</div>

<div class="card">
  <div class="card-title">What Happens Next?</div>
  <p>After creating your profile:</p>
  <ul>
    <li>Your profile will be visible to other users</li>
    <li>You can update your information anytime</li>
    <?php if ($userRole === 'cleaner'): ?>
      <li>Create cleaning services to appear in search results</li>
      <li>Homeowners can add you to their shortlist</li>
    <?php else: ?>
      <li>You can search for available cleaners</li>
      <li>Add cleaners to your shortlist for easy access</li>
    <?php endif; ?>
  </ul>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
