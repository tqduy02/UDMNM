<?php if (!defined('ABSPATH')) exit; ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php if (function_exists('wp_body_open')) wp_body_open(); ?>

<header class="site-header border-bottom">
  <nav class="navbar navbar-expand-lg navbar-light bg-white">
    <div class="container">
      <!-- Logo trái -->
      <a class="navbar-brand d-flex align-items-center" href="<?php echo esc_url(home_url('/')); ?>">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo-dark.png"
             alt="<?php bloginfo('name'); ?>" style="max-height:48px;">
      </a>

      <!-- Nút hamburger -->
      <button class="navbar-toggler ms-auto" type="button"
              data-bs-toggle="collapse" data-bs-target="#primaryNav"
              aria-controls="primaryNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menu + social -->
      <div class="collapse navbar-collapse" id="primaryNav">
        <?php
          // Menu căn GIỮA
          wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3',
            'fallback_cb'    => '__return_false',
            'depth'          => 2,
          ]);
        ?>

        <!-- Social: ẩn trên mobile, hiện trên lg -->
        <ul class="navbar-nav d-none d-lg-flex ms-lg-3 flex-row gap-3">
          <li class="nav-item"><a class="nav-link p-0" href="#"><i class="fa-brands fa-facebook-f"></i></a></li>
          <li class="nav-item"><a class="nav-link p-0" href="#"><i class="fa-brands fa-instagram"></i></a></li>
          <li class="nav-item"><a class="nav-link p-0" href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
        </ul>

        <!-- Social: chỉ hiện trên mobile (đặt cuối menu) -->
        <ul class="navbar-nav d-lg-none mt-3 flex-row gap-3 justify-content-center w-100">
          <li class="nav-item"><a class="nav-link p-0" href="#"><i class="fa-brands fa-facebook-f"></i></a></li>
          <li class="nav-item"><a class="nav-link p-0" href="#"><i class="fa-brands fa-instagram"></i></a></li>
          <li class="nav-item"><a class="nav-link p-0" href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
        </ul>
      </div>
    </div>
  </nav>
</header>
