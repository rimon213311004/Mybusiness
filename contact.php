<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$name = $email = $subject = $message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') $errors[] = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if ($subject === '') $errors[] = 'Please enter a subject.';
    if (strlen($message) < 10) $errors[] = 'Please write a message of at least 10 characters.';

    if (!$errors) {
        $stmt = db()->prepare('INSERT INTO messages (sender_type, sender_id, subject, message, is_read) VALUES ("public", NULL, ?, ?, 0)');
        $stmt->execute([$subject, $message . "\n\nFrom: $name <$email>"]);
        notify(1, 'admin', 'New contact message', $subject . ' from ' . $name, 'admin/messages.php');
        flash('success', 'Thank you! Your message has been sent. We will reply within 24 hours.');
        redirect('contact.php');
    }
    set_old(['name' => $name, 'email' => $email, 'subject' => $subject, 'message' => $message]);
}

$pageTitle = 'Contact - ' . APP_NAME;
$pageMeta = 'Contact RimonTech for a free consultation, project enquiry or support.';
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / Contact</div>
    <h1>Contact Us</h1>
    <p>Tell us about your project and get a free consultation within 24 hours.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid-2">
      <div>
        <h2 style="margin-bottom:22px;">Get In Touch</h2>
        <div class="contact-info-item">
          <div class="card-icon"><?= icon('mail') ?></div>
          <div><b>Email</b><p><a href="mailto:<?= e(setting('contact_email', APP_EMAIL)) ?>"><?= e(setting('contact_email', APP_EMAIL)) ?></a></p></div>
        </div>
        <div class="contact-info-item">
          <div class="card-icon"><?= icon('phone') ?></div>
          <div><b>Phone</b><p><a href="tel:<?= e(preg_replace('/\s+/', '', setting('contact_phone', APP_PHONE))) ?>"><?= e(setting('contact_phone', APP_PHONE)) ?></a></p></div>
        </div>
        <div class="contact-info-item">
          <div class="card-icon"><?= icon('pin') ?></div>
          <div><b>Office</b><p><?= e(setting('contact_address', APP_ADDRESS)) ?></p></div>
        </div>
        <div class="contact-info-item">
          <div class="card-icon"><?= icon('clock') ?></div>
          <div><b>Working Hours</b><p>Saturday – Thursday, 9:00 AM – 7:00 PM</p></div>
        </div>
        <div class="contact-info-item">
          <div class="card-icon"><?= icon('users') ?></div>
          <div><b>Have an Account?</b><p>Existing customers can send messages from the <a href="<?= url('customer/messages.php') ?>">customer portal</a>.</p></div>
        </div>
      </div>

      <div>
        <div class="panel">
          <div class="panel-head"><h3>Send a Message</h3></div>
          <div class="panel-body">
            <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
            <form method="post" action="<?= url('contact.php') ?>">
              <?= csrf_field() ?>
              <div class="form-grid">
                <div class="form-group">
                  <label class="form-label" for="name">Your Name *</label>
                  <input type="text" id="name" name="name" class="form-control" value="<?= old('name', $name) ?>" required>
                </div>
                <div class="form-group">
                  <label class="form-label" for="email">Email Address *</label>
                  <input type="email" id="email" name="email" class="form-control" value="<?= old('email', $email) ?>" required>
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="subject">Subject *</label>
                <input type="text" id="subject" name="subject" class="form-control" value="<?= old('subject', $subject) ?>" required>
              </div>
              <div class="form-group">
                <label class="form-label" for="message">Message *</label>
                <textarea id="message" name="message" class="form-control" required><?= old('message', $message) ?></textarea>
              </div>
              <button type="submit" class="btn btn-primary btn-block"><?= icon('mail') ?> Send Message</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
