<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Our Process - ' . APP_NAME;
$pageMeta = 'See how RimonTech delivers projects: discover, design, develop, test and launch.';
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / Process</div>
    <h1>How We Work</h1>
    <p>A clear, proven process that keeps you in control at every step.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="timeline">
      <div class="timeline-item">
        <span class="step-num">Step 01</span>
        <h3>Discovery &amp; Planning</h3>
        <p>We discuss your goals, audience and requirements. You receive a clear proposal with scope, timeline and budget.</p>
      </div>
      <div class="timeline-item">
        <span class="step-num">Step 02</span>
        <h3>Design</h3>
        <p>We craft the user experience and visual design. You review mockups and give feedback before any code is written.</p>
      </div>
      <div class="timeline-item">
        <span class="step-num">Step 03</span>
        <h3>Development</h3>
        <p>Our developers build your website with clean, fast and secure code. You get progress updates at every milestone.</p>
      </div>
      <div class="timeline-item">
        <span class="step-num">Step 04</span>
        <h3>Testing &amp; Review</h3>
        <p>We test your site on all devices and browsers, fix issues and share a preview for your final review.</p>
      </div>
      <div class="timeline-item">
        <span class="step-num">Step 05</span>
        <h3>Launch</h3>
        <p>Your website goes live with domain, hosting and SSL configured. We handle the technical launch so you don't have to.</p>
      </div>
      <div class="timeline-item">
        <span class="step-num">Step 06</span>
        <h3>Support &amp; Growth</h3>
        <p>After launch, we keep your website secure, updated and optimized. We are one message away whenever you need us.</p>
      </div>
    </div>

    <div class="section-head" style="margin-top:60px;">
      <h2>Ready to Work Together?</h2>
      <p>Start your project today and get a free consultation.</p>
      <a href="<?= url('contact.php') ?>" class="btn btn-primary" style="margin-top:18px;">Start a Project <?= icon('arrow') ?></a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
