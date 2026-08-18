<?php
declare(strict_types=1);
$pageTitle = 'Notifications';
$activeNav = 'notifications';
require_once __DIR__ . '/../includes/functions.php';
$customer = require_customer();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    db()->prepare('UPDATE notifications SET is_read = 1 WHERE user_type = "customer" AND user_id = ?')
        ->execute([$customer['id']]);
    flash('success', 'All notifications marked as read.');
    redirect('customer/notifications.php');
}

$stmt = db()->prepare('SELECT * FROM notifications WHERE user_type = "customer" AND user_id = ? ORDER BY created_at DESC LIMIT 30');
$stmt->execute([$customer['id']]);
$notifs = $stmt->fetchAll();

include __DIR__ . '/../includes/customer-header.php';
?>

<div class="panel">
  <div class="panel-head">
    <h3>Notifications</h3>
    <form method="post" action="<?= url('customer/notifications.php') ?>" style="margin:0;">
      <?= csrf_field() ?>
      <button type="submit" class="btn btn-sm btn-outline"><?= icon('check') ?> Mark All Read</button>
    </form>
  </div>
  <div style="padding:6px 0;">
    <?php foreach ($notifs as $n): ?>
      <div class="thread-item <?= (int)$n['is_read'] === 0 ? 'unread' : '' ?>">
        <div class="thread-avatar"><?= icon('bell') ?></div>
        <div class="thread-body">
          <div class="thread-top">
            <b><?= e($n['title']) ?></b>
            <span class="time"><?= time_ago($n['created_at']) ?></span>
          </div>
          <p><?= e($n['message']) ?></p>
          <?php if ($n['link']): ?><a href="<?= url($n['link']) ?>" style="font-size:0.85rem;font-weight:600;">View &rarr;</a><?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (!$notifs): ?><p style="text-align:center;color:var(--text-3);padding:30px;">No notifications.</p><?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/customer-footer.php'; ?>
