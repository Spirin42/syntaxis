<?php
/**
 * Помощники темы: обложки, цены, разбор контента на секции, раскладка этажей.
 */

if (!defined('ABSPATH')) exit;

/**
 * Палитры обложек — ровно те одиннадцать, что нарисованы в style.css.
 * light  — светлая обложка, ей нужна волосяная обводка .cover--light.
 * layout — как раскладывается название:
 *   plain    — строки через <br>
 *   lines2   — две строки разного кегля (.l1/.l2), первая огромная
 *   lines3   — три строки словарной статьи (.w1/.w2/.w3)
 *   vert     — название вертикально по корешку, одной строкой
 *   initial  — крупный инициал плюс название
 */
function sx_cover_palettes(): array {
    // Порядок важен: первая палитра достаётся книге, у которой стиль ещё
    // не выбран, поэтому сверху те, что принимают любое название,
    // а внизу — с особенностями (огромное первое слово, три строки, инициал).
    return [
        'kabinet' => ['label' => 'Бордо, линейки',                  'light' => false, 'layout' => 'plain'],
        'tabak'   => ['label' => 'Табачная, разрядка',              'light' => false, 'layout' => 'plain'],
        'polyn'   => ['label' => 'Олива, курсив по центру',         'light' => false, 'layout' => 'plain'],
        'nichey'  => ['label' => 'Чёрная, строчные',                'light' => false, 'layout' => 'plain'],
        'vnutr'   => ['label' => 'Слоновая кость, рамка',           'light' => true,  'layout' => 'plain'],
        'stekla'  => ['label' => 'Светлое стекло, тонкий шрифт',    'light' => true,  'layout' => 'plain'],
        'hor'     => ['label' => 'Киноварь, конструктивизм',        'light' => false, 'layout' => 'lines2'],
        'slovar'  => ['label' => 'Песок, словарная статья',         'light' => true,  'layout' => 'lines3'],
        'sever'   => ['label' => 'Индиго, вертикальный корешок',    'light' => false, 'layout' => 'vert'],
        'tishe'   => ['label' => 'Бутылочная зелень, инициал',      'light' => false, 'layout' => 'initial'],
        'pustoe'  => ['label' => 'Белая, крупный знак',             'light' => true,  'layout' => 'initial'],
    ];
}

/** Русское склонение: sx_plural(11, ['книга','книги','книг']) → «книг». */
function sx_plural(int $n, array $forms): string {
    $n = abs($n) % 100;
    $n1 = $n % 10;

    if ($n > 10 && $n < 20) return $forms[2];
    if ($n1 > 1 && $n1 < 5)  return $forms[1];
    if ($n1 === 1)           return $forms[0];

    return $forms[2];
}

/**
 * Заголовок страницы: точка с запятой в конце становится
 * фирменным красным знаком, как в логотипе.
 */
function sx_title_html(string $title): string {
    return preg_replace('/;\s*$/u', '<span class="wordmark__mark">;</span>', esc_html($title));
}

/** В <title> вкладки фирменная точка с запятой не нужна. */
add_filter('document_title_parts', function ($parts) {
    if (isset($parts['title'])) $parts['title'] = rtrim($parts['title'], '; ');
    return $parts;
});

/** Строки многострочного поля без пустых. */
function sx_lines(string $text): array {
    $lines = preg_split('/\R/u', trim($text));
    return array_values(array_filter(array_map('trim', $lines), fn($l) => $l !== ''));
}

/** Экранируем и разрешаем одну условность: *слово* — полужирным. */
function sx_cover_line(string $line): string {
    $line = esc_html($line);
    return preg_replace('/\*([^*]+)\*/u', '<b>$1</b>', $line);
}

/** Имена авторов книги через запятую. */
function sx_authors(int $post_id): string {
    $terms = get_the_terms($post_id, 'book_author');
    if (!$terms || is_wp_error($terms)) return '';
    return implode(', ', wp_list_pluck($terms, 'name'));
}

/** Название жанра (первый). */
function sx_genre(int $post_id): string {
    $terms = get_the_terms($post_id, 'genre');
    if (!$terms || is_wp_error($terms)) return '';
    return $terms[0]->name;
}

function sx_meta(int $post_id, string $key): string {
    return (string) get_post_meta($post_id, '_sx_' . $key, true);
}

function sx_is_soon(int $post_id): bool {
    return sx_meta($post_id, 'status') === 'soon';
}

