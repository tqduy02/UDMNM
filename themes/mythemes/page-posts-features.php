<?php
/**
 * Template Name: Posts Features
 * Description: Redirect thẳng vào bài viết đã chọn trong ACF.
 */
if (!defined('ABSPATH')) exit;

// Lấy post đã chọn từ ACF
$acf_ok = function_exists('get_field');
$featured = $acf_ok ? get_field('featured_post') : 0;

// Nếu chọn 1 bài → redirect
if ($featured instanceof WP_Post) {
  wp_redirect(get_permalink($featured->ID));
  exit;
}

// Nếu chưa chọn → load header + thông báo
get_header(); ?>

<section class="posts-features py-5">
  <div class="container">
    <h1 class="h4 fw-bold">Posts Features</h1>
    <p class="text-muted">Chưa chọn bài viết nổi bật. Hãy mở trang này trong Admin và chọn ở trường <strong>Featured Post</strong>.</p>
  </div>
</section>

<?php get_footer(); ?>
