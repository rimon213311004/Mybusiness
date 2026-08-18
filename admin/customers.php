<?php
declare(strict_types=1);
$pageTitle = 'Customers';
$activeNav = 'customers';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'toggle') {
        db()->prepare('UPDATE customers SET status = IF(status = "active", "inactive", "active") WHERE id = ?')->execute([$id]);
        flash('success', 'Customer status updated.');
        redirect('admin/customers.php');
    } elseif ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $password = $_POST['password'] ?? '';

        if (strlen($name) < 2) $errors[] = 'Enter a valid name.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email.';
        if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
        $chk = db()->prepare('SELECT id FROM customers WHERE email = ?');
        $chk->execute([$email]);
        if ($chk->fetch()) $errors[] = 'A customer with this email already exists.';

        if (!$errors) {
            db()->prepare('INSERT INTO customers (name, email, phone, company, password) VALUES (?, ?, ?, ?, ?)')
                ->execute([$name, $email, $phone ?: null, $company ?: null, password_hash($password, PASSWORD_DEFAULT)]);
            flash('success', 'Customer added successfully.');
            redirect('admin/customers.php');
        }
    }
}

$q = trim($_GET['q'] ?? '');
$sql = 'SELECT c.*, (SELECT COUNT(*) FROM projects p WHERE p.customer_id = c.id) AS project_count FROM customers c';
$params = [];
if ($q !== '') {
    $sql .= ' WHERE c.name LIKE ? OR c.email LIKE ? OR c.phone LIKE ? OR c.company LIKE ?';
    $like = '%' . $q . '%';
    $params = [$like, $like, $like, $like];
}
$sql .= ' ORDER BY c.created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$customers = $stmt->fetchAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

<form method="get" action="<?= url('admin/customers.php') ?>" style="margin-bottom:20px;">
  <div style="display:flex;gap:10px;">
    <input type="text" name="q" class="form-control" value="<?= e($q) ?>" placeholder="Search customers..." style="max-width:320px;">
    <button type="submit" class="btn btn-primary btn-sm"><?= icon('search') ?> Search</button>
  </div>
</form>

<div class="panel">
  <div class="panel-head"><h3>All Customers (<?= count($customers) ?>)</h3></div>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Customer</th><th>Contact</th><th>Projects</th><th>Joined</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($customers as $c): ?>
          <tr>
            <td><b><?= e($c['name']) ?></b><br><span style="color:var(--text-3);font-size:0.8rem;"><?= e($c['company'] ?: '') ?></span></td>
            <td><?= e($c['email']) ?><br><span style="color:var(--text-3);font-size:0.8rem;"><?= e($c['phone'] ?: '') ?></span></td>
            <td><?= (int)$c['project_count'] ?></td>
            <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
            <td><?= status_badge($c['status']) ?></td>
            <td>
              <div class="actions">
                <a class="btn btn-sm btn-outline" href="<?= url('admin/customer-details.php?id=' . $c['id']) ?>"><?= icon('eye') ?> View</a>
                <form method="post" action="<?= url('admin/customers.php') ?>" style="margin:0;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="toggle">
                  <input type="hidden" name="id" value="<?= $c['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-ghost"><?= $c['status'] === 'active' ? 'Deactivate' : 'Activate' ?></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$customers): ?><tr><td colspan="6" style="text-align:center;color:var(--text-3);padding:30px;">No customers found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Add Customer</h3></div>
  <div class="panel-body">
    <form method="post" action="<?= url('admin/customers.php') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="add">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label">Phone</label>
          <input type="text" name="phone" class="form-control">
        </div>
        <div class="form-group">
          <label class="form-label">Company</label>
          <input type="text" name="company" class="form-control">
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" minlength="8" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary"><?= icon('plus') ?> Add Customer</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
