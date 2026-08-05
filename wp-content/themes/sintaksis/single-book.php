<?php
/**
 * Страница книги.
 */

if (!defined('ABSPATH')) exit;

get_header();

while (have_posts()) : the_post();

$id      = get_the_ID();
$author  = sx_authors($id);
$chip    = sx_chip_text($id);
$dek     = get_the_excerpt();
$content = trim(apply_filters('the_content', get_the_content()));

$fragment     = sx_meta($id, 'excerpt_text');
$fragment_url = sx_meta($id, 'excerpt_link');

$specs = array_filter([
    'Автор'                 => $author,
    'Название'              => get_the_title(),
    'Жанр'                  => sx_genre($id),
    'Год издания'           => sx_meta($id, 'year'),
    'Серия'                 => sx_meta($id, 'series'),
    'Объём'                 => sx_meta($id, 'pages'),
    'Формат'                => sx_meta($id, 'format'),
    'Бумага'                => sx_meta($id, 'paper'),
    'Тираж'                 => sx_meta($id, 'print_run'),
    'ISBN'                  => sx_meta($id, 'isbn'),
    'Возрастная маркировка' => sx_meta($id, 'age'),
    'Обложка'               => sx_meta($id, 'cover_artist'),
], fn($v) => $v !== '');

$bios  = [];
$terms = get_the_terms($id, 'book_author');
if ($terms && !is_wp_error($terms)) {
    foreach ($terms as $term) {
        if (trim($term->description) !== '') $bios[] = $term->description;
    }
}

$press_quote  = sx_meta($id, 'press_quote');
$press_source = sx_meta($id, 'press_source');
$authors_page = get_page_by_path('authors');
?>

<main class="wrap">

  <nav class="crumbs" aria-label="Хлебные крошки">
    <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
    <span class="crumbs__sep">/</span>
    <a href="<?php echo esc_url(get_post_type_archive_link('book')); ?>">Книги</a>
    <span class="crumbs__sep">/</span>
    <span><?php the_title(); ?></span>
  </nav>

  <article class="bookpage">

    <div class="bookpage__aside">
      <?php get_template_part('template-parts/cover', null, ['id' => $id]); ?>
    </div>

    <div class="bookpage__main">
      <?php if ($chip !== '') : ?>
      <p class="chip<?php echo sx_meta($id, 'badge_accent') === '1' ? ' chip--accent' : ''; ?>"><?php echo esc_html($chip); ?></p>
      <?php endif; ?>
      <?php if ($author !== '') : ?>
      <p class="bookpage__author"><?php echo esc_html($author); ?></p>
      <?php endif; ?>
      <h1 class="bookpage__title"><?php the_title(); ?></h1>
      <?php if ($dek !== '') : ?>
      <p class="bookpage__dek"><?php echo esc_html($dek); ?></p>
      <?php endif; ?>

      <?php if (sx_is_soon($id)) : ?>
      <div class="buy buy--lg">
        <span class="buy__price"><?php echo esc_html(sx_meta($id, 'soon_label') ?: 'Готовим к печати'); ?></span>
        <p class="buy__note">Книга ещё в работе. Напишите нам, если хотите узнать о выходе первыми.</p>
      </div>
      <?php else : ?>
      <?php get_template_part('template-parts/buy', null, ['id' => $id, 'large' => true]); ?>
      <?php endif; ?>
    </div>

  </article>

  <?php if ($content !== '') : ?>
  <section class="section">
    <h2 class="section__label">О книге</h2>
    <div class="section__body prose"><?php echo $content; ?></div>
  </section>
  <?php endif; ?>

  <?php if ($fragment !== '') : ?>
  <section class="section">
    <h2 class="section__label">Фрагмент</h2>
    <div class="section__body">
      <div class="excerpt"><?php echo wpautop(esc_html($fragment)); ?></div>
      <?php if ($fragment_url !== '') : ?>
      <p class="excerpt__more"><a class="text-btn" href="<?php echo esc_url($fragment_url); ?>">Читать первую главу целиком <span class="arr">→</span></a></p>
      <?php endif; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($specs) : ?>
  <section class="section">
    <h2 class="section__label">Выходные данные</h2>
    <div class="section__body">
      <dl class="specs">
        <?php foreach ($specs as $label => $value) : ?>
        <div><dt><?php echo esc_html($label); ?></dt><dd><?php echo esc_html($value); ?></dd></div>
        <?php endforeach; ?>
      </dl>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($bios) : ?>
  <section class="section">
    <h2 class="section__label">Об авторе</h2>
    <div class="section__body prose">
      <?php foreach ($bios as $bio) : ?>
      <?php echo wpautop(esc_html($bio)); ?>
      <?php endforeach; ?>
      <?php if ($authors_page) : ?>
      <p><a href="<?php echo esc_url(get_permalink($authors_page)); ?>">Все авторы издательства →</a></p>
      <?php endif; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($press_quote !== '') : ?>
  <section class="section">
    <h2 class="section__label">Пресса</h2>
    <div class="section__body">
      <p class="lead-serif"><?php echo esc_html($press_quote); ?></p>
      <?php if ($press_source !== '') : ?>
      <p class="meta"><?php echo esc_html($press_source); ?></p>
      <?php endif; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php get_template_part('template-parts/more', null, [
      'label' => 'Читайте дальше',
      'items' => sx_related_ids($id, 2, 2),
  ]); ?>

</main>

<?php endwhile;

get_footer();
