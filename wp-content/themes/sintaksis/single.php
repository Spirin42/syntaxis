<?php
/**
 * Материал журнала.
 */

if (!defined('ABSPATH')) exit;

get_header();

while (have_posts()) : the_post();

$id     = get_the_ID();
$cats   = get_the_category();
$rubric = $cats ? $cats[0]->name : '';
$dek    = get_the_excerpt();
$by     = sx_byline($id);
$end    = sx_meta($id, 'end_note');

$content = apply_filters('the_content', get_the_content());

// Врезка «книга по теме» — если книга выбрана в полях материала.
$book_id = (int) sx_meta($id, 'inset_book');
$inset   = '';

if ($book_id && get_post_status($book_id) === 'publish') {
    ob_start(); ?>
    <div class="inset">
      <div class="inset__cover">
        <?php get_template_part('template-parts/cover', null, ['id' => $book_id]); ?>
      </div>
      <div class="inset__body">
        <p class="inset__label">Книга по теме</p>
        <p class="inset__title"><?php
            $a = sx_authors($book_id);
            echo esc_html($a !== '' ? $a . '. ' . get_the_title($book_id) : get_the_title($book_id));
        ?></p>
        <p class="inset__meta"><?php echo esc_html(sx_book_meta_line($book_id)); ?></p>
        <a class="text-btn" href="<?php echo esc_url(get_permalink($book_id)); ?>">О книге <span class="arr">→</span></a>
      </div>
    </div>
    <?php
    $inset = (string) ob_get_clean();
}

$content = sx_insert_inset($content, $inset);
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
    <?php if ($rubric !== '') : ?>
    <span class="crumbs__sep">/</span>
    <span><?php echo esc_html($rubric); ?></span>
    <?php endif; ?>
  </nav>

  <article class="article">

    <div class="article__head">
      <?php if ($rubric !== '') : ?>
      <p class="rubric"><?php echo esc_html($rubric); ?></p>
      <?php endif; ?>
      <h1 class="article__title"><?php the_title(); ?></h1>
      <?php if ($dek !== '') : ?>
      <p class="article__dek"><?php echo esc_html($dek); ?></p>
      <?php endif; ?>
      <p class="article__byline">
        <span><b><?php echo esc_html($by['name']); ?></b><?php echo $by['role'] !== '' ? ', ' . esc_html($by['role']) : ''; ?></span>
        <span><?php echo esc_html(sx_date($id)); ?></span>
        <?php if ($by['time'] !== '') : ?>
        <span><?php echo esc_html($by['time']); ?></span>
        <?php endif; ?>
      </p>
    </div>

    <div class="article__body">
      <?php echo $content; ?>
      <?php if ($end !== '') : ?>
      <p class="article__end"><?php echo esc_html($end); ?></p>
      <?php endif; ?>
    </div>

  </article>

  <?php get_template_part('template-parts/more', null, [
      'label' => 'Ещё в журнале',
      'items' => sx_related_ids($id, 3, 1),
  ]); ?>

</main>

<?php endwhile;

get_footer();
