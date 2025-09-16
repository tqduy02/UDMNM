<?php
if (!defined('ABSPATH')) exit;

/**
 * Khởi tạo theme (support, menu)
 */
add_action('after_setup_theme', function () {
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', ['search-form','gallery','caption']);

  register_nav_menus([
    'primary' => __('Primary Menu','mythemes'),
  ]);
});
