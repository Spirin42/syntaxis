<?php
/**
 * Обычная страница — «О нас» и всё, что появится позже.
 *
 * Заголовки h2 в редакторе превращаются в секции с ярлыком слева:
 * пишешь h2 и абзацы под ним — получаешь полосу внутренней страницы.
 */

if (!defined('ABSPATH')) exit;

get_header();

while (have_posts()) : the_post();

$id      = get_the_ID();
$kicker  = sx_meta($id, 'kicker');
$sub     = get_the_excerpt();
$content = apply_filters('the_content', get_the_content());
$parts   = sx_sections_from_content($content);
?>

<main class="wrap about-page">

  <nav class="crumbs" aria-label="Хлебные крошки">
    <a href="<?php echo esc_url(home_url('/')); ?>">Главная</a>
    <span class="crumbs__sep">/</span>
    <span><?php the_title(); ?></span>
  </nav>

  <div class="page-head">
    <?php if ($kicker !== '') : ?>
    <p class="rubric"><?php echo esc_html($kicker); ?></p>
    <?php endif; ?>
    <h1 class="page-head__title"><?php echo sx_title_html(get_the_title()); ?></h1>
    <?php if ($sub !== '') : ?>
    <p class="page-head__sub"><?php echo esc_html($sub); ?></p>
    <?php endif; ?>
  </div>

  <?php if ($parts['intro'] !== '') : ?>
  <?php echo $parts['intro']; ?>
  <?php endif; ?>

  <?php foreach ($parts['sections'] as $section) :
      // Сетки принципов, цифр и людей нельзя зажимать в колонку .prose —
      // они и так считают число колонок сами.
      $wide = (bool) preg_match('/class="(?:tenets|figures|people)"/', $section['body']);
  ?>
  <section class="section">
    <h2 class="section__label"><?php echo esc_html($section['label']); ?></h2>
    <div class="section__body<?php echo $wide ? '' : ' prose'; ?>"><?php echo $section['body']; ?></div>
  </section>
  <?php endforeach; ?>

</main>

<?php endwhile;

get_footer();
