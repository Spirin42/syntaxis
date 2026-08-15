<?php
/**
 * Поля книги и материала журнала.
 *
 * Никаких плагинов вроде ACF: поля описаны массивом ниже, а рисуются
 * и сохраняются одним общим кодом. Хочешь новое поле — допиши строчку
 * в sx_field_groups() и выведи его в шаблоне.
 */

if (!defined('ABSPATH')) exit;

/**
 * Все поля, разложенные по блокам редактора.
 * Ключ поля — без приставки: в базе он лежит как _sx_<ключ>.
 */
function sx_field_groups(string $post_type): array {
    $palette_options = [];
    foreach (sx_cover_palettes() as $key => $p) {
        $palette_options[$key] = $p['label'];
    }

    if ($post_type === 'book') {
        return [
            'card' => [
                'title'  => 'Карточка книги',
                'fields' => [
                    'year'       => ['label' => 'Год издания', 'type' => 'number', 'hint' => 'По нему книги группируются в каталоге.'],
                    'status'     => ['label' => 'Статус', 'type' => 'select', 'options' => ['sale' => 'В продаже', 'soon' => 'Готовим к печати']],
                    'soon_label' => ['label' => 'Когда выйдет', 'type' => 'text', 'hint' => 'Показывается вместо цены у книги, которая ещё готовится. Например: «Октябрь 2026».'],
                    'badge'      => ['label' => 'Ярлык на карточке', 'type' => 'text', 'hint' => 'Например: «Новинка · август 2026». Пусто — тема поставит «Жанр · Год».'],
                    'badge_accent' => ['label' => 'Ярлык красным', 'type' => 'checkbox', 'hint' => 'Так помечают новинку.'],
                ],
            ],
            'cover' => [
                'title'  => 'Обложка',
                'fields' => [
                    '_note'           => ['type' => 'note', 'text' => 'Если загрузить «Изображение книги» (справа) — покажем его. Пока картинки нет, обложка рисуется вот этими полями.'],
                    'cover_style'     => ['label' => 'Стиль обложки', 'type' => 'select', 'options' => $palette_options, 'hint' => 'Верхние шесть принимают любое название. Ниже — с характером: киноварь набирает первое слово огромным, песок ждёт три строки, индиго — одну короткую (уходит на корешок), зелень и белая просят инициал.'],
                    'cover_top'       => ['label' => 'Верхняя строка', 'type' => 'text', 'hint' => 'Пусто — подставим имя автора.'],
                    'cover_title'     => ['label' => 'Название на обложке', 'type' => 'textarea', 'rows' => 3, 'hint' => 'Каждая строка здесь — отдельная строка на обложке. Звёздочки *вот так* делают слово полужирным. Пусто — возьмём название книги.'],
                    'cover_initial'   => ['label' => 'Инициал', 'type' => 'text', 'hint' => 'Одна буква или знак. Работает только у палитр «зелень» и «белая».'],
                    'cover_bottom'    => ['label' => 'Нижняя строка', 'type' => 'text', 'hint' => 'Например: «роман» или «проза · 416 с.». Пусто — подставим жанр.'],
                    'cover_bottom_em' => ['label' => 'Нижнюю строку — курсивом', 'type' => 'checkbox'],
                    'cover_semicolon' => ['label' => 'Красная «;» в конце названия', 'type' => 'checkbox'],
                ],
            ],
            'buy' => [
                'title'  => 'Покупка',
                'fields' => [
                    'price'         => ['label' => 'Цена, ₽', 'type' => 'number'],
                    'link_ozon'     => ['label' => 'Ozon', 'type' => 'url'],
                    'link_wb'       => ['label' => 'Wildberries', 'type' => 'url'],
                    'link_labirint' => ['label' => 'Лабиринт', 'type' => 'url'],
                    'price_ebook'   => ['label' => 'Электронная, ₽', 'type' => 'number'],
                    'link_ebook'    => ['label' => 'Ссылка на электронную', 'type' => 'url'],
                    'buy_note'      => ['label' => 'Примечание под кнопками', 'type' => 'textarea', 'rows' => 2, 'hint' => 'Видно только на странице книги.'],
                ],
            ],
            'specs' => [
                'title'  => 'Выходные данные',
                'fields' => [
                    '_note'        => ['type' => 'note', 'text' => 'Пустые строки на странице книги не показываются.'],
                    'series'       => ['label' => 'Серия', 'type' => 'text'],
                    'pages'        => ['label' => 'Объём', 'type' => 'text', 'hint' => 'Например: «384 страницы».'],
                    'format'       => ['label' => 'Формат', 'type' => 'text'],
                    'paper'        => ['label' => 'Бумага', 'type' => 'text'],
                    'print_run'    => ['label' => 'Тираж', 'type' => 'text'],
                    'isbn'         => ['label' => 'ISBN', 'type' => 'text'],
                    'age'          => ['label' => 'Возрастная маркировка', 'type' => 'text'],
                    'cover_artist' => ['label' => 'Художник обложки', 'type' => 'text'],
                ],
            ],
            'extra' => [
                'title'  => 'Фрагмент и пресса',
                'fields' => [
                    'excerpt_text' => ['label' => 'Фрагмент книги', 'type' => 'textarea', 'rows' => 10, 'hint' => 'Абзацы разделяй пустой строкой. Первая буква станет буквицей.'],
                    'excerpt_link' => ['label' => 'Ссылка «читать целиком»', 'type' => 'url'],
                    'press_quote'  => ['label' => 'Цитата из прессы', 'type' => 'textarea', 'rows' => 3],
                    'press_source' => ['label' => 'Кто сказал', 'type' => 'text', 'hint' => 'Например: «Юля Мещерская, „Знамя“».'],
                ],
            ],
            'front' => [
                'title'  => 'На главной',
                'fields' => [
                    'featured'       => ['label' => 'Показывать на главной', 'type' => 'checkbox'],
                    'featured_order' => ['label' => 'Порядок', 'type' => 'number', 'hint' => 'Меньше число — выше в мозаике. Этажи режутся по схеме 1 · 3 · 2 · 4.'],
                ],
                'context' => 'side',
            ],
        ];
    }

    if ($post_type === 'post') {
        return [
            'article' => [
                'title'  => 'Материал',
                'fields' => [
                    'byline'       => ['label' => 'Автор материала', 'type' => 'text', 'hint' => 'Пусто — возьмём имя из профиля.'],
                    'role'         => ['label' => 'Должность', 'type' => 'text', 'hint' => 'Например: «главный редактор».'],
                    'reading_time' => ['label' => 'Время чтения', 'type' => 'text', 'hint' => 'Например: «12 минут».'],
                    'inset_book'   => ['label' => 'Врезка «книга по теме»', 'type' => 'book', 'hint' => 'Появится в середине текста, после третьего заголовка или в конце.'],
                    'end_note'     => ['label' => 'Строчка об авторе в конце', 'type' => 'textarea', 'rows' => 3],
                ],
            ],
            'front' => [
                'title'  => 'На главной',
                'fields' => [
                    'featured'       => ['label' => 'Показывать на главной', 'type' => 'checkbox'],
                    'featured_order' => ['label' => 'Порядок', 'type' => 'number', 'hint' => 'Меньше число — выше в мозаике.'],
                ],
                'context' => 'side',
            ],
        ];
    }

    if ($post_type === 'page') {
        return [
            'page' => [
                'title'  => 'Блоки страницы',
                'fields' => [
                    '_note'     => ['type' => 'note', 'text' => 'Заполни нужные списки, а в тексте страницы поставь пометку в квадратных скобках там, где список должен появиться: [манифест], [принципы], [цифры], [люди], [контакты]. Пустые поля просто не показываются.'],
                    'kicker'    => ['label' => 'Надзаголовок', 'type' => 'text', 'hint' => 'Красная строчка над заголовком. Например: «Об издательстве».'],
                    'manifesto' => ['label' => 'Манифест', 'type' => 'textarea', 'rows' => 3, 'hint' => 'Одна крупная фраза. Звёздочки *вот так* красят слова в красный курсив.'],
                    'tenets'    => ['label' => 'Принципы', 'type' => 'textarea', 'rows' => 6, 'hint' => 'По одному в строке, в виде: Заголовок | Пояснение.'],
                    'figures'   => ['label' => 'Цифры', 'type' => 'textarea', 'rows' => 4, 'hint' => 'По одной в строке: Число | Подпись.'],
                    'people'    => ['label' => 'Люди', 'type' => 'textarea', 'rows' => 5, 'hint' => 'По одному в строке: Имя | Роль.'],
                    'contacts'  => ['label' => 'Контакты', 'type' => 'textarea', 'rows' => 3, 'hint' => 'По одному в строке: Подпись | ссылка. Например: post@sintaksis.press | mailto:post@sintaksis.press'],
                ],
            ],
        ];
    }

    return [];
}

