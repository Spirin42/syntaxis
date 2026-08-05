<?php
/**
 * Уборка за WordPress.
 *
 * Из коробки WordPress подключает к каждой странице свой CSS блочного
 * редактора и пару скриптов. У нас правило «JS не добавлять», а стили
 * блоков конфликтуют с ручной типографикой темы (clamp, cqi), поэтому
 * всё лишнее выключаем здесь — в одном месте, чтобы было видно.
 */

if (!defined('ABSPATH')) exit;

/**
 * Стили блочного редактора и глобальные стили theme.json на сайте не нужны:
 * вся вёрстка живёт в style.css, а их пресеты и layout-правила только
 * спорят с ручной типографикой.
 *
 * Глобальные стили нельзя просто снять через wp_dequeue_style: WordPress
 * ставит их дважды — на wp_enqueue_scripts и ещё раз на wp_footer, откуда
 * потом «поднимает» в <head>. Поэтому снимаем сами обработчики.
 */
add_action('init', function () {
    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
});

/** Пусть стили блоков собираются в один файл — его дальше снимаем целиком. */
add_filter('should_load_separate_core_block_assets', '__return_false');

add_action('wp_print_styles', function () {
    foreach (['wp-block-library', 'wp-block-library-theme', 'classic-theme-styles', 'global-styles'] as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }
}, 100);

/** Скрипты, которые WordPress добавляет сам: эмодзи и встраивания. */
add_action('init', function () {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rest_output_link_wp_head');
});

add_action('wp_footer', function () {
    wp_dequeue_script('wp-embed');
}, 1);

/** Правила предзагрузки — тоже тег <script>, тоже лишний. */
add_filter('wp_speculation_rules_configuration', '__return_null');

/** Паттерны блоков с wordpress.org сломали бы вёрстку — не тянем их. */
add_filter('should_load_remote_block_patterns', '__return_false');
add_action('after_setup_theme', function () {
    remove_theme_support('core-block-patterns');
}, 20);

/**
 * Список разрешённых блоков.
 *
 * В style.css есть стили только для абзацев, заголовков, списков,
 * цитат и картинок. Колонки, галереи, кнопки и обложки из редактора
 * вышли бы на сайте неоформленными — поэтому их просто нет в меню.
 */
add_filter('allowed_block_types_all', function ($allowed, $context) {
    if (!isset($context->post) || !in_array($context->post->post_type, ['post', 'page', 'book'], true)) {
        return $allowed;
    }

    return [
        'core/paragraph',
        'core/heading',
        'core/list',
        'core/list-item',
        'core/quote',
        'core/image',
        'core/html',
    ];
}, 10, 2);

/**
 * Комментарии выключены: издательству они не нужны, а стилей для них нет.
 */
add_action('init', function () {
    foreach (['post', 'page', 'book'] as $type) {
        remove_post_type_support($type, 'comments');
        remove_post_type_support($type, 'trackbacks');
    }
}, 20);

add_filter('comments_open', '__return_false', 20);
add_filter('pings_open', '__return_false', 20);
add_filter('comments_array', '__return_empty_array', 20);

add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

add_action('wp_before_admin_bar_render', function () {
    global $wp_admin_bar;
    $wp_admin_bar->remove_node('comments');
});

/** Убираем «Привет, мир!» из свежей установки и не даём заводить новые. */
add_action('admin_bar_menu', function ($bar) {
    $bar->remove_node('wp-logo');
}, 999);
