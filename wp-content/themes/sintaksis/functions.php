<?php
/**
 * Тема «Синтаксис» — общая настройка.
 *
 * Тема классическая (PHP-шаблоны), не блочная: вся вёрстка и типографика
 * живут в style.css, который перенесён из статического макета один в один.
 * Правило проекта «JS не добавлять» здесь тоже действует — поэтому
 * inc/cleanup.php выключает скрипты, которые WordPress подключает сам.
 */

if (!defined('ABSPATH')) exit;

define('SX_VERSION', '1.0');

require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/post-types.php';
require_once get_template_directory() . '/inc/meta-boxes.php';
require_once get_template_directory() . '/inc/shortcodes.php';
require_once get_template_directory() . '/inc/cleanup.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/demo-content.php';

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', ['search-form', 'gallery', 'caption', 'style', 'script']);

    // Обложка книги — 5/7, как .cover в макете.
    add_image_size('sx-cover', 700, 980, true);

    register_nav_menus([
        'primary' => 'Шапка сайта',
        'footer'  => 'Подвал: разделы',
        'social'  => 'Подвал: соцсети',
    ]);
});

/**
 * Шрифты и стили. Версия — по времени файла: правишь style.css,
 * браузер сразу видит новое, без ручного сброса кэша.
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'sx-fonts',
        'https://fonts.googleapis.com/css2?family=Golos+Text:wght@400..900&family=Piazzolla:ital,opsz,wght@0,8..30,100..900;1,8..30,100..900&display=swap',
        [],
        null
    );

    $css = get_stylesheet_directory() . '/style.css';
    wp_enqueue_style('sx-style', get_stylesheet_uri(), ['sx-fonts'], file_exists($css) ? filemtime($css) : SX_VERSION);
});

add_filter('wp_resource_hints', function ($urls, $relation) {
    if ($relation === 'preconnect') {
        $urls[] = 'https://fonts.googleapis.com';
        $urls[] = ['href' => 'https://fonts.gstatic.com', 'crossorigin'];
    }
    return $urls;
}, 10, 2);

/** Фавиконка — та же красная точка с запятой, что в макете. */
add_action('wp_head', function () {
    if (has_site_icon()) return;
    echo '<link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns=\'http://www.w3.org/2000/svg\'%20viewBox=\'0%200%2064%2064\'%3E%3Ctext%20x=\'32\'%20y=\'54\'%20font-family=\'Georgia,serif\'%20font-size=\'64\'%20text-anchor=\'middle\'%20fill=\'%23682D57\'%3E;%3C/text%3E%3C/svg%3E">' . "\n";
}, 1);

/** Классы body нам не нужны, но WP-плагины на них рассчитывают — оставляем. */
add_filter('body_class', function ($classes) {
    if (is_front_page()) $classes[] = 'is-front';
    return $classes;
});

/**
 * Длина «читать дальше» и отбивка — в макете многоточие не встречается,
 * дек всегда дописан руками, поэтому отрезаем аккуратно.
 */
add_filter('excerpt_more', fn() => '…');
add_filter('excerpt_length', fn() => 30);
