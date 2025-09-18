<?php
/**
 * Plugin Name: Home Slider Picker
 * Description: Chọn bài viết hiển thị trong hero slider trang chủ, tích hợp trực tiếp vào theme.
 * Version: 1.1.0
 * Author: tqduy02
 */

if (!defined('ABSPATH')) exit;

final class HSP_NoShortcode {
  const META_SHOW   = '_hsp_in_slider';
  const META_WEIGHT = '_hsp_weight';
  const OPT_KEY     = 'hsp_ns_options';

  public function __construct() {
    // Metabox
    add_action('add_meta_boxes', [$this,'add_metabox']);
    add_action('save_post', [$this,'save_metabox']);
    // Settings
    add_action('admin_menu', [$this,'add_settings_page']);
    add_action('admin_init', [$this,'register_settings']);
    // Assets (optional)
    add_action('wp_enqueue_scripts', [$this,'maybe_enqueue_assets']);
  }

  /** Default options */
  public static function opts() {
    $defaults = [
      'limit'          => 5,
      'order'          => 'date_desc', // date_desc|date_asc|weight_desc|rand
      'post_types'     => ['post'],
      'enqueue_assets' => 0,           // theme đã có Swiper thì để 0
      'image_size'     => 'large',
    ];
    return wp_parse_args(get_option(self::OPT_KEY, []), $defaults);
  }

  /** Metabox */
  public function add_metabox() {
    foreach (self::opts()['post_types'] as $pt) {
      add_meta_box('hsp_meta', 'Home Slider', [$this,'render_metabox'], $pt, 'side', 'high');
    }
  }

  public function render_metabox($post) {
    wp_nonce_field('hsp_ns_save', 'hsp_ns_nonce');
    $in     = get_post_meta($post->ID, self::META_SHOW, true);
    $weight = get_post_meta($post->ID, self::META_WEIGHT, true);
    ?>
    <p><label><input type="checkbox" name="hsp_in_slider" value="1" <?php checked($in, '1'); ?>> Show in Home Slider</label></p>
    <p>Weight (ưu tiên): <input type="number" name="hsp_weight" value="<?php echo esc_attr($weight ?: 0); ?>" style="width:90px"></p>
    <?php
  }

  public function save_metabox($post_id) {
    if (!isset($_POST['hsp_ns_nonce']) || !wp_verify_nonce($_POST['hsp_ns_nonce'],'hsp_ns_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post',$post_id)) return;

    update_post_meta($post_id, self::META_SHOW, !empty($_POST['hsp_in_slider']) ? '1' : '0');
    update_post_meta($post_id, self::META_WEIGHT, isset($_POST['hsp_weight']) ? intval($_POST['hsp_weight']) : 0);
  }

  /** Settings */
  public function add_settings_page() {
    add_options_page('Home Slider', 'Home Slider', 'manage_options', 'hsp-ns', [$this,'render_settings']);
  }

  public function register_settings() {
    register_setting('hsp_ns_group', self::OPT_KEY, function($input){
      $o = self::opts();
      $o['limit'] = max(1, intval($input['limit'] ?? $o['limit']));
      $allowed = ['date_desc','date_asc','weight_desc','rand'];
      $o['order'] = in_array($input['order'] ?? '', $allowed,true) ? $input['order'] : $o['order'];
      $o['post_types'] = !empty($input['post_types']) ? array_map('sanitize_text_field',$input['post_types']) : $o['post_types'];
      $o['enqueue_assets'] = !empty($input['enqueue_assets']) ? 1 : 0;
      $o['image_size'] = sanitize_text_field($input['image_size'] ?? $o['image_size']);
      return $o;
    });
  }

  public function render_settings() {
    $o = self::opts();
    $all_pts = get_post_types(['public'=>true],'names');
    ?>
    <div class="wrap">
      <h1>Home Slider Settings</h1>
      <form method="post" action="options.php">
        <?php settings_fields('hsp_ns_group'); $key=self::OPT_KEY; ?>
        <table class="form-table">
          <tr>
            <th>Số lượng</th>
            <td><input type="number" name="<?php echo $key; ?>[limit]" value="<?php echo esc_attr($o['limit']); ?>" min="1"></td>
          </tr>
          <tr>
            <th>Thứ tự</th>
            <td>
              <select name="<?php echo $key; ?>[order]">
                <option value="date_desc" <?php selected($o['order'],'date_desc'); ?>>Mới nhất</option>
                <option value="date_asc" <?php selected($o['order'],'date_asc'); ?>>Cũ nhất</option>
                <option value="weight_desc" <?php selected($o['order'],'weight_desc'); ?>>Theo Weight</option>
                <option value="rand" <?php selected($o['order'],'rand'); ?>>Ngẫu nhiên</option>
              </select>
            </td>
          </tr>
          <tr>
            <th>Post Types</th>
            <td>
              <?php foreach($all_pts as $pt): ?>
                <label><input type="checkbox" name="<?php echo $key; ?>[post_types][]" value="<?php echo $pt; ?>" <?php checked(in_array($pt,$o['post_types'])); ?>> <?php echo $pt; ?></label><br>
              <?php endforeach; ?>
            </td>
          </tr>
          <tr>
            <th>Tự nạp Swiper</th>
            <td><input type="checkbox" name="<?php echo $key; ?>[enqueue_assets]" value="1" <?php checked($o['enqueue_assets']); ?>> Bật nếu theme chưa có</td>
          </tr>
          <tr>
            <th>Image size</th>
            <td><input type="text" name="<?php echo $key; ?>[image_size]" value="<?php echo esc_attr($o['image_size']); ?>"></td>
          </tr>
        </table>
        <?php submit_button(); ?>
      </form>
    </div>
    <?php
  }

  /** Enqueue assets if chosen */
  public function maybe_enqueue_assets() {
    $o = self::opts();
    if(!$o['enqueue_assets']) return;
    wp_enqueue_style('swiper','https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',[], '11');
    wp_enqueue_script('swiper','https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',[], '11', true);
  }

  /** Query args */
  public static function get_query_args($overrides=[]) {
    $o = self::opts();
    $args = [
      'post_type'      => $o['post_types'],
      'post_status'    => 'publish',
      'posts_per_page' => $o['limit'],
      'meta_query'     => [[ 'key'=>self::META_SHOW,'value'=>'1','compare'=>'=' ]],
    ];
    switch ($o['order']) {
      case 'date_asc':   $args['orderby']='date'; $args['order']='ASC'; break;
      case 'weight_desc':$args['meta_key']=self::META_WEIGHT; $args['orderby']='meta_value_num'; $args['order']='DESC'; break;
      case 'rand':       $args['orderby']='rand'; break;
      default:           $args['orderby']='date'; $args['order']='DESC';
    }
    return array_merge($args,$overrides);
  }

  /** Get WP_Query */
  public static function get_slider_query($overrides=[]) {
    return new WP_Query(self::get_query_args($overrides));
  }
}

// Init
add_action('plugins_loaded', function(){ $GLOBALS['hsp_ns'] = new HSP_NoShortcode(); });

// Template tag
function hsp_get_slider_query($overrides=[]) {
  return HSP_NoShortcode::get_slider_query($overrides);
}
