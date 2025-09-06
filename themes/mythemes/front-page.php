<?php /* Front Page */ get_header(); ?>
<main class="py-5">
  <!-- HERO SLIDER -->
<section class="container-fluid p-0 hero-slider-wrap">
  <div class="swiper hero-swiper">
    <div class="swiper-wrapper">
      <?php
      $q = new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => 5,
        'ignore_sticky_posts' => true
      ]);

      if ($q->have_posts()):
        while($q->have_posts()): $q->the_post();
          $cat = get_the_category();
          $cat_name = $cat ? $cat[0]->name : 'Blog';
      ?>
      <div class="swiper-slide">
        <div class="row align-items-center g-4 hero-slide">
          <!-- Text trái -->
          <div class="col-lg-6 order-2 order-lg-1">
            <span class="badge rounded-pill px-3 py-2 bg-dark text-white fw-semibold mb-3">
              <i class="fa-regular fa-circle me-2"></i><?php echo esc_html($cat_name); ?>
            </span>
            <h3 class="display-5 fw-bold lh-sm mb-3">
              <a class="text-decoration-none text-dark" href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
              </a>
            </h3>
            <p class="text-muted">
              <?php the_author(); ?> • <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago
            </p>
          </div>

          <!-- Ảnh tròn phải -->
          <div class="col-lg-6 order-1 order-lg-2">
            <div class="hero-circle mx-lg-auto">
              <?php if (has_post_thumbnail()) {
                the_post_thumbnail('large', ['class'=>'w-100 h-100 object-fit-cover rounded-circle']);
              } else {
                // ảnh dự phòng
                echo '<div class="w-100 h-100 rounded-circle bg-light"></div>';
              } ?>
            </div>
          </div>
        </div>
      </div>
      <?php
        endwhile; wp_reset_postdata();
      endif; ?>
    </div>

    <!-- Nút điều hướng -->
    <div class="hero-nav hero-prev"><i class="fa-solid fa-angles-left"></i></div>
    <div class="hero-nav hero-next"><i class="fa-solid fa-angles-right"></i></div>
  </div>
</section>

<!-- CATEGORIES (1 hàng, ACF image, luôn hiển thị 8) -->
<section class="cat-strip bg-light py-5">
  <div class="container">
    <div class="cat-row">
      <?php
      $cats = get_terms([
        'taxonomy'   => 'category',
        'hide_empty' => false,     // <= quan trọng: đừng ẩn category trống
        'number'     => 8,
        'orderby'    => 'name',
        'order'      => 'ASC',
      ]);

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
        <a class="cat-item text-center text-decoration-none" href="<?php echo esc_url(get_term_link($cat)); ?>">
          <span class="cat-thumb">
            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($cat->name); ?>">
          </span>
          <div class="cat-name fw-semibold mt-2"><?php echo esc_html($cat->name); ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- POSTS LIST (2 cột, container-xxl) -->
<section class="posts-list py-5">
  <div class="container-xxl">
    <div class="row g-4">

      <?php
      $paged = max( 1, get_query_var('paged') ?: get_query_var('page') );

      $q = new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => 6,
        'paged'          => $paged,
        'ignore_sticky_posts' => true,
      ]);

      if ($q->have_posts()):
        while($q->have_posts()): $q->the_post();
          $cat = get_the_category();
          $cat_name = $cat ? $cat[0]->name : 'Blog';
      ?>
        <div class="col-md-6">
          <article class="post-card2">
            <div class="row g-0 align-items-center">
              <!-- Thumb tròn -->
              <div class="col-auto p-4">
                <div class="thumb-round">
                  <?php
                  if (has_post_thumbnail()) {
                    the_post_thumbnail('medium', ['class'=>'w-100 h-100 object-fit-cover rounded-circle']);
                  } else {
                    echo '<div class="w-100 h-100 rounded-circle bg-light"></div>';
                  }
                  ?>
                </div>
              </div>

              <!-- Nội dung -->
              <div class="col p-4">
                <span class="badge-cat">
                  <i class="fa-regular fa-circle me-2"></i><?php echo esc_html($cat_name); ?>
                </span>

                <h3 class="h4 fw-bold mt-3 mb-2">
                  <a class="post-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>

                <div class="post-meta">
                  <?php the_author(); ?> •
                  <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago
                </div>
              </div>
            </div>
          </article>
        </div>
      <?php endwhile; ?>

      <!-- Pagination -->
      <div class="col-12 mt-3">
        <?php
          echo paginate_links([
            'total'   => $q->max_num_pages,
            'current' => $paged,
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'type'    => 'list',
          ]);
        ?>
      </div>

      <?php
        wp_reset_postdata();
      else:
        echo '<p>Chưa có bài viết.</p>';
      endif;
      ?>

    </div>
  </div>
