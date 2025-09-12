<?php
/**
 * Single Post Template
 */
if (!defined('ABSPATH')) exit;
get_header();

/** ===== ACF (Free) fields ===== */
$acf_ok        = function_exists('get_field');
$hero_subtitle = $acf_ok ? get_field('hero_subtitle') : '';
$hero_image_id = $acf_ok ? get_field('hero_image')    : 0;
$intro_rich    = $acf_ok ? get_field('intro_rich')    : '';
$banner_img_id = $acf_ok ? get_field('banner_image')  : 0;
$banner_link   = $acf_ok ? get_field('banner_link')   : '';
$banner_label  = $acf_ok ? get_field('banner_label')  : '';
$gallery_imgs  = $acf_ok ? get_field('gallery_3round'): '';

/** Category badge */
$cats = get_the_category();
$cat_name = $cats ? $cats[0]->name : 'Blog';
$cat_link = $cats ? get_category_link($cats[0]->term_id) : '#';
?>

<!-- ===== HERO ===== -->
<section class="single-hero py-5">
  <div class="container-xxl">
    <div class="hero-card rounded-4 p-4 p-lg-5">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">

          <a class="badge-cat text-decoration-none d-inline-flex align-items-center mb-3"
             href="<?php echo esc_url($cat_link); ?>">
            <i class="fa-regular fa-circle me-2"></i><?php echo esc_html($cat_name); ?>
          </a>

          <h1 class="display-6 fw-bold mb-3"><?php the_title(); ?></h1>

          <?php if (!empty($hero_subtitle)) : ?>
            <p class="lead text-muted mb-4"><?php echo esc_html($hero_subtitle); ?></p>
          <?php endif; ?>

          <div class="d-flex align-items-center gap-3 meta-line">
            <span class="avatar-40 rounded-circle overflow-hidden">
              <?php echo get_avatar(get_the_author_meta('ID'), 40); ?>
            </span>
            <span><?php the_author(); ?></span>
            <span class="dot">•</span>
            <span><?php echo esc_html( human_time_diff(get_the_time('U'), current_time('timestamp')) ); ?> ago</span>
            <span class="dot">•</span>
            <span><?php echo intval( get_comments_number() ); ?> Comments</span>
          </div>
        </div>

        <div class="col-lg-5">
          <?php if ($hero_image_id) : ?>
            <div class="hero-thumb mx-lg-auto">
              <?php echo wp_get_attachment_image($hero_image_id, 'large', false, [
                'class' => 'w-100 h-100 object-fit-cover rounded-circle',
                'alt'   => esc_attr(get_the_title()),
              ]); ?>
            </div>
          <?php elseif (has_post_thumbnail()) : ?>
            <div class="hero-thumb mx-lg-auto">
              <?php the_post_thumbnail('large', ['class'=>'w-100 h-100 object-fit-cover rounded-circle']); ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== BODY ===== -->
<section class="single-body pb-5">
  <div class="container-xxl">
    <article <?php post_class('entry'); ?>>

      <?php if (!empty($intro_rich)) : ?>
        <div class="cb-rich mb-4">
          <?php echo wp_kses_post($intro_rich); ?>
        </div>
      <?php endif; ?>

      <div class="entry-content">
        <?php
          while (have_posts()) : the_post();
            the_content();
          endwhile;
        ?>
      </div>

      <!-- Gallery 3 Round Images (nếu có) -->
      <?php if ($gallery_imgs): ?>
        <div class="cb-gallery3 text-center my-5">
          <div class="d-flex justify-content-center gap-4 flex-wrap">
            <?php foreach ($gallery_imgs as $img_id): ?>
              <span class="circle-240">
                <?php echo wp_get_attachment_image($img_id, 'large', false, [
                  'class'=>'w-100 h-100 object-fit-cover rounded-circle'
                ]); ?>
              </span>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

          <!-- Banner -->
      <?php if ($banner_img_id) : ?>
        <div class="cb-banner mt-4">
          <?php if (!empty($banner_link)) : ?><a href="<?php echo esc_url($banner_link); ?>" class="d-block"><?php endif; ?>
            <?php echo wp_get_attachment_image($banner_img_id, 'full', false, ['class'=>'w-100 rounded-4']); ?>
          <?php if (!empty($banner_link)) : ?></a><?php endif; ?>
        </div>
      <?php endif; ?>


      <!-- Tags + Share -->
      <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mt-4">
        <div class="post-tags">
          <?php the_tags('', ''); ?>
        </div>
        <div class="share-icons d-flex align-items-center gap-2">
          <a class="btn-share" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener"><i class="fa-brands fa-facebook-f"></i></a>
          <a class="btn-share" href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo rawurlencode(get_the_title()); ?>" target="_blank" rel="noopener"><i class="fa-brands fa-x-twitter"></i></a>
          <a class="btn-share" href="https://pinterest.com/pin/create/button/?url=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener"><i class="fa-brands fa-pinterest-p"></i></a>
        </div>
      </div>
    </article>
  </div>
