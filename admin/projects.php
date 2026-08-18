<?php
declare(strict_types=1);
$pageTitle = 'Projects';
$activeNav = 'projects';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$errors = [];
$form = ['title' => '', 'customer_id' => '', 'description' => '', 'status' => 'planning', 'progress' => 0, 'start_date' => '', 'due_date' => ''];
$editId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        db()->prepare('DELETE FROM projects WHERE id = ?')->execute([(int)$_POST['id']]);
        flash('success', 'Project deleted.');
        redirect('admin/projects.php');
    }

    if ($action === 'add' || $action === 'update') {
        foreach ($form as $k => $v) {
            $form[$k] = trim((string)($_POST[$k] ?? $v));
        }
        if ($action === 'update') $editId = (int)($_POST['id'] ?? 0);

        if (strlen($form['title']) < 3) $errors[] = 'Project title is required.';
        if ($form['customer_id'] === '') $errors[] = 'Please select a customer.';
        if (!in_array($form['status'], ['planning', 'in_progress', 'review', 'completed', 'delivered'], true)) $form['status'] = 'planning';
        $form['progress'] = max(0, min(100, (int)$form['progress']));

        if (!$errors) {
            $data = [$form['title'], $form['customer_id'], $form['description'] ?: null, $form['status'], $form['progress'], $form['start_date'] ?: null, $form['due_date'] ?: null];
            if ($action === 'add') {
                db()->prepare('INSERT INTO projects (title, customer_id, description, status, progress, start_date, due_date) VALUES (?, ?, ?, ?, ?, ?, ?)')->execute($data);
                $cid = (int)$form['customer_id'];
                $cust = db()->prepare('SELECT name FROM customers WHERE id = ?');
                $cust->execute([$cid]);
                $cn = $cust->fetch();
                if ($cn) notify($cid, 'customer', 'New project created', 'Your project "' . $form['title'] . '" has been created.', 'customer/projects.php');
                flash('success', 'Project created.');
            } else {
                $data[] = $editId;
                db()->prepare('UPDATE projects SET title = ?, customer_id = ?, description = ?, status = ?, progress = ?, start_date = ?, due_date = ? WHERE id = ?')->execute($data);
                $cid = (int)$form['customer_id'];
                notify($cid, 'customer', 'Project updated', 'Project "' . $form['title'] . '" was updated.', 'customer/projects.php');
                flash('success', 'Project updated.');
            }
            redirect('admin/projects.php');
        }
    }
}

// Load for editing
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $eStmt = db()->prepare('SELECT * FROM projects WHERE id = ?');
    $eStmt->execute([$editId]);
    $existing = $eStmt->fetch();
    if ($existing) {
        foreach ($form as $k => $v) $form[$k] = $existing[$k];
    }
}

$customers = db()->query('SELECT id, name, email FROM customers ORDER BY name')->fetchAll();
$projects = db()->query('SELECT p.*, c.name AS customer_name FROM projects p LEFT JOIN customers c ON c.id = p.customer_id ORDER BY p.created_at DESC')->fetchAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="panel">
  <div class="panel-head"><h3>All Projects (<?= count($projects) ?>)</h3><a class="btn btn-sm btn-primary" href="<?= url('admin/projects.php#add') ?>"><?= icon('plus') ?> New Project</a></div>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Project</th><th>Customer</th><th>Status</th><th>Progress</th><th>Due</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($projects as $p): ?>
          <tr>
            <td><a href="<?= url('admin/project-details.php?id=' . $p['id']) ?>"><b><?= e($p['title']) ?></b></a></td>
            <td><?= e($p['customer_name'] ?? '—') ?></td>
            <td><?= status_badge($p['status']) ?></td>
            <td>
              <div class="progress"><div class="progress-bar" style="width:<?= (int)$p['progress'] ?>%"></div></div>
              <span style="font-size:0.78rem;color:var(--text-3);"><?= (int)$p['progress'] ?>%</span>
            </td>
            <td><?= $p['due_date'] ? date('d M Y', strtotime($p['due_date'])) : '—' ?></td>
            <td>
              <div class="actions">
                <a class="icon-btn" href="<?= url('admin/project-details.php?id=' . $p['id']) ?>" title="View"><?= icon('eye') ?></a>
                <a class="icon-btn" href="<?= url('admin/projects.php?edit=' . $p['id'] . '#add') ?>" title="Edit"><?= icon('edit') ?></a>
                <form method="post" action="<?= url('admin/projects.php') ?>" style="margin:0;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <button type="submit" class="icon-btn danger" title="Delete" onclick="return confirm('Delete this project?');"><?= icon('trash') ?></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$projects): ?><tr><td colspan="6" style="text-align:center;color:var(--text-3);padding:30px;">No projects.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="panel" id="add">
  <div class="panel-head"><h3><?= $editId ? 'Edit Project' : 'Add Project' ?></h3></div>
  <div class="panel-body">
    <form method="post" action="<?= url('admin/projects.php' . ($editId ? '?edit=' . $editId : '')) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="<?= $editId ? 'update' : 'add' ?>">
      <?php if ($editId): ?><input type="hidden" name="id" value="<?= $editId ?>"><?php endif; ?>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Project Title</label>
          <input type="text" name="title" class="form-control" value="<?= e($form['title']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Customer</label>
          <select name="customer_id" class="form-control" required>
            <option value="">Select customer...</option>
            <?php foreach ($customers as $c): ?>
              <option value="<?= $c['id'] ?>" <?= $form['customer_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?> (<?= e($c['email']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select name="status" class="form-control">
            <?php foreach (['planning', 'in_progress', 'review', 'completed', 'delivered'] as $st): ?>
              <option value="<?= $st ?>" <?= $form['status'] === $st ? 'selected' : '' ?>><?= e(ucfirst(str_replace('_', ' ', $st))) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Progress (%)</label>
          <input type="number" name="progress" class="form-control" min="0" max="100" value="<?= (int)$form['progress'] ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Start Date</label>
          <input type="date" name="start_date" class="form-control" value="<?= e($form['start_date']) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Due Date</label>
          <input type="date" name="due_date" class="form-control" value="<?= e($form['due_date']) ?>">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"><?= e($form['description']) ?></textarea>
      </div>
      <button type="submit" class="btn btn-primary"><?= $editId ? icon('check') . ' Update' : icon('plus') . ' Create' ?> Project</button>
      <?php if ($editId): ?><a href="<?= url('admin/projects.php') ?>" class="btn btn-outline">Cancel</a><?php endif; ?>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
