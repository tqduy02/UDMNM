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

// Enqueue JS chỉ cho Blog List
add_action('wp_enqueue_scripts', function () {
  if (is_page_template('page-blog-list.php')) {
    // Lấy cat_ids từ ACF
    $selected = get_field('blog_category', get_the_ID());
    $cat_ids = [];
    if ($selected) {
      $selected = is_array($selected) ? $selected : [$selected];
      foreach ($selected as $item) {
        if (is_object($item) && isset($item->term_id)) $cat_ids[] = (int) $item->term_id;
        elseif (is_numeric($item)) $cat_ids[] = (int) $item;
      }
    }
    if (!$cat_ids) {
      $fb = get_category_by_slug('plants');
      if ($fb) $cat_ids[] = (int) $fb->term_id;
    }

    wp_enqueue_script(
      'mythemes-loadmore',
      get_template_directory_uri() . '/assets/js/loadmore.js',
      ['jquery'], '1.3', true
    );

    wp_localize_script('mythemes-loadmore', 'MYT_LOADMORE', [
      'ajaxurl' => admin_url('admin-ajax.php'),
      'nonce'   => wp_create_nonce('myt_load_more'),
      'cats'    => array_map('intval', $cat_ids), // mảng ID
    ]);
  }
});


// AJAX handler
add_action('wp_ajax_myt_load_more', 'myt_load_more_cb');
add_action('wp_ajax_nopriv_myt_load_more', 'myt_load_more_cb');

function myt_load_more_cb() {
  check_ajax_referer('myt_load_more', 'nonce');

  $paged = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;

  // Chuẩn hoá cats
  $cats = [];
  if (isset($_POST['cats'])) {
    if (is_array($_POST['cats'])) {
      $cats = array_map('intval', $_POST['cats']);
    } else {
      $raw = stripslashes((string)$_POST['cats']);
      $decoded = json_decode($raw, true);
      if (is_array($decoded)) {
        $cats = array_map('intval', $decoded);
      } else {
        $cats = array_filter(array_map('intval', explode(',', $raw)));
      }
    }
  }

  $args = [
    'post_type'           => 'post',
    'posts_per_page'      => 6,
    'paged'               => $paged,
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true, // tối ưu cho Ajax
  ];
  if ($cats) $args['category__in'] = $cats;

  $q = new WP_Query($args);

  ob_start();
  if ($q->have_posts()):
    while ($q->have_posts()): $q->the_post();
      $cat = get_the_category(); $cat_show = $cat ? $cat[0]->name : 'Blog'; ?>
      <div class="col-md-6 post-item">
        <article class="post-card2 border rounded-4">
          <div class="row g-0 align-items-center">
            <div class="col-auto p-4">
              <a class="thumb-round d-block" href="<?php the_permalink(); ?>">
                <?php if (has_post_thumbnail()):
                  the_post_thumbnail('medium', ['class'=>'w-100 h-100 object-fit-cover rounded-circle']);
                else: ?>
                  <img class="w-100 h-100 object-fit-cover rounded-circle" src="https://picsum.photos/400/400?random=<?php echo get_the_ID(); ?>" alt="<?php the_title_attribute(); ?>">
                <?php endif; ?>
              </a>
            </div>
            <div class="col p-4">
              <span class="badge-cat"><i class="fa-regular fa-circle me-2"></i><?php echo esc_html($cat_show); ?></span>
              <h3 class="h4 fw-bold mt-3 mb-2">
                <a class="post-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              </h3>
              <div class="post-meta small text-muted">
                <?php the_author(); ?> • <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago
              </div>
            </div>
          </div>
        </article>
      </div>
    <?php endwhile;
  endif; wp_reset_postdata();

  wp_send_json_success(['html' => ob_get_clean()]);
}

// Enqueue cho Blog Grid
add_action('wp_enqueue_scripts', function () {
  if (is_page_template('page-blog-grid.php')) {

    // Lấy cat_ids từ ACF của page hiện tại
    $selected = get_field('blog_category', get_the_ID());
    $cat_ids = [];
    if ($selected){
      $selected = is_array($selected) ? $selected : [$selected];
      foreach ($selected as $item){
        if (is_object($item) && isset($item->term_id)) $cat_ids[] = (int)$item->term_id;
        elseif (is_numeric($item)) $cat_ids[] = (int)$item;
      }
    }
    if (!$cat_ids){
      $fb = get_category_by_slug('fashion'); // fallback tùy bạn
      if ($fb) $cat_ids[] = (int)$fb->term_id;
    }

    wp_enqueue_script(
      'mythemes-loadmore-grid',
      get_template_directory_uri().'/assets/js/loadmore-grid.js',
      ['jquery'], '1.0', true
    );
    wp_localize_script('mythemes-loadmore-grid', 'MYT_GRID', [
      'ajaxurl' => admin_url('admin-ajax.php'),
      'nonce'   => wp_create_nonce('myt_load_more_grid'),
      'cats'    => array_map('intval', $cat_ids),
    ]);
  }
});

// AJAX handler cho Grid
add_action('wp_ajax_myt_load_more_grid', 'myt_load_more_grid_cb');
add_action('wp_ajax_nopriv_myt_load_more_grid', 'myt_load_more_grid_cb');

function myt_load_more_grid_cb(){
  check_ajax_referer('myt_load_more_grid', 'nonce');

  $paged = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;

  // Chuẩn hoá cats
  $cats = [];
  if (isset($_POST['cats'])){
    if (is_array($_POST['cats'])){
      $cats = array_map('intval', $_POST['cats']);
    } else {
      $raw = stripslashes((string)$_POST['cats']);
      $json = json_decode($raw, true);
      $cats = is_array($json) ? array_map('intval',$json)
                              : array_filter(array_map('intval', explode(',', $raw)));
    }
  }

  $args = [
    'post_type' => 'post',
    'posts_per_page' => 6,
    'paged' => $paged,
    'ignore_sticky_posts' => true,
    'no_found_rows' => true,
  ];
  if ($cats) $args['category__in'] = $cats;

  $q = new WP_Query($args);

  ob_start();
  if ($q->have_posts()):
    while($q->have_posts()): $q->the_post();
      $cat = get_the_category(); $cat_show = $cat ? $cat[0]->name : 'Blog'; ?>
      <div class="col-12 col-md-6 col-lg-4">
        <article class="grid-card border rounded-4 h-100 text-center p-4">
          <a class="grid-thumb d-block mx-auto mb-3" href="<?php the_permalink(); ?>">
            <?php if (has_post_thumbnail()):
              the_post_thumbnail('large', ['class'=>'w-100 h-100 object-fit-cover rounded-circle']);
            else: ?>
              <img class="w-100 h-100 object-fit-cover rounded-circle" src="https://picsum.photos/600/600?random=<?php echo get_the_ID(); ?>" alt="<?php the_title_attribute(); ?>">
            <?php endif; ?>
          </a>
          <span class="badge-cat mb-2"><i class="fa-regular fa-circle me-2"></i><?php echo esc_html($cat_show); ?></span>
          <h3 class="h4 fw-bold mb-2"><a class="post-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <div class="post-meta small text-muted"><?php the_author(); ?> • <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</div>
        </article>
      </div>
    <?php endwhile;
  endif; wp_reset_postdata();

  wp_send_json_success(['html' => ob_get_clean()]);
}

