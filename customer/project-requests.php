<?php
declare(strict_types=1);
$pageTitle = 'Project Requests';
$activeNav = 'project-requests';
require_once __DIR__ . '/../includes/functions.php';
$customer = require_customer();

$errors = [];
$services = db()->query('SELECT * FROM services WHERE active = 1')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $serviceType = trim($_POST['service_type'] ?? '');
    $budget = trim($_POST['budget'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($serviceType === '') $errors[] = 'Please select a service type.';
    if (strlen($message) < 10) $errors[] = 'Please describe your project (at least 10 characters).';

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO project_requests (customer_id, name, email, phone, service_type, budget, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, "new")');
        $stmt->execute([$customer['id'], $customer['name'], $customer['email'], $customer['phone'], $serviceType, $budget ?: null, $message]);
        notify(1, 'admin', 'New project request', $customer['name'] . ' submitted a new ' . $serviceType . ' request.', 'admin/project-requests.php');
        flash('success', 'Project request submitted. We will contact you within 24 hours.');
        redirect('customer/project-requests.php');
    }
}

$stmt = db()->prepare('SELECT * FROM project_requests WHERE customer_id = ? ORDER BY created_at DESC');
$stmt->execute([$customer['id']]);
$requests = $stmt->fetchAll();

include __DIR__ . '/../includes/customer-header.php';
?>

<div class="grid-2" style="align-items:start;">
  <div>
    <div class="panel">
      <div class="panel-head"><h3>Your Requests</h3></div>
      <div class="table-wrap">
        <table class="table">
          <thead><tr><th>Service</th><th>Status</th><th>Date</th></tr></thead>
          <tbody>
            <?php foreach ($requests as $r): ?>
              <tr>
                <td><b><?= e($r['service_type']) ?></b><br><span style="color:var(--text-3);font-size:0.82rem;"><?= e($r['budget'] ?: 'Budget: N/A') ?></span></td>
                <td><?= status_badge($r['status']) ?></td>
                <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$requests): ?><tr><td colspan="3" style="text-align:center;color:var(--text-3);padding:24px;">No requests yet. Submit your first one.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>Submit New Request</h3></div>
    <div class="panel-body">
      <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
      <form method="post" action="<?= url('customer/project-requests.php') ?>">
        <?= csrf_field() ?>
        <div class="form-group">
          <label class="form-label" for="service_type">Service Type *</label>
          <select id="service_type" name="service_type" class="form-control" required>
            <option value="">Select a service...</option>
            <?php foreach ($services as $sv): ?><option value="<?= e($sv['title']) ?>"><?= e($sv['title']) ?></option><?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="budget">Budget</label>
          <select id="budget" name="budget" class="form-control">
            <option value="">Select budget range...</option>
            <option value="BDT 10,000 - 15,000">BDT 10,000 - 15,000</option>
            <option value="BDT 15,000 - 35,000">BDT 15,000 - 35,000</option>
            <option value="BDT 35,000 - 50,000">BDT 35,000 - 50,000</option>
            <option value="BDT 50,000 - 80,000">BDT 50,000 - 80,000</option>
            <option value="BDT 80,000+">BDT 80,000+</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="message">Project Description *</label>
          <textarea id="message" name="message" class="form-control" placeholder="Tell us about your project, goals and timeline..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary"><?= icon('plus') ?> Submit Request</button>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/customer-footer.php'; ?>
