<?php
/**
 * Главная — мозаика этажей и блоков.
 *
 * Что попадает в мозаику: книги и материалы журнала, у которых
 * отмечена галочка «Показывать на главной». Порядок — поле «Порядок»
 * (меньше число — выше). Этажи режутся по схеме 1 · 3 · 2 · 4:
 * первый блок во всю ширину, потом ряд из трёх, потом из двух и так далее.
 */

if (!defined('ABSPATH')) exit;

get_header();

$ids = get_posts([
    'post_type'   => ['book', 'post'],
    'numberposts' => 40,
    'fields'      => 'ids',
    'meta_key'    => '_sx_featured',
    'meta_value'  => '1',
]);

// Пока на главную ничего не отмечено — показываем свежее, чтобы сайт не пустовал.
if (!$ids) {
    $ids = array_merge(
        get_posts(['post_type' => 'book', 'numberposts' => 6, 'fields' => 'ids']),
        get_posts(['post_type' => 'post', 'numberposts' => 4, 'fields' => 'ids'])
    );
} else {
    usort($ids, function ($a, $b) {
        $oa = (int) (get_post_meta($a, '_sx_featured_order', true) ?: 999);
        $ob = (int) (get_post_meta($b, '_sx_featured_order', true) ?: 999);
        if ($oa !== $ob) return $oa <=> $ob;
        return get_post_time('U', true, $b) <=> get_post_time('U', true, $a);
    });
}

$about_page = get_page_by_path('about');
$first = true;
?>

<main class="wrap">
  <div class="mosaic">

    <?php foreach (sx_pour_floors($ids) as $floor) : ?>
    <div class="floor floor--<?php echo (int) $floor['size']; ?>">
      <?php foreach ($floor['items'] as $id) :
          if (get_post_type($id) === 'book') {
              get_template_part('template-parts/block-book', null, ['id' => $id, 'heading' => $first ? 'h1' : 'h2']);
          } else {
              get_template_part('template-parts/block-note', null, ['id' => $id]);
          }
          $first = false;
      endforeach; ?>
    </div>
    <?php endforeach; ?>

    <?php if (sx_opt('about_quote') !== '') : ?>
    <div class="floor floor--1">
      <article class="block block--about" id="about">
        <p class="rubric">Об издательстве</p>
        <blockquote class="about__quote"><?php echo esc_html(sx_opt('about_quote')); ?></blockquote>
        <?php if (sx_opt('about_sub') !== '') : ?>
        <p class="about__sub"><?php echo esc_html(sx_opt('about_sub')); ?></p>
        <?php endif; ?>
        <?php if ($about_page) : ?>
        <a class="text-btn" href="<?php echo esc_url(get_permalink($about_page)); ?>">Манифест <span class="arr">→</span></a>
        <?php endif; ?>
      </article>
    </div>
    <?php endif; ?>

  </div>
</main>

<?php get_footer();
