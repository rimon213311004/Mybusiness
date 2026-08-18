<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/functions.php';

$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare('SELECT * FROM portfolio_items WHERE slug = ? AND active = 1');
$stmt->execute([$slug]);
$project = $stmt->fetch();
if (!$project) {
    http_response_code(404);
    include __DIR__ . '/../includes/header.php';
    echo '<section class="section"><div class="container"><div class="panel" style="padding:60px;text-align:center;"><h2>Project not found</h2><p>The project you are looking for does not exist.</p><a class="btn btn-primary" href="' . url('portfolio.php') . '">Back to Portfolio</a></div></div></section>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$dlStmt = db()->prepare('SELECT * FROM downloads WHERE project_id = ?');
$dlStmt->execute([$project['id']]);
$download = $dlStmt->fetch() ?: null;

$moreStmt = db()->prepare('SELECT * FROM portfolio_items WHERE active = 1 AND id != ? ORDER BY featured DESC, id DESC LIMIT 3');
$moreStmt->execute([$project['id']]);
$more = $moreStmt->fetchAll();

$pageTitle = $project['title'] . ' - ' . APP_NAME;
$pageMeta = (string)$project['description'];
include __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / <a href="<?= url('portfolio.php') ?>">Portfolio</a> / <?= e($project['title']) ?></div>
    <h1><?= e($project['title']) ?></h1>
    <p><?= e($project['category']) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="panel">
      <div class="project-cover" style="height:340px;">
        <img src="<?= file_exists(UPLOAD_DIR . '/../' . $project['image']) ? url($project['image']) : fallback_image() ?>" alt="<?= e($project['title']) ?>">
      </div>
      <div class="panel-body">
        <div class="detail-grid" style="margin-bottom:26px;">
          <div class="detail-item"><span>Category</span><b><?= e($project['category']) ?></b></div>
          <div class="detail-item"><span>Technology</span><b><?= e($project['tech_stack']) ?></b></div>
          <div class="detail-item"><span>Project Date</span><b><?= date('d M Y', strtotime($project['created_at'])) ?></b></div>
          <div class="detail-item"><span>Status</span><b>Delivered</b></div>
        </div>

        <h3 style="margin-bottom:12px;">Project Overview</h3>
        <p style="margin-bottom:24px;"><?= nl2br(e((string)$project['description'])) ?></p>

        <?php $features = json_features($project['features']); ?>
        <?php if ($features): ?>
          <h3 style="margin-bottom:12px;">Key Features</h3>
          <ul class="feature-list" style="margin-bottom:26px;">
            <?php foreach ($features as $f): ?><li><?= icon('check') ?> <?= e($f) ?></li><?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <?php if (!empty($project['case_study'])): ?>
          <h3 style="margin-bottom:12px;">Case Study</h3>
          <div class="panel" style="background:var(--surface-2);border:none;">
            <div class="panel-body"><p><?= nl2br(e((string)$project['case_study'])) ?></p></div>
          </div>
        <?php endif; ?>

        <div class="project-actions" style="margin-top:26px;">
          <?php if (!empty($project['live_demo_url'])): ?>
            <a class="btn btn-accent" href="<?= e($project['live_demo_url']) ?>" target="_blank" rel="noopener"><?= icon('eye') ?> Live Demo</a>
          <?php endif; ?>
          <?php if (!empty($project['github_url'])): ?>
            <a class="btn btn-outline" href="<?= e($project['github_url']) ?>" target="_blank" rel="noopener"><?= icon('github') ?> View on GitHub</a>
          <?php endif; ?>
          <a class="btn btn-outline" href="<?= url('contact.php') ?>"><?= icon('mail') ?> Get a Similar Project</a>
        </div>
      </div>
    </div>

    <?php if ($download && (int)$download['download_enabled'] === 1): ?>
      <div class="panel" id="download">
        <div class="panel-head"><h3><?= icon('download') ?> Download Source Code</h3></div>
        <div class="panel-body">
          <div class="download-box">
            <span class="dl-ico"><?= icon('download') ?></span>
            <div style="flex:1;min-width:200px;">
              <b><?= e($download['file_name']) ?></b>
              <span class="dl-count">ZIP Archive • Downloaded <?= (int)$download['download_count'] ?> times</span>
            </div>
            <a class="btn btn-primary" href="<?= url('download.php?file=' . urlencode($download['file_name'])) ?>"><?= icon('download') ?> Download ZIP</a>
          </div>
          <p style="margin-top:16px;font-size:0.85rem;color:var(--text-3);">
            This is a sanitized public demo bundle for learning purposes. It contains frontend, backend and database
            examples only — no production credentials, API keys or private customer data.
          </p>
        </div>
      </div>
    <?php elseif ($download && (int)$download['download_enabled'] === 0): ?>
      <div class="alert alert-error">Source code for this project is currently unavailable for download.</div>
    <?php endif; ?>
  </div>
</section>

<section class="section section-alt">
  <div class="container">
    <div class="section-head"><span class="eyebrow">Portfolio</span><h2>More Projects</h2></div>
    <div class="grid-3">
      <?php foreach ($more as $p): ?>
        <div class="card project-card">
          <div class="project-cover"><img src="<?= file_exists(UPLOAD_DIR . '/../' . $p['image']) ? url($p['image']) : fallback_image() ?>" alt="<?= e($p['title']) ?>" loading="lazy"></div>
          <div class="project-body">
            <h3><?= e($p['title']) ?></h3>
            <p><?= e($p['tech_stack']) ?></p>
            <div class="project-actions"><a class="btn btn-sm btn-outline" href="<?= url('portfolio/project.php?slug=' . urlencode($p['slug'])) ?>">View Details</a></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
