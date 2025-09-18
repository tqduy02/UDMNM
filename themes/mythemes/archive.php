<?php get_header(); ?>
<main class="container" role="main">
  <h1 class="page-title h2"><?php the_archive_title(); ?></h1>

  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <article <?php post_class('post-card'); ?>>

        <?php
        if (has_post_thumbnail()) {
          the_post_thumbnail('medium', ['loading' => 'lazy']);
        }
        ?>

        <div>
          <?php
          $cats = get_the_category();
          if ($cats) {
            echo '<span class="badge">' . esc_html($cats[0]->name) . '</span>';
          }
          ?>

          <h2 class="h5">
            <a class="underline-animate"
               href="<?php the_permalink(); ?>"
               aria-label="<?php echo esc_attr__('View post', 'mythemes'); ?>">
              <?php the_title(); ?>
            </a>
          </h2>

          <p class="meta">
            <?php
            /* translators: %s: author display name */
            printf( esc_html__('By %s', 'mythemes'), esc_html( get_the_author() ) );
            echo ' • ' . esc_html( get_the_date( get_option('date_format') ) );
            ?>
          </p>

          <p><?php echo wp_kses_post( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
        </div>
      </article>
    <?php endwhile; ?>

    <?php
    the_posts_pagination([
      'prev_text'          => esc_html__('Previous', 'mythemes'),
      'next_text'          => esc_html__('Next', 'mythemes'),
      'screen_reader_text' => esc_html__('Posts navigation', 'mythemes'),
    ]);
    ?>

  <?php else : ?>
    <p><?php echo esc_html__('No posts.', 'mythemes'); ?></p>
  <?php endif; ?>
</main>
<?php get_footer(); ?>
