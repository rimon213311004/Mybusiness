<?php
declare(strict_types=1);
$pageTitle = 'Payments';
$activeNav = 'payments';
require_once __DIR__ . '/../includes/functions.php';
$customer = require_customer();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $invoiceId = (int)($_POST['invoice_id'] ?? 0);
    $method = trim($_POST['method'] ?? '');
    $trxId = trim($_POST['trx_id'] ?? '');
    $amount = (float)($_POST['amount'] ?? 0);

    $invStmt = db()->prepare('SELECT * FROM invoices WHERE id = ? AND customer_id = ? AND status IN ("unpaid","overdue")');
    $invStmt->execute([$invoiceId, $customer['id']]);
    $invoice = $invStmt->fetch();

    if (!$invoice) $errors[] = 'Invalid invoice selected.';
    if ($method === '') $errors[] = 'Please select a payment method.';
    if ($amount <= 0) $errors[] = 'Please enter a valid amount.';
    if ($amount > (float)$invoice['amount']) $errors[] = 'Amount exceeds invoice total.';

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO payments (customer_id, invoice_id, amount, method, trx_id, status) VALUES (?, ?, ?, ?, ?, "pending")');
        $stmt->execute([$customer['id'], $invoiceId, $amount, $method, $trxId ?: null]);
        notify(1, 'admin', 'New payment submitted', $customer['name'] . ' submitted a ' . $method . ' payment of ' . money($amount), 'admin/payments.php');
        flash('success', 'Payment submitted successfully. It will be verified by admin shortly.');
        redirect('customer/payments.php');
    }
}

$invStmt = db()->prepare('SELECT * FROM invoices WHERE customer_id = ? AND status IN ("unpaid","overdue") ORDER BY created_at DESC');
$invStmt->execute([$customer['id']]);
$unpaid = $invStmt->fetchAll();

$stmt = db()->prepare('SELECT * FROM payments WHERE customer_id = ? ORDER BY created_at DESC');
$stmt->execute([$customer['id']]);
$payments = $stmt->fetchAll();

include __DIR__ . '/../includes/customer-header.php';
?>

<div class="grid-2" style="align-items:start;">
  <div class="panel">
    <div class="panel-head"><h3>Make a Payment</h3></div>
    <div class="panel-body">
      <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
      <?php if (!$unpaid): ?>
        <p style="color:var(--text-3);">You have no outstanding invoices. Great job!</p>
      <?php else: ?>
        <form method="post" action="<?= url('customer/payments.php') ?>">
          <?= csrf_field() ?>
          <div class="form-group">
            <label class="form-label" for="invoice_id">Invoice</label>
            <select id="invoice_id" name="invoice_id" class="form-control" required>
              <?php foreach ($unpaid as $inv): ?><option value="<?= $inv['id'] ?>"><?= e($inv['invoice_no']) ?> — <?= money($inv['amount']) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="amount">Amount (BDT)</label>
            <input type="number" id="amount" name="amount" class="form-control" min="1" step="1" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="method">Payment Method</label>
            <select id="method" name="method" class="form-control" required>
              <option value="">Select method...</option>
              <option value="bKash">bKash</option>
              <option value="Nagad">Nagad</option>
              <option value="Bank Transfer">Bank Transfer</option>
              <option value="Card">Card</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="trx_id">Transaction ID</label>
            <input type="text" id="trx_id" name="trx_id" class="form-control" placeholder="e.g. 9HM4K2Q7F1">
          </div>
          <button type="submit" class="btn btn-primary"><?= icon('wallet') ?> Submit Payment</button>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>Payment History</h3></div>
    <div class="table-wrap">
      <table class="table">
        <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach ($payments as $pay): ?>
            <tr>
              <td><?= date('d M Y', strtotime($pay['created_at'])) ?></td>
              <td><?= money($pay['amount']) ?></td>
              <td><?= e($pay['method']) ?></td>
              <td><?= status_badge($pay['status']) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (!$payments): ?><tr><td colspan="4" style="text-align:center;color:var(--text-3);padding:30px;">No payments yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/customer-footer.php'; ?>
