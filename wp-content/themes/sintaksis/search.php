<?php
/**
 * Поиск по сайту: книги и материалы журнала вперемешку.
 */

if (!defined('ABSPATH')) exit;

get_header();

$query = get_search_query();
$found = (int) $GLOBALS['wp_query']->found_posts;
?>

<main class="wrap">

  <nav class="crumbs" aria-label="Хлебные крошки">
    <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
    <span class="crumbs__sep">/</span>
    <span>Поиск</span>
  </nav>

  <div class="page-head">
    <p class="rubric">Поиск</p>
    <h1 class="page-head__title"><?php echo esc_html($query); ?></h1>
    <p class="page-head__count"><?php
      echo $found
        ? esc_html($found . ' ' . sx_plural($found, ['находка', 'находки', 'находок']))
        : 'Ничего не нашлось';
    ?></p>
  </div>

  <?php get_search_form(); ?>

  <?php if (have_posts()) : ?>
  <div class="feed">
    <?php while (have_posts()) : the_post(); ?>
      <?php if (get_post_type() === 'book') : ?>
      <article class="feed__item">
        <div class="feed__side">
          <p class="rubric">Книга</p>
          <p class="meta"><?php echo esc_html(sx_book_meta_line(get_the_ID())); ?></p>
        </div>
        <div class="feed__body">
          <h2 class="feed__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <?php if (get_the_excerpt() !== '') : ?>
          <p class="feed__dek"><?php echo esc_html(get_the_excerpt()); ?></p>
          <?php endif; ?>
        </div>
      </article>
      <?php else : ?>
      <?php get_template_part('template-parts/feed-item', null, ['id' => get_the_ID()]); ?>
      <?php endif; ?>
    <?php endwhile; ?>
  </div>
  <?php get_template_part('template-parts/pager'); ?>
  <?php else : ?>
  <p class="notice">По запросу ничего не нашлось. Попробуйте другое слово — или загляните в <a href="<?php echo esc_url(get_post_type_archive_link('book')); ?>">каталог</a>.</p>
  <?php endif; ?>

</main>

<?php get_footer();
