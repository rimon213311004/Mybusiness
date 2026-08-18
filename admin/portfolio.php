<?php
declare(strict_types=1);
$pageTitle = 'Portfolio';
$activeNav = 'portfolio';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$errors = [];
$form = ['title' => '', 'category' => '', 'tech_stack' => '', 'description' => '', 'features' => '', 'live_demo_url' => '', 'github_url' => '', 'case_study' => '', 'featured' => 0, 'active' => 1];
$editId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle') {
        db()->prepare('UPDATE portfolio_items SET active = 1 - active WHERE id = ?')->execute([(int)$_POST['id']]);
        redirect('admin/portfolio.php');
    }
    if ($action === 'delete') {
        db()->prepare('DELETE FROM portfolio_items WHERE id = ?')->execute([(int)$_POST['id']]);
        flash('success', 'Portfolio item deleted.');
        redirect('admin/portfolio.php');
    }

    if ($action === 'add' || $action === 'update') {
        foreach ($form as $k => $v) {
            if ($k === 'featured' || $k === 'active') $form[$k] = isset($_POST[$k]) ? 1 : 0;
            else $form[$k] = trim((string)($_POST[$k] ?? $v));
        }
        if ($action === 'update') $editId = (int)($_POST['id'] ?? 0);

        if (strlen($form['title']) < 3) $errors[] = 'Title is required.';
        $slug = slugify($form['title']);
        if ($slug === '') $errors[] = 'Could not create a valid slug.';
        $featuresArr = array_values(array_filter(array_map('trim', explode("\n", $form['features']))));

        // Image upload
        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $image = upload_file($_FILES['image'], 'portfolio', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
            if (!$image) $errors[] = 'Image upload failed.';
        }

        if (!$errors) {
            $featuresJson = json_encode($featuresArr);
            if ($action === 'add') {
                db()->prepare('INSERT INTO portfolio_items (title, slug, category, image, tech_stack, description, features, live_demo_url, github_url, case_study, featured, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)')
                    ->execute([$form['title'], $slug, $form['category'] ?: null, $image ?: null, $form['tech_stack'] ?: null, $form['description'] ?: null, $featuresJson, $form['live_demo_url'] ?: null, $form['github_url'] ?: null, $form['case_study'] ?: null, $form['featured'], $form['active']]);
                flash('success', 'Portfolio item created.');
            } else {
                $fields = 'title = ?, slug = ?, category = ?, tech_stack = ?, description = ?, features = ?, live_demo_url = ?, github_url = ?, case_study = ?, featured = ?, active = ?';
                $data = [$form['title'], $slug, $form['category'] ?: null, $form['tech_stack'] ?: null, $form['description'] ?: null, $featuresJson, $form['live_demo_url'] ?: null, $form['github_url'] ?: null, $form['case_study'] ?: null, $form['featured'], $form['active']];
                if ($image) {
                    $fields .= ', image = ?';
                    $data[] = $image;
                }
                $data[] = $editId;
                db()->prepare('UPDATE portfolio_items SET ' . $fields . ' WHERE id = ?')->execute($data);
                flash('success', 'Portfolio item updated.');
            }
            redirect('admin/portfolio.php');
        }
    }
}

if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $eStmt = db()->prepare('SELECT * FROM portfolio_items WHERE id = ?');
    $eStmt->execute([$editId]);
    $existing = $eStmt->fetch();
    if ($existing) {
        foreach ($form as $k => $v) $form[$k] = $existing[$k];
        $form['features'] = implode("\n", json_features($existing['features']));
    }
}

