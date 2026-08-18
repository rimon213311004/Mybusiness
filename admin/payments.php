<?php
declare(strict_types=1);
$pageTitle = 'Payments';
$activeNav = 'payments';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'update') {
        $status = $_POST['status'] ?? '';
        if (in_array($status, ['pending', 'completed', 'failed'], true)) {
            $stmt = db()->prepare('UPDATE payments SET status = ?, paid_at = IF(? = "completed", NOW(), paid_at) WHERE id = ?');
            $stmt->execute([$status, $status, $id]);

            $pay = db()->prepare('SELECT * FROM payments WHERE id = ?');
            $pay->execute([$id]);
            $row = $pay->fetch();
            if ($row && $status === 'completed' && $row['invoice_id']) {
                // Fully mark invoice paid only if total payments cover it
                $inv = db()->prepare('SELECT amount FROM invoices WHERE id = ?');
                $inv->execute([$row['invoice_id']]);
                $invRow = $inv->fetch();
                $paidTotal = db()->prepare('SELECT IFNULL(SUM(amount),0) FROM payments WHERE invoice_id = ? AND status = "completed"');
                $paidTotal->execute([$row['invoice_id']]);
                if ($invRow && (float)$paidTotal->fetchColumn() >= (float)$invRow['amount']) {
                    db()->prepare('UPDATE invoices SET status = "paid" WHERE id = ?')->execute([$row['invoice_id']]);
                }
                notify((int)$row['customer_id'], 'customer', 'Payment confirmed', 'Your payment of ' . money($row['amount']) . ' has been confirmed.', 'customer/payments.php');
            }
            flash('success', 'Payment status updated.');
        }
        redirect('admin/payments.php');
    } elseif ($action === 'delete') {
        db()->prepare('DELETE FROM payments WHERE id = ?')->execute([$id]);
        flash('success', 'Payment deleted.');
        redirect('admin/payments.php');
    }
}

$payments = db()->query('SELECT py.*, c.name AS customer_name, i.invoice_no FROM payments py LEFT JOIN customers c ON c.id = py.customer_id LEFT JOIN invoices i ON i.id = py.invoice_id ORDER BY py.created_at DESC')->fetchAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="panel">
  <div class="panel-head"><h3>Payments (<?= count($payments) ?>)</h3></div>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Date</th><th>Customer</th><th>Invoice</th><th>Amount</th><th>Method</th><th>Trx ID</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($payments as $py): ?>
          <tr>
            <td><?= date('d M Y H:i', strtotime($py['created_at'])) ?></td>
            <td><?= e($py['customer_name'] ?? '—') ?></td>
            <td><?= e($py['invoice_no'] ?? '—') ?></td>
            <td><b><?= money($py['amount']) ?></b></td>
            <td><?= e($py['method'] ?: '—') ?></td>
            <td><?= e($py['trx_id'] ?: '—') ?></td>
            <td><?= status_badge($py['status']) ?></td>
            <td>
              <div class="actions">
                <form method="post" action="<?= url('admin/payments.php') ?>" style="display:flex;gap:6px;align-items:center;margin:0;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="id" value="<?= $py['id'] ?>">
                  <select name="status" class="form-control" style="width:auto;padding:6px 10px;font-size:0.82rem;" onchange="this.form.submit()">
                    <?php foreach (['pending', 'completed', 'failed'] as $st): ?>
                      <option value="<?= $st ?>" <?= $py['status'] === $st ? 'selected' : '' ?>><?= e(ucfirst($st)) ?></option>
                    <?php endforeach; ?>
                  </select>
                </form>
                <form method="post" action="<?= url('admin/payments.php') ?>" style="margin:0;">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $py['id'] ?>">
                  <button type="submit" class="icon-btn danger" title="Delete" onclick="return confirm('Delete this payment?');"><?= icon('trash') ?></button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
