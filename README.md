# HTML5 Boilerplate's `.htaccess` for WordPress
[![Build Status](https://travis-ci.org/roots/wp-h5bp-htaccess.svg)](https://travis-ci.org/roots/wp-h5bp-htaccess)

Add [HTML5 Boilerplate's `.htaccess`](https://github.com/h5bp/server-configs-apache) to your WordPress installation.

## Installation

You can install this plugin via the command-line or the WordPress admin panel.

### via Command-line

If you're [using Composer to manage WordPress](https://roots.io/using-composer-with-wordpress/), add wp-h5bp-htaccess to your project's dependencies.

```sh
composer require roots/wp-h5bp-htaccess 2.0.1
```

Then activate the plugin via [wp-cli](http://wp-cli.org/commands/plugin/activate/).

```sh
wp plugin activate wp-h5bp-htaccess
```

### via WordPress Admin Panel

1. Download the [latest zip](https://github.com/roots/wp-h5bp-htaccess/archive/master.zip) of this repo.
2. In your WordPress admin panel, navigate to Plugins->Add New
3. Click Upload Plugin
4. Upload the zip file that you downloaded.

## Configuration

The plugin will work right out of the box, but we have made some options available to developers who wish to fine-tune the resulting `.htaccess` file.

### !!! WARNING !!!

Using these configuration options can result in unexpected, undesired, and even damaging behaviors. You must understand that changing the output of this plugin will change what WordPress puts into your `.htaccess` file. Misconfigured `.htaccess` files will likely result server errors. Be sure that you have a way of removing the `.htaccess` file if necessary.

### Use a custom server config (`server_configs.conf`)

You can use your own server config instead of the included Apache Server Config by H5BP. Do this by placing a file named `server_configs.conf` in the root of your theme directory.

```
/
├── wp-admin/
├── wp-content/
│   └── themes/
│       └── your-theme-here/
│           └── server_configs.conf
└── wp-includes/
```

### Hooks

There are a few WordPress filters into which a developer can hook to modify the resulting `.htaccess` file. The rules below are listed in the order in which they are applied in the code.

#### `roots/h5bp-htaccess-filters`

This hook passes an associative array of modification filters to be applied to the rules where the keys of the array represent a string that is to be searched and the values of the array represent a replacement.

Use this for simple string replacements, such as commenting out lines.

```php
add_filter('roots/h5bp-htaccess-filters', function($rules_filters) {
  // comments out all `RewriteRule`s
  $rules_filters['RewriteRule'] = '# RewriteRule';
  return $rules_filters;
});
```

#### `roots/h5bp-htaccess-rules`

This hook passes a string containing all of the rules that are going to be added to your `.htaccess` file by this plugin. 

```php
add_filter('roots/h5bp-htaccess-rules', function($server_config_rules) {
  // Removes all comments and whitespace
  return preg_replace(['/#.*/', '/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/'], ['',PHP_EOL], $server_config_rules);
});
```

## Changelog

### 2.0.1: April 29th, 2015
* Fix reference error ([#12](https://github.com/roots/wp-h5bp-htaccess/issues/12))
* Update link to h5bp server config repo ([#13](https://github.com/roots/wp-h5bp-htaccess/issues/13))

### 2.0.0: April 11th, 2015
* Update to Apache Server Configs v2.14.0
* Rewrote plugin as a class
* Rewrote logic so that server and WordPress configurations are only checked when plugin is actively in use
* Added filters to provide users with more control over the rules that are being applied
* Added a dedicated WordPress section to the bottom of h5bp-htaccess

### 1.1.0: June 7th, 2014
* Update to Apache Server Configs v2.4.1

### 1.0.0: April 30th, 2013
* Removed from [Roots Theme](http://www.rootstheme.com/), moved to plugin

## License

* [Apache Server Configs](https://github.com/h5bp/server-configs-apache): MIT License
* Everything else: MIT License