</section>

<!-- FEATURED POST -->
<section class="featured-post py-5">
  <div class="container-xxl">
    <div class="row align-items-center g-5">
      <?php
      // Lấy post ID từ Customizer
      $post_id = (int) get_theme_mod('featured_post_id', 0);

      if ($post_id) {
        // Có bài được chọn → hiển thị trực tiếp
        $post = get_post($post_id);
        if ($post) { setup_postdata($post); }
        $cat = get_the_category($post_id); $cat_name = $cat ? $cat[0]->name : 'Blog';
      ?>
        <div class="col-lg-6">
          <span class="badge-cat"><i class="fa-regular fa-circle me-2"></i><?php echo esc_html($cat_name); ?></span>
          <h2 class="featured-title mt-3 mb-3">
            <a href="<?php echo get_permalink($post_id); ?>" class="featured-link"><?php echo get_the_title($post_id); ?></a>
          </h2>
          <p class="lead text-muted"><?php echo wp_trim_words(get_the_excerpt($post_id), 30); ?></p>
          <div class="d-flex align-items-center mt-4 gap-3">
            <?php echo get_avatar(get_post_field('post_author', $post_id), 40, '', '', ['class'=>'rounded-circle']); ?>
            <div class="small text-muted">
              <?php echo get_the_author_meta('display_name', get_post_field('post_author', $post_id)); ?> •
              <?php echo human_time_diff(get_post_time('U', false, $post_id), current_time('timestamp')); ?> ago
            </div>
          </div>
        </div>
        <div class="col-lg-6 text-center">
          <div class="featured-thumb mx-auto">
            <?php
              if (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, 'large', ['class'=>'w-100 h-100 object-fit-cover rounded-circle']);
              } else {
                echo '<img class="rounded-circle w-100 h-100 object-fit-cover" src="https://picsum.photos/500/500" alt="">';
              }
            ?>
          </div>
        </div>
      <?php
        if ($post) { wp_reset_postdata(); }
      } else {
        // Không chọn → fallback bài mới nhất
        $featured = new WP_Query(['post_type'=>'post','posts_per_page'=>1,'ignore_sticky_posts'=>true]);
        if ($featured->have_posts()):
          while ($featured->have_posts()): $featured->the_post();
            $cat = get_the_category(); $cat_name = $cat ? $cat[0]->name : 'Blog';
      ?>
        <div class="col-lg-6">
          <span class="badge-cat"><i class="fa-regular fa-circle me-2"></i><?php echo esc_html($cat_name); ?></span>
          <h2 class="featured-title mt-3 mb-3">
            <a href="<?php the_permalink(); ?>" class="featured-link"><?php the_title(); ?></a>
          </h2>
          <p class="lead text-muted"><?php echo wp_trim_words(get_the_excerpt(), 30); ?></p>
          <div class="d-flex align-items-center mt-4 gap-3">
            <?php echo get_avatar(get_the_author_meta('ID'), 40, '', '', ['class'=>'rounded-circle']); ?>
            <div class="small text-muted"><?php the_author(); ?> • <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')); ?> ago</div>
          </div>
        </div>
        <div class="col-lg-6 text-center">
          <div class="featured-thumb mx-auto">
            <?php if (has_post_thumbnail()) { the_post_thumbnail('large', ['class'=>'w-100 h-100 object-fit-cover rounded-circle']); }
                  else { echo '<img class="rounded-circle w-100 h-100 object-fit-cover" src="https://picsum.photos/500/500" alt="">'; } ?>
          </div>
        </div>
      <?php
          endwhile; wp_reset_postdata();
        endif;
      }
      ?>
    </div>
  </div>
</section>


</main>
<?php get_footer(); ?>
