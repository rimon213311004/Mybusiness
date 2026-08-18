<?php
declare(strict_types=1);
$pageTitle = 'Settings';
$activeNav = 'settings';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$errors = [];
$keys = ['contact_email', 'contact_phone', 'contact_address', 'homepage_tagline', 'about_text', 'mission_text', 'vision_text', 'facebook_url', 'linkedin_url', 'twitter_url', 'github_url', 'google_maps'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($keys as $key) {
        $value = trim((string)($_POST[$key] ?? ''));
        $stmt = db()->prepare('SELECT id FROM settings WHERE setting_key = ?');
        $stmt->execute([$key]);
        if ($stmt->fetch()) {
            db()->prepare('UPDATE settings SET setting_value = ? WHERE setting_key = ?')->execute([$value, $key]);
        } else {
            db()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)')->execute([$key, $value]);
        }
    }
    flash('success', 'Settings saved successfully.');
    redirect('admin/settings.php');
}

$settings = [];
foreach (db()->query('SELECT * FROM settings') as $row) {
    $settings[$row['setting_key']] = (string)$row['setting_value'];
}

include __DIR__ . '/../includes/admin-header.php';
?>

<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

<form method="post" action="<?= url('admin/settings.php') ?>">
  <?= csrf_field() ?>
  <div class="panel">
    <div class="panel-head"><h3>Contact Information</h3></div>
    <div class="panel-body">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Contact Email</label>
          <input type="email" name="contact_email" class="form-control" value="<?= e($settings['contact_email'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Contact Phone</label>
          <input type="text" name="contact_phone" class="form-control" value="<?= e($settings['contact_phone'] ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Office Address</label>
        <input type="text" name="contact_address" class="form-control" value="<?= e($settings['contact_address'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Google Maps URL</label>
        <input type="url" name="google_maps" class="form-control" value="<?= e($settings['google_maps'] ?? '') ?>">
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>Homepage & Branding</h3></div>
    <div class="panel-body">
      <div class="form-group">
        <label class="form-label">Homepage Tagline</label>
        <input type="text" name="homepage_tagline" class="form-control" value="<?= e($settings['homepage_tagline'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label class="form-label">About Text</label>
        <textarea name="about_text" class="form-control"><?= e($settings['about_text'] ?? '') ?></textarea>
      </div>
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Mission</label>
          <textarea name="mission_text" class="form-control" style="min-height:90px;"><?= e($settings['mission_text'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Vision</label>
          <textarea name="vision_text" class="form-control" style="min-height:90px;"><?= e($settings['vision_text'] ?? '') ?></textarea>
        </div>
      </div>
    </div>
  </div>

  <div class="panel">
    <div class="panel-head"><h3>Social Links</h3></div>
    <div class="panel-body">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Facebook</label>
          <input type="url" name="facebook_url" class="form-control" value="<?= e($settings['facebook_url'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">LinkedIn</label>
          <input type="url" name="linkedin_url" class="form-control" value="<?= e($settings['linkedin_url'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Twitter</label>
          <input type="url" name="twitter_url" class="form-control" value="<?= e($settings['twitter_url'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">GitHub</label>
          <input type="url" name="github_url" class="form-control" value="<?= e($settings['github_url'] ?? '') ?>">
        </div>
      </div>
    </div>
  </div>

  <button type="submit" class="btn btn-primary"><?= icon('check') ?> Save All Settings</button>
</form>

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
