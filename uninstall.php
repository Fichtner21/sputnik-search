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