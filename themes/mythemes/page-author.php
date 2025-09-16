<?php
/**
 * Template Name: Author Page 
 */
if (!defined('ABSPATH')) exit;
get_header();

/* ========== Lấy Author ========== */
// Nếu có ACF user field 'author_user' thì dùng, không thì lấy user đầu tiên có bài viết
$acf_ok   = function_exists('get_field');
$ap_user  = $acf_ok ? get_field('author_user') : null; // kiểu 'User' (object WP_User)
if (empty($ap_user)) {
  $users = get_users([
    'has_published_posts' => ['post'],
    'number'              => 1,
  ]);
  $ap_user = $users ? $users[0] : wp_get_current_user();
}
$author_id   = is_object($ap_user) ? $ap_user->ID : (int)$ap_user;
$displayname = get_the_author_meta('display_name', $author_id);
$bio         = get_the_author_meta('description',  $author_id);

// Đếm số bài
$post_count  = count_user_posts($author_id, 'post', true);

/* ========== Social (tùy chọn) ==========
   Bạn có thể tạo ACF user fields: social_facebook, social_instagram, social_x,
   social_youtube, social_behance, social_dribbble … (Text URL). Nếu trống, render '#'.
*/
$socials = [
  'facebook' => get_user_meta($author_id, 'social_facebook', true),
  'instagram'=> get_user_meta($author_id, 'social_instagram', true),
  'x'        => get_user_meta($author_id, 'social_x', true),
  'youtube'  => get_user_meta($author_id, 'social_youtube', true),
  'behance'  => get_user_meta($author_id, 'social_behance', true),
  'dribbble' => get_user_meta($author_id, 'social_dribbble', true),
];
foreach ($socials as $k => $v) { if (empty($v)) $socials[$k] = '#'; }

/* ========== Hero Author ========== */
?>
<section class="author-hero py-5 text-center">
  <div class="container-xxl">
    <div class="mx-auto" style="max-width:880px">
      <div class="mb-3">
        <?php echo get_avatar($author_id, 128, '', $displayname, ['class'=>'rounded-circle']); ?>
      </div>
      <h1 class="h2 fw-bold mb-2">Hi, I'm <?php echo esc_html($displayname); ?></h1>

      <div class="badge-cat d-inline-flex mb-3"><?php echo intval($post_count); ?> Articles</div>

      <?php if (!empty($bio)) : ?>
        <p class="lead text-muted mb-3"><?php echo esc_html($bio); ?></p>
      <?php endif; ?>

      <!-- Social icons (luôn hiển thị) -->
      <div class="d-flex justify-content-center align-items-center gap-3">
        <a class="social-ic d-inline-flex align-items-center justify-content-center"
           href="<?php echo esc_url($socials['facebook']); ?>" target="_blank" rel="noopener">
          <i class="fa-brands fa-facebook-f"></i>
        </a>
        <a class="social-ic d-inline-flex align-items-center justify-content-center"
           href="<?php echo esc_url($socials['instagram']); ?>" target="_blank" rel="noopener">
          <i class="fa-brands fa-instagram"></i>
        </a>
        <a class="social-ic d-inline-flex align-items-center justify-content-center"
           href="<?php echo esc_url($socials['x']); ?>" target="_blank" rel="noopener">
          <i class="fa-brands fa-x-twitter"></i>
        </a>
        <a class="social-ic d-inline-flex align-items-center justify-content-center"
           href="<?php echo esc_url($socials['youtube']); ?>" target="_blank" rel="noopener">
          <i class="fa-brands fa-youtube"></i>
        </a>
        <a class="social-ic d-inline-flex align-items-center justify-content-center"
           href="<?php echo esc_url($socials['behance']); ?>" target="_blank" rel="noopener">
          <i class="fa-brands fa-behance"></i>
        </a>
        <a class="social-ic d-inline-flex align-items-center justify-content-center"
           href="<?php echo esc_url($socials['dribbble']); ?>" target="_blank" rel="noopener">
          <i class="fa-brands fa-dribbble"></i>
        </a>
      </div>
    </div>
  </div>
</section>

<?php
/* ========== Query bài viết của Author ========== */
$paged = max(1, get_query_var('paged') ?: get_query_var('page'));
$q = new WP_Query([
  'post_type'      => 'post',
  'author'         => $author_id,
  'posts_per_page' => 9,
  'paged'          => $paged,
  'ignore_sticky_posts' => true,
]);
?>

<section class="author-posts pb-5">
  <div class="container-xxl">
    <div class="row g-4">
      <?php if ($q->have_posts()): while ($q->have_posts()): $q->the_post();
        $cats = get_the_category();
        $cat_name = $cats ? $cats[0]->name : 'Blog';
        $cat_link = $cats ? get_category_link($cats[0]->term_id) : '#';
      ?>
        <div class="col-md-6 col-lg-4">
          <article class="ap-card">
            <!-- Ảnh tròn -->
            <a class="d-block ap-thumb" href="<?php the_permalink(); ?>">
              <?php
                if (has_post_thumbnail()){
                  the_post_thumbnail('large', ['class'=>'w-100 h-100 object-fit-cover']);
                } else {
                  echo '<span class="d-block w-100 h-100 bg-light"></span>';
                }
              ?>
            </a>

            <!-- Badge category -->
            <a class="ap-badge text-decoration-none" href="<?php echo esc_url($cat_link); ?>">
              <i class="fa-regular fa-circle"></i> <?php echo esc_html($cat_name); ?>
            </a>

            <!-- Tiêu đề -->
            <h3 class="ap-title">
              <a class="ap-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>

            <!-- Meta -->
            <div class="ap-meta">
              <?php the_author(); ?> •
              <?php echo esc_html( human_time_diff(get_the_time('U'), current_time('timestamp')) ); ?> ago
            </div>
          </article>
        </div>
      <?php endwhile; ?>

      <?php wp_reset_postdata(); else: ?>
        <div class="col-12"><p>Author chưa có bài viết.</p></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php get_footer(); ?>
