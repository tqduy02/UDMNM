<?php
if (!defined('ABSPATH')) exit;

add_action('after_setup_theme', function(){
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', ['search-form','gallery','caption']);
  register_nav_menus([
    'primary' => __('Primary Menu','mythemes'),
  ]);
});

add_action('wp_enqueue_scripts', function () {
  // Bootstrap 5
  wp_enqueue_style('bootstrap','https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',[], '5.3.3');
  // Font Awesome 6
  wp_enqueue_style('fa','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css',[], '6.5.2');
  // SwiperJS (slider)
  wp_enqueue_style(
    'swiper',
    'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
    [],
    '11'
  );
  wp_enqueue_script(
    'swiper',
    'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
    [],
    '11',
    true
  );
  // CSS theme
  wp_enqueue_style('mythemes-style', get_stylesheet_uri(), ['bootstrap'], '1.0');
  wp_enqueue_style('mythemes-app', get_template_directory_uri().'/assets/css/app.css', ['bootstrap'], '1.0');

  // JS Bootstrap (bundle)
  wp_enqueue_script('bootstrap','https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',[], '5.3.3', true);
  // JS theme
  wp_enqueue_script('mythemes-app', get_template_directory_uri().'/assets/js/app.js', [], '1.0', true);
});

// Thêm 'nav-item' vào <li>
add_filter('nav_menu_css_class', function($classes){
  $classes[] = 'nav-item';
  return $classes;
}, 10, 1);

// Thêm 'nav-link' vào <a>
add_filter('nav_menu_link_attributes', function($atts){
  $atts['class'] = (isset($atts['class']) ? $atts['class'].' ' : '').'nav-link';
  return $atts;
}, 10, 1);
// Ẩn admin bar cho mọi user
add_filter('show_admin_bar', '__return_false');
// Tắt tự chèn <p> và <br> của Contact Form 7
add_filter('wpcf7_autop_or_not', '__return_false');

// === Customizer: chọn Featured Post (không cần ACF Pro) ===
add_action('customize_register', function (WP_Customize_Manager $wp_customize) {
  // Tạo section riêng
  $wp_customize->add_section('homepage_featured', [
    'title'       => __('Homepage Featured', 'mythemes'),
    'priority'    => 30,
    'description' => __('Chọn 1 bài viết làm Featured ở đầu trang chủ', 'mythemes'),
  ]);

  // Setting lưu post ID
  $wp_customize->add_setting('featured_post_id', [
    'default'           => 0,
    'sanitize_callback' => 'absint',
  ]);

  // Lấy 50 bài gần nhất làm lựa chọn (bạn đổi số tùy ý)
  $posts = get_posts(['post_type'=>'post','numberposts'=>50,'orderby'=>'date','order'=>'DESC']);
  $choices = [0 => __('— Auto: bài mới nhất —','mythemes')];
  foreach ($posts as $p) { $choices[$p->ID] = wp_html_excerpt($p->post_title, 60); }

  // Control dạng select
  $wp_customize->add_control('featured_post_id', [
    'section' => 'homepage_featured',
    'label'   => __('Featured Post','mythemes'),
    'type'    => 'select',
    'choices' => $choices,
  ]);
});

