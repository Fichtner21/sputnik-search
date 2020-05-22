<?php
/**
 * Trigger this file on uninstall plugin
 *
 * @package Sputnik Search
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

global $wpdb;

$table_name = $wpdb->prefix . 'sbs_synonyms';

$wpdb->query("DROP TABLE IF EXISTS $table_name");

update_option( 'es_username', '');
update_option( 'es_password', '');
update_option( 'display_version', '');
update_option( 'styles_option', '');
update_option( 'custom_css', '');