<?php
declare(strict_types=1);
$pageTitle = 'Support';
$activeNav = 'support';
require_once __DIR__ . '/../includes/functions.php';
$customer = require_customer();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'open_ticket') {
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if ($subject === '') $errors[] = 'Please enter a subject.';
        if (strlen($message) < 5) $errors[] = 'Please describe your issue.';
        if (!$errors) {
            $stmt = db()->prepare('INSERT INTO support_tickets (customer_id, subject, message) VALUES (?, ?, ?)');
            $stmt->execute([$customer['id'], $subject, $message]);
            notify(1, 'admin', 'New support ticket', $customer['name'] . ' opened: ' . $subject, 'admin/project-requests.php');
            flash('success', 'Support ticket opened. We will reply soon.');
            redirect('customer/support.php');
        }
    } elseif ($action === 'reply') {
        $ticketId = (int)($_POST['ticket_id'] ?? 0);
        $reply = trim($_POST['message'] ?? '');
        $ts = db()->prepare('SELECT id FROM support_tickets WHERE id = ? AND customer_id = ? AND status != "closed"');
        $ts->execute([$ticketId, $customer['id']]);
        if (!$ts->fetch()) {
            $errors[] = 'Invalid ticket.';
        } elseif (strlen($reply) < 2) {
            $errors[] = 'Please write a reply.';
        } else {
            db()->prepare('INSERT INTO support_replies (ticket_id, sender_type, message) VALUES (?, "customer", ?)')->execute([$ticketId, $reply]);
            db()->prepare('UPDATE support_tickets SET status = "open" WHERE id = ?')->execute([$ticketId]);
            flash('success', 'Reply sent.');
            redirect('customer/support.php?ticket=' . $ticketId);
        }
    }
}

$activeTicketId = (int)($_GET['ticket'] ?? 0);
$stmt = db()->prepare('SELECT * FROM support_tickets WHERE customer_id = ? ORDER BY created_at DESC');
$stmt->execute([$customer['id']]);
$tickets = $stmt->fetchAll();

$ticket = null;
if ($activeTicketId) {
    $ts = db()->prepare('SELECT * FROM support_tickets WHERE id = ? AND customer_id = ?');
    $ts->execute([$activeTicketId, $customer['id']]);
    $ticket = $ts->fetch();
}

include __DIR__ . '/../includes/customer-header.php';
?>

<div class="grid-2" style="align-items:start;">
  <div>
    <div class="panel">
      <div class="panel-head"><h3>Support Tickets</h3></div>
      <div style="padding:6px 0;">
        <?php foreach ($tickets as $t): ?>
          <a href="<?= url('customer/support.php?ticket=' . $t['id']) ?>" style="display:block;">
            <div class="thread-item <?= $activeTicketId === (int)$t['id'] ? 'unread' : '' ?>">
              <div class="thread-avatar"><?= icon('lifebuoy') ?></div>
              <div class="thread-body">
                <div class="thread-top"><b><?= e($t['subject']) ?></b><span class="time"><?= time_ago($t['created_at']) ?></span></div>
                <p><?= e($t['message']) ?></p>
              </div>
              <?= status_badge($t['status']) ?>
            </div>
          </a>
        <?php endforeach; ?>
        <?php if (!$tickets): ?><p style="text-align:center;color:var(--text-3);padding:30px;">No tickets yet.</p><?php endif; ?>
      </div>
    </div>

    <div class="panel">
      <div class="panel-head"><h3>Open New Ticket</h3></div>
      <div class="panel-body">
        <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
        <form method="post" action="<?= url('customer/support.php') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="open_ticket">
          <div class="form-group">
            <label class="form-label" for="subject">Subject</label>
            <input type="text" id="subject" name="subject" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="message">Describe Your Issue</label>
            <textarea id="message" name="message" class="form-control" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary"><?= icon('lifebuoy') ?> Open Ticket</button>
        </form>
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head">
      <h3><?= $ticket ? e($ticket['subject']) : 'Ticket Conversation' ?></h3>
      <?= $ticket ? status_badge($ticket['status']) : '' ?>
    </div>
    <div class="panel-body" style="max-height:460px;overflow-y:auto;background:var(--surface-2);">
      <?php if ($ticket): ?>
        <div class="bubble received"><b style="font-size:0.85rem;">You</b><br><?= nl2br(e($ticket['message'])) ?><span class="bubble-time"><?= time_ago($ticket['created_at']) ?></span></div>
        <?php
        $rp = db()->prepare('SELECT * FROM support_replies WHERE ticket_id = ? ORDER BY created_at ASC');
        $rp->execute([$ticket['id']]);
        foreach ($rp->fetchAll() as $rep): ?>
          <div class="bubble <?= $rep['sender_type'] === 'admin' ? 'received' : 'sent' ?>">
            <b style="font-size:0.85rem;"><?= $rep['sender_type'] === 'admin' ? 'RimonTech Admin' : 'You' ?></b>
            <br><?= nl2br(e($rep['message'])) ?>
            <span class="bubble-time"><?= time_ago($rep['created_at']) ?></span>
          </div>
        <?php endforeach; ?>
        <?php if ($ticket['status'] !== 'closed'): ?>
          <form method="post" action="<?= url('customer/support.php?ticket=' . $ticket['id']) ?>" style="margin-top:14px;">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="reply">
            <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
            <div class="form-group" style="margin-bottom:10px;">
              <textarea name="message" class="form-control" placeholder="Write a reply..." required></textarea>
            </div>
            <button type="submit" class="btn btn-sm btn-primary"><?= icon('arrow') ?> Send Reply</button>
          </form>
        <?php else: ?>
          <p style="color:var(--text-3);margin-top:10px;">This ticket is closed.</p>
        <?php endif; ?>
      <?php else: ?>
        <p style="text-align:center;color:var(--text-3);padding:30px;">Select a ticket to view the conversation.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/customer-footer.php'; ?>
