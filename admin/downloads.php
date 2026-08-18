<?php
declare(strict_types=1);
$pageTitle = 'Downloads';
$activeNav = 'downloads';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle') {
        db()->prepare('UPDATE downloads SET download_enabled = 1 - download_enabled WHERE id = ?')->execute([(int)$_POST['id']]);
        flash('success', 'Download status updated.');
        redirect('admin/downloads.php');
    }
    if ($action === 'delete') {
        db()->prepare('DELETE FROM downloads WHERE id = ?')->execute([(int)$_POST['id']]);
        flash('success', 'Download record deleted. (ZIP file remains on disk)');
        redirect('admin/downloads.php');
    }
    if ($action === 'reset') {
        db()->prepare('UPDATE downloads SET download_count = 0 WHERE id = ?')->execute([(int)$_POST['id']]);
        flash('success', 'Download count reset to 0.');
        redirect('admin/downloads.php');
    }

    if ($action === 'add' || $action === 'update') {
        $projectId = (int)($_POST['project_id'] ?? 0);
        $file = $_FILES['file'] ?? null;
        $editId = $action === 'update' ? (int)($_POST['id'] ?? 0) : 0;

        $proj = db()->prepare('SELECT id FROM portfolio_items WHERE id = ?');
        $proj->execute([$projectId]);
        if (!$proj->fetch()) $errors[] = 'Select a valid portfolio project.';

        $targetName = trim($_POST['file_name'] ?? '');
        if ($targetName === '') $targetName = $file['name'] ?? '';

        $projectDir = DOWNLOAD_DIR . '/projects';
        if (!is_dir($projectDir)) mkdir($projectDir, 0777, true);

        // Store ZIP
        $storedPath = null;
        if (!empty($file['name'])) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'ZIP upload failed.';
            } elseif (pathinfo($file['name'], PATHINFO_EXTENSION) !== 'zip') {
                $errors[] = 'Only ZIP files are allowed for downloads.';
            } elseif ($file['size'] > 50 * 1024 * 1024) {
                $errors[] = 'ZIP file too large (max 50MB).';
            } else {
                $safeName = preg_replace('/[^A-Za-z0-9._-]/', '-', $targetName ?: $file['name']);
                $safeName = basename($safeName);
                if ($safeName === '' || pathinfo($safeName, PATHINFO_EXTENSION) !== 'zip') {
                    $safeName = slugify(pathinfo($safeName, PATHINFO_FILENAME)) . '.zip';
                }
                if (!move_uploaded_file($file['tmp_name'], $projectDir . '/' . $safeName)) {
                    $errors[] = 'Could not save the ZIP file.';
                } else {
                    $storedPath = 'downloads/projects/' . $safeName;
                    $targetName = $safeName;
                }
            }
        }

        if (!$errors) {
            if ($action === 'add') {
                if (!$storedPath) {
                    $errors[] = 'Upload a ZIP file to create a download.';
                } else {
                    $stmt = db()->prepare('INSERT INTO downloads (project_id, file_name, file_path, download_enabled, download_count) VALUES (?, ?, ?, 1, 0)');
                    $stmt->execute([$projectId, $targetName, $storedPath]);
                    flash('success', 'Download created and enabled.');
                    redirect('admin/downloads.php');
                }
            } else {
                $existing = db()->prepare('SELECT * FROM downloads WHERE id = ?');
                $existing->execute([$editId]);
                $row = $existing->fetch();
                if (!$row) {
                    $errors[] = 'Download record not found.';
                } else {
                    $sql = 'UPDATE downloads SET project_id = ?, file_name = ?';
                    $data = [$projectId, $targetName];
                    if ($storedPath) {
                        $sql .= ', file_path = ?';
                        $data[] = $storedPath;
                    }
                    $sql .= ' WHERE id = ?';
                    $data[] = $editId;
                    db()->prepare($sql)->execute($data);
                    flash('success', 'Download updated.');
                    redirect('admin/downloads.php');
                }
            }
        }
    }
}

$projects = db()->query('SELECT id, title FROM portfolio_items ORDER BY title')->fetchAll();
$downloads = db()->query('SELECT d.*, p.title AS project_title FROM downloads d JOIN portfolio_items p ON p.id = d.project_id ORDER BY d.created_at DESC')->fetchAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="panel">
  <div class="panel-head">
    <h3>Download Management (<?= count($downloads) ?>)</h3>
    <a class="btn btn-sm btn-primary" href="<?= url('admin/downloads.php#add') ?>"><?= icon('plus') ?> Add Download</a>
  </div>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Project</th><th>Source File</th><th>Status</th><th>Downloads</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($downloads as $d): ?>
          <tr>
            <td><b><?= e($d['project_title']) ?></b></td>
            <td><?= e($d['file_name']) ?><br>
              <span style="color:var(--text-3);font-size:0.8rem;">
                <?php
                $filePath = DOWNLOAD_DIR . '/projects/' . basename($d['file_name']);
                echo file_exists($filePath) ? 'on disk' : '<span style="color:var(--danger);">missing file</span>';
                ?>
              </span>
            </td>
            <td><?= (int)$d['download_enabled'] === 1 ? '<span class="badge badge-green">Public</span>' : '<span class="badge badge-gray">Disabled</span>' ?></td>
            <td><b><?= (int)$d['download_count'] ?></b></td>
            <td>
              <div class="actions">
                <form method="post" action="<?= url('admin/downloads.php') ?>" style="margin:0;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="toggle">
                  <input type="hidden" name="id" value="<?= $d['id'] ?>">
                  <button type="submit" class="btn btn-sm <?= (int)$d['download_enabled'] === 1 ? 'btn-outline' : 'btn-success' ?>">
                    <?= (int)$d['download_enabled'] === 1 ? 'Disable' : 'Enable' ?>
                  </button>
                </form>
                <form method="post" action="<?= url('admin/downloads.php') ?>" style="margin:0;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="reset">
                  <input type="hidden" name="id" value="<?= $d['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-ghost" title="Reset count"><?= icon('refresh') ?> Reset</button>
                </form>
                <form method="post" action="<?= url('admin/downloads.php') ?>" style="margin:0;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $d['id'] ?>">
                  <button type="submit" class="icon-btn danger" title="Delete" onclick="return confirm('Delete this download record?');"><?= icon('trash') ?></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$downloads): ?><tr><td colspan="5" style="text-align:center;color:var(--text-3);padding:30px;">No downloads configured yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="panel" id="add">
  <div class="panel-head"><h3>Add Download</h3></div>
  <div class="panel-body">
    <form method="post" action="<?= url('admin/downloads.php#add') ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="add">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Portfolio Project</label>
          <select name="project_id" class="form-control" required>
            <option value="">Select project...</option>
            <?php foreach ($projects as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['title']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">ZIP File</label>
          <input type="file" name="file" class="form-control" accept=".zip" required>
          <div class="form-hint">Sanitized public bundle only — no credentials, API keys or private data. Max 50MB.</div>
        </div>
      </div>
      <button type="submit" class="btn btn-primary"><?= icon('upload') ?> Upload &amp; Enable</button>
    </form>
  </div>
</div>

<div class="alert" style="background:var(--accent-soft);border:1px solid #a5f3fc;color:#155e75;">
  <b>Security note:</b> Only upload sanitized public demo source code. Never include database passwords, API keys,
  SMTP credentials, .env files or private customer data inside downloadable ZIPs.
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
