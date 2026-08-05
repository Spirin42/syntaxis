<?php
/**
 * Блок книги в мозаике главной.
 *
 * @var array $args ['id' => ID книги, 'heading' => 'h1'|'h2']
 */

if (!defined('ABSPATH')) exit;

$id      = (int) ($args['id'] ?? get_the_ID());
$heading = ($args['heading'] ?? 'h2') === 'h1' ? 'h1' : 'h2';
$title   = get_the_title($id);
$link    = get_permalink($id);
$chip    = sx_chip_text($id);
$dek     = get_the_excerpt($id);
$author  = sx_authors($id);
?>
<article class="block block--book">
  <div class="book">
    <a class="book__coverlink" href="<?php echo esc_url($link); ?>" aria-label="«<?php echo esc_attr($title); ?>» — страница книги">
      <?php get_template_part('template-parts/cover', null, ['id' => $id]); ?>
    </a>
    <div class="book__body">
      <?php if ($chip !== '') : ?>
      <p class="chip<?php echo sx_meta($id, 'badge_accent') === '1' ? ' chip--accent' : ''; ?>"><?php echo esc_html($chip); ?></p>
      <?php endif; ?>
      <?php if ($author !== '') : ?>
      <p class="book__author"><?php echo esc_html($author); ?></p>
      <?php endif; ?>
      <<?php echo $heading; ?> class="book__title"><a href="<?php echo esc_url($link); ?>"><?php echo esc_html($title); ?></a></<?php echo $heading; ?>>
      <?php if ($dek !== '') : ?>
      <p class="dek"><?php echo esc_html($dek); ?></p>
      <?php endif; ?>
      <?php get_template_part('template-parts/buy', null, ['id' => $id]); ?>
    </div>
  </div>
</article>
