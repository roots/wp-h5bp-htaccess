<?php namespace Roots\Plugins;

/*
Plugin Name:        HTML5 Boilerplate .htaccess
Plugin URI:         https://github.com/roots/wp-h5bp-htaccess
Description:        Adds <a href="https://github.com/h5bp/server-configs-apache">HTML5 Boilerplate's .htaccess</a>
Version             2.0.0
Author:             Roots
Author URI:         http://roots.io/plugins/html5-boilerplate-htaccess/
License:            MIT License
License URI:        http://opensource.org/licenses/MIT
*/

add_action('generate_rewrite_rules', __NAMESPACE__ . '\\ApacheServerConfig::init', -9999, 0);

class ApacheServerConfig {
  const MARKER = 'Roots Server Config';
  const TEXT_DOMAIN = 'roots';
  
  private static $instance;
  
  public static $rules_filters = [];

  protected static $mod_rewrite_enabled;
  protected static $home_path;
  protected static $wp_htaccess_file;
  protected static $roots_htaccess_file;
  
  protected $errors = [];

  public static function init() {
    if (!self::$instance) {
      self::$instance = new static();
    }
    return self::$instance;
  }
  
  protected function __construct() {
    require_once ABSPATH . '/wp-admin/includes/file.php';
    require_once ABSPATH . '/wp-admin/includes/misc.php';
    
    self::$mod_rewrite_enabled = got_mod_rewrite();
    self::$home_path = get_home_path();
    self::$wp_htaccess_file = self::$home_path . '.htaccess';
    self::$roots_htaccess_file = locate_template(['server_configs.conf', 'h5bp-htaccess.conf']);
    if (!self::$roots_htaccess_file) {
      self::$roots_htaccess_file = __DIR__ . '/h5bp-htaccess.conf';
    }
    
    if (!$this->verifySetup()) {
      $this->alerts();
    } else {
      self::setRulesFilters();
      $this->write();
    }
  }
  
  public static function setRulesFilters() {
    $home_url = parse_url(is_multisite() ? network_home_url('/') : home_url('/'));
    extract($home_url);
    self::$rules_filters = [
      'www\\.example\\.com' => preg_quote($host),
      'www.example.com/' => $host . $path,
      'www.example.com'  => $host,
      '/wordpress' => parse_url(site_url(), PHP_URL_PATH)
    ];
    self::$rules_filters = apply_filters('roots/h5bp-htaccess-filters', self::$rules_filters);
  }
  
  protected function verifySetup() {
    if (!get_option('permalink_structure')) {
      $this->errors[] = sprintf(__('Please enable %s.', self::TEXT_DOMAIN), '<a href="' . admin_url('options-permalink.php') . '">Permalinks</a>');
    }
    
    if (!$this->isWritable()) {
      $this->errors[] = sprintf(__('Please make sure your %s file is writable.', self::TEXT_DOMAIN), '<a href="' . admin_url('options-permalink.php') . '">.htaccess</a>');
    }
    
    if (!self::$mod_rewrite_enabled) {
      $this->errors[] = sprintf(__('Please enable %s.', self::TEXT_DOMAIN), '<a target="_blank" href="http://httpd.apache.org/docs/current/rewrite/">Apache mod_rewrite</a>');
    }
    
    if (!file_exists(self::$roots_htaccess_file)) {
      $this->errors[] = sprintf(__('Cannot find the file %s.', self::TEXT_DOMAIN), self::$roots_htaccess_file);
    }
    
    return empty($this->errors);
  }
  
  protected function isWritable() {
    return is_writable(self::$home_path) || (file_exists(self::$wp_htaccess_file) && is_writable(self::$wp_htaccess_file));
  }
  
  protected function write() {
    $server_config_rules = extract_from_markers(self::$wp_htaccess_file, self::MARKER);
    if (empty($server_config_rules)) {
      $server_config_rules = implode('', file(self::$roots_htaccess_file));
      $server_config_rules = str_replace(array_keys(self::$rules_filters), array_values(self::$rules_filters), $server_config_rules);
      $server_config_rules = apply_filters('roots/h5bp-htaccess-rules', $server_config_rules);
      insert_with_markers(self::$wp_htaccess_file, self::MARKER, explode(PHP_EOL, $server_config_rules));
    }
  }
  
  public function alerts() {
    $alert = function ($message) {
      echo '<div class="error"><p>' . $message . '</p></div>';
    };
    if (current_user_can('activate_plugins')) {
      add_action('admin_notices', function () use ($alert) {
        array_map($alert, $this->errors);
      });
    }
  }
}
