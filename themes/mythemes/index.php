<?php get_header(); ?>
<main id="content" class="container" role="main">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
  <article <?php post_class('post-card'); ?>>

    <?php
    if (has_post_thumbnail()) {
      the_post_thumbnail('medium', ['loading' => 'lazy', 'alt' => esc_attr(get_the_title())]);
    }
    ?>

    <div>
      <?php
      $cats = get_the_category();
      if ($cats) {
        echo '<span class="badge">' . esc_html($cats[0]->name) . '</span>';
      }
      ?>

      <h2>
        <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr__('View post', 'mythemes'); ?>">
          <?php the_title(); ?>
        </a>
      </h2>

      <p class="meta">
        <?php
        /* translators: 1: author display name, 2: date */
        printf(
          esc_html__('%1$s • %2$s', 'mythemes'),
          esc_html(get_the_author()),
          esc_html(get_the_date(get_option('date_format')))
        );
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
  <p><?php echo esc_html__('No posts yet.', 'mythemes'); ?></p>
<?php endif; ?>
</main>
<?php get_footer(); ?>
