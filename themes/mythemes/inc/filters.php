<?php
if (!defined('ABSPATH')) exit;

/**
 * Thêm class nav-item vào <li>
 */
add_filter('nav_menu_css_class', function ($classes) {
  $classes[] = 'nav-item';
  return $classes;
}, 10, 1);

/**
 * Thêm class nav-link vào <a>
 */
add_filter('nav_menu_link_attributes', function ($atts) {
  $atts['class'] = (isset($atts['class']) ? $atts['class'].' ' : '') . 'nav-link';
  return $atts;
}, 10, 1);

/**
 * Ẩn admin bar cho mọi user (tùy chọn)
 */
add_filter('show_admin_bar', '__return_false');

/**
 * Tắt tự chèn <p> và <br> của Contact Form 7 (tùy chọn)
 */
add_filter('wpcf7_autop_or_not', '__return_false');

