<?php
if (!defined('ABSPATH')) exit;

/**
 * Enqueue CSS/JS dùng chung
 */
add_action('wp_enqueue_scripts', function () {
  // CSS libs
  wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], '5.3.3');
  wp_enqueue_style('fa',        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', [], '6.5.2');
  wp_enqueue_style('swiper',    'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], '11');

  // CSS theme
  wp_enqueue_style('mythemes-style', get_stylesheet_uri(), ['bootstrap'], MYT_VERSION);
  wp_enqueue_style('mythemes-app',   MYT_URI . '/assets/css/app.css', ['bootstrap'], MYT_VERSION);

  // JS libs
  wp_enqueue_script('swiper',   'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], '11', true);
  wp_enqueue_script('bootstrap','https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', [], '5.3.3', true);

  // JS theme
  wp_enqueue_script('mythemes-app', MYT_URI . '/assets/js/app.js', [], MYT_VERSION, true);
});
