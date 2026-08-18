<?php
declare(strict_types=1);
$pageTitle = 'Invoices';
$activeNav = 'invoices';
require_once __DIR__ . '/../includes/functions.php';
$customer = require_customer();

$stmt = db()->prepare('SELECT * FROM invoices WHERE customer_id = ? ORDER BY created_at DESC');
$stmt->execute([$customer['id']]);
$invoices = $stmt->fetchAll();

include __DIR__ . '/../includes/customer-header.php';
?>

<div class="panel">
  <div class="panel-head">
    <h3>All Invoices</h3>
    <span style="color:var(--text-3);font-size:0.85rem;"><?= count($invoices) ?> invoices</span>
  </div>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Invoice No</th><th>Project</th><th>Amount</th><th>Status</th><th>Issue Date</th><th>Due Date</th></tr></thead>
      <tbody>
        <?php foreach ($invoices as $inv): ?>
          <?php $p = null;
            if ($inv['project_id']) {
                $ps = db()->prepare('SELECT title FROM projects WHERE id = ?');
                $ps->execute([$inv['project_id']]);
                $p = $ps->fetch();
            } ?>
          <tr>
            <td><b><?= e($inv['invoice_no']) ?></b></td>
            <td><?= e($p['title'] ?? '—') ?></td>
            <td><?= money($inv['amount']) ?></td>
            <td><?= status_badge($inv['status']) ?></td>
            <td><?= date('d M Y', strtotime($inv['issue_date'])) ?></td>
            <td><?= $inv['due_date'] ? date('d M Y', strtotime($inv['due_date'])) : '—' ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$invoices): ?><tr><td colspan="6" style="text-align:center;color:var(--text-3);padding:30px;">No invoices yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/customer-footer.php'; ?>
