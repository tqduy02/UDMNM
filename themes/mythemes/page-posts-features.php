<?php
/**
 * Template Name: Posts Features
 * Description: Redirect thẳng vào bài viết đã chọn trong ACF.
 */
if (!defined('ABSPATH')) exit;

// ===== Lấy post đã chọn từ ACF (hỗ trợ ID | WP_Post | array) =====
$acf_ok   = function_exists('get_field');
$featured = $acf_ok ? get_field('featured_post') : 0;

$featured_id = 0;
if (is_array($featured)) {
  $first = reset($featured);
  if ($first instanceof WP_Post)       { $featured_id = (int) $first->ID; }
  elseif (is_numeric($first))          { $featured_id = (int) $first; }
} elseif ($featured instanceof WP_Post) { $featured_id = (int) $featured->ID; }
elseif (is_numeric($featured))          { $featured_id = (int) $featured; }

// ===== Nếu có bài → điều hướng tới bản dịch ngôn ngữ hiện tại (nếu dùng Polylang) =====
if ($featured_id > 0) {
  if (function_exists('pll_get_post') && function_exists('pll_current_language')) {
    $lang = pll_current_language('slug');
    $translated = pll_get_post($featured_id, $lang);
    if ($translated) { $featured_id = (int) $translated; }
  }

  $url = get_permalink($featured_id);
  if ($url) {
    wp_safe_redirect($url, 302);
    exit;
  }
}

// ===== Nếu chưa chọn gì → hiển thị thông báo =====
get_header(); ?>

<section class="posts-features py-5">
  <div class="container">
    <h1 class="h4 fw-bold"><?php echo esc_html__('Featured Post', 'mythemes'); ?></h1>
    <p class="text-muted">
      <?php
      /* translators: %s: ACF field name */
      printf(
        esc_html__('No featured post selected. Please open this page in Admin and set the ACF field: %s.', 'mythemes'),
        'Featured Post'
      );
      ?>
    </p>
  </div>
</section>

<?php get_footer();
