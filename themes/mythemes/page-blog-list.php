<?php
/* Template Name: Blog List */
if (!defined('ABSPATH')) exit;
get_header();

/**
 * LẤY DANH SÁCH CATEGORY TỪ ACF
 * Field taxonomy ACF có thể Return: Term Object hoặc Term ID
 */
$selected = get_field('blog_category'); // có thể: term object | array term object | term id | array id | null

$cat_ids = [];
$cat_names = [];

if ($selected) {
  // Chuẩn hóa về mảng
  $selected = is_array($selected) ? $selected : [$selected];

  foreach ($selected as $item) {
    if (is_object($item) && isset($item->term_id)) {
      $cat_ids[]   = (int) $item->term_id;
      $cat_names[] = $item->name;
    } elseif (is_numeric($item)) {
      $term = get_term((int)$item, 'category');
      if ($term && !is_wp_error($term)) {
        $cat_ids[]   = (int) $term->term_id;
        $cat_names[] = $term->name;
      }
    }
  }
}

// Fallback: nếu chưa chọn gì => dùng slug 'plants' (tuỳ bạn)
if (empty($cat_ids)) {
  $fallback = get_category_by_slug('plants');
  if ($fallback) {
    $cat_ids[]   = (int) $fallback->term_id;
    $cat_names[] = $fallback->name;
  }
}

// Tiêu đề hero
$hero_title = (count($cat_names) > 1)
  ? 'Categories : ' . implode(', ', array_map('esc_html', $cat_names))
  : 'Category : ' . esc_html(reset($cat_names));

// (Tuỳ chọn) Mô tả: lấy intro của category đầu tiên (nếu muốn gộp thì bạn tự xử lý thêm)
$first_id = (int) $cat_ids[0];
$intro    = get_field('category_intro', 'category_' . $first_id);

// Paged và Query lần đầu
$paged = max(1, get_query_var('paged') ?: get_query_var('page'));

$args = [
  'post_type'           => 'post',
  'posts_per_page'      => 6,
  'paged'               => $paged,
  'ignore_sticky_posts' => true,
];

if (!empty($cat_ids)) {
  $args['category__in'] = array_map('intval', $cat_ids); // OR logic
}

$q = new WP_Query($args);
?>

<!-- HERO -->
<section class="archive-hero py-5 py-lg-6 border-bottom">
  <div class="container-xxl">
    <nav class="crumbs small mb-3" aria-label="breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="text-muted text-decoration-none">Home</a>
      <span class="mx-2 text-muted">›</span>
      <span class="text-muted">Blog</span>
    </nav>

    <h1 class="display-5 fw-bold mb-3"><?php echo $hero_title; ?></h1>

    <?php if ($intro): ?>
      <div class="lead text-muted max-w-800"><?php echo wp_kses_post($intro); ?></div>
    <?php endif; ?>
  </div>
</section>

<!-- LIST -->
<section class="blog-page py-5">
  <div class="container-xxl">
    <div id="blogList" class="row g-4 blog-list">
      <?php if ($q->have_posts()):
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
      else:
        echo '<p>Chưa có bài viết.</p>';
      endif; wp_reset_postdata(); ?>
    </div>

    <?php if ($q->max_num_pages > 1): ?>
      <div class="text-center mt-4">
        <button id="loadMorePosts"
                class="btn btn-dark px-4 py-2"
                data-current="<?php echo esc_attr($paged); ?>"
                data-max="<?php echo esc_attr(max(1, $q->max_num_pages)); ?>">
          More posts
        </button>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php get_footer(); ?>
