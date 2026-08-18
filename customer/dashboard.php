<?php
declare(strict_types=1);
$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
require_once __DIR__ . '/../includes/functions.php';
$customer = require_customer();

$s = db()->prepare('SELECT COUNT(*) FROM projects WHERE customer_id = ?');
$s->execute([$customer['id']]); $stats['projects'] = (int)$s->fetchColumn();

$s = db()->prepare('SELECT COUNT(*) FROM project_requests WHERE customer_id = ?');
$s->execute([$customer['id']]); $stats['requests'] = (int)$s->fetchColumn();

$s = db()->prepare('SELECT COUNT(*) FROM invoices WHERE customer_id = ?');
$s->execute([$customer['id']]); $stats['invoices'] = (int)$s->fetchColumn();

$s = db()->prepare('SELECT IFNULL(SUM(amount),0) FROM payments WHERE customer_id = ? AND status = "completed"');
$s->execute([$customer['id']]); $stats['paid'] = (float)$s->fetchColumn();

$s = db()->prepare('SELECT IFNULL(SUM(amount),0) FROM invoices WHERE customer_id = ? AND status IN ("unpaid","overdue")');
$s->execute([$customer['id']]); $stats['due'] = (float)$s->fetchColumn();

$recent = db()->prepare('SELECT * FROM projects WHERE customer_id = ? ORDER BY created_at DESC LIMIT 4');
$recent->execute([$customer['id']]); $recentProjects = $recent->fetchAll();

$msgs = db()->prepare('SELECT * FROM messages WHERE sender_type = "admin" AND receiver_type = "customer" AND receiver_id = ? ORDER BY created_at DESC LIMIT 3');
$msgs->execute([$customer['id']]); $recentMsgs = $msgs->fetchAll();

$notifs = db()->prepare('SELECT * FROM notifications WHERE user_type = "customer" AND user_id = ? ORDER BY created_at DESC LIMIT 4');
$notifs->execute([$customer['id']]); $recentNotifs = $notifs->fetchAll();

include __DIR__ . '/../includes/customer-header.php';
?>

<div class="stat-grid">
  <div class="stat-card">
    <span class="stat-ico indigo"><?= icon('clipboard') ?></span>
    <div><b><?= $stats['projects'] ?></b><span>Projects</span></div>
  </div>
  <div class="stat-card">
    <span class="stat-ico cyan"><?= icon('inbox') ?></span>
    <div><b><?= $stats['requests'] ?></b><span>Project Requests</span></div>
  </div>
  <div class="stat-card">
    <span class="stat-ico green"><?= icon('wallet') ?></span>
    <div><b><?= money($stats['paid']) ?></b><span>Total Paid</span></div>
  </div>
  <div class="stat-card">
    <span class="stat-ico orange"><?= icon('file') ?></span>
    <div><b><?= money($stats['due']) ?></b><span>Amount Due</span></div>
  </div>
</div>

<div class="grid-2" style="align-items:start;">
  <div>
    <div class="panel">
      <div class="panel-head"><h3>Recent Projects</h3><a href="<?= url('customer/projects.php') ?>" class="btn btn-sm btn-ghost">View All</a></div>
      <div class="table-wrap">
        <table class="table">
          <thead><tr><th>Project</th><th>Status</th><th>Progress</th></tr></thead>
          <tbody>
            <?php foreach ($recentProjects as $p): ?>
              <tr>
                <td><a href="<?= url('customer/project-details.php?id=' . $p['id']) ?>"><?= e($p['title']) ?></a></td>
                <td><?= status_badge($p['status']) ?></td>
                <td>
                  <div class="progress"><div class="progress-bar" style="width:<?= (int)$p['progress'] ?>%"></div></div>
                  <span style="font-size:0.78rem;color:var(--text-3);"><?= (int)$p['progress'] ?>%</span>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$recentProjects): ?><tr><td colspan="3" style="text-align:center;color:var(--text-3);">No projects yet.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head"><h3>Latest Notifications</h3><a href="<?= url('customer/notifications.php') ?>" class="btn btn-sm btn-ghost">View All</a></div>
      <div class="panel-body" style="padding:10px 22px;">
        <?php foreach ($recentNotifs as $n): ?>
          <div class="thread-item" style="border:none;">
            <div class="thread-avatar"><?= icon('bell') ?></div>
            <div class="thread-body">
              <div class="thread-top"><b><?= e($n['title']) ?></b><span class="time"><?= time_ago($n['created_at']) ?></span></div>
              <p><?= e($n['message']) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (!$recentNotifs): ?><p style="color:var(--text-3);padding:14px 0;">No notifications yet.</p><?php endif; ?>
      </div>
    </div>
  </div>

  <div>
    <div class="panel">
      <div class="panel-head"><h3>Welcome, <?= e($customer['name']) ?>!</h3></div>
      <div class="panel-body">
        <p style="margin-bottom:16px;">From here you can track your projects, view invoices, make payments and contact our support team.</p>
        <div class="detail-grid">
          <div class="detail-item"><span>Email</span><b><?= e($customer['email']) ?></b></div>
          <div class="detail-item"><span>Phone</span><b><?= e($customer['phone'] ?: '—') ?></b></div>
          <div class="detail-item"><span>Company</span><b><?= e($customer['company'] ?: '—') ?></b></div>
          <div class="detail-item"><span>Member Since</span><b><?= date('d M Y', strtotime($customer['created_at'])) ?></b></div>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:20px;">
          <a href="<?= url('customer/project-requests.php') ?>" class="btn btn-sm btn-primary">New Project Request</a>
          <a href="<?= url('customer/profile.php') ?>" class="btn btn-sm btn-outline">Edit Profile</a>
        </div>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head"><h3>Recent Messages</h3><a href="<?= url('customer/messages.php') ?>" class="btn btn-sm btn-ghost">View All</a></div>
      <div class="panel-body" style="padding:10px 22px;">
        <?php foreach ($recentMsgs as $m): ?>
          <div class="thread-item" style="border:none;">
            <div class="thread-avatar"><?= e(strtoupper(substr('Admin', 0, 1))) ?></div>
            <div class="thread-body">
              <div class="thread-top"><b><?= e($m['subject']) ?></b><span class="time"><?= time_ago($m['created_at']) ?></span></div>
              <p><?= e($m['message']) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (!$recentMsgs): ?><p style="color:var(--text-3);padding:14px 0;">No messages yet.</p><?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/customer-footer.php'; ?>
