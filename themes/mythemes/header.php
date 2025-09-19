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

      <!-- Logo -->
      <a class="navbar-brand d-flex align-items-center" href="<?php echo esc_url( home_url('/') ); ?>">
        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/logo-dark.png' ); ?>"
             alt="<?php echo esc_attr( get_bloginfo('name') ); ?>" style="max-height:48px;">
      </a>

      <!-- Nút hamburger -->
      <button class="navbar-toggler ms-auto" type="button"
              data-bs-toggle="collapse" data-bs-target="#primaryNav"
              aria-controls="primaryNav" aria-expanded="false"
              aria-label="<?php echo esc_attr__('Toggle navigation', 'mythemes'); ?>">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menu + social -->
      <div class="collapse navbar-collapse" id="primaryNav">

        <!-- Header nhỏ mobile: Language trên góc phải -->
        <div class="offcanvas-head d-lg-none d-flex align-items-center justify-content-end">
          <?php if ( function_exists('pll_the_languages') ) :
            $langsM = pll_the_languages(['raw'=>1,'hide_if_empty'=>0,'hide_current'=>0]);
            $curSlug = pll_current_language('slug');
            $cur     = $langsM[$curSlug] ?? null;
            $curFlag = !empty($cur['flag_url']) ? esc_url($cur['flag_url']) : '';
            $abbr    = strtoupper($curSlug ?: 'EN');
          ?>
            <div class="lang-switcher dropdown">
              <button class="btn btn-lang btn-sm d-inline-flex align-items-center gap-2"
                      id="langDropdownM" data-bs-toggle="dropdown" aria-expanded="false">
                <?php if ($curFlag): ?>
                  <img src="<?php echo $curFlag; ?>" alt="<?php echo esc_attr($abbr); ?>" width="18" height="12" />
                <?php endif; ?>
                <span class="lang-label"><?php echo esc_html($abbr); ?></span>
                <i class="fa-solid fa-chevron-down small"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdownM">
                <?php foreach ($langsM as $slug => $l):
                  $is_current = !empty($l['current_lang']);
                  $flag = !empty($l['flag_url']) ? esc_url($l['flag_url']) : '';
                  $abbr = strtoupper($slug);
                ?>
                  <li>
                    <a class="dropdown-item <?php echo $is_current ? 'active' : ''; ?>" href="<?php echo esc_url($l['url']); ?>">
                      <?php if ($flag): ?><img src="<?php echo $flag; ?>" width="18" height="12" /><?php endif; ?>
                      <span class="ms-2"><?php echo esc_html($abbr); ?></span>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
        </div>

        <!-- Menu chính -->
        <?php
          wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-3',
            'fallback_cb'    => '__return_false',
            'depth'          => 2,
          ]);
        ?>

        <!-- Social + Language (Desktop) -->
        <ul class="navbar-nav d-none d-lg-flex ms-lg-3 flex-row gap-3 align-items-center">
          <li class="nav-item"><a class="nav-link p-0" href="#"><i class="fa-brands fa-facebook-f"></i></a></li>
          <li class="nav-item"><a class="nav-link p-0" href="#"><i class="fa-brands fa-instagram"></i></a></li>
          <li class="nav-item"><a class="nav-link p-0" href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
          <li class="nav-item d-flex align-items-center">
            <?php if ( function_exists('pll_the_languages') ) :
              $langs   = pll_the_languages(['raw'=>1,'hide_if_empty'=>0,'hide_current'=>0]);
              $current = pll_current_language('slug');
              $cur     = $langs[$current] ?? null;
              $curFlag = !empty($cur['flag_url']) ? esc_url($cur['flag_url']) : '';
              $abbr    = strtoupper($current ?: 'EN');
            ?>
              <div class="lang-switcher dropdown">
                <button class="btn btn-lang btn-sm d-flex align-items-center gap-2"
                        id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                  <?php if ($curFlag): ?>
                    <img src="<?php echo $curFlag; ?>" alt="<?php echo esc_attr($abbr); ?>" width="18" height="12" />
                  <?php endif; ?>
                  <span class="lang-label d-none d-xl-inline"><?php echo esc_html($abbr); ?></span>
                  <i class="fa-solid fa-chevron-down small"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
                  <?php foreach ($langs as $slug => $l):
                    $is_current = !empty($l['current_lang']);
                    $flag = !empty($l['flag_url']) ? esc_url($l['flag_url']) : '';
                    $abbr = strtoupper($slug);
                  ?>
                    <li>
                      <a class="dropdown-item <?php echo $is_current ? 'active' : ''; ?>" href="<?php echo esc_url($l['url']); ?>">
                        <?php if ($flag): ?><img src="<?php echo $flag; ?>" width="18" height="12" /><?php endif; ?>
                        <span class="ms-2"><?php echo esc_html($abbr); ?></span>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
          </li>
        </ul>
      </div>

      <!-- Backdrop -->
      <div class="navbar-backdrop" aria-hidden="true"></div>
    </div>
  </nav>
</header>
