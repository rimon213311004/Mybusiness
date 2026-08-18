<?php
declare(strict_types=1);
/**
 * Shared service detail renderer.
 * Usage: render_service('website-development');
 */
require_once __DIR__ . '/../includes/functions.php';

function render_service(string $slug): void
{
    $stmt = db()->prepare('SELECT * FROM services WHERE slug = ? AND active = 1');
    $stmt->execute([$slug]);
    $service = $stmt->fetch();
    if (!$service) {
        http_response_code(404);
        include __DIR__ . '/../includes/header.php';
        echo '<section class="section"><div class="container"><div class="panel" style="padding:60px;text-align:center;"><h2>Service not found</h2><p>The service you are looking for does not exist.</p><a class="btn btn-primary" href="' . url('services.php') . '">View All Services</a></div></div></section>';
        include __DIR__ . '/../includes/footer.php';
        return;
    }

    $related = db()->query('SELECT * FROM portfolio_items WHERE active = 1 AND category = "Web Application" ORDER BY id DESC LIMIT 3')->fetchAll();

    $pageTitle = $service['title'] . ' - ' . APP_NAME;
    $pageMeta = (string)$service['short_desc'];
    include __DIR__ . '/../includes/header.php';
    ?>
    <section class="page-hero">
      <div class="container">
        <div class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / <a href="<?= url('services.php') ?>">Services</a> / <?= e($service['title']) ?></div>
        <h1><?= e($service['title']) ?></h1>
        <p><?= e($service['short_desc']) ?></p>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="grid-2">
          <div>
            <div class="card-icon"><?= icon($service['icon']) ?></div>
            <h2 style="margin-bottom:16px;">About This Service</h2>
            <p><?= nl2br(e((string)$service['description'])) ?></p>
            <a href="<?= url('contact.php') ?>" class="btn btn-primary" style="margin-top:20px;">Request This Service <?= icon('arrow') ?></a>
          </div>
          <div>
            <div class="card" style="background:var(--dark);border:none;color:#e2e8f0;">
              <h3 style="color:#fff;margin-bottom:18px;">What's Included</h3>
              <ul class="feature-list">
                <?php foreach (json_features($service['features']) as $f): ?><li><?= icon('check') ?> <?= e($f) ?></li><?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section section-alt">
      <div class="container">
        <div class="section-head">
          <span class="eyebrow">Related Work</span>
          <h2>Recent Projects</h2>
        </div>
        <div class="grid-3">
          <?php foreach ($related as $p): ?>
            <div class="card project-card">
              <div class="project-cover"><img src="<?= file_exists(UPLOAD_DIR . '/../' . $p['image']) ? url($p['image']) : fallback_image() ?>" alt="<?= e($p['title']) ?>" loading="lazy"></div>
              <div class="project-body">
                <h3><?= e($p['title']) ?></h3>
                <p><?= e($p['tech_stack']) ?></p>
                <div class="project-actions">
                  <a class="btn btn-sm btn-outline" href="<?= url('portfolio/project.php?slug=' . urlencode($p['slug'])) ?>">View Details</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="cta-band">
          <h2>Interested in <?= e($service['title']) ?>?</h2>
          <p>Get a free, no-obligation quote for your project today.</p>
          <a href="<?= url('contact.php') ?>" class="btn btn-light">Get a Free Quote <?= icon('arrow') ?></a>
        </div>
      </div>
    </section>
    <?php
    include __DIR__ . '/../includes/footer.php';
}
