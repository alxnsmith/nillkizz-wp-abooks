<?php

/**
 * Plugin Name: Nillkizz-ABooks Plugin
 * Description: Плагин для аудиокниг
 * Version: 0.2.0
 * Author: Alexander Smith
 * Author URI: https://t.me/alxndr_smith
 */


if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}


if (!class_exists('NillkizzAbooks')) :
  class NillkizzAbooks
  {
    public $includes = [
      // Included Plugins
      'includes/acf-pro/acf.php',
      'includes/search-everything/search-everything.php',

      // Init Data
      'includes/acf-init.php',

      // Code
      'includes/rest_api.php',
      'includes/taxonomies.php',
      'includes/post_types.php',
      'includes/admin.php',
    ];
    function __construct()
    {
      // Do nothing.
    }

    function initialize()
    {
      $this->define('NILLKIZZ_ABOOKS_PATH', trailingslashit(plugin_dir_path(__FILE__)));

      $this->includes();
    }

    function includes()
    {
      foreach ($this->includes as $include) {
        require_once NILLKIZZ_ABOOKS_PATH . $include;
      }
    }

    function define($name, $value = true)
    {
      if (!defined($name)) {
        define($name, $value);
      }
    }
  }

  function nabooks()
  {
    global $nabooks;

    // Instantiate only once.
    if (!isset($nabooks)) {
      $nabooks = new NillkizzAbooks();
      $nabooks->initialize();
    }
    return $nabooks;
  }

  // Instantiate.
  nabooks();

endif;