/** «780 ₽» либо пусто, если книга ещё готовится. */
function sx_price(int $post_id): string {
    $price = sx_meta($post_id, 'price');
    return $price !== '' ? $price . ' ₽' : '';
}

/** Ярлык-чип: свой текст, иначе «Жанр · Год». */
function sx_chip_text(int $post_id): string {
    $badge = sx_meta($post_id, 'badge');
    if ($badge !== '') return $badge;

    $parts = array_filter([sx_genre($post_id), sx_meta($post_id, 'year')]);
    return implode(' · ', $parts);
}

/** «Роман · 2026 · 780 ₽» — строка под ссылкой в полосе «читайте дальше». */
function sx_book_meta_line(int $post_id): string {
    $parts = array_filter([
        sx_genre($post_id),
        sx_meta($post_id, 'year'),
        sx_is_soon($post_id) ? sx_meta($post_id, 'soon_label') : sx_price($post_id),
    ]);
    return implode(' · ', $parts);
}

/**
 * Разбирает готовый HTML контента на секции по заголовкам h2.
 * Внутренние страницы устроены как «ярлык слева — текст справа»,
 * а в редакторе владелец просто пишет h2 и абзацы под ним.
 *
 * @return array{intro:string, sections:array<int,array{label:string,body:string}>}
 */
