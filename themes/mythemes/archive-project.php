<?php
/**
 * Archive Projects (/projects/)
 */
get_header(); ?>

<header class="page-hero">
  <h1 class="page-title h2 underline-animate"><?php post_type_archive_title(); ?></h1>

  <?php
  if (function_exists('yoast_breadcrumb')) {
    yoast_breadcrumb(
      '<nav class="breadcrumb" aria-label="' . esc_attr__('Breadcrumb', 'mythemes') . '">',
      '</nav>'
    );
  } elseif (function_exists('rank_math_the_breadcrumbs')) {
    echo '<nav class="breadcrumb" aria-label="' . esc_attr__('Breadcrumb', 'mythemes') . '">';
    rank_math_the_breadcrumbs();
    echo '</nav>';
  } else { ?>
    <nav class="breadcrumb" aria-label="<?php echo esc_attr__('Breadcrumb', 'mythemes'); ?>">
      <a href="<?php echo esc_url(home_url('/')); ?>">
        <?php echo esc_html__('Home', 'mythemes'); ?>
      </a>
      <span aria-hidden="true">›</span>
      <span><?php post_type_archive_title(); ?></span>
    </nav>
  <?php } ?>
</header>

<div class="container container-1240">
<?php if (have_posts()): ?>
  <div class="row g-4">
    <?php while (have_posts()): the_post(); ?>
      <div class="col-12 col-sm-6 col-lg-4">
        <article <?php post_class('card h-100 shadow-sm border-0'); ?>>

          <a href="<?php the_permalink(); ?>" class="ratio ratio-16x9 overflow-hidden d-block" aria-label="<?php echo esc_attr__('View project', 'mythemes'); ?>">
            <?php if (has_post_thumbnail()) {
              the_post_thumbnail('large', ['class' => 'w-100 h-100 object-fit-cover', 'loading' => 'lazy']);
            } else { ?>
              <div class="thumb-placeholder w-100 h-100" aria-hidden="true"></div>
            <?php } ?>
          </a>

          <div class="card-body">
            <div class="meta mb-2">
              <?php
              $cats = get_the_terms(get_the_ID(), 'project_cat');
              if ($cats && !is_wp_error($cats)) {
                $first = $cats[0];
                echo '<a href="' . esc_url(get_term_link($first)) . '">' . esc_html($first->name) . '</a>';
              }
              ?>
            </div>

            <h2 class="h5 mb-2">
              <a class="card-title-link underline-animate" href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
              </a>
            </h2>

            <p class="text-muted line-clamp-2 mb-3"><?php echo get_the_excerpt(); ?></p>

            <div class="small text-muted d-flex align-items-center gap-2 card-meta">
              <span class="card-meta__date">
                <?php echo esc_html( get_the_date( get_option('date_format') ) ); ?>
              </span>
              <span class="card-meta__sep" aria-hidden="true">•</span>
              <span class="card-meta__terms">
                <?php
                // tech_stack có thể dài, strip tags để text-thẳng cho ellipsis
                $terms_text = wp_strip_all_tags(get_the_term_list(get_the_ID(), 'tech_stack', '', ', '));
                echo esc_html($terms_text);
                ?>
              </span>
            </div>
          </div>

        </article>
      </div>
    <?php endwhile; ?>
  </div>

  <?php
  the_posts_pagination([
    'prev_text'          => esc_html__('Previous', 'mythemes'),
    'next_text'          => esc_html__('Next', 'mythemes'),
    'screen_reader_text' => esc_html__('Projects navigation', 'mythemes'),
  ]);
  ?>

<?php else: ?>
  <p><?php echo esc_html__('No projects found.', 'mythemes'); ?></p>
<?php endif; ?>
</div>

<?php get_footer();