/** Регистрируем поля, чтобы WordPress знал о них (ревизии, права доступа). */
add_action('init', function () {
    foreach (['book', 'post', 'page'] as $type) {
        foreach (sx_field_groups($type) as $group) {
            foreach ($group['fields'] as $key => $field) {
                if ($key === '_note') continue;
                register_post_meta($type, '_sx_' . $key, [
                    'single'        => true,
                    'type'          => 'string',
                    'show_in_rest'  => false,
                    'auth_callback' => fn() => current_user_can('edit_posts'),
                ]);
            }
        }
    }
}, 30);

add_action('add_meta_boxes', function ($post_type) {
    foreach (sx_field_groups($post_type) as $id => $group) {
        add_meta_box(
            'sx_' . $id,
            $group['title'],
            'sx_render_meta_box',
            $post_type,
            $group['context'] ?? 'normal',
            'high',
            ['group' => $group]
        );
    }
});

function sx_render_meta_box(WP_Post $post, array $box): void {
    $group = $box['args']['group'];
    wp_nonce_field('sx_save_meta', 'sx_meta_nonce');

    echo '<div class="sx-fields">';

    foreach ($group['fields'] as $key => $field) {
        if (($field['type'] ?? '') === 'note') {
            echo '<p class="sx-note">' . esc_html($field['text']) . '</p>';
            continue;
        }

        $name  = '_sx_' . $key;
        $value = (string) get_post_meta($post->ID, $name, true);
        $id    = 'sx-' . $key;

        echo '<p class="sx-field sx-field--' . esc_attr($field['type']) . '">';

        if ($field['type'] === 'checkbox') {
            printf(
                '<label for="%1$s"><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s> %4$s</label>',
                esc_attr($id),
                esc_attr($name),
                checked($value, '1', false),
                esc_html($field['label'])
            );
        } else {
            printf('<label for="%s"><span class="sx-label">%s</span>', esc_attr($id), esc_html($field['label']));

            switch ($field['type']) {
                case 'textarea':
                    printf(
                        '<textarea id="%s" name="%s" rows="%d" class="widefat">%s</textarea>',
                        esc_attr($id), esc_attr($name), (int) ($field['rows'] ?? 4), esc_textarea($value)
                    );
                    break;

                case 'select':
                    printf('<select id="%s" name="%s" class="widefat">', esc_attr($id), esc_attr($name));
                    foreach ($field['options'] as $ov => $ol) {
                        printf('<option value="%s" %s>%s</option>', esc_attr($ov), selected($value, $ov, false), esc_html($ol));
                    }
                    echo '</select>';
                    break;

                case 'book':
                    printf('<select id="%s" name="%s" class="widefat"><option value="">— нет —</option>', esc_attr($id), esc_attr($name));
                    foreach (get_posts(['post_type' => 'book', 'numberposts' => 100, 'orderby' => 'title', 'order' => 'ASC']) as $b) {
                        printf('<option value="%d" %s>%s</option>', $b->ID, selected($value, (string) $b->ID, false), esc_html($b->post_title));
                    }
                    echo '</select>';
                    break;

                default:
                    printf(
                        '<input type="%s" id="%s" name="%s" value="%s" class="widefat">',
                        esc_attr($field['type'] === 'number' ? 'number' : ($field['type'] === 'url' ? 'url' : 'text')),
                        esc_attr($id), esc_attr($name), esc_attr($value)
                    );
            }

            echo '</label>';
        }

        if (!empty($field['hint'])) {
            echo '<span class="sx-hint">' . esc_html($field['hint']) . '</span>';
        }

        echo '</p>';
    }

    echo '</div>';
}

