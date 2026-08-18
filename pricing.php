<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$plans = db()->query('SELECT * FROM pricing_plans WHERE active = 1 ORDER BY id')->fetchAll();

$pageTitle = 'Pricing - ' . APP_NAME;
$pageMeta = 'Simple and transparent pricing plans for websites, e-commerce and web applications from RimonTech.';
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / Pricing</div>
    <h1>Pricing Plans</h1>
    <p>Honest pricing with no hidden costs. Choose the plan that fits your business.</p>
  </div>
</section>

<section class="section">
  <div class="container">
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

    <div class="panel" style="margin-top:44px;">
      <div class="panel-body">
        <div class="section-head" style="margin-bottom:18px;">
          <h2 style="font-size:1.4rem;">Need Something Custom?</h2>
          <p>Every project is different. Tell us your requirements and get a free, no-obligation quote within 24 hours.</p>
        </div>
        <div style="text-align:center;">
          <a href="<?= url('contact.php') ?>" class="btn btn-primary">Request a Custom Quote <?= icon('arrow') ?></a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
