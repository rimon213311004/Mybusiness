<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$faqs = [
    ['q' => 'How long does it take to build a website?', 'a' => 'A standard business website typically takes 2–4 weeks. E-commerce stores take 4–6 weeks, and custom web applications range from 6–12 weeks depending on complexity. We always share a clear timeline before starting.'],
    ['q' => 'How much does a website cost?', 'a' => 'Our packages start from BDT 15,000. The final price depends on the number of pages, features and complexity. You can view our transparent pricing or request a custom quote.'],
    ['q' => 'Do I get the source code of my website?', 'a' => 'Yes. Once your project is delivered and fully paid, you own everything — the source code, design files and domain credentials. For selected public demo projects, source code is also available for download from our portfolio.'],
    ['q' => 'Do you provide hosting and domain?', 'a' => 'We help you choose the right hosting and domain, and we can manage the setup for you. Hosting costs are billed separately by the hosting provider.'],
    ['q' => 'Can I update the website myself after launch?', 'a' => 'Yes. We can integrate a content management system (CMS) so you can update text, images and products yourself. We also provide training after launch.'],
    ['q' => 'What happens after my website is launched?', 'a' => 'We provide free support for 30 days after launch. After that, you can choose our maintenance plan for updates, backups, security and priority support.'],
    ['q' => 'Do you accept custom project requests?', 'a' => 'Absolutely. If you have a unique requirement, submit a project request and we will respond within 24 hours with a proposal.'],
    ['q' => 'How do I make payments?', 'a' => 'We accept bank transfer, bKash, Nagad and other major payment methods. Payments are typically split into milestones — 50% advance and 50% on delivery.'],
];

$pageTitle = 'FAQ - ' . APP_NAME;
$pageMeta = 'Frequently asked questions about RimonTech services, pricing, timelines and support.';
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="crumbs"><a href="<?= url('index.php') ?>">Home</a> / FAQ</div>
    <h1>Frequently Asked Questions</h1>
    <p>Answers to the questions we hear most often.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="faq-list">
      <?php foreach ($faqs as $i => $faq): ?>
        <div class="faq-item <?= $i === 0 ? 'open' : '' ?>">
          <div class="faq-q"><?= e($faq['q']) ?><?= icon('plus') ?></div>
          <div class="faq-a"><p><?= e($faq['a']) ?></p></div>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="section-head" style="margin-top:56px;">
      <h2>Still Have Questions?</h2>
      <p>Get in touch and we will answer within 24 hours.</p>
      <a href="<?= url('contact.php') ?>" class="btn btn-primary" style="margin-top:18px;">Contact Us <?= icon('arrow') ?></a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
