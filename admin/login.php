<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

if (current_admin()) {
    redirect('admin/dashboard.php');
}

$email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Please enter your email and password.';
    } else {
        $stmt = db()->prepare('SELECT * FROM admins WHERE email = ?');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$admin['id'];
            flash('success', 'Welcome back, ' . $admin['name'] . '!');
            redirect('admin/dashboard.php');
        } else {
            $errors[] = 'Invalid credentials.';
        }
    }
}

$pageTitle = 'Admin Login - ' . APP_NAME;
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

<div class="auth-wrap" style="background:var(--dark);">
  <div class="auth-card">
    <a href="<?= url('index.php') ?>" class="brand">
      <span class="brand-mark">R</span>
      <span class="brand-text"><?= e(APP_NAME) ?></span>
    </a>
    <h1>Admin Login</h1>
    <p class="auth-sub">Restricted area — authorized personnel only.</p>

    <?php flash_render(); ?>
    <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

    <form method="post" action="<?= url('admin/login.php') ?>">
      <?= csrf_field() ?>
      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" value="<?= e($email) ?>" required autofocus>
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block"><?= icon('arrow') ?> Sign In</button>
    </form>

    <div class="auth-alt">
      <a href="<?= url('index.php') ?>">&larr; Back to website</a>
    </div>
  </div>
</div>

</body>
</html>
