<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    } else {
        $stmt = db()->prepare('SELECT id FROM customers WHERE email = ?');
        $stmt->execute([$email]);
        $exists = $stmt->fetch();
        if (!$exists) {
            $errors[] = 'No account found with this email address.';
        } else {
            // Store a reset token (in production this would be emailed)
            $token = bin2hex(random_bytes(32));
            db()->prepare('DELETE FROM password_resets WHERE email = ?')->execute([$email]);
            $stmt = db()->prepare('INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)');
            $stmt->execute([$email, $token, date('Y-m-d H:i:s', time() + 3600)]);
            $resetUrl = url('customer/forgot-password.php?reset=' . $token . '&email=' . urlencode($email));
            flash('success', 'Password reset link generated. In this demo the link is shown below:');
            flash('success', 'Reset link: ' . $resetUrl);
            redirect('customer/forgot-password.php');
        }
    }
}

// Handle reset with token
$token = trim($_GET['reset'] ?? '');
$resetEmail = trim($_GET['email'] ?? '');
if ($token !== '' && $resetEmail !== '' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = db()->prepare('SELECT * FROM password_resets WHERE token = ? AND email = ? AND expires_at > NOW()');
    $stmt->execute([$token, $resetEmail]);
    $resetRow = $stmt->fetch();
    if (!$resetRow) {
        $errors[] = 'This reset link is invalid or has expired.';
        $token = '';
    }
}

if ($token !== '' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $newPass = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if (strlen($newPass) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    } elseif ($newPass !== $confirm) {
        $errors[] = 'Passwords do not match.';
    } else {
        $stmt = db()->prepare('SELECT * FROM password_resets WHERE token = ? AND email = ? AND expires_at > NOW()');
        $stmt->execute([$token, $resetEmail]);
        $resetRow = $stmt->fetch();
        if ($resetRow) {
            db()->prepare('UPDATE customers SET password = ? WHERE email = ?')
                ->execute([password_hash($newPass, PASSWORD_DEFAULT), $resetEmail]);
            db()->prepare('DELETE FROM password_resets WHERE email = ?')->execute([$resetEmail]);
            flash('success', 'Password updated successfully. Please sign in with your new password.');
            redirect('customer/login.php');
        }
        $errors[] = 'This reset link is invalid or has expired.';
    }
}

$pageTitle = 'Forgot Password - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?></title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
<link rel="icon" type="image/svg+xml" href="<?= url('assets/icons/favicon.svg') ?>">
</head>
<body>

<div class="auth-wrap">
  <div class="auth-card">
    <a href="<?= url('index.php') ?>" class="brand">
      <span class="brand-mark">R</span>
      <span class="brand-text"><?= e(APP_NAME) ?></span>
    </a>
    <h1><?= $token !== '' ? 'Set New Password' : 'Forgot Password' ?></h1>
    <p class="auth-sub"><?= $token !== '' ? 'Choose a new password for your account.' : 'Enter your email and we will help you reset your password.' ?></p>

    <?php flash_render(); ?>
    <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

    <?php if ($token !== ''): ?>
      <form method="post" action="<?= url('customer/forgot-password.php?reset=' . urlencode($token) . '&email=' . urlencode($resetEmail)) ?>">
        <?= csrf_field() ?>
        <div class="form-group">
          <label class="form-label" for="password">New Password</label>
          <input type="password" id="password" name="password" class="form-control" minlength="8" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="confirm">Confirm New Password</label>
          <input type="password" id="confirm" name="confirm" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block"><?= icon('check') ?> Update Password</button>
      </form>
    <?php else: ?>
      <form method="post" action="<?= url('customer/forgot-password.php') ?>">
        <?= csrf_field() ?>
        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input type="email" id="email" name="email" class="form-control" value="<?= e($email) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block"><?= icon('mail') ?> Send Reset Link</button>
      </form>
    <?php endif; ?>

    <div class="auth-alt">
      <a href="<?= url('customer/login.php') ?>">&larr; Back to login</a>
    </div>
  </div>
</div>

</body>
</html>
