<?php
if (!defined('ABSPATH')) exit;

/**
 * Customizer: chọn 1 bài viết Featured cho trang chủ (không cần ACF)
 */
add_action('customize_register', function (WP_Customize_Manager $wp_customize) {

  $wp_customize->add_section('homepage_featured', [
    'title'       => __('Homepage Featured', 'mythemes'),
    'priority'    => 30,
    'description' => __('Chọn 1 bài viết làm Featured ở đầu trang chủ', 'mythemes'),
  ]);

  $wp_customize->add_setting('featured_post_id', [
    'default'           => 0,
    'sanitize_callback' => 'absint',
  ]);

  $posts = get_posts(['post_type'=>'post','numberposts'=>50,'orderby'=>'date','order'=>'DESC']);
  $choices = [0 => __('— Auto: bài mới nhất —','mythemes')];
  foreach ($posts as $p) {
    $choices[$p->ID] = wp_html_excerpt($p->post_title, 60);
  }

  $wp_customize->add_control('featured_post_id', [
    'section' => 'homepage_featured',
    'label'   => __('Featured Post','mythemes'),
    'type'    => 'select',
    'choices' => $choices,
  ]);
});
