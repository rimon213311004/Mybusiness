<?php
declare(strict_types=1);
require_once __DIR__ . '/functions.php';

$currentPage = basename($_SERVER['SCRIPT_NAME']);
$activeService = $currentPage === 'services.php';
$activeSolutions = $currentPage === 'solutions.php';
if (str_contains($_SERVER['SCRIPT_NAME'], '/service/')) {
    $activeService = true;
}
if (str_contains($_SERVER['SCRIPT_NAME'], '/solutions/')) {
    $activeSolutions = true;
}
$customer = current_customer();
$unread = $customer ? unread_count('customer', (int)$customer['id']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? APP_NAME . ' - ' . APP_TAGLINE) ?></title>
<meta name="description" content="<?= e($pageMeta ?? APP_TAGLINE) ?>">
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
<link rel="icon" type="image/svg+xml" href="<?= url('assets/icons/favicon.svg') ?>">
</head>
<body>

<header class="site-header" id="siteHeader">
  <nav class="navbar container">
    <a href="<?= url('index.php') ?>" class="brand">
      <span class="brand-mark">R</span>
      <span class="brand-text"><?= e(APP_NAME) ?></span>
    </a>

    <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation"><?= icon('menu') ?></button>

    <div class="nav-menu" id="navMenu">
      <ul class="nav-links">
        <li><a href="<?= url('index.php') ?>" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Home</a></li>
        <li><a href="<?= url('about.php') ?>" class="<?= $currentPage === 'about.php' ? 'active' : '' ?>">About</a></li>
        <li class="has-dropdown">
          <a href="<?= url('services.php') ?>" class="<?= $activeService ? 'active' : '' ?>">Services</a>
          <ul class="dropdown">
            <li><a href="<?= url('service/website-development.php') ?>">Website Development</a></li>
            <li><a href="<?= url('service/ecommerce.php') ?>">E-Commerce</a></li>
            <li><a href="<?= url('service/web-application.php') ?>">Custom Web Application</a></li>
            <li><a href="<?= url('service/landing-page.php') ?>">Landing Page</a></li>
            <li><a href="<?= url('service/seo.php') ?>">SEO &amp; Optimization</a></li>
            <li><a href="<?= url('service/maintenance.php') ?>">Website Maintenance</a></li>
          </ul>
        </li>
        <li class="has-dropdown">
          <a href="<?= url('solutions.php') ?>" class="<?= $activeSolutions ? 'active' : '' ?>">Solutions</a>
          <ul class="dropdown">
            <li><a href="<?= url('solutions/business-website.php') ?>">Business Website</a></li>
            <li><a href="<?= url('solutions/ecommerce-store.php') ?>">E-Commerce</a></li>
            <li><a href="<?= url('solutions/school-website.php') ?>">School Website</a></li>
            <li><a href="<?= url('solutions/restaurant-website.php') ?>">Restaurant Website</a></li>
            <li><a href="<?= url('solutions/clinic-website.php') ?>">Clinic Website</a></li>
            <li><a href="<?= url('solutions/hotel-website.php') ?>">Hotel Website</a></li>
            <li><a href="<?= url('solutions/portfolio-website.php') ?>">Portfolio Website</a></li>
            <li><a href="<?= url('solutions/custom-software.php') ?>">Custom Software</a></li>
          </ul>
        </li>
        <li><a href="<?= url('portfolio.php') ?>" class="<?= $currentPage === 'portfolio.php' || str_contains($_SERVER['SCRIPT_NAME'], '/portfolio/') ? 'active' : '' ?>">Portfolio</a></li>
        <li><a href="<?= url('pricing.php') ?>" class="<?= $currentPage === 'pricing.php' ? 'active' : '' ?>">Pricing</a></li>
        <li><a href="<?= url('process.php') ?>" class="<?= $currentPage === 'process.php' ? 'active' : '' ?>">Process</a></li>
        <li><a href="<?= url('faq.php') ?>" class="<?= $currentPage === 'faq.php' ? 'active' : '' ?>">FAQ</a></li>
        <li><a href="<?= url('contact.php') ?>" class="<?= $currentPage === 'contact.php' ? 'active' : '' ?>">Contact</a></li>
      </ul>
      <div class="nav-actions">
        <?php if ($customer): ?>
          <a href="<?= url('customer/dashboard.php') ?>" class="btn btn-sm btn-ghost">
            Dashboard <?php if ($unread > 0): ?><span class="dot-badge"><?= $unread ?></span><?php endif; ?>
          </a>
          <a href="<?= url('customer/logout.php') ?>" class="btn btn-sm btn-outline">Logout</a>
        <?php else: ?>
          <a href="<?= url('customer/login.php') ?>" class="btn btn-sm btn-ghost">Customer Login</a>
          <a href="<?= url('customer/signup.php') ?>" class="btn btn-sm btn-primary">Sign Up</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
</header>

<main>
<?php flash_render(); ?>
