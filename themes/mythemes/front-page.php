<?php /* Front Page */ get_header(); ?>
<main class="py-5">

<!-- HERO SLIDER (HSP) -->
<section class="container-fluid p-0 hero-slider-wrap" aria-label="<?php echo esc_attr__('Hero slider', 'mythemes'); ?>">
  <div class="swiper hero-swiper">
    <div class="swiper-wrapper">
      <?php
      // Lấy danh sách bài được tick “Show in Home Slider”
      $q = function_exists('hsp_get_slider_query')
            ? hsp_get_slider_query([
                // 'posts_per_page' => 5, // bỏ comment nếu muốn ép số lượng tại đây
              ])
            : new WP_Query(['post_type'=>'post','posts_per_page'=>0]); // fallback trống

      if ($q->have_posts()):
        while($q->have_posts()): $q->the_post();
          $cat = get_the_category();
          $cat_name = $cat ? $cat[0]->name : esc_html__('Blog', 'mythemes');
      ?>
      <div class="swiper-slide">
        <div class="row align-items-center g-4 hero-slide">
          <!-- Text trái -->
          <div class="col-lg-6 order-2 order-lg-1">
            <span class="badge rounded-pill px-3 py-2 bg-dark text-white fw-semibold mb-3">
              <i class="fa-regular fa-circle me-2" aria-hidden="true"></i><?php echo esc_html($cat_name); ?>
            </span>
            <h3 class="display-5 fw-bold lh-sm mb-3">
              <a class="text-decoration-none text-dark" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr__('View post', 'mythemes'); ?>">
                <?php the_title(); ?>
              </a>
            </h3>
            <p class="text-muted">
              <?php
              /* translators: %s: time ago, e.g. '5 mins' */
              $ago = human_time_diff(get_the_time('U'), current_time('timestamp'));
              printf( esc_html__('%s ago', 'mythemes'), esc_html($ago) );
              ?>
            </p>
          </div>

          <!-- Ảnh tròn phải -->
          <div class="col-lg-6 order-1 order-lg-2">
            <div class="hero-circle mx-lg-auto">
              <?php if (has_post_thumbnail()) {
                // GỢI Ý: dùng size “slider-lg” nếu đã add_image_size để cắt trung tâm
                the_post_thumbnail('large', ['class'=>'w-100 h-100 object-fit-cover rounded-circle', 'alt'=>esc_attr(get_the_title())]);
              } else {
                echo '<div class="w-100 h-100 rounded-circle bg-light" aria-hidden="true"></div>';
              } ?>
            </div>
          </div>
        </div>
      </div>
      <?php
        endwhile; wp_reset_postdata();
      else:
        // Không có bài nào được tick -> fallback
        echo '<div class="swiper-slide"><div class="p-5">'.esc_html__('No slides selected.', 'mythemes').'</div></div>';
      endif; ?>
    </div>

    <!-- Nút điều hướng -->
    <div class="hero-nav hero-prev" aria-label="<?php echo esc_attr__('Previous slide', 'mythemes'); ?>"><i class="fa-solid fa-angles-left" aria-hidden="true"></i></div>
    <div class="hero-nav hero-next" aria-label="<?php echo esc_attr__('Next slide', 'mythemes'); ?>"><i class="fa-solid fa-angles-right" aria-hidden="true"></i></div>
  </div>
</section>


<!-- CATEGORIES (1 hàng, ACF image, luôn hiển thị 8) -->
<section class="cat-strip bg-light py-5" aria-label="<?php echo esc_attr__('Popular categories', 'mythemes'); ?>">
  <div class="container">
    <div class="cat-row">
      <?php
      $cats = get_terms([
        'taxonomy'   => 'category',
        'hide_empty' => false,     // đừng ẩn category trống
        'number'     => 8,
        'orderby'    => 'name',
        'order'      => 'ASC',
      ]);

      if (!is_wp_error($cats) && $cats) :
        foreach ($cats as $cat):
          // Lấy ảnh từ ACF (field name: category_image)
          $acf_img = get_field('category_image', 'category_' . $cat->term_id);
          if (is_array($acf_img) && !empty($acf_img['url'])) {
            $img_url = $acf_img['sizes']['medium'] ?? $acf_img['url'];
          } elseif (!empty($acf_img)) {
            $img_url = wp_get_attachment_image_url($acf_img, 'medium');
          } else {
            $img_url = 'https://picsum.photos/300?random=' . $cat->term_id; // fallback
          }
      ?>
        <a class="cat-item text-center text-decoration-none" href="<?php echo esc_url(get_term_link($cat)); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: category name */ __('View category: %s', 'mythemes'), $cat->name ) ); ?>">
          <span class="cat-thumb">
            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($cat->name); ?>">
          </span>
          <div class="cat-name fw-semibold mt-2"><?php echo esc_html($cat->name); ?></div>
        </a>
      <?php
        endforeach;
      endif;
      ?>
    </div>
  </div>
</section>

