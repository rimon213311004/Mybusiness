<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$testimonials = db()->query('SELECT * FROM testimonials WHERE active = 1 ORDER BY id DESC LIMIT 3')->fetchAll();

$pageTitle = 'About Us - ' . APP_NAME;
$pageMeta = 'Learn more about RimonTech, our mission, vision and the team behind the web solutions.';
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / About</div>
    <h1>About RimonTech</h1>
    <p>We turn ideas into digital products that people love to use.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid-2">
      <div>
        <div class="section-head" style="text-align:left;margin:0 0 20px;">
          <span class="eyebrow">Who We Are</span>
          <h2>Your Trusted Web Development Partner</h2>
        </div>
        <p><?= e(setting('about_text')) ?></p>
        <p style="margin-top:16px;">We believe every business — no matter the size — deserves a professional web presence. That is why we combine modern design, clean code and honest communication in every project we take on.</p>
        <div class="grid-2" style="margin-top:28px;">
          <div class="card" style="padding:20px;">
            <div class="card-icon" style="margin-bottom:12px;"><?= icon('shield') ?></div>
            <h3>Our Mission</h3>
            <p><?= e(setting('mission_text')) ?></p>
          </div>
          <div class="card" style="padding:20px;">
            <div class="card-icon" style="margin-bottom:12px;"><?= icon('eye') ?></div>
            <h3>Our Vision</h3>
            <p><?= e(setting('vision_text')) ?></p>
          </div>
        </div>
      </div>
      <div>
        <div class="card" style="background:var(--dark);color:#e2e8f0;border:none;">
          <h3 style="color:#fff;margin-bottom:22px;">Why Choose RimonTech?</h3>
          <ul class="feature-list">
            <li><?= icon('check') ?> <strong>On-time delivery</strong> with clear milestones</li>
            <li><?= icon('check') ?> <strong>Transparent pricing</strong> — no hidden charges</li>
            <li><?= icon('check') ?> <strong>Modern, SEO-friendly code</strong> built to perform</li>
            <li><?= icon('check') ?> <strong>Dedicated support</strong> even after launch</li>
            <li><?= icon('check') ?> <strong>Security-first</strong> development practices</li>
            <li><?= icon('check') ?> <strong>Free consultation</strong> before you commit</li>
          </ul>
          <div class="hero-stats" style="margin-top:30px;">
            <div class="stat"><b style="color:#fff;">50+</b><span>Projects</span></div>
            <div class="stat"><b style="color:#fff;">40+</b><span>Clients</span></div>
            <div class="stat"><b style="color:#fff;">98%</b><span>Satisfaction</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">How We Work</span>
      <h2>Simple, Clear and Reliable</h2>
    </div>
    <div class="grid-4">
      <div class="card"><div class="card-icon"><?= icon('users') ?></div><h3>Understand</h3><p>We listen to your goals and requirements first.</p></div>
      <div class="card"><div class="card-icon"><?= icon('settings') ?></div><h3>Design &amp; Build</h3><p>We craft the design and develop with clean, scalable code.</p></div>
      <div class="card"><div class="card-icon"><?= icon('check') ?></div><h3>Test &amp; Launch</h3><p>Thorough testing on every device before going live.</p></div>
      <div class="card"><div class="card-icon"><?= icon('lifebuoy') ?></div><h3>Support</h3><p>We stay with you after launch with reliable support.</p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Testimonials</span>
      <h2>What Our Clients Say</h2>
    </div>
    <div class="grid-3">
      <?php foreach ($testimonials as $t): ?>
        <div class="card testimonial-card">
          <div class="quote-ico"><?= icon('quote') ?></div>
          <p><?= e($t['content']) ?></p>
          <div style="margin-top:10px;"><?= render_stars((int)$t['rating']) ?></div>
          <div class="testimonial-footer">
            <span class="testimonial-avatar"><?= e(strtoupper(substr($t['customer_name'], 0, 1))) ?></span>
            <div><b><?= e($t['customer_name']) ?></b><span><?= e($t['role']) ?><?= $t['company'] ? ' · ' . e($t['company']) : '' ?></span></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
