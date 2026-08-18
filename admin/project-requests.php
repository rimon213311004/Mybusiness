<?php
declare(strict_types=1);
$pageTitle = 'Project Requests';
$activeNav = 'project-requests';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'update') {
        $status = $_POST['status'] ?? '';
        if (in_array($status, ['new', 'contacted', 'in_progress', 'completed', 'closed'], true)) {
            db()->prepare('UPDATE project_requests SET status = ? WHERE id = ?')->execute([$status, $id]);
            flash('success', 'Request status updated.');
        }
        redirect('admin/project-requests.php');
    } elseif ($action === 'delete') {
        db()->prepare('DELETE FROM project_requests WHERE id = ?')->execute([$id]);
        flash('success', 'Request deleted.');
        redirect('admin/project-requests.php');
    }
}

$status = $_GET['status'] ?? '';
$sql = 'SELECT r.*, c.name AS customer_name FROM project_requests r LEFT JOIN customers c ON c.id = r.customer_id';
$params = [];
if ($status !== '' && in_array($status, ['new', 'contacted', 'in_progress', 'completed', 'closed'], true)) {
    $sql .= ' WHERE r.status = ?';
    $params[] = $status;
}
$sql .= ' ORDER BY r.created_at DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="tabs">
  <a href="<?= url('admin/project-requests.php') ?>" class="tab <?= $status === '' ? 'active' : '' ?>">All</a>
  <?php foreach (['new', 'contacted', 'in_progress', 'completed', 'closed'] as $st): ?>
    <a href="<?= url('admin/project-requests.php?status=' . $st) ?>" class="tab <?= $status === $st ? 'active' : '' ?>"><?= e(ucfirst($st)) ?></a>
  <?php endforeach; ?>
</div>

<div class="panel">
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Client</th><th>Service</th><th>Budget</th><th>Message</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($requests as $r): ?>
          <tr>
            <td><b><?= e($r['name']) ?></b><br>
              <span style="color:var(--text-3);font-size:0.8rem;"><?= e($r['email']) ?><br><?= e($r['phone'] ?: '') ?></span>
              <?php if ($r['customer_name']): ?><div><span class="badge badge-blue">Customer</span></div><?php endif; ?>
            </td>
            <td><?= e($r['service_type']) ?></td>
            <td><?= e($r['budget'] ?: '—') ?></td>
            <td style="max-width:260px;"><span style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= e($r['message']) ?>"><?= e($r['message']) ?></span></td>
            <td><?= status_badge($r['status']) ?></td>
            <td>
              <form method="post" action="<?= url('admin/project-requests.php') ?>" style="display:flex;gap:6px;align-items:center;">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                <select name="status" class="form-control" style="width:auto;padding:6px 10px;font-size:0.82rem;" onchange="this.form.submit()">
                  <?php foreach (['new', 'contacted', 'in_progress', 'completed', 'closed'] as $st): ?>
                    <option value="<?= $st ?>" <?= $r['status'] === $st ? 'selected' : '' ?>><?= e(ucfirst($st)) ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$requests): ?><tr><td colspan="6" style="text-align:center;color:var(--text-3);padding:30px;">No requests found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
