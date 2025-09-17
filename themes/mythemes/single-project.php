<?php
/**
 * Single Project
 */
get_header(); ?>

  <?php if (have_posts()): while (have_posts()): the_post(); ?>

    <header class="page-hero">
      <h1 class="page-title display-6 underline-animate"><?php the_title(); ?></h1>

      <?php
      if (function_exists('yoast_breadcrumb')) {
        yoast_breadcrumb('<nav class="breadcrumb" aria-label="breadcrumb">','</nav>');
      } elseif (function_exists('rank_math_the_breadcrumbs')) {
        echo '<nav class="breadcrumb" aria-label="breadcrumb">';
        rank_math_the_breadcrumbs();
        echo '</nav>';
      } else { ?>
        <nav class="breadcrumb" aria-label="breadcrumb">
          <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
          <span>›</span>
          <a href="<?php echo esc_url(get_post_type_archive_link('project')); ?>">Projects</a>
          <span>›</span>
          <span><?php the_title(); ?></span>
        </nav>
      <?php } ?>
    </header>

    <article <?php post_class('mx-auto'); ?> style="max-width: 820px;">
        <?php if (has_post_thumbnail()): ?>
        <figure class="mb-4 single-project-thumb">
            <?php the_post_thumbnail('full', [
            'class'   => 'w-100 h-100',
            'loading' => 'lazy'
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
          echo '<div class="mb-2 fw-semibold">Tech stack</div>';
          echo '<div class="tech-list">' . wp_kses_post($tech_list) . '</div>';
        }
        ?>
        <div class="footer-meta small mt-4">
          <?php echo get_the_date(); ?> • by <?php the_author(); ?>
          <?php $c = get_comments_number(); echo ' • <i class="fa-regular fa-comment"></i> ' . intval($c); ?>
        </div>
      </footer>

      <hr class="my-5"/>
      <?php comments_template(); ?>
    </article>

  <?php endwhile; endif; ?>
</div>

<?php get_footer();
