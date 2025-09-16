<?php
if (!defined('ABSPATH')) exit;

/**
 * AJAX: nạp thêm Post (layout Grid)
 */
add_action('wp_ajax_myt_load_more_grid',        'myt_load_more_grid_cb');
add_action('wp_ajax_nopriv_myt_load_more_grid', 'myt_load_more_grid_cb');

function myt_load_more_grid_cb() {
  check_ajax_referer('myt_load_more_grid', 'nonce');

  $paged = isset($_POST['page']) ? max(1, (int)$_POST['page']) : 1;

  // Chuẩn hoá cats (array/json/csv)
  $cats = [];
  if (isset($_POST['cats'])) {
    if (is_array($_POST['cats'])) {
      $cats = array_map('intval', $_POST['cats']);
    } else {
      $raw  = stripslashes((string)$_POST['cats']);
      $json = json_decode($raw, true);
      $cats = is_array($json) ? array_map('intval', $json)
                              : array_filter(array_map('intval', explode(',', $raw)));
    }
  }

  $args = [
    'post_type'           => 'post',
    'posts_per_page'      => 6,
    'paged'               => $paged,
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
  ];
  if ($cats) $args['category__in'] = $cats;

  $q = new WP_Query($args);

  ob_start();
  if ($q->have_posts()):
    while ($q->have_posts()): $q->the_post();
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
