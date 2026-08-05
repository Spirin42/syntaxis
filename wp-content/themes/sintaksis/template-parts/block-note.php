<?php
/**
 * Блок материала журнала в мозаике главной.
 *
 * @var array $args ['id' => ID материала]
 */

if (!defined('ABSPATH')) exit;

$id     = (int) ($args['id'] ?? get_the_ID());
$cats   = get_the_category($id);
$rubric = $cats ? $cats[0]->name : '';
$dek    = get_the_excerpt($id);
$time   = sx_meta($id, 'reading_time');
$meta   = implode(' · ', array_filter([sx_date($id), $time]));
?>
<article class="block block--note">
  <?php if ($rubric !== '') : ?>
  <p class="rubric"><?php echo esc_html($rubric); ?></p>
  <?php endif; ?>
  <h2 class="note__title"><a href="<?php echo esc_url(get_permalink($id)); ?>"><?php echo esc_html(get_the_title($id)); ?></a></h2>
  <?php if ($dek !== '') : ?>
  <p class="dek"><?php echo esc_html($dek); ?></p>
  <?php endif; ?>
  <p class="meta"><?php echo esc_html($meta); ?></p>
</article>
