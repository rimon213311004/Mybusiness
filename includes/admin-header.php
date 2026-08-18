<?php
declare(strict_types=1);
require_once __DIR__ . '/functions.php';
$admin = require_admin();
$activeNav = $activeNav ?? '';
$pageTitle = $pageTitle ?? 'Admin Dashboard';
$unread = unread_count('admin', (int)$admin['id']);

$navItems = [
    'dashboard' => ['label' => 'Dashboard', 'url' => 'admin/dashboard.php', 'icon' => 'grid'],
    'customers' => ['label' => 'Customers', 'url' => 'admin/customers.php', 'icon' => 'users'],
    'project-requests' => ['label' => 'Project Requests', 'url' => 'admin/project-requests.php', 'icon' => 'inbox'],
    'projects' => ['label' => 'Projects', 'url' => 'admin/projects.php', 'icon' => 'clipboard'],
    'services' => ['label' => 'Services', 'url' => 'admin/services.php', 'icon' => 'layers'],
    'solutions' => ['label' => 'Solutions', 'url' => 'admin/solutions.php', 'icon' => 'cpu'],
    'portfolio' => ['label' => 'Portfolio', 'url' => 'admin/portfolio.php', 'icon' => 'eye'],
    'pricing' => ['label' => 'Pricing', 'url' => 'admin/pricing.php', 'icon' => 'tag'],
    'testimonials' => ['label' => 'Testimonials', 'url' => 'admin/testimonials.php', 'icon' => 'quote'],
    'messages' => ['label' => 'Messages', 'url' => 'admin/messages.php', 'icon' => 'mail'],
    'invoices' => ['label' => 'Invoices', 'url' => 'admin/invoices.php', 'icon' => 'file'],
    'payments' => ['label' => 'Payments', 'url' => 'admin/payments.php', 'icon' => 'wallet'],
    'documents' => ['label' => 'Documents', 'url' => 'admin/documents.php', 'icon' => 'layers'],
    'downloads' => ['label' => 'Downloads', 'url' => 'admin/downloads.php', 'icon' => 'download'],
    'notifications' => ['label' => 'Notifications', 'url' => 'admin/notifications.php', 'icon' => 'bell'],
    'settings' => ['label' => 'Settings', 'url' => 'admin/settings.php', 'icon' => 'settings'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?> Admin</title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
<link rel="icon" type="image/svg+xml" href="<?= url('assets/icons/favicon.svg') ?>">
</head>
<body class="dashboard-body">

<div class="dashboard admin-dashboard">
  <aside class="sidebar" id="sidebar">
    <a href="<?= url('admin/dashboard.php') ?>" class="brand sidebar-brand">
      <span class="brand-mark">R</span>
      <span class="brand-text"><?= e(APP_NAME) ?></span>
    </a>
    <p class="sidebar-caption">Admin Panel</p>
    <nav class="sidebar-nav">
      <?php foreach ($navItems as $key => $item): ?>
        <?php $extra = $key === 'notifications' && $unread > 0 ? '<span class="dot-badge">' . $unread . '</span>' : ''; ?>
        <a href="<?= url($item['url']) ?>" class="sidebar-link <?= $activeNav === $key ? 'active' : '' ?>">
          <span class="sidebar-icon"><?= icon($item['icon']) ?></span>
          <?= e($item['label']) ?><?= $extra ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
      <a href="<?= url('index.php') ?>" class="sidebar-link"><?= icon('eye') ?> View Website</a>
      <a href="<?= url('admin/logout.php') ?>" class="sidebar-link"><?= icon('logout') ?> Logout</a>
    </div>
  </aside>

  <div class="dashboard-main">
    <header class="topbar">
      <button class="nav-toggle sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar"><?= icon('menu') ?></button>
      <h1 class="topbar-title"><?= e($pageTitle) ?></h1>
      <div class="topbar-right">
        <a href="<?= url('admin/notifications.php') ?>" class="topbar-icon" title="Notifications">
          <?= icon('bell') ?>
          <?php if ($unread > 0): ?><span class="dot-badge"><?= $unread ?></span><?php endif; ?>
        </a>
        <div class="topbar-user">
          <span class="avatar"><?= e(strtoupper(substr($admin['name'], 0, 1))) ?></span>
          <span class="topbar-user-name"><?= e($admin['name']) ?></span>
        </div>
      </div>
    </header>

    <div class="dashboard-content">
      <?php flash_render(); ?>
