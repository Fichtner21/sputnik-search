<?php
/**
* @package Sputnik Search
*/
namespace Inc\Base;

class Deactivate {
    public static function deactivate() {
        // Flush rewrite rules
        add_action( 'init', 'flush_rewrite_rules' );
        // Remove log files
        if( file_exists(plugin_dir_path( dirname( __FILE__, 2 ) ) . 'sql_dump.log') ) {
            unlink(plugin_dir_path( dirname( __FILE__, 2 ) ) . 'sql_dump.log');
        }

        if( file_exists(plugin_dir_path( dirname( __FILE__, 2 ) ) . 'sql_dump.log') ) {
            unlink(plugin_dir_path( dirname( __FILE__, 2 ) ) . 'synchronize-dump.log');
        }

        if( file_exists(plugin_dir_path( dirname( __FILE__, 2 ) ) . 'sql_dump.log') ) {
            unlink(plugin_dir_path( dirname( __FILE__, 2 ) ) . 'app_dev.log');
        }
    }
}