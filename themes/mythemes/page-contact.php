<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 * Description: Contact page with ACF fields and CF7.
 */
if (!defined('ABSPATH')) exit;

get_header();

$acf_ok = function_exists('get_field');

/** === ACF fields (per-language via Polylang) === */
$hero_title = $acf_ok ? ( get_field('contact_hero_title') ?: get_the_title() ) : get_the_title();
$info_title = $acf_ok ? ( get_field('contact_info_title') ?: esc_html__('Let’s talk about everything', 'mythemes') ) : esc_html__('Let’s talk about everything', 'mythemes');
$info_desc  = $acf_ok ? (string) get_field('contact_info_desc') : '';

$address = $acf_ok ? (string) get_field('contact_address') : '';
$email   = $acf_ok ? (string) get_field('contact_email')   : '';
$phone   = $acf_ok ? (string) get_field('contact_phone')   : '';

$socials = [
  'facebook' => $acf_ok ? (string) get_field('contact_facebook')  : '',
  'instagram'=> $acf_ok ? (string) get_field('contact_instagram') : '',
  'x'        => $acf_ok ? (string) get_field('contact_x')         : '',
  'behance'  => $acf_ok ? (string) get_field('contact_behance')   : '',
  'dribbble' => $acf_ok ? (string) get_field('contact_dribbble')  : '',
];

$cf7_shortcode = $acf_ok ? ( get_field('contact_form_shortcode') ?: '' ) : '';
?>

<!-- ===== HERO (center) ===== -->
<section class="page-hero py-5 text-center">
  <div class="container-xxl">
    <h1 class="display-6 fw-bold mb-2"><?php echo esc_html($hero_title); ?></h1>

    <nav class="breadcrumb-small" aria-label="<?php echo esc_attr__('Breadcrumb', 'mythemes'); ?>">
      <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item">
          <a href="<?php echo esc_url( home_url('/') ); ?>">
            <?php echo esc_html__('Home', 'mythemes'); ?>
          </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo esc_html( get_the_title() ); ?></li>
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

        <?php if (!empty($info_desc)) : ?>
          <p class="text-muted mb-4"><?php echo wp_kses_post($info_desc); ?></p>
        <?php endif; ?>

        <?php if (!empty($address)) : ?>
          <div class="d-flex align-items-start gap-3 mb-3">
            <i class="fa-solid fa-location-dot mt-1" aria-hidden="true"></i>
            <span><?php echo esc_html($address); ?></span>
          </div>
        <?php endif; ?>

        <?php if (!empty($email)) :
          $email_disp = antispambot($email);
          $email_href = antispambot($email);
        ?>
          <div class="d-flex align-items-start gap-3 mb-3">
            <i class="fa-regular fa-envelope mt-1" aria-hidden="true"></i>
            <a href="mailto:<?php echo esc_attr($email_href); ?>"><?php echo esc_html($email_disp); ?></a>
          </div>
        <?php endif; ?>

        <?php if (!empty($phone)) :
          $tel_href = preg_replace('/\D+/', '', $phone);
        ?>
          <div class="d-flex align-items-start gap-3 mb-4">
            <i class="fa-solid fa-phone mt-1" aria-hidden="true"></i>
            <a href="tel:<?php echo esc_attr($tel_href); ?>"><?php echo esc_html($phone); ?></a>
          </div>
        <?php endif; ?>

        <!-- Social icons: always visible -->
        <div class="d-flex align-items-center gap-3" aria-label="<?php echo esc_attr__('Social links', 'mythemes'); ?>">
          <a class="social-ic" href="<?php echo esc_url( $socials['facebook'] ?: '#' ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__('Facebook', 'mythemes'); ?>">
            <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
          </a>
          <a class="social-ic" href="<?php echo esc_url( $socials['instagram'] ?: '#' ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__('Instagram', 'mythemes'); ?>">
            <i class="fa-brands fa-instagram" aria-hidden="true"></i>
          </a>
          <a class="social-ic" href="<?php echo esc_url( $socials['x'] ?: '#' ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__('X (Twitter)', 'mythemes'); ?>">
            <i class="fa-brands fa-x-twitter" aria-hidden="true"></i>
          </a>
          <a class="social-ic" href="<?php echo esc_url( $socials['behance'] ?: '#' ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__('Behance', 'mythemes'); ?>">
            <i class="fa-brands fa-behance" aria-hidden="true"></i>
          </a>
          <a class="social-ic" href="<?php echo esc_url( $socials['dribbble'] ?: '#' ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr__('Dribbble', 'mythemes'); ?>">
            <i class="fa-brands fa-dribbble" aria-hidden="true"></i>
          </a>
        </div>
      </div>

      <!-- Right form (language-aware CF7, map thủ công EN/VI) -->
      <div class="col-lg-7" role="region" aria-label="<?php echo esc_attr__('Contact form', 'mythemes'); ?>">
        <?php
          // ID form CF7 (lấy số từ URL khi edit form)
          $map = [
            'en' => 198, // ID số form Contact EN
            'vi' => 383, // ID số form Contact VI
          ];

          // Lấy ngôn ngữ hiện tại
          $lang = function_exists('pll_current_language') ? pll_current_language('slug') : 'en';

          // Chọn ID theo ngôn ngữ (mặc định về EN nếu không có)
          $form_id = isset($map[$lang]) ? $map[$lang] : $map['en'];

          // Render form
          if ($form_id) {
            echo do_shortcode('[contact-form-7 id="' . intval($form_id) . '"]');
          } else {
            echo '<div class="alert alert-warning small">' .
                esc_html__('Please set CF7 IDs for EN/VI in the template.', 'mythemes') .
                '</div>';
          }
        ?>
      </div>


    </div>
  </div>
</section>

<?php get_footer(); ?>
