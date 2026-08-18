<?php
declare(strict_types=1);
require_once __DIR__ . '/functions.php';
?>
</main>

<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="<?= url('index.php') ?>" class="brand">
          <span class="brand-mark">R</span>
          <span class="brand-text"><?= e(APP_NAME) ?></span>
        </a>
        <p><?= e(APP_TAGLINE) ?>. We design and build fast, secure and beautiful web solutions for growing businesses.</p>
        <div class="footer-social">
          <a href="<?= e(setting('facebook_url', '#')) ?>" aria-label="Facebook">f</a>
          <a href="<?= e(setting('linkedin_url', '#')) ?>" aria-label="LinkedIn">in</a>
          <a href="<?= e(setting('twitter_url', '#')) ?>" aria-label="Twitter">t</a>
          <a href="<?= e(setting('github_url', '#')) ?>" aria-label="GitHub">gh</a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Services</h4>
        <ul>
          <li><a href="<?= url('service/website-development.php') ?>">Website Development</a></li>
          <li><a href="<?= url('service/ecommerce.php') ?>">E-Commerce</a></li>
          <li><a href="<?= url('service/web-application.php') ?>">Web Applications</a></li>
          <li><a href="<?= url('service/landing-page.php') ?>">Landing Pages</a></li>
          <li><a href="<?= url('service/seo.php') ?>">SEO</a></li>
          <li><a href="<?= url('service/maintenance.php') ?>">Maintenance</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Company</h4>
        <ul>
          <li><a href="<?= url('about.php') ?>">About Us</a></li>
          <li><a href="<?= url('portfolio.php') ?>">Portfolio</a></li>
          <li><a href="<?= url('pricing.php') ?>">Pricing</a></li>
          <li><a href="<?= url('process.php') ?>">Our Process</a></li>
          <li><a href="<?= url('faq.php') ?>">FAQ</a></li>
          <li><a href="<?= url('contact.php') ?>">Contact</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Get In Touch</h4>
        <ul class="footer-contact">
          <li><?= icon('mail') ?> <a href="mailto:<?= e(setting('contact_email', APP_EMAIL)) ?>"><?= e(setting('contact_email', APP_EMAIL)) ?></a></li>
          <li><?= icon('phone') ?> <a href="tel:<?= e(preg_replace('/\s+/', '', setting('contact_phone', APP_PHONE))) ?>"><?= e(setting('contact_phone', APP_PHONE)) ?></a></li>
          <li><?= icon('pin') ?> <?= e(setting('contact_address', APP_ADDRESS)) ?></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. All rights reserved.</p>
      <div class="footer-links">
        <a href="<?= url('customer/login.php') ?>">Customer Login</a>
        <a href="<?= url('admin/login.php') ?>">Admin</a>
      </div>
    </div>
  </div>
</footer>

<script src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>
