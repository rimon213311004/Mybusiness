<?php
declare(strict_types=1);
$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$counts = [
    'customers' => (int)db()->query('SELECT COUNT(*) FROM customers')->fetchColumn(),
    'requests' => (int)db()->query('SELECT COUNT(*) FROM project_requests WHERE status = "new"')->fetchColumn(),
    'projects' => (int)db()->query('SELECT COUNT(*) FROM projects')->fetchColumn(),
    'messages' => (int)db()->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn(),
    'downloads' => (int)db()->query('SELECT IFNULL(SUM(download_count),0) FROM downloads')->fetchColumn(),
    'unpaid' => (float)db()->query('SELECT IFNULL(SUM(amount),0) FROM invoices WHERE status IN ("unpaid","overdue")')->fetchColumn(),
    'paid' => (float)db()->query('SELECT IFNULL(SUM(amount),0) FROM payments WHERE status = "completed"')->fetchColumn(),
    'revenue' => (float)db()->query('SELECT IFNULL(SUM(amount),0) FROM invoices WHERE status = "paid"')->fetchColumn(),
];

$recentRequests = db()->query('SELECT * FROM project_requests ORDER BY created_at DESC LIMIT 5')->fetchAll();
$recentCustomers = db()->query('SELECT * FROM customers ORDER BY created_at DESC LIMIT 5')->fetchAll();
$recentDownloads = db()->query('SELECT d.*, p.title AS project_title FROM downloads d JOIN portfolio_items p ON p.id = d.project_id ORDER BY d.download_count DESC LIMIT 5')->fetchAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="stat-grid">
  <div class="stat-card">
    <span class="stat-ico indigo"><?= icon('users') ?></span>
    <div><b><?= $counts['customers'] ?></b><span>Total Customers</span></div>
  </div>
  <div class="stat-card">
    <span class="stat-ico cyan"><?= icon('inbox') ?></span>
    <div><b><?= $counts['requests'] ?></b><span>New Requests</span></div>
  </div>
  <div class="stat-card">
    <span class="stat-ico green"><?= icon('clipboard') ?></span>
    <div><b><?= $counts['projects'] ?></b><span>Projects</span></div>
  </div>
  <div class="stat-card">
    <span class="stat-ico orange"><?= icon('download') ?></span>
    <div><b><?= $counts['downloads'] ?></b><span>Total Downloads</span></div>
  </div>
  <div class="stat-card">
    <span class="stat-ico green"><?= icon('wallet') ?></span>
    <div><b><?= money($counts['revenue']) ?></b><span>Revenue (paid)</span></div>
  </div>
  <div class="stat-card">
    <span class="stat-ico red"><?= icon('file') ?></span>
    <div><b><?= money($counts['unpaid']) ?></b><span>Unpaid Invoices</span></div>
  </div>
  <div class="stat-card">
    <span class="stat-ico indigo"><?= icon('mail') ?></span>
    <div><b><?= $counts['messages'] ?></b><span>Unread Messages</span></div>
  </div>
  <div class="stat-card">
    <span class="stat-ico cyan"><?= icon('chart') ?></span>
    <div><b><?= money($counts['paid']) ?></b><span>Payments Collected</span></div>
  </div>
</div>

<div class="grid-2" style="align-items:start;">
  <div class="panel">
    <div class="panel-head"><h3>Recent Project Requests</h3><a href="<?= url('admin/project-requests.php') ?>" class="btn btn-sm btn-ghost">View All</a></div>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>Client</th><th>Service</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
          <?php foreach ($recentRequests as $r): ?>
            <tr>
              <td><b><?= e($r['name']) ?></b><br><span style="color:var(--text-3);font-size:0.8rem;"><?= e($r['email']) ?></span></td>
              <td><?= e($r['service_type']) ?></td>
              <td><?= status_badge($r['status']) ?></td>
              <td><?= time_ago($r['created_at']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>Popular Downloads</h3><a href="<?= url('admin/downloads.php') ?>" class="btn btn-sm btn-ghost">Manage</a></div>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>Project</th><th>File</th><th>Downloads</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach ($recentDownloads as $d): ?>
            <tr>
              <td><b><?= e($d['project_title']) ?></b></td>
              <td><?= e($d['file_name']) ?></td>
              <td><b><?= (int)$d['download_count'] ?></b></td>
              <td><?= (int)$d['download_enabled'] === 1 ? '<span class="badge badge-green">Public</span>' : '<span class="badge badge-gray">Hidden</span>' ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="panel">
  <div class="panel-head"><h3>Recent Customers</h3><a href="<?= url('admin/customers.php') ?>" class="btn btn-sm btn-ghost">View All</a></div>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Company</th><th>Joined</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($recentCustomers as $c): ?>
          <tr>
            <td><b><?= e($c['name']) ?></b></td>
            <td><?= e($c['email']) ?></td>
            <td><?= e($c['phone'] ?: '—') ?></td>
            <td><?= e($c['company'] ?: '—') ?></td>
            <td><?= date('d M Y', strtotime($c['created_at'])) ?></td>
            <td><?= status_badge($c['status']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
