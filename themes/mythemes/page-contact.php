<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 */

if (!defined('ABSPATH')) exit;
get_header();

$acf_ok = function_exists('get_field');

/** === ACF fields (Free) === */
$hero_title   = $acf_ok ? (get_field('contact_hero_title') ?: get_the_title()) : get_the_title();
$info_title   = $acf_ok ? (get_field('contact_info_title') ?: "Let's Talk about Everything") : "Let's Talk about Everything";
$info_desc    = $acf_ok ? get_field('contact_info_desc') : '';

$address      = $acf_ok ? get_field('contact_address') : '';
$email        = $acf_ok ? get_field('contact_email')   : '';
$phone        = $acf_ok ? get_field('contact_phone')   : '';

$socials = [
  'facebook' => $acf_ok ? get_field('contact_facebook') : '',
  'instagram'=> $acf_ok ? get_field('contact_instagram'): '',
  'x'        => $acf_ok ? get_field('contact_x')        : '',
  'behance'  => $acf_ok ? get_field('contact_behance')  : '',
  'dribbble' => $acf_ok ? get_field('contact_dribbble') : '',
];

$cf7_shortcode = $acf_ok ? (get_field('contact_form_shortcode') ?: '') : '';
?>

<!-- ===== HERO (center) ===== -->
<section class="page-hero py-5 text-center">
  <div class="container-xxl">
    <h1 class="display-6 fw-bold mb-2"><?php echo esc_html($hero_title); ?></h1>
    <nav class="breadcrumb-small" aria-label="breadcrumb">
      <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo esc_html(get_the_title()); ?></li>
      </ol>
    </nav>
  </div>
</section>

<!-- ===== BODY ===== -->
<section class="contact-body pb-5">
  <div class="container-xxl contact-container">
    <div class="row g-5 align-items-start">
      <!-- Left info -->
      <div class="col-lg-5">
        <h2 class="h3 fw-bold mb-3"><?php echo esc_html($info_title); ?></h2>
        <?php if ($info_desc): ?>
          <p class="text-muted mb-4"><?php echo wp_kses_post($info_desc); ?></p>
        <?php endif; ?>

        <?php if ($address): ?>
          <div class="d-flex align-items-start gap-3 mb-3">
            <i class="fa-solid fa-location-dot mt-1"></i>
            <span><?php echo esc_html($address); ?></span>
          </div>
        <?php endif; ?>

        <?php if ($email): ?>
          <div class="d-flex align-items-start gap-3 mb-3">
            <i class="fa-regular fa-envelope mt-1"></i>
            <a href="mailto:<?php echo antispambot($email); ?>"><?php echo antispambot($email); ?></a>
          </div>
        <?php endif; ?>

        <?php if ($phone): ?>
          <div class="d-flex align-items-start gap-3 mb-4">
            <i class="fa-solid fa-phone mt-1"></i>
            <a href="tel:<?php echo preg_replace('/\D+/', '', $phone); ?>"><?php echo esc_html($phone); ?></a>
          </div>
        <?php endif; ?>

        <!-- Social icons: luôn hiển thị -->
        <div class="d-flex align-items-center gap-3">
          <a class="social-ic" href="<?php echo esc_url($socials['facebook'] ?: '#'); ?>" target="_blank" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a class="social-ic" href="<?php echo esc_url($socials['instagram'] ?: '#'); ?>" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a class="social-ic" href="<?php echo esc_url($socials['x'] ?: '#'); ?>" target="_blank" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
          <a class="social-ic" href="<?php echo esc_url($socials['behance'] ?: '#'); ?>" target="_blank" aria-label="Behance"><i class="fa-brands fa-behance"></i></a>
          <a class="social-ic" href="<?php echo esc_url($socials['dribbble'] ?: '#'); ?>" target="_blank" aria-label="Dribbble"><i class="fa-brands fa-dribbble"></i></a>
        </div>
      </div>

      <!-- Right form (không bọc khung/line) -->
      <div class="col-lg-7">
        <?php
          if ($cf7_shortcode) {
            echo do_shortcode($cf7_shortcode);
          } else {
            echo '<div class="alert alert-warning small">Please add your Contact Form 7 shortcode in ACF: <em>contact_form_shortcode</em>.</div>';
          }
        ?>
      </div>
    </div>
  </div>
</section>

<?php get_footer();
