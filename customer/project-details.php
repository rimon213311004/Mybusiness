<?php
declare(strict_types=1);
$pageTitle = 'Project Details';
$activeNav = 'projects';
require_once __DIR__ . '/../includes/functions.php';
$customer = require_customer();

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM projects WHERE id = ? AND customer_id = ?');
$stmt->execute([$id, $customer['id']]);
$project = $stmt->fetch();
if (!$project) {
    flash('error', 'Project not found.');
    redirect('customer/projects.php');
}

$invoices = db()->prepare('SELECT * FROM invoices WHERE customer_id = ? AND project_id = ? ORDER BY created_at DESC');
$invoices->execute([$customer['id'], $project['id']]);
$projectInvoices = $invoices->fetchAll();

$docs = db()->prepare('SELECT * FROM documents WHERE customer_id = ? AND project_id = ? ORDER BY created_at DESC');
$docs->execute([$customer['id'], $project['id']]);
$projectDocs = $docs->fetchAll();

include __DIR__ . '/../includes/customer-header.php';
?>

<div class="panel">
  <div class="panel-head">
    <h3><?= e($project['title']) ?></h3>
    <?= status_badge($project['status']) ?>
  </div>
  <div class="panel-body">
    <div class="detail-grid" style="margin-bottom:22px;">
      <div class="detail-item"><span>Status</span><b><?= e(ucfirst(str_replace('_', ' ', $project['status']))) ?></b></div>
      <div class="detail-item"><span>Progress</span><b><?= (int)$project['progress'] ?>%</b></div>
      <div class="detail-item"><span>Start Date</span><b><?= $project['start_date'] ? date('d M Y', strtotime($project['start_date'])) : '—' ?></b></div>
      <div class="detail-item"><span>Due Date</span><b><?= $project['due_date'] ? date('d M Y', strtotime($project['due_date'])) : '—' ?></b></div>
    </div>

    <div class="progress" style="height:12px;margin-bottom:22px;">
      <div class="progress-bar" style="width:<?= (int)$project['progress'] ?>%"></div>
    </div>

    <h3 style="margin-bottom:8px;">Description</h3>
    <p style="margin-bottom:22px;"><?= nl2br(e((string)$project['description'])) ?></p>

    <div class="grid-2" style="align-items:start;">
      <div class="panel" style="margin-bottom:0;">
        <div class="panel-head"><h3>Invoices</h3></div>
        <div class="table-wrap">
          <table class="table">
            <thead><tr><th>Invoice No</th><th>Amount</th><th>Status</th><th>Due</th></tr></thead>
            <tbody>
              <?php foreach ($projectInvoices as $inv): ?>
                <tr>
                  <td><?= e($inv['invoice_no']) ?></td>
                  <td><?= money($inv['amount']) ?></td>
                  <td><?= status_badge($inv['status']) ?></td>
                  <td><?= $inv['due_date'] ? date('d M Y', strtotime($inv['due_date'])) : '—' ?></td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$projectInvoices): ?><tr><td colspan="4" style="color:var(--text-3);">No invoices.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="panel" style="margin-bottom:0;">
        <div class="panel-head"><h3>Documents</h3></div>
        <div class="table-wrap">
          <table class="table">
            <thead><tr><th>File</th><th>Uploaded</th></tr></thead>
            <tbody>
              <?php foreach ($projectDocs as $d): ?>
                <tr>
                  <td><a href="<?= url($d['file_path']) ?>"><?= e($d['file_name']) ?></a></td>
                  <td><?= time_ago($d['created_at']) ?></td>
                </tr>
              <?php endforeach; ?>
              <?php if (!$projectDocs): ?><tr><td colspan="2" style="color:var(--text-3);">No documents.</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div style="display:flex;gap:10px;flex-wrap:wrap;">
  <a href="<?= url('customer/projects.php') ?>" class="btn btn-outline">&larr; Back to Projects</a>
  <a href="<?= url('customer/messages.php') ?>" class="btn btn-primary"><?= icon('mail') ?> Message Admin</a>
</div>

<?php include __DIR__ . '/../includes/customer-footer.php'; ?>
