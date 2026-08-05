<?php
/**
 * Ряд покупки: цена и кнопки-магазины.
 *
 * @var array $args ['id' => ID книги, 'large' => bool, 'note' => bool]
 */

if (!defined('ABSPATH')) exit;

$id    = (int) ($args['id'] ?? get_the_ID());
$large = !empty($args['large']);

if (sx_is_soon($id)) return;

$price = sx_price($id);
$links = sx_buy_links($id);
$note  = $large ? sx_meta($id, 'buy_note') : '';

if ($price === '' && !$links) return;
?>
<div class="buy<?php echo $large ? ' buy--lg' : ''; ?>">
  <?php if ($price !== '') : ?>
  <span class="buy__price"><?php echo esc_html($price); ?></span>
  <?php endif; ?>
  <?php foreach ($links as $i => $link) : ?>
  <a class="buy__link<?php echo ($large && $i === 0) ? ' buy__link--solid' : ''; ?>" href="<?php echo esc_url($link['url']); ?>"<?php echo str_starts_with($link['url'], home_url()) ? '' : ' rel="noopener"'; ?>><?php echo esc_html($link['label']); ?></a>
  <?php endforeach; ?>
  <?php if ($note !== '') : ?>
  <p class="buy__note"><?php echo esc_html($note); ?></p>
  <?php endif; ?>
</div>
