<?php if (!defined('ABSPATH')) exit; ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body id="top" <?php body_class(); ?>>

<header class="site-header">
  <div class="wrap site-header__row">
    <a class="wordmark" href="<?php echo esc_url(home_url('/')); ?>">синтаксис<span class="wordmark__mark">;</span></a>
    <nav class="site-nav" aria-label="Основные разделы">
      <?php
      wp_nav_menu([
          'theme_location' => 'primary',
          'container'      => false,
          'items_wrap'     => '%3$s',
          'depth'          => 1,
          'walker'         => new SX_Nav_Walker(),
          'fallback_cb'    => 'sx_nav_fallback',
      ]);
      ?>
    </nav>
  </div>
</header>
