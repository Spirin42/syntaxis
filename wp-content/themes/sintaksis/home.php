<?php
/**
 * Индекс журнала — лента всех материалов.
 * Это страница, выбранная в «Настройки → Чтение → Страница записей».
 */

if (!defined('ABSPATH')) exit;

get_header();

$page_id = (int) get_option('page_for_posts');
$title   = $page_id ? get_the_title($page_id) : 'Журнал';
$sub     = sx_opt('journal_intro');
$total   = (int) wp_count_posts('post')->publish;
?>

<main class="wrap">

  <nav class="crumbs" aria-label="Хлебные крошки">
    <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
    <span class="crumbs__sep">/</span>
    <span><?php echo esc_html($title); ?></span>
  </nav>

  <div class="page-head">
    <p class="rubric">Журнал издательства</p>
    <h1 class="page-head__title"><?php echo esc_html($title); ?></h1>
    <?php if ($sub !== '') : ?>
    <p class="page-head__sub"><?php echo esc_html($sub); ?></p>
    <?php endif; ?>
    <?php if ($total) : ?>
    <p class="page-head__count"><?php echo esc_html($total . ' ' . sx_plural($total, ['материал', 'материала', 'материалов'])); ?></p>
    <?php endif; ?>
  </div>

  <?php if (have_posts()) : ?>
  <div class="feed">
    <?php while (have_posts()) : the_post(); ?>
    <?php get_template_part('template-parts/feed-item', null, ['id' => get_the_ID()]); ?>
    <?php endwhile; ?>
  </div>
  <?php get_template_part('template-parts/pager'); ?>
  <?php else : ?>
  <p class="notice">Материалов пока нет.</p>
  <?php endif; ?>

</main>

<?php get_footer();
