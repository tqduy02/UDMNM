<?php if (!defined('ABSPATH')) exit; ?>

<footer class="site-footer bg-dark text-white mt-5">
  <div class="container py-5 py-lg-6">

    <!-- Newsletter -->
    <div class="text-center mb-5">
      <h2 class="fw-bold display-6 mb-2">Get the best blog stories into your inbox !</h2>
      <p class="text-white-50 mb-4">Sign up for free and be the first to get notified about new posts.</p>

      <!-- Form Newsletter bằng Contact Form 7 -->
      <div class="newsletter-wrap mx-auto">
        <?php echo do_shortcode('[contact-form-7 id="48faa3a" title="Newsletter"]'); ?>
      </div>
    </div>

  </div>

  <!-- Footer bottom (copyright) -->
  <div class="footer-bottom border-top border-secondary">
    <div class="container">
      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center py-3 small text-white-50 gap-2">
        <div>© <?php echo date('Y'); ?> Being – Personal Blog.</div>
        <div>Design by AssiaGroupe – All rights reserved.</div>
      </div>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
