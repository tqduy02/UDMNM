<?php
if (!defined('ABSPATH')) exit;

/**
 * Enqueue + localize script cho page-blog-list.php
 * - Lấy cat_ids từ ACF (field: blog_category) của Page hiện tại
 * - Fallback category slug 'plants' nếu không chọn
 */
add_action('wp_enqueue_scripts', function () {
  if (!is_page_template('page-blog-list.php')) return;

  $selected = get_field('blog_category', get_the_ID());
  $cat_ids  = [];

  if ($selected) {
    $selected = is_array($selected) ? $selected : [$selected];
    foreach ($selected as $item) {
      if (is_object($item) && isset($item->term_id)) $cat_ids[] = (int)$item->term_id;
      elseif (is_numeric($item))                     $cat_ids[] = (int)$item;
    }
  }
  if (!$cat_ids) {
    $fb = get_category_by_slug('plants');
    if ($fb) $cat_ids[] = (int)$fb->term_id;
  }

  wp_enqueue_script(
    'mythemes-loadmore',
    MYT_URI . '/assets/js/loadmore.js',
    ['jquery'], MYT_VERSION, true
  );

  wp_localize_script('mythemes-loadmore', 'MYT_LOADMORE', [
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce'   => wp_create_nonce('myt_load_more'),
    'cats'    => array_map('intval', $cat_ids),
  ]);
});
