<?php
if (!defined('ABSPATH')) exit;

/**
 * ACF Local JSON: set đường dẫn lưu/đọc
 */
add_filter('acf/settings/save_json', function ($path) {
  $path = get_stylesheet_directory() . '/acf-json';
  if (!file_exists($path)) {
    wp_mkdir_p($path);
  }
  return $path;
});

add_filter('acf/settings/load_json', function ($paths) {
  $paths[] = get_stylesheet_directory() . '/acf-json';
  return $paths;
});

/**
 * One-time re-save tất cả field groups để sinh JSON ban đầu
 * - Tự tắt sau khi chạy 1 lần (flag trong options)
 */
add_action('admin_init', function () {
  if (!is_admin() || !function_exists('acf_get_field_groups')) return;

  $flag = 'mytheme_acf_json_resaved';
  if (get_option($flag)) return;

  $groups = acf_get_field_groups();
  if (!empty($groups)) {
    foreach ($groups as $g) {
      $full = acf_get_field_group($g['key']);
      if ($full) acf_update_field_group($full);
    }
  }
  update_option($flag, current_time('mysql'));
});

/**
 * Xóa file JSON khi xóa/trash field group
 */
function mytheme_acf_delete_json_file($field_group) {
  if (empty($field_group['key'])) return;
  $file = trailingslashit(get_stylesheet_directory() . '/acf-json') . $field_group['key'] . '.json';
  if (file_exists($file)) @unlink($file);
}
add_action('acf/delete_field_group', 'mytheme_acf_delete_json_file', 10, 1);
add_action('acf/trash_field_group',  'mytheme_acf_delete_json_file', 10, 1);

/**
 * Khi untrash: ép ACF ghi lại JSON ngay
 */
add_action('acf/untrash_field_group', function ($field_group) {
  if (empty($field_group['key'])) return;
  $full = acf_get_field_group($field_group['key']);
  if ($full) acf_update_field_group($full);
}, 10, 1);
