<?php
declare(strict_types=1);
$pageTitle = 'Invoices';
$activeNav = 'invoices';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        if (in_array($status, ['unpaid', 'paid', 'overdue', 'cancelled'], true)) {
            db()->prepare('UPDATE invoices SET status = ? WHERE id = ?')->execute([$status, $id]);
            $inv = db()->prepare('SELECT * FROM invoices WHERE id = ?');
            $inv->execute([$id]);
            $row = $inv->fetch();
            if ($row) {
                notify((int)$row['customer_id'], 'customer', 'Invoice updated', 'Your invoice ' . $row['invoice_no'] . ' is now ' . $status . '.', 'customer/invoices.php');
            }
            flash('success', 'Invoice status updated.');
        }
        redirect('admin/invoices.php');
    } elseif ($action === 'delete') {
        db()->prepare('DELETE FROM invoices WHERE id = ?')->execute([(int)$_POST['id']]);
        flash('success', 'Invoice deleted.');
        redirect('admin/invoices.php');
    } elseif ($action === 'add') {
        $customerId = (int)($_POST['customer_id'] ?? 0);
        $projectId = (int)($_POST['project_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);
        $dueDate = trim($_POST['due_date'] ?? '');
        $status = $_POST['status'] ?? 'unpaid';

        if (!$customerId) $errors[] = 'Select a customer.';
        if ($amount <= 0) $errors[] = 'Enter a valid amount.';

        if (!$errors) {
            $num = 'INV-' . date('Y') . '-' . str_pad((string)(db()->query('SELECT COUNT(*) FROM invoices')->fetchColumn() + 1), 3, '0', STR_PAD_LEFT);
            db()->prepare('INSERT INTO invoices (customer_id, project_id, invoice_no, amount, status, issue_date, due_date) VALUES (?, ?, ?, ?, ?, CURDATE(), ?)')
                ->execute([$customerId, $projectId ?: null, $num, $amount, $status, $dueDate ?: null]);
            notify($customerId, 'customer', 'New invoice issued', 'Invoice ' . $num . ' for ' . money($amount) . ' has been issued.', 'customer/invoices.php');
            flash('success', 'Invoice ' . $num . ' created.');
            redirect('admin/invoices.php');
        }
    }
}

$customers = db()->query('SELECT id, name FROM customers ORDER BY name')->fetchAll();
$projects = db()->query('SELECT id, title FROM projects ORDER BY title')->fetchAll();
$invoices = db()->query('SELECT i.*, c.name AS customer_name, p.title AS project_title FROM invoices i LEFT JOIN customers c ON c.id = i.customer_id LEFT JOIN projects p ON p.id = i.project_id ORDER BY i.created_at DESC')->fetchAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="panel">
  <div class="panel-head"><h3>Invoices (<?= count($invoices) ?>)</h3><a class="btn btn-sm btn-primary" href="<?= url('admin/invoices.php#add') ?>"><?= icon('plus') ?> New Invoice</a></div>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Invoice No</th><th>Customer</th><th>Project</th><th>Amount</th><th>Status</th><th>Due</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($invoices as $inv): ?>
          <tr>
            <td><b><?= e($inv['invoice_no']) ?></b></td>
            <td><?= e($inv['customer_name'] ?? '—') ?></td>
            <td><?= e($inv['project_title'] ?? '—') ?></td>
            <td><b><?= money($inv['amount']) ?></b></td>
            <td><?= status_badge($inv['status']) ?></td>
            <td><?= $inv['due_date'] ? date('d M Y', strtotime($inv['due_date'])) : '—' ?></td>
            <td>
              <div class="actions">
                <form method="post" action="<?= url('admin/invoices.php') ?>" style="display:flex;gap:6px;align-items:center;margin:0;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="id" value="<?= $inv['id'] ?>">
                  <select name="status" class="form-control" style="width:auto;padding:6px 10px;font-size:0.82rem;" onchange="this.form.submit()">
                    <?php foreach (['unpaid', 'paid', 'overdue', 'cancelled'] as $st): ?>
                      <option value="<?= $st ?>" <?= $inv['status'] === $st ? 'selected' : '' ?>><?= e(ucfirst($st)) ?></option>
                    <?php endforeach; ?>
                  </select>
                </form>
                <form method="post" action="<?= url('admin/invoices.php') ?>" style="margin:0;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $inv['id'] ?>">
                  <button type="submit" class="icon-btn danger" title="Delete" onclick="return confirm('Delete this invoice?');"><?= icon('trash') ?></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="panel" id="add">
  <div class="panel-head"><h3>Create Invoice</h3></div>
  <div class="panel-body">
    <form method="post" action="<?= url('admin/invoices.php#add') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="add">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Customer</label>
          <select name="customer_id" class="form-control" required>
            <option value="">Select customer...</option>
            <?php foreach ($customers as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Project (optional)</label>
          <select name="project_id" class="form-control">
            <option value="0">— None —</option>
            <?php foreach ($projects as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['title']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Amount (BDT)</label>
          <input type="number" name="amount" class="form-control" min="1" required>
        </div>
        <div class="form-group">
          <label class="form-label">Due Date</label>
          <input type="date" name="due_date" class="form-control">
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" class="form-control">
            <?php foreach (['unpaid', 'paid', 'overdue', 'cancelled'] as $st): ?><option value="<?= $st ?>"><?= e(ucfirst($st)) ?></option><?php endforeach; ?>
          </select>
        </div>
      </div>
      <button type="submit" class="btn btn-primary"><?= icon('plus') ?> Create Invoice</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
