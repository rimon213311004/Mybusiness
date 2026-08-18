<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$solutions = db()->query('SELECT * FROM solutions WHERE active = 1 ORDER BY id')->fetchAll();
$categories = array_unique(array_map(fn($s) => $s['category'], $solutions));

$pageTitle = 'Solutions - ' . APP_NAME;
$pageMeta = 'Industry-specific website solutions: business, e-commerce, school, restaurant, clinic, hotel, portfolio and custom software.';
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / Solutions</div>
    <h1>Industry Solutions</h1>
    <p>Ready-made website packages tailored for your industry.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php foreach ($categories as $cat): ?>
      <div class="section-head" style="margin-bottom:26px;">
        <span class="eyebrow"><?= e($cat) ?></span>
      </div>
      <div class="grid-4" style="margin-bottom:44px;">
        <?php foreach ($solutions as $sol):
            if ($sol['category'] !== $cat) continue; ?>
          <div class="card">
            <div class="card-icon"><?= icon($sol['icon']) ?></div>
            <h3><?= e($sol['title']) ?></h3>
            <p><?= e($sol['short_desc']) ?></p>
            <a class="card-link" href="<?= url('solutions/' . $sol['slug'] . '.php') ?>">View <?= icon('arrow') ?></a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
