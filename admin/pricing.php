<?php
declare(strict_types=1);
$pageTitle = 'Pricing';
$activeNav = 'pricing';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$errors = [];
$form = ['title' => '', 'price' => 0, 'period' => 'one-time', 'description' => '', 'features' => '', 'highlighted' => 0, 'active' => 1];
$editId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle') {
        db()->prepare('UPDATE pricing_plans SET active = 1 - active WHERE id = ?')->execute([(int)$_POST['id']]);
        redirect('admin/pricing.php');
    }
    if ($action === 'delete') {
        db()->prepare('DELETE FROM pricing_plans WHERE id = ?')->execute([(int)$_POST['id']]);
        flash('success', 'Plan deleted.');
        redirect('admin/pricing.php');
    }

    if ($action === 'add' || $action === 'update') {
        foreach ($form as $k => $v) {
            if ($k === 'highlighted' || $k === 'active') $form[$k] = isset($_POST[$k]) ? 1 : 0;
            elseif ($k === 'price') $form[$k] = (float)($_POST[$k] ?? 0);
            else $form[$k] = trim((string)($_POST[$k] ?? $v));
        }
        if ($action === 'update') $editId = (int)($_POST['id'] ?? 0);

        if (strlen($form['title']) < 2) $errors[] = 'Plan title is required.';
        if ($form['price'] < 0) $errors[] = 'Price must be positive.';
        $featuresArr = array_values(array_filter(array_map('trim', explode("\n", $form['features']))));

        if (!$errors) {
            $featuresJson = json_encode($featuresArr);
            if ($action === 'add') {
                db()->prepare('INSERT INTO pricing_plans (title, price, period, description, features, highlighted, active) VALUES (?, ?, ?, ?, ?, ?, ?)')
                    ->execute([$form['title'], $form['price'], $form['period'], $form['description'] ?: null, $featuresJson, $form['highlighted'], $form['active']]);
                flash('success', 'Plan created.');
            } else {
                db()->prepare('UPDATE pricing_plans SET title = ?, price = ?, period = ?, description = ?, features = ?, highlighted = ?, active = ? WHERE id = ?')
                    ->execute([$form['title'], $form['price'], $form['period'], $form['description'] ?: null, $featuresJson, $form['highlighted'], $form['active'], $editId]);
                flash('success', 'Plan updated.');
            }
            redirect('admin/pricing.php');
        }
    }
}

if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $eStmt = db()->prepare('SELECT * FROM pricing_plans WHERE id = ?');
    $eStmt->execute([$editId]);
    $existing = $eStmt->fetch();
    if ($existing) {
        foreach ($form as $k => $v) $form[$k] = $existing[$k];
        $form['features'] = implode("\n", json_features($existing['features']));
    }
}

$plans = db()->query('SELECT * FROM pricing_plans ORDER BY id')->fetchAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="panel">
  <div class="panel-head"><h3>Pricing Plans (<?= count($plans) ?>)</h3><a class="btn btn-sm btn-primary" href="<?= url('admin/pricing.php#add') ?>"><?= icon('plus') ?> New Plan</a></div>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Plan</th><th>Price</th><th>Period</th><th>Highlighted</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($plans as $pl): ?>
          <tr>
            <td><b><?= e($pl['title']) ?></b></td>
            <td><?= money($pl['price']) ?></td>
            <td><?= e($pl['period']) ?></td>
            <td><?= (int)$pl['highlighted'] === 1 ? '<span class="badge badge-purple">Yes</span>' : '—' ?></td>
            <td><?= (int)$pl['active'] === 1 ? '<span class="badge badge-green">Active</span>' : '<span class="badge badge-gray">Hidden</span>' ?></td>
            <td>
              <div class="actions">
                <a class="icon-btn" href="<?= url('admin/pricing.php?edit=' . $pl['id'] . '#add') ?>" title="Edit"><?= icon('edit') ?></a>
                <form method="post" action="<?= url('admin/pricing.php') ?>" style="margin:0;">
                  <?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $pl['id'] ?>">
                  <button type="submit" class="icon-btn" title="Toggle"><?= icon('eye') ?></button>
                </form>
                <form method="post" action="<?= url('admin/pricing.php') ?>" style="margin:0;">
                  <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $pl['id'] ?>">
                  <button type="submit" class="icon-btn danger" title="Delete" onclick="return confirm('Delete this plan?');"><?= icon('trash') ?></button>
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
  <div class="panel-head"><h3><?= $editId ? 'Edit Plan' : 'Add Plan' ?></h3></div>
  <div class="panel-body">
    <form method="post" action="<?= url('admin/pricing.php' . ($editId ? '?edit=' . $editId : '')) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="<?= $editId ? 'update' : 'add' ?>">
      <?php if ($editId): ?><input type="hidden" name="id" value="<?= $editId ?>"><?php endif; ?>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Plan Title</label>
          <input type="text" name="title" class="form-control" value="<?= e($form['title']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Price (BDT)</label>
          <input type="number" name="price" class="form-control" min="0" value="<?= e((string)$form['price']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Period</label>
          <select name="period" class="form-control">
            <?php foreach (['one-time', 'monthly', 'yearly'] as $per): ?>
              <option value="<?= $per ?>" <?= $form['period'] === $per ? 'selected' : '' ?>><?= e(ucfirst($per)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <input type="text" name="description" class="form-control" value="<?= e($form['description']) ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Features (one per line)</label>
        <textarea name="features" class="form-control"><?= e($form['features']) ?></textarea>
      </div>
      <div style="display:flex;gap:20px;margin-bottom:18px;">
        <label style="display:flex;gap:8px;align-items:center;"><input type="checkbox" name="highlighted" value="1" <?= $form['highlighted'] ? 'checked' : '' ?>> <span>Highlighted</span></label>
        <label style="display:flex;gap:8px;align-items:center;"><input type="checkbox" name="active" value="1" <?= $form['active'] ? 'checked' : '' ?>> <span>Active</span></label>
      </div>
      <button type="submit" class="btn btn-primary"><?= $editId ? icon('check') . ' Update' : icon('plus') . ' Create' ?></button>
      <?php if ($editId): ?><a href="<?= url('admin/pricing.php') ?>" class="btn btn-outline">Cancel</a><?php endif; ?>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
