<?php
declare(strict_types=1);
/**
 * Shared solution detail renderer.
 * Usage: render_solution('business-website');
 */
require_once __DIR__ . '/../includes/functions.php';

function render_solution(string $slug): void
{
    $stmt = db()->prepare('SELECT * FROM solutions WHERE slug = ? AND active = 1');
    $stmt->execute([$slug]);
    $solution = $stmt->fetch();
    if (!$solution) {
        http_response_code(404);
        include __DIR__ . '/../includes/header.php';
        echo '<section class="section"><div class="container"><div class="panel" style="padding:60px;text-align:center;"><h2>Solution not found</h2><p>The solution you are looking for does not exist.</p><a class="btn btn-primary" href="' . url('solutions.php') . '">View All Solutions</a></div></div></section>';
        include __DIR__ . '/../includes/footer.php';
        return;
    }

    $pageTitle = $solution['title'] . ' - ' . APP_NAME;
    $pageMeta = (string)$solution['short_desc'];
    include __DIR__ . '/../includes/header.php';
    ?>
    <section class="page-hero">
      <div class="container">
        <div class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / <a href="<?= url('solutions.php') ?>">Solutions</a> / <?= e($solution['title']) ?></div>
        <h1><?= e($solution['title']) ?></h1>
        <p><?= e($solution['short_desc']) ?></p>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="grid-2">
          <div>
            <div class="card-icon"><?= icon($solution['icon']) ?></div>
            <h2 style="margin-bottom:16px;">Perfect for Your Industry</h2>
            <p><?= nl2br(e((string)$solution['description'])) ?></p>
            <a href="<?= url('contact.php') ?>" class="btn btn-primary" style="margin-top:20px;">Start This Solution <?= icon('arrow') ?></a>
          </div>
          <div>
            <div class="card" style="background:var(--dark);border:none;color:#e2e8f0;">
              <h3 style="color:#fff;margin-bottom:18px;">Everything Included</h3>
              <ul class="feature-list">
                <?php foreach (json_features($solution['features']) as $f): ?><li><?= icon('check') ?> <?= e($f) ?></li><?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section section-alt">
      <div class="container">
        <div class="section-head">
          <span class="eyebrow">Portfolio</span>
          <h2>Similar Projects We've Built</h2>
        </div>
        <div class="grid-3">
          <?php $related = db()->query('SELECT * FROM portfolio_items WHERE active = 1 ORDER BY featured DESC, id DESC LIMIT 3')->fetchAll(); ?>
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
          <h2>Get Your <?= e($solution['title']) ?></h2>
          <p>Launch within weeks with a fixed price and clear timeline.</p>
          <a href="<?= url('contact.php') ?>" class="btn btn-light">Get a Free Quote <?= icon('arrow') ?></a>
        </div>
      </div>
    </section>
    <?php
    include __DIR__ . '/../includes/footer.php';
}
