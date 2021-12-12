<?php
add_action('init', 'register_audio_books_post_type');
function register_audio_books_post_type()
{
  $labels = [
    'name' =>               'Аудиокниги',
    'singular_name' =>      'Аудиокнига',
    'add_new' =>            'Добавить аудиокнигу',
    'add_new_item' =>       'Добавить новую аудиокнигу',
    'edit_item' =>          '✏ Редактировать аудиокнигу',
    'new_item' =>           '🆕 Новая аудиокнига',
    'all_items' =>          '📋 Все аудиокниги',
    'view_item' =>          '📄 Просмотр аудиокниг на сайте',
    'search_items' =>       '🔍 Искать аудиокниги',
    'not_found' =>          '🚫 Аудиокниг не найдено.',
    'not_found_in_trash' => '🚫 В корзине нет аудиокниг.',
    'menu_name' =>          'Аудиокниги'
  ];
  $args = [
    'labels' => $labels,
    'public' => true,
    'show_in_rest' => true,
    'rest_base' => 'abooks',
    'has_archive' => true,
    'menu_icon' => 'dashicons-playlist-audio',
    'menu_position' => 20,
    'supports' => ['title', 'thumbnail', 'excerpt'],
    'taxonomies' => [
      'abook_collection',
      'abook_author',
      'abook_tag',
      'abook_publisher',
      'abook_reader',
      'abook_genre',
    ]
  ];
  register_post_type('abook', $args);
}


add_filter(
  'manage_edit-abook_columns',
  function ($columns) {
    $columns['authors'] = 'Автор';
    $columns['genres'] = 'Жанр';
    return $columns;
  }
);

add_filter('manage_posts_custom_column', function ($name) {
  $post_id = get_the_ID();
  switch ($name) {
    case 'authors':
      $authors = wp_get_post_terms($post_id, 'abook_author');
      $authors_rendered = render_taxonomy($authors);
      echo $authors_rendered;
      break;
    case 'genres':
      $genres = wp_get_post_terms($post_id, 'abook_genre');
      $genres_rendered = render_taxonomy($genres);
      echo $genres_rendered;
      break;
  }
}, 25, 2);

function render_taxonomy($terms)
{
  $prepare_term = function ($term) {
    return $term->name;
  };

  $terms_rendered = join(', ', array_map($prepare_term, $terms));

  return $terms_rendered;
}
