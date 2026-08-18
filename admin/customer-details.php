<?php
declare(strict_types=1);
$pageTitle = 'Customer Details';
$activeNav = 'customers';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM customers WHERE id = ?');
$stmt->execute([$id]);
$customer = $stmt->fetch();
if (!$customer) {
    flash('error', 'Customer not found.');
    redirect('admin/customers.php');
}

$projects = db()->prepare('SELECT * FROM projects WHERE customer_id = ? ORDER BY created_at DESC');
$projects->execute([$id]);
$customerProjects = $projects->fetchAll();

$invoices = db()->prepare('SELECT * FROM invoices WHERE customer_id = ? ORDER BY created_at DESC');
$invoices->execute([$id]);
$customerInvoices = $invoices->fetchAll();

$totalPaid = db()->prepare('SELECT IFNULL(SUM(amount),0) FROM payments WHERE customer_id = ? AND status = "completed"');
$totalPaid->execute([$id]);
$totalPaidValue = (float)$totalPaid->fetchColumn();

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="panel">
  <div class="panel-head">
    <h3><?= e($customer['name']) ?></h3>
    <?= status_badge($customer['status']) ?>
  </div>
  <div class="panel-body">
    <div class="detail-grid" style="margin-bottom:22px;">
      <div class="detail-item"><span>Email</span><b><?= e($customer['email']) ?></b></div>
      <div class="detail-item"><span>Phone</span><b><?= e($customer['phone'] ?: '—') ?></b></div>
      <div class="detail-item"><span>Company</span><b><?= e($customer['company'] ?: '—') ?></b></div>
      <div class="detail-item"><span>Address</span><b><?= e($customer['address'] ?: '—') ?></b></div>
      <div class="detail-item"><span>Member Since</span><b><?= date('d M Y', strtotime($customer['created_at'])) ?></b></div>
      <div class="detail-item"><span>Total Paid</span><b><?= money($totalPaidValue) ?></b></div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <a href="<?= url('admin/customers.php') ?>" class="btn btn-sm btn-outline">&larr; Back</a>
      <a href="<?= url('admin/messages.php?customer=' . $customer['id']) ?>" class="btn btn-sm btn-primary"><?= icon('mail') ?> Message Customer</a>
    </div>
  </div>
</div>

<div class="grid-2" style="align-items:start;">
  <div class="panel">
    <div class="panel-head"><h3>Projects</h3></div>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>Title</th><th>Status</th><th>Progress</th></tr></thead>
        <tbody>
          <?php foreach ($customerProjects as $p): ?>
            <tr>
              <td><a href="<?= url('admin/project-details.php?id=' . $p['id']) ?>"><?= e($p['title']) ?></a></td>
              <td><?= status_badge($p['status']) ?></td>
              <td><?= (int)$p['progress'] ?>%</td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$customerProjects): ?><tr><td colspan="3" style="color:var(--text-3);">No projects.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="panel">
    <div class="panel-head"><h3>Invoices & Payments</h3></div>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>Invoice</th><th>Amount</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach ($customerInvoices as $inv): ?>
            <tr>
              <td><?= e($inv['invoice_no']) ?></td>
              <td><?= money($inv['amount']) ?></td>
              <td><?= status_badge($inv['status']) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$customerInvoices): ?><tr><td colspan="3" style="color:var(--text-3);">No invoices.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
