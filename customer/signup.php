<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

if (current_customer()) {
    redirect('customer/dashboard.php');
}

$errors = [];
$form = ['name' => '', 'email' => '', 'phone' => '', 'company' => '', 'address' => '', 'password' => '', 'confirm' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($form as $k => $v) {
        $form[$k] = trim((string)($_POST[$k] ?? ''));
    }

    if (strlen($form['name']) < 2) $errors[] = 'Please enter your full name.';
    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (strlen($form['password']) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($form['password'] !== $form['confirm']) $errors[] = 'Passwords do not match.';

    $stmt = db()->prepare('SELECT id FROM customers WHERE email = ?');
    $stmt->execute([$form['email']]);
    if ($stmt->fetch()) $errors[] = 'An account with this email already exists.';

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO customers (name, email, phone, company, address, password) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $form['name'], $form['email'], $form['phone'] ?: null, $form['company'] ?: null,
            $form['address'] ?: null, password_hash($form['password'], PASSWORD_DEFAULT),
        ]);
        notify(1, 'admin', 'New customer registered', $form['name'] . ' (' . $form['email'] . ') just signed up.', 'admin/customers.php');
        $id = (int)db()->lastInsertId();
        session_regenerate_id(true);
        $_SESSION['customer_id'] = $id;
        flash('success', 'Account created successfully. Welcome to ' . APP_NAME . '!');
        redirect('customer/dashboard.php');
    }
    set_old($form);
}

$pageTitle = 'Customer Signup - ' . APP_NAME;
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
  <div class="auth-card" style="max-width:520px;">
    <a href="<?= url('index.php') ?>" class="brand">
      <span class="brand-mark">R</span>
      <span class="brand-text"><?= e(APP_NAME) ?></span>
    </a>
    <h1>Create Your Account</h1>
    <p class="auth-sub">Track your projects, invoices and support requests.</p>

    <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

    <form method="post" action="<?= url('customer/signup.php') ?>">
      <?= csrf_field() ?>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label" for="name">Full Name *</label>
          <input type="text" id="name" name="name" class="form-control" value="<?= old('name', $form['name']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="email">Email Address *</label>
          <input type="email" id="email" name="email" class="form-control" value="<?= old('email', $form['email']) ?>" required>
        </div>
      </div>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label" for="phone">Phone</label>
          <input type="text" id="phone" name="phone" class="form-control" value="<?= old('phone', $form['phone']) ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="company">Company</label>
          <input type="text" id="company" name="company" class="form-control" value="<?= old('company', $form['company']) ?>">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="address">Address</label>
        <input type="text" id="address" name="address" class="form-control" value="<?= old('address', $form['address']) ?>">
      </div>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label" for="password">Password *</label>
          <input type="password" id="password" name="password" class="form-control" minlength="8" required>
          <div class="form-hint">Minimum 8 characters.</div>
        </div>
        <div class="form-group">
          <label class="form-label" for="confirm">Confirm Password *</label>
          <input type="password" id="confirm" name="confirm" class="form-control" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-block"><?= icon('user') ?> Create Account</button>
    </form>

    <div class="auth-alt">
      Already have an account? <a href="<?= url('customer/login.php') ?>">Sign in</a>
    </div>
  </div>
</div>

</body>
</html>
