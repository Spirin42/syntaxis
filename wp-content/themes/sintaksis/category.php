<?php
/**
 * Рубрика журнала — та же лента, но с заголовком рубрики.
 */

if (!defined('ABSPATH')) exit;

get_header();

$term    = get_queried_object();
$journal = get_option('page_for_posts') ? get_permalink(get_option('page_for_posts')) : '';
?>

<main class="wrap">

  <nav class="crumbs" aria-label="Хлебные крошки">
    <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
    <span class="crumbs__sep">/</span>
    <?php if ($journal) : ?>
    <a href="<?php echo esc_url($journal); ?>">Журнал</a>
    <?php else : ?>
    <span>Журнал</span>
    <?php endif; ?>
    <span class="crumbs__sep">/</span>
    <span><?php echo esc_html($term->name); ?></span>
  </nav>

  <div class="page-head">
    <p class="rubric">Рубрика</p>
    <h1 class="page-head__title"><?php echo esc_html($term->name); ?></h1>
    <?php if (trim($term->description) !== '') : ?>
    <p class="page-head__sub"><?php echo esc_html(wp_strip_all_tags($term->description)); ?></p>
    <?php endif; ?>
    <p class="page-head__count"><?php
      $n = (int) $term->count;
      echo esc_html($n . ' ' . sx_plural($n, ['материал', 'материала', 'материалов']));
    ?></p>
  </div>

  <?php if (have_posts()) : ?>
  <div class="feed">
    <?php while (have_posts()) : the_post(); ?>
    <?php get_template_part('template-parts/feed-item', null, ['id' => get_the_ID()]); ?>
    <?php endwhile; ?>
  </div>
  <?php get_template_part('template-parts/pager'); ?>
  <?php else : ?>
  <p class="notice">В этой рубрике пока пусто.</p>
  <?php endif; ?>

</main>

<?php get_footer();
