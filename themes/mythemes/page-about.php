<?php
/**
 * Template Name: About
 * Description: About page – uses Featured Image as round hero.
 */
if (!defined('ABSPATH')) exit;
get_header();

$ok = function_exists('get_field');

// Text fields
$heading   = $ok ? get_field('about_heading') : '';
$intro     = $ok ? get_field('about_intro')   : '';
$btn_text  = $ok ? get_field('about_btn_text') : '';
$btn_url   = $ok ? get_field('about_btn_url')  : '';

// Social (có thể trống nhưng vẫn render)
$fb = $ok ? (string) get_field('facebook_url')  : '';
$ig = $ok ? (string) get_field('instagram_url') : '';
$tw = $ok ? (string) get_field('twitter_url')   : '';
$be = $ok ? (string) get_field('behance_url')   : '';
$dr = $ok ? (string) get_field('dribbble_url')  : '';

$page_title = $heading !== '' ? $heading : get_the_title();

// Lấy Featured Image nếu có
$feat_id = has_post_thumbnail() ? get_post_thumbnail_id() : 0;
?>

<!-- ===== Breadcrumb ===== -->
<section class="about-breadcrumb pt-4 pb-2">
  <div class="container-1120 text-center">
    <h1 class="about-title fw-bold mb-2"><?php echo esc_html($page_title); ?></h1>
    <nav class="crumbs small text-muted" aria-label="breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>" class="text-reset text-decoration-none">Home</a>
      <span class="mx-2">›</span>
      <span class="text-dark"><?php echo esc_html(get_the_title()); ?></span>
    </nav>
  </div>
</section>

<!-- ===== Hero ===== -->
<section class="about-hero py-5">
  <div class="container-1120">
    <div class="row align-items-center g-5">
      <div class="col-lg-5">
        <div class="about-round overflow-hidden mx-lg-0 mx-auto">
          <?php if ($feat_id): ?>
            <?php echo wp_get_attachment_image($feat_id, 'large', false, [
              'class'=>'w-100 h-100 object-fit-cover',
              'alt'  => esc_attr($page_title),
            ]); ?>
          <?php endif; ?>
        </div>
      </div>

      <div class="col-lg-7">
        <?php if (!empty($intro)): ?>
          <div class="about-intro text-muted mb-4">
            <?php echo wp_kses_post($intro); ?>
          </div>
        <?php endif; ?>

        <!-- CTA + Social -->
        <div class="d-flex align-items-center gap-3 mt-4">
          <?php 
            // Nếu không có URL thì để là "#" để nút vẫn hiển thị
            $btn_url = !empty($about_btn_url) ? esc_url($about_btn_url) : '#';
          ?>
          <a href="<?php echo $btn_url; ?>" class="about-cta">Get In Touch</a>

          <div class="about-social d-flex gap-2">
            <a href="<?php echo !empty($about_facebook) ? esc_url($about_facebook) : '#'; ?>" class="soc"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="<?php echo !empty($about_instagram) ? esc_url($about_instagram) : '#'; ?>" class="soc"><i class="fa-brands fa-instagram"></i></a>
            <a href="<?php echo !empty($about_twitter) ? esc_url($about_twitter) : '#'; ?>" class="soc"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="<?php echo !empty($about_youtube) ? esc_url($about_youtube) : '#'; ?>" class="soc"><i class="fa-brands fa-youtube"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php get_footer(); ?>
