<?php
declare(strict_types=1);
$pageTitle = 'Messages';
$activeNav = 'messages';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'mark_read') {
        db()->prepare('UPDATE messages SET is_read = 1 WHERE is_read = 0')->execute();
        flash('success', 'All messages marked as read.');
        redirect('admin/messages.php');
    } elseif ($action === 'reply') {
        $originalId = (int)($_POST['id'] ?? 0);
        $replyText = trim($_POST['reply'] ?? '');
        $orig = db()->prepare('SELECT * FROM messages WHERE id = ?');
        $orig->execute([$originalId]);
        $origRow = $orig->fetch();

        if (!$origRow) {
            $errors[] = 'Message not found.';
        } elseif (strlen($replyText) < 2) {
            $errors[] = 'Please write a reply.';
        } else {
            $customerId = null;
            if ($origRow['sender_type'] === 'customer' && $origRow['sender_id']) {
                $customerId = (int)$origRow['sender_id'];
            }
            $subject = 'Re: ' . $origRow['subject'];
            if ($customerId) {
                db()->prepare('INSERT INTO messages (sender_type, sender_id, receiver_type, receiver_id, subject, message, is_read) VALUES ("admin", NULL, "customer", ?, ?, ?, 0)')
                    ->execute([$customerId, $subject, $replyText]);
                notify($customerId, 'customer', 'New reply from admin', $subject, 'customer/messages.php');
            } else {
                db()->prepare('INSERT INTO messages (sender_type, sender_id, receiver_type, receiver_id, subject, message, is_read) VALUES ("admin", NULL, NULL, NULL, ?, ?, 0)')
                    ->execute([$subject, $replyText]);
            }
            db()->prepare('UPDATE messages SET is_read = 1 WHERE id = ?')->execute([$originalId]);
            flash('success', 'Reply sent.');
            redirect('admin/messages.php');
        }
    } elseif ($action === 'delete') {
        db()->prepare('DELETE FROM messages WHERE id = ?')->execute([(int)$_POST['id']]);
        flash('success', 'Message deleted.');
        redirect('admin/messages.php');
    }
}

$customerFilter = (int)($_GET['customer'] ?? 0);
$sql = 'SELECT m.*, c.name AS customer_name FROM messages m LEFT JOIN customers c ON c.id = m.sender_id';
$params = [];
if ($customerFilter > 0) {
    $sql .= ' WHERE m.sender_id = ?';
    $params[] = $customerFilter;
}
$sql .= ' ORDER BY m.created_at DESC LIMIT 60';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll();

$customers = db()->query('SELECT id, name FROM customers ORDER BY name')->fetchAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

<form method="get" action="<?= url('admin/messages.php') ?>" style="margin-bottom:20px;display:flex;gap:10px;flex-wrap:wrap;">
  <select name="customer" class="form-control" style="max-width:260px;" onchange="this.form.submit()">
    <option value="0">All messages</option>
    <?php foreach ($customers as $c): ?><option value="<?= $c['id'] ?>" <?= $customerFilter === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
  </select>
</form>
<form method="post" action="<?= url('admin/messages.php') ?>" style="margin:0 0 20px;">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="mark_read">
  <button type="submit" class="btn btn-sm btn-outline"><?= icon('check') ?> Mark All Read</button>
</form>

<div class="panel">
  <div class="panel-head"><h3>Messages (<?= count($messages) ?>)</h3></div>
  <div style="padding:6px 0;">
    <?php foreach ($messages as $m): ?>
      <?php $from = $m['sender_type'] === 'customer' ? ($m['customer_name'] ?? 'Customer') : ($m['sender_type'] === 'admin' ? 'RimonTech Admin' : 'Visitor'); ?>
      <div class="thread-item <?= (int)$m['is_read'] === 0 ? 'unread' : '' ?>">
        <div class="thread-avatar"><?= e(strtoupper(substr($from, 0, 1))) ?></div>
        <div class="thread-body">
          <div class="thread-top">
            <b><?= e($from) ?> — <?= e($m['subject']) ?></b>
            <span class="time"><?= time_ago($m['created_at']) ?></span>
          </div>
          <p><?= e($m['message']) ?></p>
          <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;">
            <form method="post" action="<?= url('admin/messages.php') ?>" style="display:flex;gap:6px;flex:1;min-width:240px;margin:0;">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="reply">
              <input type="hidden" name="id" value="<?= $m['id'] ?>">
              <input type="text" name="reply" class="form-control" placeholder="Write a reply..." style="padding:7px 12px;font-size:0.85rem;">
              <button type="submit" class="btn btn-sm btn-primary">Reply</button>
            </form>
            <form method="post" action="<?= url('admin/messages.php') ?>" style="margin:0;">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $m['id'] ?>">
              <button type="submit" class="icon-btn danger" title="Delete" onclick="return confirm('Delete this message?');"><?= icon('trash') ?></button>
            </form>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (!$messages): ?><p style="text-align:center;color:var(--text-3);padding:30px;">No messages.</p><?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
