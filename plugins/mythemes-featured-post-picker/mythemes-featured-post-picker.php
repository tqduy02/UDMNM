<?php
/**
 * Plugin Name: MyThemes – Featured Post Picker
 * Description: Chọn Featured Post cho theme (hỗ trợ đa ngôn ngữ với Polylang). Cung cấp hàm myfp_get_featured_post_id().
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: mythemes
 */

if (!defined('ABSPATH')) exit;

class MyThemes_Featured_Post_Picker {
  const OPT_PREFIX = 'myfp_featured_post_';

  public function __construct() {
    add_action('admin_menu', [$this, 'add_menu']);
    add_action('admin_init', [$this, 'maybe_save']);
  }

  /** Danh sách ngôn ngữ (slug). Nếu không có Polylang → mảng 1 phần tử 'default'. */
  public static function langs() {
    if (function_exists('pll_languages_list')) {
      $langs = pll_languages_list(['fields' => 'slug']);
      if (is_array($langs) && $langs) return $langs;
    }
    return ['default'];
  }

  /** Lấy option key theo lang */
  public static function opt_key($lang) {
    $lang = $lang ?: 'default';
    return self::OPT_PREFIX . $lang;
  }

  /** Thêm trang trong Appearance */
  public function add_menu() {
    add_theme_page(
      __('Featured Post', 'mythemes'),
      __('Featured Post', 'mythemes'),
      'manage_options',
      'myfp-featured-post',
      [$this, 'render_page']
    );
  }

  /** Lưu dữ liệu */
  public function maybe_save() {
    if (!is_admin() || !isset($_POST['myfp_action']) || $_POST['myfp_action'] !== 'save') return;
    if (!current_user_can('manage_options')) return;
    check_admin_referer('myfp_save_featured', 'myfp_nonce');

    $langs = self::langs();
    foreach ($langs as $lang) {
      $field = 'myfp_post_' . $lang;
      $val   = isset($_POST[$field]) ? absint($_POST[$field]) : 0;
      update_option(self::opt_key($lang), $val);
    }

    wp_safe_redirect(add_query_arg('updated', '1', menu_page_url('myfp-featured-post', false)));
    exit;
  }

  /** Danh sách post cho select (lọc theo lang nếu có) */
  private function get_posts_for_select($lang) {
    $args = [
      'post_type'      => 'post',
      'posts_per_page' => 200,
      'orderby'        => 'date',
      'order'          => 'DESC',
      'post_status'    => 'publish',
      'fields'         => 'ids',
    ];
    if ($lang && function_exists('pll_languages_list')) {
      // Polylang hỗ trợ tham số 'lang' trong WP_Query
      $args['lang'] = $lang;
    }
    $q = new WP_Query($args);
    return $q->posts ?: [];
  }

  /** Render trang cài đặt */
  public function render_page() {
    if (!current_user_can('manage_options')) return;

    $has_pll = function_exists('pll_languages_list');
    $langs   = self::langs();
    ?>
    <div class="wrap">
      <h1><?php echo esc_html__('Featured Post', 'mythemes'); ?></h1>
      <?php if (isset($_GET['updated'])): ?>
        <div id="message" class="updated notice is-dismissible"><p><?php echo esc_html__('Settings saved.', 'mythemes'); ?></p></div>
      <?php endif; ?>

      <form method="post">
        <?php wp_nonce_field('myfp_save_featured', 'myfp_nonce'); ?>
        <input type="hidden" name="myfp_action" value="save" />

        <table class="form-table" role="presentation">
          <tbody>
          <?php foreach ($langs as $lang): 
            $label = $has_pll ? sprintf(__('Language: %s', 'mythemes'), strtoupper($lang)) : __('Default', 'mythemes');
            $opt   = (int) get_option(self::opt_key($lang), 0);
            $ids   = $this->get_posts_for_select($lang);
          ?>
            <tr>
              <th scope="row"><label for="myfp_post_<?php echo esc_attr($lang); ?>"><?php echo esc_html($label); ?></label></th>
              <td>
                <select name="myfp_post_<?php echo esc_attr($lang); ?>" id="myfp_post_<?php echo esc_attr($lang); ?>" class="regular-text">
                  <option value="0"><?php echo esc_html__('— Select a post —', 'mythemes'); ?></option>
                  <?php foreach ($ids as $pid): ?>
                    <option value="<?php echo esc_attr($pid); ?>" <?php selected($opt, $pid); ?>>
                      <?php echo esc_html(get_the_title($pid)); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <p class="description">
                  <?php echo esc_html__('Pick a post to feature on the homepage section.', 'mythemes'); ?>
                </p>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>

        <?php submit_button(__('Save Changes', 'mythemes')); ?>
      </form>
    </div>
    <?php
  }
}
new MyThemes_Featured_Post_Picker();

/**
 * Template tag: Lấy ID Featured Post theo ngôn ngữ hiện tại (nếu có Polylang)
 * @param string|null $lang Slug ngôn ngữ (vd: 'en', 'vi'); null = ngôn ngữ hiện tại hoặc 'default'
 * @return int
 */
function myfp_get_featured_post_id($lang = null) {
  if (function_exists('pll_current_language')) {
    if ($lang === null) $lang = pll_current_language('slug');
  }
  $lang = $lang ?: 'default';
  return (int) get_option(MyThemes_Featured_Post_Picker::OPT_PREFIX . $lang, 0);
}

/**
 * (Tuỳ chọn) Lấy WP_Post
 */
function myfp_get_featured_post($lang = null) {
  $id = myfp_get_featured_post_id($lang);
  return $id ? get_post($id) : null;
}
