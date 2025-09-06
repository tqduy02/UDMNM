<?php
/* Template Name: Blog List */
if (!defined('ABSPATH')) exit;
get_header();

// Phân trang
$paged = max(1, get_query_var('paged') ?: get_query_var('page'));

$q = new WP_Query([
  'post_type'           => 'post',
  'posts_per_page'      => 10,
  'paged'               => $paged,
  'ignore_sticky_posts' => true,
]);
?>
<section class="blog-page py-5">
  <div class="container-xxl">

    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
      <h1 class="h3 m-0"><?php echo esc_html(get_the_title()); ?></h1>
      <!-- Dropdown dẫn qua 2 trang riêng -->
      <div class="d-flex align-items-center gap-2">
        <label class="small text-muted">View:</label>
        <select class="form-select form-select-sm" style="width:180px;" onchange="location.href=this.value;">
          <option value="<?php echo esc_url(get_permalink(get_page_by_path('blog-grid'))); ?>">Blog Grid</option>
          <option value="<?php echo esc_url(get_permalink()); ?>" selected>Blog List</option>
        </select>
      </div>
    </div>

    <?php if ($q->have_posts()): ?>
      <div class="row g-4 blog-list">
        <?php while($q->have_posts()): $q->the_post();
          $cat = get_the_category(); $cat_name = $cat ? $cat[0]->name : 'Blog'; ?>
          <div class="col-md-6">
            <article class="post-card2 border rounded-4">
              <div class="row g-0 align-items-center">
                <div class="col-auto p-4">
                  <a class="thumb-round d-block" href="<?php the_permalink(); ?>">
                    <?php if (has_post_thumbnail()): ?>
                      <?php the_post_thumbnail('medium', ['class'=>'w-100 h-100 object-fit-cover rounded-circle']); ?>
                    <?php else: ?>
                      <img class="w-100 h-100 object-fit-cover rounded-circle" src="https://picsum.photos/400/400?random=<?php echo get_the_ID(); ?>" alt="<?php the_title_attribute(); ?>">
                    <?php endif; ?>
                  </a>
                </div>
                <div class="col p-4">
                  <span class="badge-cat">
                    <i class="fa-regular fa-circle me-2"></i><?php echo esc_html($cat_name); ?>
                  </span>
                  <h3 class="h4 fw-bold mt-3 mb-2">
                    <a class="link-underline-anim" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                  </h3>
                  <div class="post-meta small text-muted">
                    <?php the_author(); ?> • <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago
                  </div>
                </div>
              </div>
            </article>
          </div>
        <?php endwhile; ?>
      </div>

      <div class="mt-4">
        <?php
          echo paginate_links([
            'total'     => $q->max_num_pages,
            'current'   => $paged,
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'type'      => 'list',
          ]);
        ?>
      </div>

    <?php else: ?>
      <p>Chưa có bài viết.</p>
    <?php endif; wp_reset_postdata(); ?>

  </div>
</section>
<?php get_footer(); ?>