<!-- POSTS LIST (2 cột, container-xxl) -->
<section class="posts-list py-5" aria-label="<?php echo esc_attr__('Latest posts', 'mythemes'); ?>">
  <div class="container-xxl">
    <div class="row g-4">

      <?php
      $paged = max( 1, get_query_var('paged') ?: get_query_var('page') );

      $q = new WP_Query([
        'post_type'           => 'post',
        'posts_per_page'      => 6,
        'paged'               => $paged,
        'ignore_sticky_posts' => true,
      ]);

      if ($q->have_posts()):
        while($q->have_posts()): $q->the_post();
          $cat = get_the_category();
          $cat_name = $cat ? $cat[0]->name : esc_html__('Blog', 'mythemes');
      ?>
        <div class="col-md-6">
          <article <?php post_class('post-card2'); ?>>
            <div class="row g-0 align-items-center">
              <!-- Thumb tròn -->
              <div class="col-auto p-4">
                <div class="thumb-round">
                  <?php
                  if (has_post_thumbnail()) {
                    the_post_thumbnail('medium', ['class'=>'w-100 h-100 object-fit-cover rounded-circle', 'alt'=>esc_attr(get_the_title())]);
                  } else {
                    echo '<div class="w-100 h-100 rounded-circle bg-light" aria-hidden="true"></div>';
                  }
                  ?>
                </div>
              </div>

              <!-- Nội dung -->
              <div class="col p-4">
                <span class="badge-cat">
                  <i class="fa-regular fa-circle me-2" aria-hidden="true"></i><?php echo esc_html($cat_name); ?>
                </span>

                <h3 class="h4 fw-bold mt-3 mb-2">
                  <a class="post-link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr__('View post', 'mythemes'); ?>">
                    <?php the_title(); ?>
                  </a>
                </h3>

                <div class="post-meta">
                  <?php
                  /* translators: %s: time ago, e.g. '3 hours' */
                  $ago = human_time_diff(get_the_time('U'), current_time('timestamp'));
                  printf( esc_html__('%s ago', 'mythemes'), esc_html($ago) );
                  ?>
                </div>
              </div>
            </div>
          </article>
        </div>
      <?php endwhile; ?>

      <?php
        wp_reset_postdata();
      else:
        echo '<p>'.esc_html__('No posts yet.', 'mythemes').'</p>';
      endif;
      ?>

    </div>
  </div>
</section>

<!-- FEATURED POST -->
<section class="featured-post py-5" aria-label="<?php echo esc_attr__('Featured post', 'mythemes'); ?>">
  <div class="container-xxl">
    <div class="row align-items-center g-5">
      <?php
      // 1) Lấy ID featured theo thứ tự: plugin -> Customizer -> bài mới nhất
      $pid = 0;

      if (function_exists('myfp_get_featured_post_id')) {
        $pid = (int) myfp_get_featured_post_id(); // từ plugin (đa ngôn ngữ)
      }

      if (!$pid) {
        $pid = (int) get_theme_mod('featured_post_id', 0); // từ Customizer
      }

      if (!$pid) {
        $latest = new WP_Query([
          'post_type'           => 'post',
          'posts_per_page'      => 1,
          'ignore_sticky_posts' => true,
          'no_found_rows'       => true,
          'fields'              => 'ids',
        ]);
        if (!empty($latest->posts[0])) {
          $pid = (int) $latest->posts[0];
        }
        wp_reset_postdata();
      }

      // Nếu vẫn không có bài thì dừng (khỏi render phần này)
      if ($pid) :
        // 2) Thu thập dữ liệu một lần
        $title       = get_the_title($pid);
        $permalink   = get_permalink($pid);
        $excerpt     = wp_trim_words(get_the_excerpt($pid), 30);
        $author_id   = (int) get_post_field('post_author', $pid);
        $author_name = get_the_author_meta('display_name', $author_id);
        $ago         = human_time_diff(get_post_time('U', false, $pid), current_time('timestamp'));
        $cats        = get_the_category($pid);
        $cat_name    = $cats ? $cats[0]->name : esc_html__('Blog', 'mythemes');

        // 3) Render 1 lần
      ?>
      <div class="col-lg-6">
        <span class="badge-cat">
          <i class="fa-regular fa-circle me-2" aria-hidden="true"></i><?php echo esc_html($cat_name); ?>
        </span>

        <h2 class="featured-title mt-3 mb-3">
          <a href="<?php echo esc_url($permalink); ?>"
             class="featured-link"
             aria-label="<?php echo esc_attr__('View post', 'mythemes'); ?>">
            <?php echo esc_html($title); ?>
          </a>
        </h2>

        <?php if ($excerpt) : ?>
          <p class="lead text-muted"><?php echo esc_html($excerpt); ?></p>
        <?php endif; ?>

        <div class="d-flex align-items-center mt-4 gap-3">
          <?php echo get_avatar($author_id, 40, '', '', ['class' => 'rounded-circle']); ?>
          <div class="small text-muted">
            <?php
              /* translators: 1: author name, 2: time ago */
              printf( esc_html__('%1$s • %2$s ago', 'mythemes'), esc_html($author_name), esc_html($ago) );
            ?>
          </div>
        </div>
      </div>

      <div class="col-lg-6 text-center">
        <div class="featured-thumb mx-auto">
          <?php if (has_post_thumbnail($pid)) :
            echo get_the_post_thumbnail($pid, 'large', [
              'class'    => 'w-100 h-100 object-fit-cover rounded-circle',
              'alt'      => esc_attr($title),
              'loading'  => 'lazy',
              'decoding' => 'async',
            ]);
          else : ?>
            <img class="rounded-circle w-100 h-100 object-fit-cover"
                 src="https://picsum.photos/500/500"
                 alt="<?php echo esc_attr__('Placeholder image', 'mythemes'); ?>"
                 loading="lazy" decoding="async">
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>


</main>
<?php get_footer(); ?>
