<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$services = db()->query('SELECT * FROM services WHERE active = 1 ORDER BY id LIMIT 6')->fetchAll();
$featuredProjects = db()->query('SELECT * FROM portfolio_items WHERE active = 1 AND featured = 1 ORDER BY id DESC LIMIT 3')->fetchAll();
$solutions = db()->query('SELECT * FROM solutions WHERE active = 1 ORDER BY id LIMIT 8')->fetchAll();
$testimonials = db()->query('SELECT * FROM testimonials WHERE active = 1 ORDER BY id DESC LIMIT 3')->fetchAll();
$plans = db()->query('SELECT * FROM pricing_plans WHERE active = 1 ORDER BY id LIMIT 4')->fetchAll();

$pageTitle = APP_NAME . ' - ' . APP_TAGLINE;
$pageMeta = 'Professional web design and development agency in Dhaka. Websites, e-commerce, web applications, SEO and maintenance.';
include __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container hero-inner">
    <div>
      <h1>We Build <span class="grad">Fast, Beautiful</span> Websites That Grow Your Business</h1>
      <p class="lead"><?= e(setting('homepage_tagline', APP_TAGLINE)) ?> From landing pages to full e-commerce platforms — designed, developed and delivered by RimonTech.</p>
      <div class="hero-actions">
        <a href="<?= url('contact.php') ?>" class="btn btn-accent">Start a Project <?= icon('arrow') ?></a>
        <a href="<?= url('portfolio.php') ?>" class="btn btn-outline btn-light">View Our Work</a>
      </div>
      <div class="hero-stats">
        <div class="stat"><b>50+</b><span>Projects Delivered</span></div>
        <div class="stat"><b>40+</b><span>Happy Clients</span></div>
        <div class="stat"><b>7+</b><span>Years Experience</span></div>
        <div class="stat"><b>24/7</b><span>Support</span></div>
      </div>
    </div>
    <div class="hero-visual">
      <div class="hero-card">
        <h3>Recent Project</h3>
        <div class="mock-row">
          <span class="mock-ico"><?= icon('clipboard') ?></span>
          <div><b>Job Tracking Application</b><span>Next.js • Node.js • MongoDB</span></div>
          <span class="mock-pill">Live</span>
        </div>
        <div class="mock-row">
          <span class="mock-ico"><?= icon('cart') ?></span>
          <div><b>Sadia Fashion Store</b><span>Vue.js • Laravel • bKash</span></div>
          <span class="mock-pill">Live</span>
        </div>
        <div class="mock-row">
          <span class="mock-ico"><?= icon('shield') ?></span>
          <div><b>Uttara Clinic Portal</b><span>React • Node.js</span></div>
          <span class="mock-pill">Live</span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Our Services</span>
      <h2>Everything You Need to Succeed Online</h2>
      <p>Complete web solutions from design to deployment — all under one roof.</p>
    </div>
    <div class="grid-3">
      <?php foreach ($services as $s): ?>
        <div class="card">
          <div class="card-icon"><?= icon($s['icon']) ?></div>
          <h3><?= e($s['title']) ?></h3>
          <p><?= e($s['short_desc']) ?></p>
          <a class="card-link" href="<?= url('service/' . $s['slug'] . '.php') ?>">Learn more <?= icon('arrow') ?></a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Featured Work</span>
      <h2>Projects We're Proud Of</h2>
      <p>A few of the websites and applications we have delivered recently.</p>
    </div>
    <div class="grid-3">
      <?php foreach ($featuredProjects as $p): ?>
        <div class="card project-card">
          <div class="project-cover">
            <img src="<?= file_exists(UPLOAD_DIR . '/../' . $p['image']) ? url($p['image']) : fallback_image() ?>" alt="<?= e($p['title']) ?>" loading="lazy">
          </div>
          <div class="project-body">
            <h3><?= e($p['title']) ?></h3>
            <p><?= e($p['tech_stack']) ?></p>
            <div class="project-tags">
              <span><?= e($p['category']) ?></span>
              <?php foreach (array_slice(json_features($p['features']), 0, 2) as $f): ?><span><?= e($f) ?></span><?php endforeach; ?>
            </div>
            <div class="project-actions">
              <a class="btn btn-sm btn-outline" href="<?= url('portfolio/project.php?slug=' . urlencode($p['slug'])) ?>">Details</a>
              <?php if (!empty($p['live_demo_url'])): ?><a class="btn btn-sm btn-ghost" href="<?= e($p['live_demo_url']) ?>" target="_blank" rel="noopener">Live Demo</a><?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <div style="text-align:center;margin-top:34px;">
      <a href="<?= url('portfolio.php') ?>" class="btn btn-primary">View All Projects <?= icon('arrow') ?></a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Industry Solutions</span>
      <h2>Tailored Solutions for Your Business</h2>
      <p>Ready-made website packages designed for your industry.</p>
    </div>
    <div class="grid-4">
      <?php foreach ($solutions as $sol): ?>
        <div class="card">
          <div class="card-icon"><?= icon($sol['icon']) ?></div>
          <h3><?= e($sol['title']) ?></h3>
          <p><?= e($sol['short_desc']) ?></p>
          <a class="card-link" href="<?= url('solutions/' . $sol['slug'] . '.php') ?>">View <?= icon('arrow') ?></a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-alt">
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

<section class="section">
  <div class="container">
    <div class="section-head">
      <span class="eyebrow">Transparent Pricing</span>
      <h2>Simple Plans, Honest Pricing</h2>
      <p>No hidden costs. Pick a plan and get started today.</p>
    </div>
    <div class="grid-4">
      <?php foreach ($plans as $plan): ?>
        <div class="card price-card <?= $plan['highlighted'] ? 'highlighted' : '' ?>">
          <h3><?= e($plan['title']) ?></h3>
          <p><?= e($plan['description']) ?></p>
          <div class="price"><?= money($plan['price']) ?> <small>/ <?= e($plan['period']) ?></small></div>
          <ul class="feature-list">
            <?php foreach (json_features($plan['features']) as $f): ?><li><?= icon('check') ?> <?= e($f) ?></li><?php endforeach; ?>
          </ul>
          <a href="<?= url('contact.php') ?>" class="btn btn-<?= $plan['highlighted'] ? 'primary' : 'outline' ?> btn-block" style="margin-top:20px;">Get Started</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="cta-band">
      <h2>Ready to Start Your Project?</h2>
      <p>Tell us about your idea and get a free consultation within 24 hours.</p>
      <a href="<?= url('contact.php') ?>" class="btn btn-light">Get Free Consultation <?= icon('arrow') ?></a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
