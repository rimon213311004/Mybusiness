<?php
declare(strict_types=1);
$pageTitle = 'Testimonials';
$activeNav = 'testimonials';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$errors = [];
$form = ['customer_name' => '', 'role' => '', 'company' => '', 'content' => '', 'rating' => 5, 'active' => 1];
$editId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle') {
        db()->prepare('UPDATE testimonials SET active = 1 - active WHERE id = ?')->execute([(int)$_POST['id']]);
        redirect('admin/testimonials.php');
    }
    if ($action === 'delete') {
        db()->prepare('DELETE FROM testimonials WHERE id = ?')->execute([(int)$_POST['id']]);
        flash('success', 'Testimonial deleted.');
        redirect('admin/testimonials.php');
    }

    if ($action === 'add' || $action === 'update') {
        foreach ($form as $k => $v) {
            if ($k === 'active') $form[$k] = isset($_POST['active']) ? 1 : 0;
            elseif ($k === 'rating') $form[$k] = (int)($_POST['rating'] ?? 5);
            else $form[$k] = trim((string)($_POST[$k] ?? $v));
        }
        if ($action === 'update') $editId = (int)($_POST['id'] ?? 0);

        if (strlen($form['customer_name']) < 2) $errors[] = 'Customer name is required.';
        if (strlen($form['content']) < 10) $errors[] = 'Testimonial content is too short.';

        if (!$errors) {
            if ($action === 'add') {
                db()->prepare('INSERT INTO testimonials (customer_name, role, company, content, rating, active) VALUES (?, ?, ?, ?, ?, ?)')
                    ->execute([$form['customer_name'], $form['role'] ?: null, $form['company'] ?: null, $form['content'], $form['rating'], $form['active']]);
                flash('success', 'Testimonial added.');
            } else {
                db()->prepare('UPDATE testimonials SET customer_name = ?, role = ?, company = ?, content = ?, rating = ?, active = ? WHERE id = ?')
                    ->execute([$form['customer_name'], $form['role'] ?: null, $form['company'] ?: null, $form['content'], $form['rating'], $form['active'], $editId]);
                flash('success', 'Testimonial updated.');
            }
            redirect('admin/testimonials.php');
        }
    }
}

if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $eStmt = db()->prepare('SELECT * FROM testimonials WHERE id = ?');
    $eStmt->execute([$editId]);
    $existing = $eStmt->fetch();
    if ($existing) foreach ($form as $k => $v) $form[$k] = $existing[$k];
}

$testimonials = db()->query('SELECT * FROM testimonials ORDER BY id DESC')->fetchAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="panel">
  <div class="panel-head"><h3>Testimonials (<?= count($testimonials) ?>)</h3><a class="btn btn-sm btn-primary" href="<?= url('admin/testimonials.php#add') ?>"><?= icon('plus') ?> New Testimonial</a></div>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Customer</th><th>Rating</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($testimonials as $t): ?>
          <tr>
            <td><b><?= e($t['customer_name']) ?></b><br><span style="color:var(--text-3);font-size:0.8rem;"><?= e($t['role'] ?: '') ?><?= $t['company'] ? ' · ' . e($t['company']) : '' ?></span></td>
            <td><?= render_stars((int)$t['rating']) ?></td>
            <td><?= (int)$t['active'] === 1 ? '<span class="badge badge-green">Active</span>' : '<span class="badge badge-gray">Hidden</span>' ?></td>
            <td>
              <div class="actions">
                <a class="icon-btn" href="<?= url('admin/testimonials.php?edit=' . $t['id'] . '#add') ?>" title="Edit"><?= icon('edit') ?></a>
                <form method="post" action="<?= url('admin/testimonials.php') ?>" style="margin:0;">
                  <?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $t['id'] ?>">
                  <button type="submit" class="icon-btn" title="Toggle"><?= icon('eye') ?></button>
                </form>
                <form method="post" action="<?= url('admin/testimonials.php') ?>" style="margin:0;">
                  <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $t['id'] ?>">
                  <button type="submit" class="icon-btn danger" title="Delete" onclick="return confirm('Delete this testimonial?');"><?= icon('trash') ?></button>
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
  <div class="panel-head"><h3><?= $editId ? 'Edit Testimonial' : 'Add Testimonial' ?></h3></div>
  <div class="panel-body">
    <form method="post" action="<?= url('admin/testimonials.php' . ($editId ? '?edit=' . $editId : '')) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="<?= $editId ? 'update' : 'add' ?>">
      <?php if ($editId): ?><input type="hidden" name="id" value="<?= $editId ?>"><?php endif; ?>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Customer Name</label>
          <input type="text" name="customer_name" class="form-control" value="<?= e($form['customer_name']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Rating</label>
          <select name="rating" class="form-control">
            <?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>" <?= (int)$form['rating'] === $i ? 'selected' : '' ?>><?= $i ?> stars</option><?php endfor; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Role</label>
          <input type="text" name="role" class="form-control" value="<?= e($form['role']) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Company</label>
          <input type="text" name="company" class="form-control" value="<?= e($form['company']) ?>">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Testimonial Content</label>
        <textarea name="content" class="form-control" required><?= e($form['content']) ?></textarea>
      </div>
      <div class="form-group">
        <label style="display:flex;gap:8px;align-items:center;"><input type="checkbox" name="active" value="1" <?= $form['active'] ? 'checked' : '' ?>> <span>Active</span></label>
      </div>
      <button type="submit" class="btn btn-primary"><?= $editId ? icon('check') . ' Update' : icon('plus') . ' Create' ?></button>
      <?php if ($editId): ?><a href="<?= url('admin/testimonials.php') ?>" class="btn btn-outline">Cancel</a><?php endif; ?>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
