<?php
/**
 * Single Project
 */
get_header(); ?>

<main id="content" role="main">
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

    <header class="page-hero">
      <h1 class="page-title display-6 underline-animate"><?php the_title(); ?></h1>

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
          <a href="<?php echo esc_url(get_post_type_archive_link('project')); ?>">
            <?php echo esc_html__('Projects', 'mythemes'); ?>
          </a>
          <span aria-hidden="true">›</span>
          <span><?php the_title(); ?></span>
        </nav>
      <?php } ?>
    </header>

    <article <?php post_class('mx-auto'); ?> style="max-width:820px;">
      <?php if (has_post_thumbnail()) : ?>
        <figure class="mb-4 single-project-thumb">
          <?php the_post_thumbnail('full', [
            'class'   => 'w-100 h-100',
            'loading' => 'lazy',
            'alt'     => esc_attr(get_the_title()),
          ]); ?>
        </figure>
      <?php endif; ?>

      <div class="content lh-lg">
        <?php the_content(); ?>
      </div>

      <footer class="mt-5">
        <?php
        $tech_list = get_the_term_list(get_the_ID(), 'tech_stack', '', ' ');
        if ($tech_list) {
          echo '<div class="mb-2 fw-semibold">' . esc_html__('Tech stack', 'mythemes') . '</div>';
          echo '<div class="tech-list">' . wp_kses_post($tech_list) . '</div>';
        }
        ?>
        <div class="footer-meta small mt-4">
          <?php
          /* translators: 1: date, 2: author name */
          printf(
            esc_html__('%1$s • by %2$s', 'mythemes'),
            esc_html(get_the_date(get_option('date_format'))),
            esc_html(get_the_author())
          );

          $c = (int) get_comments_number();
          echo ' • <i class="fa-regular fa-comment" aria-hidden="true"></i> ';
          $c_text = _n('%s comment', '%s comments', $c, 'mythemes');
          printf( esc_html($c_text), number_format_i18n($c) );
          ?>
        </div>
      </footer>

      <hr class="my-5" />
      <?php comments_template(); ?>
    </article>

  <?php endwhile; endif; ?>
</main>

<?php get_footer();
