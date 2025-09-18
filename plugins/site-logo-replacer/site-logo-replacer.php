<?php
/**
 * Plugin Name: Site Logo Replacer
 * Description: Thay logo WordPress mặc định bằng logo của website (login + admin bar).
 * Version: 1.0.0
 * Author: tqduy02
 */

if (!defined('ABSPATH')) exit;

class Site_Logo_Replacer {
  const OPT_KEY = 'slr_options';

  public function __construct() {
    add_action('admin_menu', [$this, 'add_menu']);
    add_action('admin_init', [$this, 'register_settings']);
    add_action('login_enqueue_scripts', [$this, 'login_logo_css']);
    add_action('admin_bar_menu', [$this, 'replace_adminbar_logo'], 1);
  }

  public static function get_opts() {
    $defaults = [
      'attachment_id' => 0,
      'width' => 220,
      'height' => 80,
      'hide_wp_logo' => 0,
    ];
    return wp_parse_args(get_option(self::OPT_KEY, []), $defaults);
  }

  public function add_menu() {
    add_theme_page('Site Logo', 'Site Logo', 'manage_options', 'slr', [$this, 'render_page']);
  }

  public function register_settings() {
    register_setting('slr_group', self::OPT_KEY, function($input){
      $out = self::get_opts();
      $out['attachment_id'] = isset($input['attachment_id']) ? intval($input['attachment_id']) : 0;
      $out['width'] = isset($input['width']) ? intval($input['width']) : 220;
      $out['height'] = isset($input['height']) ? intval($input['height']) : 80;
      $out['hide_wp_logo'] = !empty($input['hide_wp_logo']) ? 1 : 0;
      return $out;
    });
  }

  public function render_page() {
    $opts = self::get_opts();
    $logo = $opts['attachment_id'] ? wp_get_attachment_image_url($opts['attachment_id'], 'full') : '';
    ?>
    <div class="wrap">
      <h1>Site Logo Replacer</h1>
      <form method="post" action="options.php">
        <?php settings_fields('slr_group'); ?>
        <?php $opt_name = self::OPT_KEY; ?>
        <table class="form-table" role="presentation">
          <tr>
            <th scope="row">Logo</th>
            <td>
              <div style="margin-bottom:8px">
                <img id="slr-preview" src="<?php echo esc_url($logo); ?>" style="max-width:300px;height:auto;<?php echo $logo?'':'display:none'; ?>">
              </div>
              <input type="hidden" id="slr_attachment_id" name="<?php echo esc_attr($opt_name); ?>[attachment_id]" value="<?php echo esc_attr($opts['attachment_id']); ?>">
              <button type="button" class="button" id="slr_pick">Chọn ảnh</button>
              <button type="button" class="button" id="slr_clear" <?php disabled(!$opts['attachment_id']); ?>>Xóa</button>
            </td>
          </tr>
          <tr>
            <th scope="row">Kích thước</th>
            <td>
              Rộng (px): <input type="number" name="<?php echo esc_attr($opt_name); ?>[width]" value="<?php echo esc_attr($opts['width']); ?>" style="width:90px">
              &nbsp; Cao (px): <input type="number" name="<?php echo esc_attr($opt_name); ?>[height]" value="<?php echo esc_attr($opts['height']); ?>" style="width:90px">
              <p class="description">Áp dụng cho logo trang đăng nhập.</p>
            </td>
          </tr>
          <tr>
            <th scope="row">Admin Bar</th>
            <td>
              <label><input type="checkbox" name="<?php echo esc_attr($opt_name); ?>[hide_wp_logo]" value="1" <?php checked($opts['hide_wp_logo'],1); ?>> Ẩn logo WordPress mặc định</label>
              <p class="description">Nếu không ẩn, plugin sẽ thay icon WordPress bằng logo của bạn.</p>
            </td>
          </tr>
        </table>
        <?php submit_button(); ?>
      </form>
    </div>
    <script>
      (function($){
        let frame;
        $('#slr_pick').on('click', function(e){
          e.preventDefault();
          if(frame){ frame.open(); return; }
          frame = wp.media({ title:'Chọn logo', button:{ text:'Dùng ảnh này' }, multiple:false });
          frame.on('select', function(){
            const attachment = frame.state().get('selection').first().toJSON();
            $('#slr_attachment_id').val(attachment.id);
            $('#slr-preview').attr('src', attachment.url).show();
            $('#slr_clear').prop('disabled', false);
          });
          frame.open();
        });
        $('#slr_clear').on('click', function(e){
          e.preventDefault();
          $('#slr_attachment_id').val('');
          $('#slr-preview').hide().attr('src','');
          $(this).prop('disabled', true);
        });
      })(jQuery);
    </script>
    <?php
  }

  public function login_logo_css() {
    $o = self::get_opts();
    if (!$o['attachment_id']) return;
    $url = wp_get_attachment_image_url($o['attachment_id'], 'full');
    $w = max(1,(int)$o['width']); $h = max(1,(int)$o['height']);
    ?>
    <style>
      .login h1 a {
        background-image:url('<?php echo esc_url($url); ?>') !important;
        background-size: contain !important;
        width: <?php echo $w; ?>px !important;
        height: <?php echo $h; ?>px !important;
      }
    </style>
    <?php
  }

  public function replace_adminbar_logo($wp_admin_bar) {
    if (!is_admin_bar_showing()) return;
    $o = self::get_opts();

    if ($o['hide_wp_logo']) {
      $wp_admin_bar->remove_node('wp-logo');
      return;
    }
    if (!$o['attachment_id']) return;

    $url = wp_get_attachment_image_url($o['attachment_id'], 'thumbnail');
    if (!$url) return;

    // Thay icon WP
    $node = $wp_admin_bar->get_node('wp-logo');
    if ($node) {
      $node->meta['class'] .= ' slr-wp-logo';
      $wp_admin_bar->add_node([
        'id'    => 'wp-logo',
        'title' => '<span class="ab-icon" style="background-image:url('.esc_url($url).'); background-size:cover;"></span><span class="ab-label">Site</span>',
        'href'  => home_url('/'),
        'meta'  => $node->meta
      ]);
      add_action('admin_head', function(){
        echo '<style>#wpadminbar #wp-admin-bar-wp-logo>.ab-item .ab-icon:before{content:"" !important;}</style>';
      });
      add_action('wp_head', function(){
        echo '<style>#wpadminbar #wp-admin-bar-wp-logo>.ab-item .ab-icon:before{content:"" !important;}</style>';
      });
    }
  }
}

add_action('plugins_loaded', function(){
  new Site_Logo_Replacer();
});

// enqueue media on our settings page
add_action('admin_enqueue_scripts', function($hook){
  if ($hook === 'appearance_page_slr') {
    wp_enqueue_media();
    wp_enqueue_script('jquery');
  }
});
