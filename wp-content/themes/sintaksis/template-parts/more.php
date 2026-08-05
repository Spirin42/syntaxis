<?php
/**
 * Полоса «читайте дальше» — общая для книги и статьи.
 *
 * @var array $args ['label' => string, 'items' => int[] ID материалов и книг]
 */

if (!defined('ABSPATH')) exit;

$label = $args['label'] ?? 'Читайте дальше';
$items = $args['items'] ?? [];

if (!$items) return;
?>
<section class="more">
  <p class="more__label"><?php echo esc_html($label); ?></p>
  <div class="more__row">
    <?php foreach ($items as $id) :
        $id      = (int) $id;
        $is_book = get_post_type($id) === 'book';
        $author  = $is_book ? sx_authors($id) : '';
        $cats    = $is_book ? [] : get_the_category($id);
        $rubric  = $is_book ? 'Книга' : ($cats ? $cats[0]->name : 'Журнал');
        $title   = $is_book && $author !== ''
            ? $author . '. ' . get_the_title($id)
            : get_the_title($id);
        $meta    = $is_book ? sx_book_meta_line($id) : sx_date($id);
    ?>
    <article class="more__item">
      <p class="rubric"><?php echo esc_html($rubric); ?></p>
      <h3><a href="<?php echo esc_url(get_permalink($id)); ?>"><?php echo esc_html($title); ?></a></h3>
      <?php if ($meta !== '') : ?>
      <p class="meta"><?php echo esc_html($meta); ?></p>
      <?php endif; ?>
    </article>
    <?php endforeach; ?>
  </div>
</section>
