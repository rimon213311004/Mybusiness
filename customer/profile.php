<?php
declare(strict_types=1);
$pageTitle = 'Profile';
$activeNav = 'profile';
require_once __DIR__ . '/../includes/functions.php';
$customer = require_customer();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $form = [
        'name' => trim($_POST['name'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'company' => trim($_POST['company'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
    ];
    $newPass = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (strlen($form['name']) < 2) $errors[] = 'Please enter your name.';

    if ($newPass !== '' || $confirm !== '') {
        if (strlen($newPass) < 8) $errors[] = 'New password must be at least 8 characters.';
        elseif ($newPass !== $confirm) $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        db()->prepare('UPDATE customers SET name = ?, phone = ?, company = ?, address = ? WHERE id = ?')
            ->execute([$form['name'], $form['phone'] ?: null, $form['company'] ?: null, $form['address'] ?: null, $customer['id']]);
        if ($newPass !== '') {
            db()->prepare('UPDATE customers SET password = ? WHERE id = ?')->execute([password_hash($newPass, PASSWORD_DEFAULT), $customer['id']]);
        }
        flash('success', 'Profile updated successfully.');
        redirect('customer/profile.php');
    }
}

include __DIR__ . '/../includes/customer-header.php';
?>

<div class="panel">
  <div class="panel-head"><h3>Edit Profile</h3></div>
  <div class="panel-body">
    <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
    <form method="post" action="<?= url('customer/profile.php') ?>">
      <?= csrf_field() ?>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label" for="name">Full Name</label>
          <input type="text" id="name" name="name" class="form-control" value="<?= e($customer['name']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input type="email" id="email" class="form-control" value="<?= e($customer['email']) ?>" disabled>
          <div class="form-hint">Email cannot be changed.</div>
        </div>
        <div class="form-group">
          <label class="form-label" for="phone">Phone</label>
          <input type="text" id="phone" name="phone" class="form-control" value="<?= e($customer['phone']) ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="company">Company</label>
          <input type="text" id="company" name="company" class="form-control" value="<?= e($customer['company']) ?>">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="address">Address</label>
        <input type="text" id="address" name="address" class="form-control" value="<?= e($customer['address']) ?>">
      </div>

      <h3 style="font-size:1rem;margin:24px 0 14px;">Change Password</h3>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label" for="new_password">New Password</label>
          <input type="password" id="new_password" name="new_password" class="form-control">
          <div class="form-hint">Leave blank to keep current password.</div>
        </div>
        <div class="form-group">
          <label class="form-label" for="confirm_password">Confirm New Password</label>
          <input type="password" id="confirm_password" name="confirm_password" class="form-control">
        </div>
      </div>

      <button type="submit" class="btn btn-primary"><?= icon('check') ?> Save Changes</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/customer-footer.php'; ?>