function sx_sections_from_content(string $html): array {
    $parts = preg_split('/<h2\b[^>]*>(.*?)<\/h2>/si', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    $intro = trim((string) array_shift($parts));

    $sections = [];
    for ($i = 0; $i < count($parts); $i += 2) {
        $sections[] = [
            'label' => wp_strip_all_tags($parts[$i]),
            'body'  => trim((string) ($parts[$i + 1] ?? '')),
        ];
    }

    return ['intro' => $intro, 'sections' => $sections];
}

/**
 * Раскладка мозаики: сколько блоков в каждом этаже.
 * Схема полосы — как в макете: 1 · 3 · 2 · 4, дальше по кругу.
 * Неполный последний этаж не страшен: «хвост» ряда растягивается сам.
 */
function sx_pour_floors(array $items, array $pattern = [1, 3, 2, 4]): array {
    $floors = [];
    $i = 0;
    $p = 0;

    while ($i < count($items)) {
        $size = $pattern[$p % count($pattern)];
        $floors[] = ['size' => $size, 'items' => array_slice($items, $i, $size)];
        $i += $size;
        $p++;
    }

    return $floors;
}

/** Фамилия для сортировки авторов — последнее слово имени, если не задана вручную. */
function sx_surname(string $name, int $term_id = 0): string {
    if ($term_id) {
        $manual = get_term_meta($term_id, '_sx_surname', true);
        if ($manual) return (string) $manual;
    }
    $words = preg_split('/\s+/u', trim($name));
    return (string) end($words);
}

/** Ссылки-магазины книги: [подпись, url, ...]. */
function sx_buy_links(int $post_id): array {
    $shops = [
        'ozon'     => 'Ozon',
        'wb'       => 'Wildberries',
        'labirint' => 'Лабиринт',
    ];

    $links = [];
    foreach ($shops as $key => $label) {
        $url = sx_meta($post_id, 'link_' . $key);
        if ($url !== '') $links[] = ['label' => $label, 'url' => $url];
    }

    $ebook = sx_meta($post_id, 'link_ebook');
    if ($ebook !== '') {
        $price = sx_meta($post_id, 'price_ebook');
        $links[] = [
            'label' => $price !== '' ? 'Электронная — ' . $price . ' ₽' : 'Электронная',
            'url'   => $ebook,
        ];
    }

    return $links;
}

/** Дата материала журнала по-русски: «28 июля 2026». */
function sx_date(int $post_id): string {
    return (string) get_the_date('j F Y', $post_id);
}

/** Подпись под материалом журнала: автор + должность. */
function sx_byline(int $post_id): array {
    return [
        'name' => sx_meta($post_id, 'byline') ?: get_the_author_meta('display_name', (int) get_post_field('post_author', $post_id)),
        'role' => sx_meta($post_id, 'role'),
        'time' => sx_meta($post_id, 'reading_time'),
    ];
}

/**
 * Вставляет врезку «книга по теме» примерно в середину статьи —
 * после абзаца, ближайшего к середине текста.
 */
function sx_insert_inset(string $html, string $inset): string {
    if ($inset === '') return $html;

    $parts = preg_split('/(<\/p>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    $total = count(array_filter($parts, fn($p) => $p === '</p>'));

    if ($total < 4) return $html . $inset;

    $target = intdiv($total, 2);
    $seen   = 0;
    $out    = '';

    foreach ($parts as $part) {
        $out .= $part;
        if ($part === '</p>' && ++$seen === $target) $out .= $inset;
    }

    return $out;
}

/**
 * Что показать в полосе «читайте дальше»: свежие материалы журнала
 * плюс несколько книг, кроме той страницы, где мы сейчас.
 */
function sx_related_ids(int $exclude, int $posts_n = 2, int $books_n = 2): array {
    $posts = get_posts([
        'post_type'        => 'post',
        'numberposts'      => $posts_n,
        'post__not_in'     => [$exclude],
        'fields'           => 'ids',
        'suppress_filters' => false,
    ]);

    $books = get_posts([
        'post_type'        => 'book',
        'numberposts'      => $books_n,
        'post__not_in'     => [$exclude],
        'fields'           => 'ids',
        'meta_key'         => '_sx_year',
        'orderby'          => ['meta_value_num' => 'DESC', 'date' => 'DESC'],
        'suppress_filters' => false,
    ]);

    return array_merge($posts, $books);
}

/**
 * Меню в шапке и подвале — это просто ссылки подряд, без списка,
 * как в макете. Текущий раздел помечается aria-current, перед ним
 * CSS рисует красную точку.
 */
class SX_Nav_Walker extends Walker_Nav_Menu {

    public function __construct(private bool $external = false) {}

    public function start_lvl(&$output, $depth = 0, $args = null) {}
    public function end_lvl(&$output, $depth = 0, $args = null) {}
    public function end_el(&$output, $item, $depth = 0, $args = null) {}

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        if ($depth > 0) return;

        // Фильтр nav_menu_css_class применяет сам Walker_Nav_Menu, а мы его
        // метод переопределили — значит, применяем сами. Иначе наши же
        // поправки к «текущему разделу» (ниже) до разметки не доедут.
        $classes = apply_filters('nav_menu_css_class', array_filter((array) $item->classes), $item, $args, $depth);

        $current = (bool) array_intersect(
            $classes,
            ['current-menu-item', 'current-menu-parent', 'current-menu-ancestor']
        );

        $output .= sprintf(
            '<a href="%s"%s>%s%s</a>',
            esc_url($item->url),
            $current ? ' aria-current="page"' : '',
            esc_html($item->title),
            $this->external ? ' <span class="ext">↗</span>' : ''
        );
    }
}

/** Пока меню не собрано в админке, показываем разделы по умолчанию. */
function sx_nav_fallback(): void {
    $links = [
        'Книги'  => get_post_type_archive_link('book'),
        'Журнал' => get_permalink(get_option('page_for_posts')) ?: home_url('/'),
        'Авторы' => home_url('/authors/'),
        'О нас'  => home_url('/about/'),
    ];

    foreach ($links as $title => $url) {
        if (!$url) continue;
        printf('<a href="%s">%s</a>', esc_url($url), esc_html($title));
    }
}

/**
 * Какой пункт меню считать текущим.
 *
 * Две поправки к тому, что делает WordPress сам:
 *
 * 1. Он вешает `current_page_parent` на пункт «страница записей» вообще
 *    на всех страницах, которые не являются страницами, — из-за этого
 *    «Журнал» подсвечивался и в каталоге, и на странице книги.
 * 2. Про то, что страница книги и архив жанра относятся к разделу
 *    «Книги», он не знает. Сравниваем адрес пункта с корнем раздела —
 *    так работает и для обычной ссылки, и для пункта-архива.
 */
add_filter('nav_menu_css_class', function ($classes, $item) {
    $classes = array_diff((array) $classes, ['current_page_parent']);

    $path = fn($url) => untrailingslashit((string) parse_url((string) $url, PHP_URL_PATH));

    $here       = $path($item->url);
    $books      = $path(get_post_type_archive_link('book'));
    $journal_id = (int) get_option('page_for_posts');
    $journal    = $journal_id ? $path(get_permalink($journal_id)) : '';

    $in_books   = is_post_type_archive('book') || is_singular('book') || is_tax('genre');
    $in_journal = is_home() || is_singular('post') || is_category() || is_date();

    if (($here === $books && $in_books) || ($here !== '' && $here === $journal && $in_journal)) {
        $classes[] = 'current-menu-item';
    }

    return array_unique($classes);
}, 10, 2);
