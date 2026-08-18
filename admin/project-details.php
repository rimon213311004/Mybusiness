<?php
declare(strict_types=1);
$pageTitle = 'Project Details';
$activeNav = 'projects';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT p.*, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone FROM projects p LEFT JOIN customers c ON c.id = p.customer_id WHERE p.id = ?');
$stmt->execute([$id]);
$project = $stmt->fetch();
if (!$project) {
    flash('error', 'Project not found.');
    redirect('admin/projects.php');
}

$invoices = db()->prepare('SELECT * FROM invoices WHERE project_id = ? ORDER BY created_at DESC');
$invoices->execute([$id]);
$projectInvoices = $invoices->fetchAll();

$docs = db()->prepare('SELECT * FROM documents WHERE project_id = ? ORDER BY created_at DESC');
$docs->execute([$id]);
$projectDocs = $docs->fetchAll();

$tickets = db()->prepare('SELECT * FROM support_tickets WHERE customer_id = ? ORDER BY created_at DESC LIMIT 5');
$tickets->execute([$project['customer_id']]);
$projectTickets = $tickets->fetchAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="panel">
  <div class="panel-head">
    <h3><?= e($project['title']) ?></h3>
    <?= status_badge($project['status']) ?>
  </div>
  <div class="panel-body">
    <div class="detail-grid" style="margin-bottom:22px;">
      <div class="detail-item"><span>Customer</span><b><?= e($project['customer_name'] ?? '—') ?></b></div>
      <div class="detail-item"><span>Email</span><b><?= e($project['customer_email'] ?? '—') ?></b></div>
      <div class="detail-item"><span>Phone</span><b><?= e($project['customer_phone'] ?? '—') ?></b></div>
      <div class="detail-item"><span>Progress</span><b><?= (int)$project['progress'] ?>%</b></div>
      <div class="detail-item"><span>Start Date</span><b><?= $project['start_date'] ? date('d M Y', strtotime($project['start_date'])) : '—' ?></b></div>
      <div class="detail-item"><span>Due Date</span><b><?= $project['due_date'] ? date('d M Y', strtotime($project['due_date'])) : '—' ?></b></div>
    </div>
    <h3 style="margin-bottom:8px;">Description</h3>
    <p><?= nl2br(e((string)$project['description'])) ?></p>
    <div style="margin-top:18px;">
      <a href="<?= url('admin/projects.php?edit=' . $project['id'] . '#add') ?>" class="btn btn-sm btn-primary"><?= icon('edit') ?> Edit Project</a>
      <a href="<?= url('admin/projects.php') ?>" class="btn btn-sm btn-outline">&larr; Back</a>
    </div>
  </div>
</div>

<div class="grid-2" style="align-items:start;">
  <div class="panel">
    <div class="panel-head"><h3>Invoices</h3><a class="btn btn-sm btn-ghost" href="<?= url('admin/invoices.php') ?>">Manage</a></div>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>No</th><th>Amount</th><th>Status</th><th>Due</th></tr></thead>
        <tbody>
          <?php foreach ($projectInvoices as $inv): ?>
            <tr><td><?= e($inv['invoice_no']) ?></td><td><?= money($inv['amount']) ?></td><td><?= status_badge($inv['status']) ?></td><td><?= $inv['due_date'] ? date('d M Y', strtotime($inv['due_date'])) : '—' ?></td></tr>
          <?php endforeach; ?>
          <?php if (!$projectInvoices): ?><tr><td colspan="4" style="color:var(--text-3);">No invoices.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="panel">
    <div class="panel-head"><h3>Documents</h3><a class="btn btn-sm btn-ghost" href="<?= url('admin/documents.php') ?>">Manage</a></div>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>File</th><th>Uploaded By</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach ($projectDocs as $d): ?>
            <tr><td><a href="<?= url($d['file_path']) ?>"><?= e($d['file_name']) ?></a></td><td><?= e($d['uploaded_by']) ?></td><td><?= time_ago($d['created_at']) ?></td></tr>
          <?php endforeach; ?>
          <?php if (!$projectDocs): ?><tr><td colspan="3" style="color:var(--text-3);">No documents.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
