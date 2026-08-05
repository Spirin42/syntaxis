<?php if (!defined('ABSPATH')) exit; ?>

<footer class="site-footer">
  <div class="wrap">
    <div class="site-footer__top">
      <p class="site-footer__mark">синтаксис<span class="wordmark__mark">;</span></p>
      <?php if (sx_opt('tagline')) : ?>
      <p class="site-footer__tag"><?php echo esc_html(sx_opt('tagline')); ?></p>
      <?php endif; ?>
    </div>

    <div class="site-footer__row">
      <nav class="site-footer__nav" aria-label="Разделы сайта">
        <?php
        wp_nav_menu([
            'theme_location' => 'footer',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'depth'          => 1,
            'walker'         => new SX_Nav_Walker(),
            'fallback_cb'    => 'sx_nav_fallback',
        ]);
        ?>
      </nav>
      <?php if (has_nav_menu('social')) : ?>
      <nav class="site-footer__nav" aria-label="Соцсети">
        <?php
        wp_nav_menu([
            'theme_location' => 'social',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'depth'          => 1,
            'walker'         => new SX_Nav_Walker(true),
        ]);
        ?>
      </nav>
      <?php endif; ?>
      <?php if (sx_opt('email')) : ?>
      <a class="site-footer__mail" href="mailto:<?php echo esc_attr(sx_opt('email')); ?>"><?php echo esc_html(sx_opt('email')); ?></a>
      <?php endif; ?>
    </div>

    <?php if (sx_opt('footer_note')) : ?>
    <p class="site-footer__note"><?php echo esc_html(sx_opt('footer_note')); ?></p>
    <?php endif; ?>

    <div class="site-footer__base">
      <span><?php echo esc_html(sx_opt('copyright')); ?></span>
      <a href="#top">Наверх ↑</a>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
