<?php
function register_taxonomies()
{
  $taxonomies = [
    [
      'abook_collection',
      'query_var' => 'collections',
      'name_icon' => "📚 ",
      'cases' => [
        'Сборник',
        'Сборник',
        'plural' => ['Сборники', 'Сборникам']
      ]
    ],
    [
      'abook_author',
      'query_var' => 'authors',
      'name_icon' => "🧑‍💻 ",
      'cases' => [
        'Автор',
        'Автора',
        'plural' => ['Авторы', 'Авторам']
      ]
    ],
    [
      'abook_tag',
      'query_var' => 'tags',
      'name_icon' => "📌 ",
      'cases' => [
        'Метка',
        'Метку',
        'plural' => ['Метки', 'Меткам']
      ]
    ],
    [
      'abook_publisher',
      'query_var' => 'publishers',
      'name_icon' => "🏢 ",
      'cases' => [
        'Издатель',
        'Издателя',
        'plural' => ['Издатели', 'Издателям']
      ]
    ],
    [
      'abook_reader',
      'query_var' => 'readers',
      'name_icon' => "🗣 ",
      'cases' => [
        'Чтец',
        'Чтеца',
        'plural' => ['Чтецы', 'Чтецам']
      ]
    ],
    [
      'abook_genre',
      'query_var' => 'genres',
      'name_icon' => "📖 ",
      'cases' => [
        'Жанр',
        'Жанр',
        'plural' => ['Жанры', 'Жанрам']
      ]
    ],
  ];

  foreach ($taxonomies as $tax) {
    $nc = $tax['cases']; // "Name Cases"
    register_taxonomy($tax[0], [], [
      'labels'                => [
        'menu_name'         => $tax['name_icon'] . $nc['plural'][0],
        'name'              => $tax['name_icon'] . $nc[0],
        'singular_name'     => $tax['name_icon'] . $nc[0],
        'search_items'      => '🔍 Искать ' . strtolower($nc[1]),
        'all_items'         => '📋 Все ' . strtolower($nc['plural'][0]),
        'view_item '        => '👁 Смотреть ' . strtolower($nc[1]),
        'parent_item'       => '⬆ Родительский ' . strtolower($nc[0]),
        'parent_item_colon' => '⬆ Родительский ' . strtolower($nc[0]) . ':',
        'edit_item'         => '✏ Изменить ' . strtolower($nc[1]),
        'update_item'       => '🔄 Обновить ' . strtolower($nc[1]),
        'add_new_item'      => 'Добавить ' . strtolower($nc[1]),
        'new_item_name'     => '➕ Новое имя ' . strtolower($nc[1]),
        'back_to_items'     => '⬅ Назад к ' . strtolower($nc['plural'][1]),
      ],
      'show_in_rest'        => true,
      'rest_base'           => 'ab_' . $tax['query_var'],
      'query_var'           => $tax['query_var'],
    ]);
  }
}
add_action('init', 'register_taxonomies');
