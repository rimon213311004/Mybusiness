<?php
declare(strict_types=1);
$pageTitle = 'Messages';
$activeNav = 'messages';
require_once __DIR__ . '/../includes/functions.php';
$customer = require_customer();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($subject === '') $errors[] = 'Please enter a subject.';
    if (strlen($message) < 5) $errors[] = 'Please write your message.';

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO messages (sender_type, sender_id, receiver_type, receiver_id, subject, message, is_read) VALUES ("customer", ?, "admin", NULL, ?, ?, 0)');
        $stmt->execute([$customer['id'], $subject, $message]);
        notify(1, 'admin', 'New message from customer', $customer['name'] . ': ' . $subject, 'admin/messages.php');
        flash('success', 'Message sent to admin.');
        redirect('customer/messages.php');
    }
}

$stmt = db()->prepare('
    SELECT * FROM messages
    WHERE (sender_type = "customer" AND sender_id = ?)
       OR (sender_type = "admin" AND receiver_type = "customer" AND receiver_id = ?)
    ORDER BY created_at DESC
    LIMIT 50
');
$stmt->execute([$customer['id'], $customer['id']]);
$thread = $stmt->fetchAll();
$thread = array_reverse($thread);

include __DIR__ . '/../includes/customer-header.php';
?>

<div class="grid-2" style="align-items:start;">
  <div class="panel">
    <div class="panel-head"><h3>Conversation with RimonTech</h3></div>
    <div class="panel-body" style="max-height:520px;overflow-y:auto;background:var(--surface-2);">
      <?php foreach ($thread as $m): ?>
        <?php $mine = $m['sender_type'] === 'customer'; ?>
        <div class="bubble <?= $mine ? 'sent' : 'received' ?>">
          <b style="font-size:0.85rem;"><?= $mine ? 'You' : 'RimonTech Admin' ?></b> — <?= e($m['subject']) ?>
          <br><?= nl2br(e($m['message'])) ?>
          <span class="bubble-time"><?= time_ago($m['created_at']) ?></span>
        </div>
      <?php endforeach; ?>
      <?php if (!$thread): ?><p style="text-align:center;color:var(--text-3);padding:20px;">No messages yet. Say hello!</p><?php endif; ?>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>Send a Message</h3></div>
    <div class="panel-body">
      <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
      <form method="post" action="<?= url('customer/messages.php') ?>">
        <?= csrf_field() ?>
        <div class="form-group">
          <label class="form-label" for="subject">Subject</label>
          <input type="text" id="subject" name="subject" class="form-control" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="message">Message</label>
          <textarea id="message" name="message" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary"><?= icon('mail') ?> Send</button>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/customer-footer.php'; ?>