add_action('save_post', function ($post_id, $post) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!isset($_POST['sx_meta_nonce']) || !wp_verify_nonce($_POST['sx_meta_nonce'], 'sx_save_meta')) return;
    if (!current_user_can('edit_post', $post_id)) return;

    foreach (sx_field_groups($post->post_type) as $group) {
        foreach ($group['fields'] as $key => $field) {
            if ($key === '_note') continue;

            $name = '_sx_' . $key;

            if ($field['type'] === 'checkbox') {
                isset($_POST[$name]) ? update_post_meta($post_id, $name, '1') : delete_post_meta($post_id, $name);
                continue;
            }

            if (!isset($_POST[$name])) continue;
            $raw = wp_unslash($_POST[$name]);

            $value = match ($field['type']) {
                'url'      => esc_url_raw($raw),
                'number'   => preg_replace('/[^0-9.\-]/', '', $raw),
                'textarea' => sanitize_textarea_field($raw),
                default    => sanitize_text_field($raw),
            };

            $value === '' ? delete_post_meta($post_id, $name) : update_post_meta($post_id, $name, $value);
        }
    }
}, 10, 2);

/** Фамилия автора — для сортировки списка по алфавиту. */
add_action('book_author_edit_form_fields', function ($term) {
    $value = get_term_meta($term->term_id, '_sx_surname', true);
    ?>
    <tr class="form-field">
        <th><label for="sx-surname">Фамилия</label></th>
        <td>
            <input type="text" id="sx-surname" name="sx_surname" value="<?php echo esc_attr($value); ?>">
            <p class="description">Только если автоматическая сортировка ошиблась: по умолчанию фамилией считается последнее слово имени.</p>
        </td>
    </tr>
    <?php
});

add_action('edited_book_author', function ($term_id) {
    if (!current_user_can('manage_categories')) return;
    if (!isset($_POST['sx_surname'])) return;

    $value = sanitize_text_field(wp_unslash($_POST['sx_surname']));
    $value === '' ? delete_term_meta($term_id, '_sx_surname') : update_term_meta($term_id, '_sx_surname', $value);
});

/** Немного порядка в блоках редактора. Только в админке — на сайт не попадает. */
add_action('admin_head', function () {
    ?>
    <style>
      .sx-fields { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 4px 20px; }
      .sx-field--textarea, .sx-field--note { grid-column: 1 / -1; }
      .sx-fields .sx-label { display: block; font-weight: 600; margin-bottom: 3px; }
      .sx-fields .sx-hint { display: block; margin-top: 3px; color: #646970; font-size: 12px; line-height: 1.4; }
      .sx-note { grid-column: 1 / -1; margin: 0 0 6px; padding: 8px 12px; background: #f6f7f7; border-left: 3px solid #682D57; font-size: 13px; }
      #sx_front .sx-fields { grid-template-columns: 1fr; }
    </style>
    <?php
});
