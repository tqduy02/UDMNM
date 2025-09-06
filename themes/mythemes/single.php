<?php get_header(); ?>
<main class="container">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<article>
<h1><?php the_title(); ?></h1>
<p class="meta"><?php the_author(); ?> • <?php echo get_the_date(); ?></p>
<?php the_content(); ?>
</article>
<?php endwhile; endif; ?>
</main>
<?php get_footer(); ?>