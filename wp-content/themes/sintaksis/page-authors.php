<?php
/**
 * Template Name: Авторы
 *
 * Список авторов по алфавиту фамилий — только имена, без страниц и фотографий.
 * Имя приглушается, если все книги автора ещё готовятся к печати.
 */

if (!defined('ABSPATH')) exit;

get_header();

the_post();

$page_id = get_the_ID();
$kicker  = sx_meta($page_id, 'kicker') ?: 'Кого мы издаём';
$sub     = get_the_excerpt() ?: sx_opt('authors_intro');

// Один проход по книгам: у кого из авторов всё ещё в работе.
$soon_only = [];
foreach (get_posts(['post_type' => 'book', 'numberposts' => -1]) as $book) {
    $terms = get_the_terms($book->ID, 'book_author');
    if (!$terms || is_wp_error($terms)) continue;

    foreach ($terms as $term) {
        $soon_only[$term->term_id] = ($soon_only[$term->term_id] ?? true) && sx_is_soon($book->ID);
    }
}

$terms  = get_terms(['taxonomy' => 'book_author', 'hide_empty' => true]) ?: [];
$people = [];

foreach ($terms as $term) {
    $surname = sx_surname($term->name, $term->term_id);
    $key     = mb_strtoupper(str_replace('ё', 'е', mb_strtolower($surname)));

    $people[] = [
        'name'   => $term->name,
        'letter' => mb_substr($key, 0, 1),
        'sort'   => $key . ' ' . $term->name,
        'soon'   => !empty($soon_only[$term->term_id]),
    ];
}

usort($people, fn($a, $b) => strcmp($a['sort'], $b['sort']));

$groups = [];
foreach ($people as $person) {
    $groups[$person['letter']][] = $person;
}
?>

<main class="wrap">

  <nav class="crumbs" aria-label="Хлебные крошки">
    <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
    <span class="crumbs__sep">/</span>
    <span><?php the_title(); ?></span>
  </nav>

  <div class="page-head">
    <p class="rubric"><?php echo esc_html($kicker); ?></p>
    <h1 class="page-head__title"><?php the_title(); ?></h1>
    <?php if ($sub !== '') : ?>
    <p class="page-head__sub"><?php echo esc_html($sub); ?></p>
    <?php endif; ?>
    <?php if ($people) : ?>
    <p class="page-head__count"><?php
      $n = count($people);
      echo esc_html($n . ' ' . sx_plural($n, ['имя', 'имени', 'имён']));
    ?></p>
    <?php endif; ?>
  </div>

  <?php if ($groups) : ?>
  <div class="authors">
    <?php foreach ($groups as $letter => $list) : ?>
    <div class="authors__group">
      <p class="authors__letter" aria-hidden="true"><?php echo esc_html($letter); ?></p>
      <ul class="authors__list">
        <?php foreach ($list as $person) : ?>
        <li<?php echo $person['soon'] ? ' class="is-soon"' : ''; ?>><?php echo esc_html($person['name']); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else : ?>
  <p class="notice">Авторов пока нет: они появятся здесь, как только выйдет первая книга.</p>
  <?php endif; ?>

</main>

<?php get_footer();
