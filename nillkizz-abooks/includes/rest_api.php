<?php
add_action('rest_api_init', function () {
  register_rest_route('nabooks/v1', '/books', array(
    'methods'  => 'GET',
    'callback' => 'abooks',
    'permission_callback' => '__return_true',
  ));
  register_rest_route('nabooks/v1', '/books/(?P<id>\d+)', array(
    'methods'  => 'GET',
    'callback' => 'abooks_detail',
    'permission_callback' => '__return_true',
  ));
  register_rest_route('nabooks/v1', '/genres-with-books', array(
    'methods'  => 'GET',
    'callback' => 'genres_with_books',
    'permission_callback' => '__return_true',
  ));
});

function genres_with_books()
{
  $response = array_map(['Handler', 'prepare_genre'], get_terms('abook_genre'));
  return $response;
}

function abooks_detail($request)
{
  $abook = get_post($request['id']);

  if (empty($abook)) return new WP_Error('Has no abooks', 'Invalid id', array('status' => 404));
  return Handler::prepare_book_detail($abook);
}

function abooks()
{
  global $wp_query;
  $args = Query::get_query_args("abook");
  $books = query_posts($args);

  header('X-WP-TotalPages: ' . $wp_query->max_num_pages);
  header('X-WP-Total: ' . $wp_query->found_posts);

  if (0 == $wp_query->found_posts) return new WP_Error('Has no abooks', 'Invalid query params', array('status' => 404));
  return array_map(['Handler', 'prepare_book_short'], $books);
}


class Handler
{
  public static function get_chapters_info($book_id)
  {
    $chapters = array_map(['self', 'prepare_chapter'], get_field('chapters', $book_id));
    $full_length = array_reduce($chapters, function ($carry, $item) {
      $carry = !empty($carry) ? $carry : 0;
      return $carry + $item['length']['value'];
    });
    return [
      'chapters' => $chapters,
      'full_length' => [
        'value' => $full_length,
        'formatted' => gmdate("H:i:s", (int)$full_length)
      ]
    ];
  }

  public static function prepare_term($term)
  {
    return [
      'id' => $term->term_id,
      'name' => $term->name,
    ];
  }

  public static function prepare_chapter($chapter)
  {
    $file = $chapter['file'];
    $meta = wp_get_attachment_metadata($file['ID']);
    return [
      'name' => $file['title'],
      'url' => $file['url'],
      'length' => [
        'value' => $meta['length'],
        'formatted' => $meta['length_formatted']
      ]
    ];
  }

  public static function prepare_book_short($book, $extend = false)
  {
    $thumbnail_id = get_post_thumbnail_id($book->ID);
    $chapters_info = self::get_chapters_info($book->ID);
    $response = [
      'id'           => $book->ID,
      'name'         => $book->post_title,
      'image'        => [
        'thumbnail'  => wp_get_attachment_image_url($thumbnail_id, 'thumbnail'),
        'medium'     => wp_get_attachment_image_url($thumbnail_id, 'medium'),
        'large'      => wp_get_attachment_image_url($thumbnail_id, 'large'),
        'full'       => wp_get_attachment_image_url($thumbnail_id, 'full'),
      ],
      'full_length'  => $chapters_info['full_length'],
      'author'       => array_map(['self', 'prepare_term'], wp_get_post_terms($book->ID, 'abook_author')),
    ];

    if (!$extend) return $response;
    return ['response' => $response, 'chapters_info' => $chapters_info];
  }

  public static function prepare_book_detail($book)
  {
    $book_short = self::prepare_book_short($book, true);
    $chapters_info = $book_short['chapters_info'];

    return array_merge($book_short['response'], [
      'description'  => $book->post_excerpt,
      'chapters'     => $chapters_info['chapters'],
      'collections'  => array_map(['self', 'prepare_term'], wp_get_post_terms($book->ID, 'abook_collection')),
      'genre'        => array_map(['self', 'prepare_term'], wp_get_post_terms($book->ID, 'abook_genre')),
      'tags'         => array_map(['self', 'prepare_term'], wp_get_post_terms($book->ID, 'abook_tag')),
      'publisher'    => array_map(['self', 'prepare_term'], wp_get_post_terms($book->ID, 'abook_publisher')),
      'reader'       => array_map(['self', 'prepare_term'], wp_get_post_terms($book->ID, 'abook_reader')),
    ]);
  }

  public static function prepare_genre($term)
  {
    $args = [
      'post_type' => 'abook',
      'tax_query' => [
        [
          'taxonomy' => 'abook_genre',
          'terms'    => [$term->term_id]
        ]
      ]
    ];
    if (!empty($_REQUEST['posts_per_page'])) $args['posts_per_page'] = $_REQUEST['posts_per_page'];
    $books = array_map(['Handler', 'prepare_book_short'], query_posts($args));

    return [
      'id'     => $term->term_id,
      'name'   => $term->name,
      'count'  => $term->count,
      'books'  => $books
    ];
  }
}

class Query
{
  public static function get_query_args($post_type = "post")
  {
    $args = [
      'post_type' => $post_type,
      'post_status' => 'publish',
    ];
    $params = [];

    if (strlen($_SERVER['QUERY_STRING']) > 1) {
      foreach (explode('&', $_SERVER['QUERY_STRING']) as $param) {
        $kv = explode('=', $param);
        if (count($kv) == 2 && !empty($kv[1]) &&  !empty($kv[0])) $params[] = $kv;
      }
      foreach ($params as $param) {
        switch ($param[0]) {
          case 's':
            $args[$param[0]] = urldecode($param[1]);
            break;
          default:
            $values = explode(',', $param[1]);
            $args[$param[0]] = (count($values) > 1) ? $values : $values[0];
        }
      }
    }

    foreach (array_keys($_REQUEST) as $param) {
      $matches = [];
      if (preg_match('/term_(.*)/', $param, $matches)) {
        $args = self::add_tax_query_to_args($args, $matches[1], $_REQUEST[$param], $post_type . '_');
      } else if ('tax_query_relation' == $param) {
        $args['tax_query']['relation'] = $_REQUEST[$param];
      } else if ('tax_query_field' == $param) {
        $args['tax_query']['field'] = $_REQUEST[$param];
      }
    }
    return $args;
  }

  public static function add_tax_query_to_args($args, $tax, $terms_id, $tax_prefix = '')
  {
    if (!is_array($args['tax_query'])) {
      $args['tax_query'] = ['relation' => 'AND'];
    }
    $args['tax_query'][] = [
      'taxonomy' => $tax_prefix . $tax,
      'terms'    => explode(',', $terms_id),
    ];
    return $args;
  }
}