$projects = db()->query('SELECT * FROM portfolio_items ORDER BY id DESC')->fetchAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="panel">
  <div class="panel-head"><h3>Portfolio Items (<?= count($projects) ?>)</h3><a class="btn btn-sm btn-primary" href="<?= url('admin/portfolio.php#add') ?>"><?= icon('plus') ?> New Item</a></div>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Project</th><th>Category</th><th>Featured</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($projects as $p): ?>
          <tr>
            <td><b><?= e($p['title']) ?></b><br><span style="color:var(--text-3);font-size:0.8rem;"><?= e($p['tech_stack'] ?: '') ?></span></td>
            <td><span class="badge badge-blue"><?= e($p['category']) ?></span></td>
            <td><?= (int)$p['featured'] === 1 ? '<span class="badge badge-purple">Featured</span>' : '—' ?></td>
            <td><?= (int)$p['active'] === 1 ? '<span class="badge badge-green">Active</span>' : '<span class="badge badge-gray">Hidden</span>' ?></td>
            <td>
              <div class="actions">
                <a class="icon-btn" href="<?= url('admin/portfolio.php?edit=' . $p['id'] . '#add') ?>" title="Edit"><?= icon('edit') ?></a>
                <form method="post" action="<?= url('admin/portfolio.php') ?>" style="margin:0;">
                  <?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <button type="submit" class="icon-btn" title="Toggle"><?= icon('eye') ?></button>
                </form>
                <form method="post" action="<?= url('admin/portfolio.php') ?>" style="margin:0;">
                  <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $p['id'] ?>">
                  <button type="submit" class="icon-btn danger" title="Delete" onclick="return confirm('Delete this portfolio item?');"><?= icon('trash') ?></button>
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
  <div class="panel-head"><h3><?= $editId ? 'Edit Item' : 'Add Portfolio Item' ?></h3></div>
  <div class="panel-body">
    <form method="post" action="<?= url('admin/portfolio.php' . ($editId ? '?edit=' . $editId : '')) ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="<?= $editId ? 'update' : 'add' ?>">
      <?php if ($editId): ?><input type="hidden" name="id" value="<?= $editId ?>"><?php endif; ?>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Project Title</label>
          <input type="text" name="title" class="form-control" value="<?= e($form['title']) ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label">Category</label>
          <select name="category" class="form-control">
            <?php foreach (['Web Application', 'Business Website', 'E-Commerce', 'Healthcare', 'School', 'Restaurant', 'Hotel', 'Portfolio'] as $cat): ?>
              <option value="<?= e($cat) ?>" <?= $form['category'] === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Tech Stack</label>
          <input type="text" name="tech_stack" class="form-control" value="<?= e($form['tech_stack']) ?>" placeholder="e.g. Next.js, Node.js, MongoDB">
        </div>
        <div class="form-group">
          <label class="form-label">Live Demo URL</label>
          <input type="url" name="live_demo_url" class="form-control" value="<?= e($form['live_demo_url']) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">GitHub URL</label>
          <input type="url" name="github_url" class="form-control" value="<?= e($form['github_url']) ?>" placeholder="https://github.com/user/repo">
        </div>
        <div class="form-group">
          <label class="form-label">Cover Image</label>
          <input type="file" name="image" class="form-control" accept="image/*">
          <?php if (!empty($existing['image'])): ?><div class="form-hint">Current: <?= e($existing['image']) ?></div><?php endif; ?>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"><?= e($form['description']) ?></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Key Features (one per line)</label>
        <textarea name="features" class="form-control"><?= e($form['features']) ?></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Case Study</label>
        <textarea name="case_study" class="form-control"><?= e($form['case_study']) ?></textarea>
      </div>
      <div style="display:flex;gap:20px;margin-bottom:18px;flex-wrap:wrap;">
        <label style="display:flex;gap:8px;align-items:center;"><input type="checkbox" name="featured" value="1" <?= $form['featured'] ? 'checked' : '' ?>> <span>Featured</span></label>
        <label style="display:flex;gap:8px;align-items:center;"><input type="checkbox" name="active" value="1" <?= $form['active'] ? 'checked' : '' ?>> <span>Active</span></label>
      </div>
      <button type="submit" class="btn btn-primary"><?= $editId ? icon('check') . ' Update' : icon('plus') . ' Create' ?></button>
      <?php if ($editId): ?><a href="<?= url('admin/portfolio.php') ?>" class="btn btn-outline">Cancel</a><?php endif; ?>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
