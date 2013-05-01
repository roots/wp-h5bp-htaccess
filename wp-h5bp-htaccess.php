<?php
/*
Plugin Name:        HTML5 Boilerplate .htaccess
Plugin URI:         https://github.com/retlehs/wp-h5bp-htaccess
Description:        Adds <a href="https://github.com/h5bp/html5-boilerplate/blob/master/.htaccess">HTML5 Boilerplate's .htaccess</a>
Version             1.0.0
Author:             Ben Word
Author URI:         http://benword.com/
License:            MIT License
License URI:        http://opensource.org/licenses/MIT
*/

/**
 * Show an admin notice if .htaccess isn't writable
 */
function roots_htaccess_writable() {
  if (!is_writable(get_home_path() . '.htaccess')) {
    if (current_user_can('administrator')) {
      add_action('admin_notices', create_function('', "echo '<div class=\"error\"><p>" . sprintf(__('Please make sure your <a href="%s">.htaccess</a> file is writable ', 'roots'), admin_url('options-permalink.php')) . "</p></div>';"));
    }
  }
}
add_action('admin_init', 'roots_htaccess_writable');

/**
 * Add HTML5 Boilerplate's .htaccess via WordPress
 */
function roots_add_h5bp_htaccess($content) {
  global $wp_rewrite;
  $home_path = function_exists('get_home_path') ? get_home_path() : ABSPATH;
  $htaccess_file = $home_path . '.htaccess';
  $mod_rewrite_enabled = function_exists('got_mod_rewrite') ? got_mod_rewrite() : false;

  if ((!file_exists($htaccess_file) && is_writable($home_path) && $wp_rewrite->using_mod_rewrite_permalinks()) || is_writable($htaccess_file)) {
    if ($mod_rewrite_enabled) {
      $h5bp_rules = extract_from_markers($htaccess_file, 'HTML5 Boilerplate');
      if ($h5bp_rules === array()) {
        $filename = dirname(__FILE__) . '/h5bp-htaccess';
        return insert_with_markers($htaccess_file, 'HTML5 Boilerplate', extract_from_markers($filename, 'HTML5 Boilerplate'));
      }
    }
  }

  return $content;
}
add_action('generate_rewrite_rules', 'roots_add_h5bp_htaccess');
