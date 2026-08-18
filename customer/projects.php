<?php
declare(strict_types=1);
$pageTitle = 'My Projects';
$activeNav = 'projects';
require_once __DIR__ . '/../includes/functions.php';
$customer = require_customer();

$status = $_GET['status'] ?? '';
$sql = 'SELECT * FROM projects WHERE customer_id = ?';
$params = [$customer['id']];
if ($status !== '' && in_array($status, ['planning', 'in_progress', 'review', 'completed', 'delivered'], true)) {
    $sql .= ' AND status = ?';
    $params[] = $status;
}
$sql .= ' ORDER BY created_at DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

include __DIR__ . '/../includes/customer-header.php';
?>

<div class="tabs">
  <a href="<?= url('customer/projects.php') ?>" class="tab <?= $status === '' ? 'active' : '' ?>">All</a>
  <?php foreach (['planning', 'in_progress', 'review', 'completed', 'delivered'] as $st): ?>
    <a href="<?= url('customer/projects.php?status=' . $st) ?>" class="tab <?= $status === $st ? 'active' : '' ?>"><?= e(ucfirst(str_replace('_', ' ', $st))) ?></a>
  <?php endforeach; ?>
</div>

<div class="panel">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Project</th><th>Status</th><th>Progress</th><th>Due Date</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($projects as $p): ?>
          <tr>
            <td><b><?= e($p['title']) ?></b><br><span style="color:var(--text-3);font-size:0.82rem;">Started <?= date('d M Y', strtotime($p['created_at'])) ?></span></td>
            <td><?= status_badge($p['status']) ?></td>
            <td>
              <div class="progress"><div class="progress-bar" style="width:<?= (int)$p['progress'] ?>%"></div></div>
              <span style="font-size:0.78rem;color:var(--text-3);"><?= (int)$p['progress'] ?>%</span>
            </td>
            <td><?= $p['due_date'] ? date('d M Y', strtotime($p['due_date'])) : '—' ?></td>
            <td><a class="btn btn-sm btn-outline" href="<?= url('customer/project-details.php?id=' . $p['id']) ?>">Details</a></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$projects): ?><tr><td colspan="5" style="text-align:center;color:var(--text-3);padding:30px;">No projects found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/customer-footer.php'; ?>
