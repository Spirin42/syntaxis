<?php
/**
 * Каталог книг: архив /books/ и архивы жанров.
 * Книги сгруппированы подзаголовками — сперва то, что готовится к печати,
 * дальше по годам от новых к старым.
 */

if (!defined('ABSPATH')) exit;

get_header();

$is_genre = is_tax('genre');
$term     = $is_genre ? get_queried_object() : null;

$rubric = $is_genre ? 'Жанр' : 'Каталог';
$title  = $is_genre ? $term->name : 'Книги';
$sub    = $is_genre ? $term->description : sx_opt('books_intro');

$total  = 0;
$genres = wp_list_pluck(get_terms(['taxonomy' => 'genre', 'hide_empty' => true]) ?: [], 'name');

// Раскладываем книги по группам, сохраняя порядок запроса (год по убыванию).
$groups = [];

if (have_posts()) {
    while (have_posts()) {
        the_post();
        $total++;
        // 9999 — «готовим к печати» наверх, 0000 — книги без года вниз.
        $key = sx_is_soon(get_the_ID()) ? '9999' : (sx_meta(get_the_ID(), 'year') ?: '0000');
        $groups[$key][] = get_the_ID();
    }
}

krsort($groups, SORT_STRING);

$group_labels = ['9999' => 'Готовим к печати', '0000' => 'Без года'];
?>

<main class="wrap">

  <nav class="crumbs" aria-label="Хлебные крошки">
    <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
    <span class="crumbs__sep">/</span>
    <?php if ($is_genre) : ?>
    <a href="<?php echo esc_url(get_post_type_archive_link('book')); ?>">Книги</a>
    <span class="crumbs__sep">/</span>
    <span><?php echo esc_html($title); ?></span>
    <?php else : ?>
    <span>Книги</span>
    <?php endif; ?>
  </nav>

  <div class="page-head">
    <p class="rubric"><?php echo esc_html($rubric); ?></p>
    <h1 class="page-head__title"><?php echo esc_html($title); ?></h1>
    <?php if ($sub !== '') : ?>
    <p class="page-head__sub"><?php echo esc_html(wp_strip_all_tags($sub)); ?></p>
    <?php endif; ?>
    <?php if ($total) : ?>
    <p class="page-head__count"><?php
      echo esc_html($total . ' ' . sx_plural($total, ['книга', 'книги', 'книг']));
      if (!$is_genre && $genres) echo esc_html(' · ' . mb_strtolower(implode(', ', $genres)));
    ?></p>
    <?php endif; ?>
  </div>

  <?php if ($groups) : ?>
  <div class="catalog">
    <?php foreach ($groups as $year => $ids) : ?>
    <p class="catalog-split"><?php echo esc_html($group_labels[$year] ?? $year); ?></p>
    <?php foreach ($ids as $id) : ?>
    <?php get_template_part('template-parts/card-book', null, ['id' => $id]); ?>
    <?php endforeach; ?>
    <?php endforeach; ?>
  </div>
  <?php else : ?>
  <p class="notice">Книг пока нет.</p>
  <?php endif; ?>

</main>

<?php get_footer();
