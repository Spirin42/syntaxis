<?php
/**
 * Страница «не найдено».
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<main class="wrap about-page">

  <nav class="crumbs" aria-label="Хлебные крошки">
    <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
    <span class="crumbs__sep">/</span>
    <span>404</span>
  </nav>

  <div class="page-head">
    <p class="rubric">Ошибка 404</p>
    <h1 class="page-head__title">Такой страницы нет<span class="wordmark__mark">;</span></h1>
    <p class="page-head__sub">Возможно, книга переехала на новый адрес или ссылка набрана с опечаткой. Загляните в каталог или поищите по названию.</p>
  </div>

  <section class="section">
    <h2 class="section__label">Поиск</h2>
    <div class="section__body">
      <?php get_search_form(); ?>
    </div>
  </section>

  <?php get_template_part('template-parts/more', null, [
      'label' => 'Пока вы здесь',
      'items' => sx_related_ids(0, 2, 2),
  ]); ?>

</main>

<?php get_footer();
