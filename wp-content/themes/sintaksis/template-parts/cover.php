<?php
/**
 * Обложка книги.
 *
 * Если у книги загружено изображение обложки — показываем его.
 * Если нет — рисуем обложку средствами CSS, как в макете: палитра
 * выбирается в поле «Стиль обложки», текст берётся из полей книги.
 *
 * @var array $args ['id' => ID книги]
 */

if (!defined('ABSPATH')) exit;

$id = (int) ($args['id'] ?? get_the_ID());

if (has_post_thumbnail($id)) {
    echo get_the_post_thumbnail($id, 'sx-cover', [
        'class' => 'cover cover--photo',
        'alt'   => sprintf('Обложка книги «%s»', get_the_title($id)),
    ]);
    return;
}

$palettes = sx_cover_palettes();
$key      = sx_meta($id, 'cover_style');
if (!isset($palettes[$key])) $key = 'kabinet';
$palette = $palettes[$key];

$top     = sx_meta($id, 'cover_top') ?: sx_authors($id);
$initial = sx_meta($id, 'cover_initial');
$bottom  = sx_meta($id, 'cover_bottom') ?: mb_strtolower(sx_genre($id));
$em      = sx_meta($id, 'cover_bottom_em') === '1';

$lines = sx_lines(sx_meta($id, 'cover_title') ?: get_the_title($id));
$lines = array_map('sx_cover_line', $lines);

if (sx_meta($id, 'cover_semicolon') === '1' && $lines) {
    $lines[count($lines) - 1] .= '<span class="accent">;</span>';
}

// Раскладка названия — своя у трёх палитр, у остальных строки через <br>.
$display = '';
$vert    = '';

switch ($palette['layout']) {
    case 'lines2':                       // .l1 — огромная первая строка, .l2 — остальное
        // Название в одну строку целиком в .l1 не влезет: у этой палитры
        // первая строка идёт в 26cqi. Режем по первому пробелу.
        if (count($lines) === 1 && str_contains($lines[0], ' ')) {
            $lines = explode(' ', $lines[0], 2);
        }
        $first = array_shift($lines) ?? '';
        $rest  = implode(' ', $lines);
        $display = '<span class="l1">' . $first . '</span>';
        if ($rest !== '') $display .= '<span class="l2">' . $rest . '</span>';
        break;

    case 'lines3':                       // словарная статья: три строки разного начертания
        $w = [$lines[0] ?? '', $lines[1] ?? '', implode(' ', array_slice($lines, 2))];
        foreach ($w as $n => $text) {
            if ($text !== '') $display .= '<span class="w' . ($n + 1) . '">' . $text . '</span>';
        }
        break;

    case 'vert':                         // название по корешку, всегда одной строкой
        $vert = implode(' ', $lines);
        break;

    default:
        $display = implode('<br>', $lines);
}

$classes = ['cover'];
if ($palette['light']) $classes[] = 'cover--light';
$classes[] = 'cover--' . $key;
?>
<div class="<?php echo esc_attr(implode(' ', $classes)); ?>">
  <?php if ($vert !== '') : ?>
  <p class="cover__vert"><?php echo $vert; ?></p>
  <?php endif; ?>
  <?php if ($top !== '') : ?>
  <p class="cover__top"><?php echo esc_html($top); ?></p>
  <?php endif; ?>
  <?php if ($initial !== '' && $palette['layout'] === 'initial') : ?>
  <p class="cover__initial"><?php echo esc_html($initial); ?></p>
  <?php endif; ?>
  <?php if ($display !== '') : ?>
  <p class="cover__display"><?php echo $display; ?></p>
  <?php endif; ?>
  <p class="cover__bottom"><?php
    if ($bottom !== '') {
        echo $em ? '<em>' . esc_html($bottom) . '</em>' : '<span>' . esc_html($bottom) . '</span>';
    }
  ?><span class="cover__logo">синтаксис;</span></p>
</div>
