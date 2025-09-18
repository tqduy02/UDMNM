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

