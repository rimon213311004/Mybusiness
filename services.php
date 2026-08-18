<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$services = db()->query('SELECT * FROM services WHERE active = 1 ORDER BY id')->fetchAll();

$pageTitle = 'Services - ' . APP_NAME;
$pageMeta = 'Professional web services: website development, e-commerce, web applications, landing pages, SEO and maintenance.';
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / Services</div>
    <h1>Our Services</h1>
    <p>Complete digital solutions that help your business grow online.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid-3">
      <?php foreach ($services as $s): ?>
        <div class="card">
          <div class="card-icon"><?= icon($s['icon']) ?></div>
          <h3><?= e($s['title']) ?></h3>
          <p><?= e($s['short_desc']) ?></p>
          <ul class="feature-list">
            <?php foreach (array_slice(json_features($s['features']), 0, 3) as $f): ?><li><?= icon('check') ?> <?= e($f) ?></li><?php endforeach; ?>
          </ul>
          <a class="btn btn-sm btn-outline" style="margin-top:8px;" href="<?= url('service/' . $s['slug'] . '.php') ?>">View Details <?= icon('arrow') ?></a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <div class="cta-band">
      <h2>Not Sure Which Service You Need?</h2>
      <p>Talk to us and we will recommend the best solution for your business — free of charge.</p>
      <a href="<?= url('contact.php') ?>" class="btn btn-light">Get Free Advice <?= icon('arrow') ?></a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
