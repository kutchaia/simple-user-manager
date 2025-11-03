<?php
/*
Plugin Name: Simple User Manager
Plugin URI: https://example.com/
Description: Minimal admin UI (class-based) to list, add, change roles, and delete WordPress users with AJAX and pagination. Demo plugin — review before production.
Version: 0.2
Author: kutchaia (with assistant)
License: GPLv2 or later
Text Domain: simple-user-manager
*/

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'SUM_Plugin' ) ) {

    final class SUM_Plugin {
        public static $version = '0.2';
        public static $dir;
        public static $url;

        public static function init() {
            self::$dir = plugin_dir_path( __FILE__ );
            self::$url = plugin_dir_url( __FILE__ );

            require_once self::$dir . 'includes/class-sum-admin.php';
            SUM_Admin::get_instance();
        }
    }

    SUM_Plugin::init();
} 
