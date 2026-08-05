<?php
/**
 * Запасной шаблон. Сюда попадают только те случаи, для которых
 * не нашлось файла поточнее: архивы по дате, по автору и тому подобное.
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<main class="wrap">

  <nav class="crumbs" aria-label="Хлебные крошки">
    <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
    <span class="crumbs__sep">/</span>
    <span><?php echo esc_html(wp_strip_all_tags(get_the_archive_title() ?: 'Материалы')); ?></span>
  </nav>

  <div class="page-head">
    <p class="rubric">Журнал</p>
    <h1 class="page-head__title"><?php echo esc_html(wp_strip_all_tags(get_the_archive_title() ?: 'Материалы')); ?></h1>
  </div>

  <?php if (have_posts()) : ?>
  <div class="feed">
    <?php while (have_posts()) : the_post(); ?>
    <?php get_template_part('template-parts/feed-item', null, ['id' => get_the_ID()]); ?>
    <?php endwhile; ?>
  </div>
  <?php get_template_part('template-parts/pager'); ?>
  <?php else : ?>
  <p class="notice">Здесь пока пусто.</p>
  <?php endif; ?>

</main>

<?php get_footer();
