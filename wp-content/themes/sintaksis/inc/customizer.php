<?php
/**
 * Настройки сайта: подвал и блок «Об издательстве» на главной.
 * Живут в «Внешний вид → Настроить → Синтаксис».
 */

if (!defined('ABSPATH')) exit;

function sx_settings(): array {
    return [
        'tagline'     => ['label' => 'Подпись под логотипом в подвале', 'default' => 'Издательство современной русской литературы', 'type' => 'text'],
        'email'       => ['label' => 'Почта', 'default' => 'post@sintaksis.press', 'type' => 'text'],
        'footer_note' => ['label' => 'Строчка про самотёк', 'default' => 'Читаем самотёк: присылайте рукописи на почту, отвечаем в течение месяца.', 'type' => 'textarea'],
        'copyright'   => ['label' => 'Копирайт', 'default' => '© ' . date('Y') . ', издательство «Синтаксис»', 'type' => 'text'],
        'books_intro'   => ['label' => 'Каталог: подводка под заголовком', 'default' => 'Всё, что мы издали, — от первой брошюры до того, что уходит в типографию этой осенью. Из продажи не выводим ничего: если книга кончилась, её допечатывают.', 'type' => 'textarea'],
        'journal_intro' => ['label' => 'Журнал: подводка под заголовком', 'default' => 'Эссе, интервью, рецензии и хроника издательства. Пишем о том, что читаем и издаём.', 'type' => 'textarea'],
        'authors_intro' => ['label' => 'Авторы: подводка под заголовком', 'default' => 'Список по алфавиту фамилий; приглушённым набраны те, чьи книги ещё готовятся.', 'type' => 'textarea'],
        'about_quote' => ['label' => 'Главная: цитата в блоке «Об издательстве»', 'default' => 'Нам интересны книги, в которых язык — не упаковка для истории, а само событие.', 'type' => 'textarea'],
        'about_sub'   => ['label' => 'Главная: подводка под цитатой', 'default' => '«Синтаксис» — независимое издательство современной русской прозы и поэзии. Мы выпускаем восемь–десять книг в год и делаем каждую так, как будто она первая.', 'type' => 'textarea'],
    ];
}

function sx_opt(string $key): string {
    $settings = sx_settings();
    return (string) get_theme_mod('sx_' . $key, $settings[$key]['default'] ?? '');
}

add_action('customize_register', function (WP_Customize_Manager $wp_customize) {
    $wp_customize->add_section('sx_section', [
        'title'    => 'Синтаксис',
        'priority' => 20,
    ]);

    foreach (sx_settings() as $key => $s) {
        $wp_customize->add_setting('sx_' . $key, [
            'default'           => $s['default'],
            'sanitize_callback' => $s['type'] === 'textarea' ? 'sanitize_textarea_field' : 'sanitize_text_field',
            'transport'         => 'refresh',
        ]);

        $wp_customize->add_control('sx_' . $key, [
            'label'   => $s['label'],
            'section' => 'sx_section',
            'type'    => $s['type'],
        ]);
    }
});
