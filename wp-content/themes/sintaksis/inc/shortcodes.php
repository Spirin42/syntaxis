<?php
/**
 * Подстановки для страниц.
 *
 * На странице «О нас» есть блоки, которые не набираются абзацами:
 * манифест, нумерованные принципы, цифры, люди, контакты. Их содержимое
 * правится в полях под редактором, а в текст страницы ставится пометка
 * в квадратных скобках — там блок и появится.
 */

if (!defined('ABSPATH')) exit;

/** «Заголовок | Текст» построчно → массив пар. */
function sx_pairs(string $text): array {
    $rows = [];
    foreach (sx_lines($text) as $line) {
        $bits  = array_map('trim', explode('|', $line, 2));
        $rows[] = [$bits[0], $bits[1] ?? ''];
    }
    return $rows;
}

function sx_page_meta(string $key): string {
    $id = get_the_ID();
    return $id ? (string) get_post_meta($id, '_sx_' . $key, true) : '';
}

add_shortcode('манифест', function () {
    $text = sx_page_meta('manifesto');
    if ($text === '') return '';

    // *вот так* — красным курсивом, как в макете
    $html = preg_replace('/\*([^*]+)\*/u', '<em>$1</em>', esc_html($text));
    return '<p class="manifesto">' . $html . '</p>';
});

add_shortcode('принципы', function () {
    $rows = sx_pairs(sx_page_meta('tenets'));
    if (!$rows) return '';

    $out = '<ol class="tenets">';
    foreach ($rows as [$title, $text]) {
        $out .= '<li><h3>' . esc_html($title) . '</h3>';
        if ($text !== '') $out .= '<p>' . esc_html($text) . '</p>';
        $out .= '</li>';
    }
    return $out . '</ol>';
});

add_shortcode('цифры', function () {
    $rows = sx_pairs(sx_page_meta('figures'));
    if (!$rows) return '';

    $out = '<div class="figures">';
    foreach ($rows as [$number, $label]) {
        $out .= '<div><b>' . esc_html($number) . '</b><span>' . esc_html($label) . '</span></div>';
    }
    return $out . '</div>';
});

add_shortcode('люди', function () {
    $rows = sx_pairs(sx_page_meta('people'));
    if (!$rows) return '';

    $out = '<ul class="people">';
    foreach ($rows as [$name, $role]) {
        $out .= '<li><b>' . esc_html($name) . '</b><span>' . esc_html($role) . '</span></li>';
    }
    return $out . '</ul>';
});

add_shortcode('контакты', function () {
    $rows = sx_pairs(sx_page_meta('contacts'));
    if (!$rows) return '';

    $out = '<div class="contact-line">';
    foreach ($rows as [$label, $url]) {
        $out .= $url !== ''
            ? '<a href="' . esc_url($url) . '">' . esc_html($label) . '</a>'
            : '<span>' . esc_html($label) . '</span>';
    }
    return $out . '</div>';
});

/**
 * Редактор кладёт пометку внутрь абзаца: <p>[принципы]</p>. Подстановка
 * возвращает список или сетку, а они внутри <p> стоять не могут —
 * поэтому снимаем обёртку заранее, до раскрытия пометок.
 */
add_filter('the_content', function ($html) {
    return preg_replace(
        '~<p>\s*(\[(?:манифест|принципы|цифры|люди|контакты)\])\s*</p>~u',
        '$1',
        $html
    );
}, 9);
