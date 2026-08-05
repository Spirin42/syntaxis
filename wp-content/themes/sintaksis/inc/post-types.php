<?php
/**
 * Типы контента.
 *
 * Книги      — свой тип «book», архив /books/, страница /books/<слаг>/.
 * Журнал     — встроенные записи WordPress, переименованные в «Журнал»,
 *              с адресами /journal/<слаг>/. Встроенные, а не свой тип:
 *              так бесплатно достаются рубрики, RSS, поиск и лента.
 * Авторы     — таксономия у книг. Своих страниц у авторов нет
 *              (решение владельца: authors.html — просто список имён),
 *              поэтому таксономия без публичных адресов.
 * Жанры      — таксономия у книг, архив /genre/<слаг>/ рисуется каталогом.
 */

if (!defined('ABSPATH')) exit;

add_action('init', function () {

    register_post_type('book', [
        'labels' => [
            'name'               => 'Книги',
            'singular_name'      => 'Книга',
            'add_new'            => 'Добавить книгу',
            'add_new_item'       => 'Новая книга',
            'edit_item'          => 'Редактировать книгу',
            'new_item'           => 'Новая книга',
            'view_item'          => 'Смотреть страницу книги',
            'search_items'       => 'Искать книги',
            'not_found'          => 'Книг пока нет',
            'not_found_in_trash' => 'В корзине книг нет',
            'all_items'          => 'Все книги',
            'menu_name'          => 'Книги',
        ],
        'public'        => true,
        'menu_icon'     => 'dashicons-book-alt',
        'menu_position' => 4,
        'supports'      => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
        'has_archive'   => 'books',
        'rewrite'       => ['slug' => 'books', 'with_front' => false],
        'show_in_rest'  => true,
        'taxonomies'    => ['book_author', 'genre'],
    ]);

    register_taxonomy('book_author', ['book'], [
        'labels' => [
            'name'          => 'Авторы',
            'singular_name' => 'Автор',
            'add_new_item'  => 'Добавить автора',
            'all_items'     => 'Все авторы',
            'edit_item'     => 'Редактировать автора',
            'search_items'  => 'Искать авторов',
            'menu_name'     => 'Авторы',
            'not_found'     => 'Авторов пока нет',
        ],
        'hierarchical'       => true,   // чекбоксы, а не поле с автодополнением: меньше опечаток-дублей
        'public'             => false,
        'publicly_queryable' => false,  // отдельных страниц у авторов нет
        'show_ui'            => true,
        'show_admin_column'  => true,
        'show_in_rest'       => true,
        'rewrite'            => false,
    ]);

    register_taxonomy('genre', ['book'], [
        'labels' => [
            'name'          => 'Жанры',
            'singular_name' => 'Жанр',
            'add_new_item'  => 'Добавить жанр',
            'all_items'     => 'Все жанры',
            'menu_name'     => 'Жанры',
        ],
        'hierarchical'      => true,
        'public'            => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'genre', 'with_front' => false],
    ]);
});

/**
 * Встроенные «Записи» — это наш журнал. Переименовываем ярлыки,
 * чтобы в админке нигде не было слова «Записи».
 */
add_filter('post_type_labels_post', function ($labels) {
    $labels->name               = 'Журнал';
    $labels->singular_name      = 'Материал';
    $labels->menu_name          = 'Журнал';
    $labels->add_new            = 'Добавить материал';
    $labels->add_new_item       = 'Новый материал';
    $labels->edit_item          = 'Редактировать материал';
    $labels->new_item           = 'Новый материал';
    $labels->view_item          = 'Смотреть материал';
    $labels->all_items          = 'Все материалы';
    $labels->search_items       = 'Искать материалы';
    $labels->not_found          = 'Материалов пока нет';
    $labels->not_found_in_trash = 'В корзине материалов нет';
    return $labels;
});

/** Рубрики журнала: «Эссе», «Интервью», «Рецензия», «Хроника». */
add_filter('taxonomy_labels_category', function ($labels) {
    $labels->name          = 'Рубрики';
    $labels->singular_name = 'Рубрика';
    $labels->menu_name     = 'Рубрики';
    $labels->add_new_item  = 'Добавить рубрику';
    $labels->all_items     = 'Все рубрики';
    return $labels;
});

/** Метки журналу не нужны — прячем, чтобы не отвлекали. */
add_action('init', function () {
    unregister_taxonomy_for_object_type('post_tag', 'post');
}, 20);

/**
 * Каталог и архивы: книг на странице столько, чтобы каталог
 * умещался целиком (их у издательства десятки, не тысячи).
 */
add_action('pre_get_posts', function ($q) {
    if (is_admin() || !$q->is_main_query()) return;

    if ($q->is_post_type_archive('book') || $q->is_tax('genre')) {
        $q->set('posts_per_page', 60);
        $q->set('orderby', ['meta_value_num' => 'DESC', 'date' => 'DESC']);
        $q->set('meta_key', '_sx_year');
    }
});

/** Сортировка книг в админке — по году издания, как в каталоге. */
add_filter('manage_book_posts_columns', function ($cols) {
    $out = [];
    foreach ($cols as $key => $label) {
        $out[$key] = $label;
        if ($key === 'title') {
            $out['sx_year']  = 'Год';
            $out['sx_price'] = 'Цена';
            $out['sx_front'] = 'На главной';
        }
    }
    return $out;
});

add_action('manage_book_posts_custom_column', function ($col, $post_id) {
    switch ($col) {
        case 'sx_year':
            echo esc_html(get_post_meta($post_id, '_sx_year', true) ?: '—');
            break;
        case 'sx_price':
            $p = get_post_meta($post_id, '_sx_price', true);
            echo $p ? esc_html($p) . ' ₽' : '<span style="color:#a00">готовится</span>';
            break;
        case 'sx_front':
            echo get_post_meta($post_id, '_sx_featured', true) ? '●' : '';
            break;
    }
}, 10, 2);

add_filter('manage_post_posts_columns', function ($cols) {
    $cols['sx_front'] = 'На главной';
    return $cols;
});

add_action('manage_post_posts_custom_column', function ($col, $post_id) {
    if ($col === 'sx_front') echo get_post_meta($post_id, '_sx_featured', true) ? '●' : '';
}, 10, 2);
