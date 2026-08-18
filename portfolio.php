<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$category = $_GET['category'] ?? '';
$q = trim($_GET['q'] ?? '');

$sql = 'SELECT * FROM portfolio_items WHERE active = 1';
$params = [];
if ($category !== '') {
    $sql .= ' AND category = ?';
    $params[] = $category;
}
if ($q !== '') {
    $sql .= ' AND (title LIKE ? OR tech_stack LIKE ? OR description LIKE ?)';
    $like = '%' . $q . '%';
    $params[] = $like; $params[] = $like; $params[] = $like;
}
$sql .= ' ORDER BY featured DESC, id DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

$cats = db()->query('SELECT DISTINCT category FROM portfolio_items WHERE active = 1 ORDER BY category')->fetchAll(PDO::FETCH_COLUMN);
$downloads = db()->query('SELECT project_id, download_enabled, download_count FROM downloads')->fetchAll();
$downloadMap = [];
foreach ($downloads as $d) {
    $downloadMap[$d['project_id']] = $d;
}

$pageTitle = 'Portfolio - ' . APP_NAME;
$pageMeta = 'Browse recent projects by RimonTech: web applications, business websites, e-commerce stores and more.';
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / Portfolio</div>
    <h1>Our Portfolio</h1>
    <p>Real projects, real results — built for our clients.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <form class="panel" method="get" action="<?= url('portfolio.php') ?>" style="padding:20px;margin-bottom:28px;">
      <div class="form-grid" style="gap:14px;align-items:end;">
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label">Search</label>
          <input type="text" class="form-control" name="q" value="<?= e($q) ?>" placeholder="Search projects, technology...">
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <label class="form-label">Category</label>
          <select class="form-control" name="category">
            <option value="">All Categories</option>
            <?php foreach ($cats as $c): ?>
              <option value="<?= e($c) ?>" <?= $category === $c ? 'selected' : '' ?>><?= e($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group" style="margin-bottom:0;">
          <button type="submit" class="btn btn-primary"><?= icon('search') ?> Filter</button>
        </div>
      </div>
    </form>

    <?php if (!$projects): ?>
      <div class="panel" style="padding:40px;text-align:center;color:var(--text-3);">No projects found. Try a different filter.</div>
    <?php endif; ?>

    <div class="grid-3">
      <?php foreach ($projects as $p): ?>
        <?php $dl = $downloadMap[$p['id']] ?? null; ?>
        <div class="card project-card">
          <div class="project-cover">
            <img src="<?= file_exists(UPLOAD_DIR . '/../' . $p['image']) ? url($p['image']) : fallback_image() ?>" alt="<?= e($p['title']) ?>" loading="lazy">
          </div>
          <div class="project-body">
            <h3><?= e($p['title']) ?></h3>
            <p><?= e($p['tech_stack']) ?></p>
            <div class="project-tags">
              <span><?= e($p['category']) ?></span>
            </div>
            <div class="project-actions">
              <a class="btn btn-sm btn-outline" href="<?= url('portfolio/project.php?slug=' . urlencode($p['slug'])) ?>"><?= icon('eye') ?> Details</a>
              <?php if (!empty($p['github_url'])): ?>
                <a class="btn btn-sm btn-outline" href="<?= e($p['github_url']) ?>" target="_blank" rel="noopener" title="View source on GitHub"><?= icon('github') ?> GitHub</a>
              <?php endif; ?>
              <?php if ($dl && (int)$dl['download_enabled'] === 1): ?>
                <a class="btn btn-sm btn-primary" href="<?= url('portfolio/project.php?slug=' . urlencode($p['slug']) . '#download') ?>"><?= icon('download') ?> Source</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
