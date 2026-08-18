<?php
declare(strict_types=1);
$pageTitle = 'Services';
$activeNav = 'services';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$errors = [];
$form = ['title' => '', 'short_desc' => '', 'icon' => 'code', 'description' => '', 'features' => '', 'active' => 1];
$editId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle') {
        db()->prepare('UPDATE services SET active = 1 - active WHERE id = ?')->execute([(int)$_POST['id']]);
        redirect('admin/services.php');
    }
    if ($action === 'delete') {
        db()->prepare('DELETE FROM services WHERE id = ?')->execute([(int)$_POST['id']]);
        flash('success', 'Service deleted.');
        redirect('admin/services.php');
    }

    if ($action === 'add' || $action === 'update') {
        foreach ($form as $k => $v) {
            if ($k === 'active') $form[$k] = isset($_POST['active']) ? 1 : 0;
            else $form[$k] = trim((string)($_POST[$k] ?? $v));
        }
        if ($action === 'update') $editId = (int)($_POST['id'] ?? 0);

        if (strlen($form['title']) < 3) $errors[] = 'Title is required.';
        $slug = slugify($form['title']);
        if ($slug === '') $errors[] = 'Could not create a valid slug.';

        $featuresArr = array_values(array_filter(array_map('trim', explode("\n", $form['features']))));

        if (!$errors) {
            $featuresJson = json_encode($featuresArr);
            if ($action === 'add') {
                db()->prepare('INSERT INTO services (title, slug, short_desc, icon, description, features, active) VALUES (?, ?, ?, ?, ?, ?, ?)')
                    ->execute([$form['title'], $slug, $form['short_desc'] ?: null, $form['icon'], $form['description'] ?: null, $featuresJson, $form['active']]);
                flash('success', 'Service created.');
            } else {
                db()->prepare('UPDATE services SET title = ?, slug = ?, short_desc = ?, icon = ?, description = ?, features = ?, active = ? WHERE id = ?')
                    ->execute([$form['title'], $slug, $form['short_desc'] ?: null, $form['icon'], $form['description'] ?: null, $featuresJson, $form['active'], $editId]);
                flash('success', 'Service updated.');
            }
            redirect('admin/services.php');
        }
    }
}

if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $eStmt = db()->prepare('SELECT * FROM services WHERE id = ?');
    $eStmt->execute([$editId]);
    $existing = $eStmt->fetch();
    if ($existing) {
        foreach ($form as $k => $v) $form[$k] = $existing[$k];
        $form['features'] = implode("\n", json_features($existing['features']));
    }
}

$services = db()->query('SELECT * FROM services ORDER BY id')->fetchAll();
$iconOptions = ['code', 'cart', 'app', 'rocket', 'search', 'shield', 'briefcase', 'graduation', 'utensils', 'stethoscope', 'bed', 'user', 'cpu'];

include __DIR__ . '/../includes/admin-header.php';
?>

<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="panel">
  <div class="panel-head"><h3>Services (<?= count($services) ?>)</h3><a class="btn btn-sm btn-primary" href="<?= url('admin/services.php#add') ?>"><?= icon('plus') ?> New Service</a></div>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Service</th><th>Slug</th><th>Icon</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($services as $s): ?>
          <tr>
            <td><b><?= e($s['title']) ?></b></td>
            <td><?= e($s['slug']) ?></td>
            <td><span class="card-icon" style="width:34px;height:34px;margin:0;"><?= icon($s['icon']) ?></span></td>
            <td><?= (int)$s['active'] === 1 ? '<span class="badge badge-green">Active</span>' : '<span class="badge badge-gray">Hidden</span>' ?></td>
            <td>
              <div class="actions">
                <a class="icon-btn" href="<?= url('admin/services.php?edit=' . $s['id'] . '#add') ?>" title="Edit"><?= icon('edit') ?></a>
                <form method="post" action="<?= url('admin/services.php') ?>" style="margin:0;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $s['id'] ?>">
                  <button type="submit" class="icon-btn" title="Toggle"><?= icon('eye') ?></button>
                </form>
                <form method="post" action="<?= url('admin/services.php') ?>" style="margin:0;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $s['id'] ?>">
                  <button type="submit" class="icon-btn danger" title="Delete" onclick="return confirm('Delete this service?');"><?= icon('trash') ?></button>
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
  <div class="panel-head"><h3><?= $editId ? 'Edit Service' : 'Add Service' ?></h3></div>
  <div class="panel-body">
    <form method="post" action="<?= url('admin/services.php' . ($editId ? '?edit=' . $editId : '')) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="<?= $editId ? 'update' : 'add' ?>">
      <?php if ($editId): ?><input type="hidden" name="id" value="<?= $editId ?>"><?php endif; ?>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" value="<?= e($form['title']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Icon</label>
          <select name="icon" class="form-control">
            <?php foreach ($iconOptions as $io): ?><option value="<?= $io ?>" <?= $form['icon'] === $io ? 'selected' : '' ?>><?= e($io) ?></option><?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Short Description</label>
        <input type="text" name="short_desc" class="form-control" value="<?= e($form['short_desc']) ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"><?= e($form['description']) ?></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Features (one per line)</label>
        <textarea name="features" class="form-control"><?= e($form['features']) ?></textarea>
      </div>
      <div class="form-group">
        <label class="form-check" style="display:flex;gap:8px;align-items:center;">
          <input type="checkbox" name="active" value="1" <?= $form['active'] ? 'checked' : '' ?>> <span>Active (visible on website)</span>
        </label>
      </div>
      <button type="submit" class="btn btn-primary"><?= $editId ? icon('check') . ' Update' : icon('plus') . ' Create' ?></button>
      <?php if ($editId): ?><a href="<?= url('admin/services.php') ?>" class="btn btn-outline">Cancel</a><?php endif; ?>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
