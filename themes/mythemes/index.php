<?php get_header(); ?>
<main class="container">
<?php if (have_posts()): while (have_posts()): the_post(); ?>
<article class="post-card">
<?php if (has_post_thumbnail()) { the_post_thumbnail('medium'); } ?>
<div>
<?php $cats = get_the_category(); if($cats){ echo '<span class="badge">'.esc_html($cats[0]->name).'</span>'; } ?>
<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
<p class="meta"><?php the_author(); ?> • <?php echo get_the_date(); ?></p>
<p><?php echo wp_kses_post(wp_trim_words(get_the_excerpt(), 28)); ?></p>
</div>
</article>
<?php endwhile; the_posts_pagination(); else: ?>
<p>Chưa có bài viết.</p>
<?php endif; ?>
</main>
<?php get_footer(); ?>