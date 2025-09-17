<?php
if (!defined('ABSPATH')) exit;

/**
 * File cấu hình trung tâm:
 * - Khai báo hằng số, kiểm tra môi trường
 * - Require các file chức năng đã tách trong /inc
 */

// ===== HẰNG SỐ & KIỂM TRA CƠ BẢN =====
define('MYT_VERSION', '1.0.0');
define('MYT_DIR', get_template_directory());
define('MYT_URI', get_template_directory_uri());

// ===== REQUIRE: CORE THEME =====
require_once MYT_DIR . '/inc/setup.php';
require_once MYT_DIR . '/inc/enqueue.php';
require_once MYT_DIR . '/inc/filters.php';

// ===== REQUIRE: CUSTOMIZER =====
require_once MYT_DIR . '/inc/customizer/featured.php';

// ===== REQUIRE: ACF LOCAL JSON =====
require_once MYT_DIR . '/inc/acf/acf-json.php';

// ===== REQUIRE: BLOG (ENQUEUE THEO TRANG) =====
require_once MYT_DIR . '/inc/blog/list-assets.php';
require_once MYT_DIR . '/inc/blog/grid-assets.php';

// ===== REQUIRE: AJAX HANDLERS =====
require_once MYT_DIR . '/inc/ajax/loadmore-list.php';
require_once MYT_DIR . '/inc/ajax/loadmore-grid.php';

// ===== REQUIRE: CUSTOM POST TYPES + TAXONOMIES =====
require_once MYT_DIR . '/inc/cpt/project.php';

// ===== Seed sample Projects (fixed: run after taxonomies exist) =====
add_action('init', function () {
  // chạy 1 lần
  if (get_option('seed_projects_done')) return;

  // đảm bảo taxonomy tồn tại
  if (!taxonomy_exists('project_cat') || !taxonomy_exists('tech_stack')) {
    return; // tránh lỗi nếu chưa khai báo CPT/Taxo
  }

  // dữ liệu mẫu
  $projects = [
    [
      'title'   => 'Personal Portfolio Website',
      'excerpt' => 'A personal portfolio website showcasing programming skills, blog posts, and a contact form.',
      'content' => 'A personal portfolio website built with React and TailwindCSS, including About, Projects, Blog, and Contact pages. Integrated a contact form via Node.js API. Designed with SEO optimization and fully responsive across devices.',
      'cats'    => ['Web Development'],
      'techs'   => ['React','TailwindCSS','Node.js'],
    ],
    [
      'title'   => 'Book Management App',
      'excerpt' => 'A CRUD application for managing books with a modern UI and JSON-server API.',
      'content' => 'A React Vite app that displays a list of books, with add, edit, and delete functionality. Book covers are fetched from picsum.photos. All state is managed with React Context API. A practice project focusing on CRUD and state management.',
      'cats'    => ['Application'],
      'techs'   => ['React','JSON-server','Context API'],
    ],
    [
      'title'   => 'Shopee Clone E-commerce',
      'excerpt' => 'An educational e-commerce platform inspired by Shopee.',
      'content' => 'Shopee clone project divided into two parts: Laravel 12 REST API backend and React + Tailwind frontend. Core features include product management, cart, checkout, and admin dashboard. Simplified by excluding chat, coupons, and reviews.',
      'cats'    => ['Web Development'],
      'techs'   => ['Laravel','React','TailwindCSS','MySQL'],
    ],
    [
      'title'   => 'Pizza Store WordPress',
      'excerpt' => 'A pizza store website using WooCommerce, loyalty points plugin, and category slider.',
      'content' => 'A custom WordPress child theme (based on Storefront) for a pizza shop. Features include a hero slider, category row with ACF, loyalty points system, and Elementor templates for cart/checkout. Responsive design with brand color #f4d24f.',
      'cats'    => ['WordPress'],
      'techs'   => ['WordPress','WooCommerce','ACF','Elementor'],
    ],
    [
      'title'   => 'Online Course Platform',
      'excerpt' => 'A Udemy-like website for publishing and managing online courses.',
      'content' => 'An online course platform with React + Tailwind frontend and Node.js/Express backend. Instructors can add courses, manage lessons, while students can purchase and watch in their dashboard. Includes a Quill rich text editor for content.',
      'cats'    => ['Application'],
      'techs'   => ['React','Node.js','TailwindCSS','Quill'],
    ],
  ];

  // hàm tạo term nếu chưa có
  $ensure_terms = function(array $names, string $taxonomy) {
    $term_ids = [];
    foreach ($names as $name) {
      $exists = term_exists($name, $taxonomy);
      if ($exists && !is_wp_error($exists)) {
        $term_ids[] = (int)($exists['term_id'] ?? $exists);
      } else {
        $new = wp_insert_term($name, $taxonomy);
        if (!is_wp_error($new)) {
          $term_ids[] = (int)$new['term_id'];
        }
      }
    }
    return $term_ids;
  };

  foreach ($projects as $p) {
    // tạo terms trước
    $cat_ids  = $ensure_terms($p['cats'],  'project_cat');
    $tech_ids = $ensure_terms($p['techs'], 'tech_stack');

    // tạo bài project
    $post_id = wp_insert_post([
      'post_type'    => 'project',
      'post_status'  => 'publish',
      'post_title'   => $p['title'],
      'post_excerpt' => $p['excerpt'],
      'post_content' => $p['content'],
    ]);

    if ($post_id && !is_wp_error($post_id)) {
      // gán taxonomy
      if (!empty($cat_ids))  wp_set_object_terms($post_id, $cat_ids,  'project_cat', false);
      if (!empty($tech_ids)) wp_set_object_terms($post_id, $tech_ids, 'tech_stack', false);
    }
  }

  update_option('seed_projects_done', true);
}, 20); // priority 20 để chắc chắn taxonomies đã đăng ký
