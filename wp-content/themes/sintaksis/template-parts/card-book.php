<?php
/**
 * Карточка книги в каталоге.
 *
 * @var array $args ['id' => ID книги]
 */

if (!defined('ABSPATH')) exit;

$id     = (int) ($args['id'] ?? get_the_ID());
$title  = get_the_title($id);
$link   = get_permalink($id);
$author = sx_authors($id);
$genre  = mb_strtolower(sx_genre($id));
$year   = sx_meta($id, 'year');
$soon   = sx_is_soon($id);
?>
<article class="card">
  <a class="card__cover" href="<?php echo esc_url($link); ?>" aria-label="«<?php echo esc_attr($title); ?>» — страница книги">
    <?php get_template_part('template-parts/cover', null, ['id' => $id]); ?>
  </a>
  <?php if ($author !== '') : ?>
  <p class="card__author"><?php echo esc_html($author); ?></p>
  <?php endif; ?>
  <h2 class="card__title"><a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a></h2>
  <p class="card__meta">
    <?php if ($soon) : ?>
      <span class="card__soon"><?php echo esc_html(sx_meta($id, 'soon_label') ?: 'Готовится'); ?></span>
      <span><?php echo esc_html($genre); ?></span>
    <?php else : ?>
      <?php if (sx_price($id) !== '') : ?><span class="card__price"><?php echo esc_html(sx_price($id)); ?></span><?php endif; ?>
      <span><?php echo esc_html(implode(' · ', array_filter([$genre, $year]))); ?></span>
    <?php endif; ?>
  </p>
</article>
