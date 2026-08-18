<?php
declare(strict_types=1);
$pageTitle = 'Documents';
$activeNav = 'documents';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        db()->prepare('DELETE FROM documents WHERE id = ?')->execute([(int)$_POST['id']]);
        flash('success', 'Document deleted.');
        redirect('admin/documents.php');
    }

    if ($action === 'upload') {
        $customerId = (int)($_POST['customer_id'] ?? 0);
        $projectId = (int)($_POST['project_id'] ?? 0);
        $file = $_FILES['file'] ?? null;

        if (!$customerId) $errors[] = 'Select a customer.';
        $path = upload_file($file, 'documents', ['pdf', 'zip', 'docx', 'txt', 'jpg', 'png']);
        if (!$path) $errors[] = 'Upload failed. Check file type and size.';

        if (!$errors) {
            db()->prepare('INSERT INTO documents (customer_id, project_id, file_name, file_path, uploaded_by) VALUES (?, ?, ?, ?, "admin")')
                ->execute([$customerId, $projectId ?: null, $file['name'], $path]);
            notify($customerId, 'customer', 'New document from admin', 'Admin shared: ' . $file['name'], 'customer/documents.php');
            flash('success', 'Document uploaded.');
            redirect('admin/documents.php');
        }
    }
}

$customers = db()->query('SELECT id, name FROM customers ORDER BY name')->fetchAll();
$projects = db()->query('SELECT id, title FROM projects ORDER BY title')->fetchAll();
$documents = db()->query('SELECT d.*, c.name AS customer_name, p.title AS project_title FROM documents d LEFT JOIN customers c ON c.id = d.customer_id LEFT JOIN projects p ON p.id = d.project_id ORDER BY d.created_at DESC')->fetchAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

<div class="panel">
  <div class="panel-head"><h3>Documents (<?= count($documents) ?>)</h3><a class="btn btn-sm btn-primary" href="<?= url('admin/documents.php#upload') ?>"><?= icon('upload') ?> Upload</a></div>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>File</th><th>Customer</th><th>Project</th><th>Uploaded By</th><th>Date</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($documents as $d): ?>
          <tr>
            <td><b><?= e($d['file_name']) ?></b></td>
            <td><?= e($d['customer_name'] ?? '—') ?></td>
            <td><?= e($d['project_title'] ?? '—') ?></td>
            <td><span class="badge <?= $d['uploaded_by'] === 'admin' ? 'badge-purple' : 'badge-blue' ?>"><?= e($d['uploaded_by']) ?></span></td>
            <td><?= date('d M Y', strtotime($d['created_at'])) ?></td>
            <td>
              <div class="actions">
                <a class="icon-btn" href="<?= url($d['file_path']) ?>" target="_blank" title="View"><?= icon('eye') ?></a>
                <form method="post" action="<?= url('admin/documents.php') ?>" style="margin:0;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $d['id'] ?>">
                  <button type="submit" class="icon-btn danger" title="Delete" onclick="return confirm('Delete this document?');"><?= icon('trash') ?></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="panel" id="upload">
  <div class="panel-head"><h3>Upload Document for Customer</h3></div>
  <div class="panel-body">
    <form method="post" action="<?= url('admin/documents.php#upload') ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="upload">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Customer</label>
          <select name="customer_id" class="form-control" required>
            <option value="">Select customer...</option>
            <?php foreach ($customers as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Project (optional)</label>
          <select name="project_id" class="form-control">
            <option value="0">— None —</option>
            <?php foreach ($projects as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['title']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">File</label>
          <input type="file" name="file" class="form-control" required>
          <div class="form-hint">Allowed: PDF, ZIP, DOCX, TXT, images. Max <?= MAX_UPLOAD_MB ?>MB.</div>
        </div>
      </div>
      <button type="submit" class="btn btn-primary"><?= icon('upload') ?> Upload</button>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
