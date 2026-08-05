<?php
/**
 * Постраничная навигация — две ссылки, без нумерации.
 */

if (!defined('ABSPATH')) exit;

global $wp_query;
if ($wp_query->max_num_pages < 2) return;
?>
<nav class="pager" aria-label="Страницы">
  <span class="pager__side"><?php previous_posts_link('← Новее'); ?></span>
  <span class="pager__side"><?php next_posts_link('Раньше →'); ?></span>
</nav>
