<?php
/* Template Name: Blog Grid */
if (!defined('ABSPATH')) exit;
get_header();

/** Lấy category từ ACF (hỗ trợ nhiều giá trị) */
$selected = get_field('blog_category'); // term object | id | array | null
$cat_ids = []; $cat_names = [];
if ($selected){
  $selected = is_array($selected) ? $selected : [$selected];
  foreach ($selected as $item){
    if (is_object($item) && isset($item->term_id)){
      $cat_ids[] = (int)$item->term_id;
      $cat_names[] = $item->name;
    } elseif (is_numeric($item)){
      $t = get_term((int)$item, 'category');
      if ($t && !is_wp_error($t)){ $cat_ids[] = (int)$t->term_id; $cat_names[] = $t->name; }
    }
  }
}
if (!$cat_ids){
  $fb = get_category_by_slug('fashion'); // fallback tùy bạn
  if ($fb){ $cat_ids[] = (int)$fb->term_id; $cat_names[] = $fb->name; }
}

/** Tiêu đề hero: Category/Categories */
$cats_list  = implode(', ', $cat_names);
if (count($cat_names) > 1) {
  $hero_title = sprintf(__('Categories: %s', 'mythemes'), $cats_list);
} else {
  $hero_title = sprintf(__('Category: %s', 'mythemes'), reset($cat_names));
}

/** Intro từ ACF trên taxonomy (sẽ theo bản dịch của term trong Polylang) */
$intro = get_field('category_intro', 'category_'.(int)$cat_ids[0]);

$paged = max(1, get_query_var('paged') ?: get_query_var('page'));
$args = [
  'post_type'           => 'post',
  'posts_per_page'      => 6,
  'paged'               => $paged,
  'ignore_sticky_posts' => true,
];
if ($cat_ids) $args['category__in'] = $cat_ids;

$q = new WP_Query($args);
?>

<section class="archive-hero py-5 py-lg-6 border-bottom text-center">
  <div class="container-xxl">
    <h1 class="display-5 fw-bold mb-2"><?php echo esc_html($hero_title); ?></h1>

    <nav class="crumbs small mb-3" aria-label="<?php echo esc_attr__('Breadcrumb', 'mythemes'); ?>">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="text-muted text-decoration-none">
        <?php echo esc_html__('Home', 'mythemes'); ?>
      </a>
      <span class="mx-2 text-muted" aria-hidden="true">›</span>
      <span class="text-muted"><?php echo esc_html__('Blog', 'mythemes'); ?></span>
    </nav>

    <?php if ($intro): ?>
      <div class="lead text-muted max-w-800 mx-auto"><?php echo wp_kses_post($intro); ?></div>
    <?php endif; ?>
  </div>
</section>

<section class="blog-grid py-5" aria-label="<?php echo esc_attr__('Blog grid', 'mythemes'); ?>">
  <div class="container-xxl">
    <div id="gridList" class="row g-4">
      <?php if ($q->have_posts()):
        while($q->have_posts()): $q->the_post();
          $cat = get_the_category();
          $cat_show = $cat ? $cat[0]->name : esc_html__('Blog', 'mythemes'); ?>
          <div class="col-12 col-md-6 col-lg-4">
            <article class="grid-card border rounded-4 h-100 text-center p-4">
              <a class="grid-thumb d-block mx-auto mb-3" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr__('View post', 'mythemes'); ?>">
                <?php if (has_post_thumbnail()):
                  the_post_thumbnail('large', ['class'=>'w-100 h-100 object-fit-cover rounded-circle', 'alt'=>esc_attr(get_the_title())]);
                else: ?>
                  <img class="w-100 h-100 object-fit-cover rounded-circle"
                       src="https://picsum.photos/600/600?random=<?php echo esc_attr(get_the_ID()); ?>"
                       alt="<?php echo esc_attr(get_the_title()); ?>">
                <?php endif; ?>
              </a>

              <span class="badge-cat mb-2">
                <i class="fa-regular fa-circle me-2" aria-hidden="true"></i><?php echo esc_html($cat_show); ?>
              </span>

              <h3 class="h4 fw-bold mb-2">
                <a class="post-link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr__('View post', 'mythemes'); ?>">
                  <?php the_title(); ?>
                </a>
              </h3>

              <div class="post-meta small text-muted">
                <?php
                  /* translators: %s: time ago (e.g., "3 hours") */
                  $ago = human_time_diff(get_the_time('U'), current_time('timestamp'));
                  printf( esc_html__('%s ago', 'mythemes'), esc_html($ago) );
                ?>
              </div>
            </article>
          </div>
        <?php endwhile;
      else: ?>
        <p><?php echo esc_html__('No posts yet.', 'mythemes'); ?></p>
      <?php endif; wp_reset_postdata(); ?>
    </div>

    <?php if ($q->max_num_pages > 1): ?>
      <div class="text-center mt-4">
        <button id="loadMoreGrid"
                class="btn btn-dark px-4 py-2"
                data-current="<?php echo esc_attr($paged); ?>"
                data-max="<?php echo esc_attr( max(1, $q->max_num_pages) ); ?>">
          <?php echo esc_html__('More posts', 'mythemes'); ?>
        </button>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php get_footer(); ?>
