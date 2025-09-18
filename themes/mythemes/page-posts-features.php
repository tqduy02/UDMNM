<?php
/**
 * Template Name: Posts Features
 * Template Post Type: page
 * Description: Redirect thẳng vào bài viết đã chọn trong ACF.
 */
if (!defined('ABSPATH')) exit;

// ===== Lấy giá trị từ ACF trên CHÍNH trang hiện tại =====
$page_id  = get_queried_object_id();
$acf_ok   = function_exists('get_field');
$featured = $acf_ok ? get_field('featured_post', $page_id) : 0;

// Chuẩn hoá mọi kiểu trả về (ID | WP_Post | array)
$featured_id = 0;
if ($featured) {
  if (is_array($featured)) {
    $first = reset($featured);
    $featured_id = is_object($first) ? (int)$first->ID : (int)$first;
  } else {
    $featured_id = is_object($featured) ? (int)$featured->ID : (int)$featured;
  }
}

// ===== Map sang bản dịch theo ngôn ngữ hiện tại (Polylang) =====
if ($featured_id > 0 && function_exists('pll_get_post') && function_exists('pll_current_language')) {
  $lang       = pll_current_language('slug'); // 'vi' hoặc 'en'
  $translated = pll_get_post($featured_id, $lang);

  // --- Chọn 1 trong 2 hành vi ---
  // [A] Có fallback: nếu không có bản dịch thì vẫn dùng bài gốc
  if ($translated) $featured_id = (int)$translated;

  // [B] Không fallback (BẮT BUỘC đúng ngôn ngữ): bỏ comment 3 dòng dưới và xoá khối [A] nếu muốn
  /*
  if ($translated) {
    $featured_id = (int)$translated;
  } else {
    $featured_id = 0; // không redirect
  }
  */
}

// ===== Redirect nếu có bài =====
if ($featured_id > 0) {
  $url = get_permalink($featured_id);
  if ($url && !headers_sent()) {
    wp_safe_redirect($url, 302);
    exit;
  }
}

// ===== Nếu chưa chọn gì hoặc không có bài phù hợp → thông báo =====
get_header(); ?>

<section class="posts-features py-5">
  <div class="container">
    <h1 class="h4 fw-bold"><?php echo esc_html__('Featured Post', 'mythemes'); ?></h1>
    <p class="text-muted">
      <?php
      printf(
        esc_html__('No featured post selected (or no translation in this language). Please open this page in Admin and set the ACF field: %s.', 'mythemes'),
        'Featured Post'
      );
      ?>
    </p>
  </div>
</section>

<?php get_footer();
