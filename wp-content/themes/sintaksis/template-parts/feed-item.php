<?php
/**
 * Строка в ленте журнала: слева рубрика и дата, справа заголовок и дек.
 *
 * @var array $args ['id' => ID материала]
 */

if (!defined('ABSPATH')) exit;

$id     = (int) ($args['id'] ?? get_the_ID());
$cats   = get_the_category($id);
$rubric = $cats ? $cats[0]->name : '';
$dek    = get_the_excerpt($id);
$meta   = implode(' · ', array_filter([sx_date($id), sx_meta($id, 'reading_time')]));
?>
<article class="feed__item">
  <div class="feed__side">
    <?php if ($rubric !== '') : ?>
    <p class="rubric"><?php echo esc_html($rubric); ?></p>
    <?php endif; ?>
    <p class="meta"><?php echo esc_html($meta); ?></p>
  </div>
  <div class="feed__body">
    <h2 class="feed__title"><a href="<?php echo esc_url(get_permalink($id)); ?>"><?php echo esc_html(get_the_title($id)); ?></a></h2>
    <?php if ($dek !== '') : ?>
    <p class="feed__dek"><?php echo esc_html($dek); ?></p>
    <?php endif; ?>
  </div>
</article>
