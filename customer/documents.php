<?php
declare(strict_types=1);
$pageTitle = 'Documents';
$activeNav = 'documents';
require_once __DIR__ . '/../includes/functions.php';
$customer = require_customer();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $projectId = (int)($_POST['project_id'] ?? 0);
    $file = $_FILES['file'] ?? null;

    if ($projectId) {
        $ps = db()->prepare('SELECT id FROM projects WHERE id = ? AND customer_id = ?');
        $ps->execute([$projectId, $customer['id']]);
        if (!$ps->fetch()) $errors[] = 'Invalid project selected.';
    }

    $path = upload_file($file, 'documents', ['pdf', 'zip', 'docx', 'txt', 'jpg', 'png']);
    if (!$path) $errors[] = 'File upload failed. Please check the file type (max ' . MAX_UPLOAD_MB . 'MB).';

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO documents (customer_id, project_id, file_name, file_path, uploaded_by) VALUES (?, ?, ?, ?, "customer")');
        $stmt->execute([$customer['id'], $projectId ?: null, $file['name'], $path]);
        notify(1, 'admin', 'Customer uploaded a document', $customer['name'] . ' uploaded ' . $file['name'], 'admin/documents.php');
        flash('success', 'Document uploaded successfully.');
        redirect('customer/documents.php');
    }
}

$stmt = db()->prepare('SELECT * FROM documents WHERE customer_id = ? ORDER BY created_at DESC');
$stmt->execute([$customer['id']]);
$docs = $stmt->fetchAll();

$ps = db()->prepare('SELECT id, title FROM projects WHERE customer_id = ?');
$ps->execute([$customer['id']]);
$projects = $ps->fetchAll();

include __DIR__ . '/../includes/customer-header.php';
?>

<div class="grid-2" style="align-items:start;">
  <div class="panel">
    <div class="panel-head"><h3>Your Documents</h3></div>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>File</th><th>Project</th><th>Date</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($docs as $d): ?>
            <tr>
              <td><b><?= e($d['file_name']) ?></b><br><span style="color:var(--text-3);font-size:0.78rem;"><?= e($d['uploaded_by']) ?></span></td>
              <td>
                <?php if ($d['project_id']):
                    $pj = db()->prepare('SELECT title FROM projects WHERE id = ?');
                    $pj->execute([$d['project_id']]);
                    $pt = $pj->fetch();
                    echo e($pt['title'] ?? '—');
                else: echo '—'; endif; ?>
              </td>
              <td><?= date('d M Y', strtotime($d['created_at'])) ?></td>
              <td><a class="btn btn-sm btn-ghost" href="<?= url($d['file_path']) ?>" target="_blank"><?= icon('eye') ?> View</a></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$docs): ?><tr><td colspan="4" style="text-align:center;color:var(--text-3);padding:30px;">No documents yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>Upload Document</h3></div>
    <div class="panel-body">
      <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
      <form method="post" action="<?= url('customer/documents.php') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="form-group">
          <label class="form-label" for="project_id">Related Project (optional)</label>
          <select id="project_id" name="project_id" class="form-control">
            <option value="0">— None —</option>
            <?php foreach ($projects as $pr): ?><option value="<?= $pr['id'] ?>"><?= e($pr['title']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="file">File</label>
          <input type="file" id="file" name="file" class="form-control" required>
          <div class="form-hint">Allowed: PDF, ZIP, DOCX, TXT, images. Max <?= MAX_UPLOAD_MB ?>MB.</div>
        </div>
        <button type="submit" class="btn btn-primary"><?= icon('upload') ?> Upload</button>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/customer-footer.php'; ?>
