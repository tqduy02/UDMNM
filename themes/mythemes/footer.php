<?php if (!defined('ABSPATH')) exit; ?>

<footer class="site-footer bg-dark text-white mt-5" role="contentinfo">
  <div class="container py-5 py-lg-6">

    <!-- Newsletter -->
    <div class="text-center mb-5">
      <h2 class="fw-bold display-6 mb-2">
        <?php echo esc_html__('Get the best blog stories into your inbox!', 'mythemes'); ?>
      </h2>
      <p class="text-white-50 mb-4">
        <?php echo esc_html__('Sign up for free and be the first to get notified about new posts.', 'mythemes'); ?>
      </p>

      <!-- Form Newsletter (Polylang-aware CF7) -->
      <div class="newsletter-wrap mx-auto" aria-label="<?php echo esc_attr__('Newsletter signup', 'mythemes'); ?>">
        <?php
        // Map ID/title cho từng ngôn ngữ (điền ID thực tế của form EN/VI)
        $lang = function_exists('pll_current_language') ? pll_current_language('slug') : 'en';
        $cf7_map = [
          'en' => ['id' => '48faa3a', 'title' => __('Newsletter', 'mythemes')],
          'vi' => ['id' => 'd2edc2c', 'title' => __('Newsletter VI', 'mythemes')], // ví dụ: 'Bản tin'
        ];
        $cfg = isset($cf7_map[$lang]) ? $cf7_map[$lang] : $cf7_map['en'];

        echo do_shortcode( sprintf(
          '[contact-form-7 id="%s" title="%s"]',
          esc_attr($cfg['id']),
          esc_attr($cfg['title'])
        ) );
        ?>
      </div>
    </div>

  </div>

  <!-- Footer bottom (copyright) -->
  <div class="footer-bottom border-top border-secondary">
    <div class="container">
      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-center py-3 small text-white-50 gap-2">
        <div>
          <?php
          /* translators: 1: year, 2: site name, 3: site tagline */
          printf(
            esc_html__('© %1$s %2$s — %3$s.', 'mythemes'),
            esc_html( date_i18n('Y') ),
            esc_html( get_bloginfo('name') ),
            esc_html( get_bloginfo('description') )
          );
          ?>
        </div>
        <div>
          <?php
          /* translators: %s: designer/agency name */
          printf(
            esc_html__('Design by %s — All rights reserved.', 'mythemes'),
            'AssiaGroupe'
          );
          ?>
        </div>
      </div>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