</section>

<!-- ===== AUTHOR BOX ===== -->
<section class="author-box-wrap pb-5">
  <div class="container-xxl">
    <div class="author-card rounded-4 p-4 p-lg-5 text-center">
      <div class="author-avatar mx-auto mb-3">
        <?php echo get_avatar(get_the_author_meta('ID'), 120, '', get_the_author(), ['class'=>'rounded-circle']); ?>
      </div>
      <h3 class="h4 fw-bold mb-2">Hi, I'm <?php the_author(); ?></h3>
      <?php if (get_the_author_meta('description')): ?>
        <p class="text-muted mb-3"><?php echo esc_html(get_the_author_meta('description')); ?></p>
      <?php endif; ?>
      <a class="btn btn-dark px-4" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">View Articles</a>
    </div>
  </div>
</section>

<!-- ===== PREV / NEXT ===== -->
<section class="adjacent-posts pb-5">
  <div class="container-xxl">
    <div class="row g-4">
      <div class="col-lg-6">
        <?php $prev = get_previous_post();
        if ($prev): ?>
          <a class="adj-card d-flex align-items-center gap-3 p-3 rounded-4 text-decoration-none" href="<?php echo esc_url(get_permalink($prev)); ?>">
            <span class="adj-arrow"><i class="fa-solid fa-arrow-left-long"></i></span>
            <span class="adj-thumb">
              <?php echo get_the_post_thumbnail($prev->ID, 'thumbnail', ['class'=>'rounded-circle']); ?>
            </span>
            <div>
              <div class="small text-muted mb-1"><?php echo esc_html(get_the_date('', $prev)); ?></div>
              <div class="fw-semibold text-dark"><?php echo esc_html(get_the_title($prev)); ?></div>
            </div>
          </a>
        <?php endif; ?>
      </div>

      <div class="col-lg-6">
        <?php $next = get_next_post();
        if ($next): ?>
          <a class="adj-card d-flex align-items-center gap-3 p-3 rounded-4 text-decoration-none justify-content-end" href="<?php echo esc_url(get_permalink($next)); ?>">
            <div class="text-end">
              <div class="small text-muted mb-1"><?php echo esc_html(get_the_date('', $next)); ?></div>
              <div class="fw-semibold text-dark"><?php echo esc_html(get_the_title($next)); ?></div>
            </div>
            <span class="adj-thumb">
              <?php echo get_the_post_thumbnail($next->ID, 'thumbnail', ['class'=>'rounded-circle']); ?>
            </span>
            <span class="adj-arrow"><i class="fa-solid fa-arrow-right-long"></i></span>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- ===== REPLY (only form) ===== -->
<section class="single-reply py-5">
  <div class="container-xxl">
    <?php if ( comments_open() ) :
  comment_form([
    'title_reply'          => __('Share your opinion to my email', 'mythemes'),
    'title_reply_before'   => '<h3 class="mb-3 fw-bold">',
    'title_reply_after'    => '</h3>',

    // Ẩn thông báo "Logged in as..."
    'logged_in_as'         => '',

    // Không dùng fields mặc định (WP ẩn khi logged-in)
    'fields'               => [],

    // Gộp Message + Name + Email để luôn hiển thị
    'comment_field'        =>
      '<p class="comment-form-comment mb-3">' .
        '<textarea id="comment" name="comment" cols="45" rows="6" maxlength="65525" required ' .
        'placeholder="'. esc_attr__('Message*','mythemes') .'" ' .
        'class="form-control rounded-4 p-3"></textarea>' .
      '</p>' .
      '<p class="comment-form-author mb-3">' .
        '<input id="author" name="author" type="text" value="" size="30" maxlength="245" ' .
        'placeholder="'. esc_attr__('Name*','mythemes') .'" ' .
        'class="form-control rounded-4 p-3" />' .
      '</p>' .
      '<p class="comment-form-email mb-3">' .
        '<input id="email" name="email" type="email" value="" size="30" maxlength="100" ' .
        'placeholder="'. esc_attr__('Email*','mythemes') .'" ' .
        'class="form-control rounded-4 p-3" />' .
      '</p>',

    // Tắt notes mặc định
    'comment_notes_before' => '',
    'comment_notes_after'  => '',

    // Nút gửi: button custom để dễ style
    'submit_button'        => '<button type="submit" id="submit" class="btn-reply">%4$s</button>',
    'label_submit'         => __('Send Reply', 'mythemes'),
    'submit_field'         => '<p class="form-submit mt-3">%1$s %2$s</p>',
  ]);
endif; ?>

  </div>
</section>


<?php get_footer(); ?>
