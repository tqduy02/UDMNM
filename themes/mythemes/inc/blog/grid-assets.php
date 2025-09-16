<?php
if (!defined('ABSPATH')) exit;

/**
 * Enqueue + localize script cho page-blog-grid.php
 * - Lấy cat_ids từ ACF (field: blog_category)
 * - Fallback category slug 'fashion'
 */
add_action('wp_enqueue_scripts', function () {
  if (!is_page_template('page-blog-grid.php')) return;

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
    $fb = get_category_by_slug('fashion');
    if ($fb) $cat_ids[] = (int)$fb->term_id;
  }

  wp_enqueue_script(
    'mythemes-loadmore-grid',
    MYT_URI . '/assets/js/loadmore-grid.js',
    ['jquery'], MYT_VERSION, true
  );

  wp_localize_script('mythemes-loadmore-grid', 'MYT_GRID', [
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce'   => wp_create_nonce('myt_load_more_grid'),
    'cats'    => array_map('intval', $cat_ids),
  ]);
});
