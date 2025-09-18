<?php if (!defined('ABSPATH')) exit; ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php if (function_exists('wp_body_open')) wp_body_open(); ?>

<a class="skip-link visually-hidden-focusable" href="#content">
  <?php echo esc_html__('Skip to content', 'mythemes'); ?>
</a>

<header class="site-header border-bottom" role="banner">
  <nav class="navbar navbar-expand-lg navbar-light bg-white" role="navigation" aria-label="<?php echo esc_attr__('Main navigation', 'mythemes'); ?>">
    <div class="container">

      <!-- Logo trái -->
      <a class="navbar-brand d-flex align-items-center" href="<?php echo esc_url( home_url('/') ); ?>" aria-label="<?php echo esc_attr__('Home', 'mythemes'); ?>">
        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-dark.png' ); ?>"
             alt="<?php echo esc_attr( get_bloginfo('name') ); ?>" style="max-height:48px;">
      </a>

      <!-- Nút hamburger -->
      <button class="navbar-toggler ms-auto" type="button"
              data-bs-toggle="collapse" data-bs-target="#primaryNav"
              aria-controls="primaryNav" aria-expanded="false" aria-label="<?php echo esc_attr__('Toggle navigation', 'mythemes'); ?>">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menu + social -->
      <div class="collapse navbar-collapse" id="primaryNav">
        <?php
          wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3',
            'fallback_cb'    => '__return_false',
            'depth'          => 2,
          ]);
        ?>

        <!-- Social: ẩn trên mobile, hiện trên lg -->
        <ul class="navbar-nav d-none d-lg-flex ms-lg-3 flex-row gap-3" aria-label="<?php echo esc_attr__('Social links', 'mythemes'); ?>">
          <li class="nav-item">
            <a class="nav-link p-0" href="#" aria-label="<?php echo esc_attr__('Facebook', 'mythemes'); ?>">
              <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link p-0" href="#" aria-label="<?php echo esc_attr__('Instagram', 'mythemes'); ?>">
              <i class="fa-brands fa-instagram" aria-hidden="true"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link p-0" href="#" aria-label="<?php echo esc_attr__('X (Twitter)', 'mythemes'); ?>">
              <i class="fa-brands fa-x-twitter" aria-hidden="true"></i>
            </a>
          </li>
        </ul>

        <!-- Social: chỉ hiện trên mobile (đặt cuối menu) -->
        <ul class="navbar-nav d-lg-none mt-3 flex-row gap-3 justify-content-center w-100" aria-label="<?php echo esc_attr__('Social links', 'mythemes'); ?>">
          <li class="nav-item">
            <a class="nav-link p-0" href="#" aria-label="<?php echo esc_attr__('Facebook', 'mythemes'); ?>">
              <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link p-0" href="#" aria-label="<?php echo esc_attr__('Instagram', 'mythemes'); ?>">
              <i class="fa-brands fa-instagram" aria-hidden="true"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link p-0" href="#" aria-label="<?php echo esc_attr__('X (Twitter)', 'mythemes'); ?>">
              <i class="fa-brands fa-x-twitter" aria-hidden="true"></i>
            </a>
          </li>
        </ul>
      </div>

      <!-- Backdrop phủ body khi menu mở (mobile) -->
      <div class="navbar-backdrop" aria-hidden="true"></div>
    </div>
  </nav>
</header>
