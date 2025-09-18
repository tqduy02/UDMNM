<?php
/**
 * Template Name: About
 * Template Post Type: page
 * Description: About page – uses Featured Image as round hero.
 */
if (!defined('ABSPATH')) exit;

get_header();


$ok      = function_exists('get_field');
$page_id = get_queried_object_id();

// Text fields (ACF – theo ngôn ngữ của bản dịch trang)
$heading  = $ok ? (string) get_field('about_heading', $page_id)  : '';
$intro    = $ok ? (string) get_field('about_intro', $page_id)    : '';
$btn_text = $ok ? (string) get_field('about_btn_text', $page_id) : '';
$btn_url  = $ok ? (string) get_field('about_btn_url', $page_id)  : '';

// Social (có thể trống)
$fb = $ok ? (string) get_field('facebook_url', $page_id)  : '';
$ig = $ok ? (string) get_field('instagram_url', $page_id) : '';
$tw = $ok ? (string) get_field('twitter_url', $page_id)   : '';
$be = $ok ? (string) get_field('behance_url', $page_id)   : '';
$dr = $ok ? (string) get_field('dribbble_url', $page_id)  : '';

$page_title = ($heading !== '') ? $heading : get_the_title();


// Ảnh đại diện trang (round hero)
$feat_id = has_post_thumbnail() ? get_post_thumbnail_id() : 0;
?>

<!-- ===== Breadcrumb ===== -->
<section class="about-breadcrumb pt-4 pb-2">
  <div class="container-1120 text-center">
    <h1 class="about-title fw-bold mb-2"><?php echo esc_html( $page_title ); ?></h1>

    <nav class="crumbs small text-muted" aria-label="<?php echo esc_attr__('Breadcrumb', 'mythemes'); ?>">
      <a href="<?php echo esc_url( home_url('/') ); ?>" class="text-reset text-decoration-none">
        <?php echo esc_html__('Home', 'mythemes'); ?>
      </a>
      <span class="mx-2" aria-hidden="true">›</span>
      <span class="text-dark"><?php echo esc_html( get_the_title() ); ?></span>
    </nav>
  </div>
</section>

<!-- ===== Hero ===== -->
<section class="about-hero py-5" aria-label="<?php echo esc_attr__('About hero', 'mythemes'); ?>">
  <div class="container-1120">
    <div class="row align-items-center g-5">

      <div class="col-lg-5">
        <div class="about-round overflow-hidden mx-lg-0 mx-auto">
          <?php if ($feat_id): ?>
            <?php echo wp_get_attachment_image(
              $feat_id,
              'large',
              false,
              [
                'class' => 'w-100 h-100 object-fit-cover',
                'alt'   => esc_attr( $page_title ),
              ]
            ); ?>
          <?php else: ?>
            <div class="w-100 h-100 bg-light" aria-hidden="true"></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="col-lg-7">

        <?php if (!empty($intro)) : ?>
          <div class="about-intro text-muted mb-4">
            <?php echo wp_kses_post( $intro ); ?>
          </div>
        <?php endif; ?>

        <!-- CTA + Social -->
        <div class="d-flex align-items-center gap-3 mt-4">
          <?php
          // Fallback: nếu không có URL/label, vẫn hiển thị nút với giá trị mặc định
          $cta_url  = !empty($btn_url)  ? esc_url($btn_url) : '#';
          $cta_text = !empty($btn_text) ? $btn_text : esc_html__('Get in touch', 'mythemes');
          ?>
          <a href="<?php echo $cta_url; ?>" class="about-cta">
            <?php echo esc_html( $cta_text ); ?>
          </a>

          <div class="about-social d-flex gap-2" aria-label="<?php echo esc_attr__('Social links', 'mythemes'); ?>">
            <a href="<?php echo $fb ? esc_url($fb) : '#'; ?>" class="soc" aria-label="<?php echo esc_attr__('Facebook', 'mythemes'); ?>">
              <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
            </a>
            <a href="<?php echo $ig ? esc_url($ig) : '#'; ?>" class="soc" aria-label="<?php echo esc_attr__('Instagram', 'mythemes'); ?>">
              <i class="fa-brands fa-instagram" aria-hidden="true"></i>
            </a>
            <a href="<?php echo $tw ? esc_url($tw) : '#'; ?>" class="soc" aria-label="<?php echo esc_attr__('X (Twitter)', 'mythemes'); ?>">
              <i class="fa-brands fa-x-twitter" aria-hidden="true"></i>
            </a>
            <a href="<?php echo $be ? esc_url($be) : '#'; ?>" class="soc" aria-label="<?php echo esc_attr__('Behance', 'mythemes'); ?>">
              <i class="fa-brands fa-behance" aria-hidden="true"></i>
            </a>
            <a href="<?php echo $dr ? esc_url($dr) : '#'; ?>" class="soc" aria-label="<?php echo esc_attr__('Dribbble', 'mythemes'); ?>">
              <i class="fa-brands fa-dribbble" aria-hidden="true"></i>
            </a>
          </div>
        </div>

      </div>

    </div>
  </div>
</section>

<?php get_footer(); ?>
