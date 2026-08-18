<?php
declare(strict_types=1);
require_once __DIR__ . '/functions.php';
$customer = require_customer();
$activeNav = $activeNav ?? '';
$pageTitle = $pageTitle ?? 'Customer Dashboard';
$unread = unread_count('customer', (int)$customer['id']);

$navItems = [
    'dashboard' => ['label' => 'Dashboard', 'url' => 'customer/dashboard.php', 'icon' => 'grid'],
    'projects' => ['label' => 'Projects', 'url' => 'customer/projects.php', 'icon' => 'clipboard'],
    'project-requests' => ['label' => 'Project Requests', 'url' => 'customer/project-requests.php', 'icon' => 'inbox'],
    'messages' => ['label' => 'Messages', 'url' => 'customer/messages.php', 'icon' => 'mail'],
    'invoices' => ['label' => 'Invoices', 'url' => 'customer/invoices.php', 'icon' => 'file'],
    'payments' => ['label' => 'Payments', 'url' => 'customer/payments.php', 'icon' => 'wallet'],
    'documents' => ['label' => 'Documents', 'url' => 'customer/documents.php', 'icon' => 'layers'],
    'notifications' => ['label' => 'Notifications', 'url' => 'customer/notifications.php', 'icon' => 'bell'],
    'support' => ['label' => 'Support', 'url' => 'customer/support.php', 'icon' => 'lifebuoy'],
    'profile' => ['label' => 'Profile', 'url' => 'customer/profile.php', 'icon' => 'user'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
<link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
<link rel="icon" type="image/svg+xml" href="<?= url('assets/icons/favicon.svg') ?>">
</head>
<body class="dashboard-body">

<div class="dashboard">
  <aside class="sidebar" id="sidebar">
    <a href="<?= url('index.php') ?>" class="brand sidebar-brand">
      <span class="brand-mark">R</span>
      <span class="brand-text"><?= e(APP_NAME) ?></span>
    </a>
    <p class="sidebar-caption">Customer Portal</p>
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
      <a href="<?= url('customer/logout.php') ?>" class="sidebar-link"><?= icon('logout') ?> Logout</a>
    </div>
  </aside>

  <div class="dashboard-main">
    <header class="topbar">
      <button class="nav-toggle sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar"><?= icon('menu') ?></button>
      <h1 class="topbar-title"><?= e($pageTitle) ?></h1>
      <div class="topbar-right">
        <a href="<?= url('customer/notifications.php') ?>" class="topbar-icon" title="Notifications">
          <?= icon('bell') ?>
          <?php if ($unread > 0): ?><span class="dot-badge"><?= $unread ?></span><?php endif; ?>
        </a>
        <div class="topbar-user">
          <span class="avatar"><?= e(strtoupper(substr($customer['name'], 0, 1))) ?></span>
          <span class="topbar-user-name"><?= e($customer['name']) ?></span>
        </div>
      </div>
    </header>

    <div class="dashboard-content">
      <?php flash_render(); ?>
