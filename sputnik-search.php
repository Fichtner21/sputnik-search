<?php
/**
* @package Sputnik Search
*/

/*
Plugin Name: Sputnik Search
Plugin URI: https://sputnik.pl
Description: Advanced search in website using ElasticSearch
Version: 1.9.4
Author: Sputnik
Author URI: https://sputnik.pl
Text Domain: sputnik-search
*/

// For security
defined('ABSPATH') or die('Hey, you cant access this file!');

// Require Composer Autoload
if( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
    require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

if(defined('WP_DEBUG') && WP_DEBUG) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'debugger.php';
}

// activation
if(!function_exists('search_activate')) {
    function search_activate() {
        Inc\Base\Activate::activate();
        Inc\Base\Activate::create_synonyms_db_table();
    }
    register_activation_hook( __FILE__, 'search_activate' );
}

// deactivation
if(!function_exists('search_deactivate')) {
    function search_deactivate() {
        Inc\Base\Deactivate::deactivate();
    }
    register_deactivation_hook( __FILE__, 'search_deactivate' );
}

// Register all includes
if( class_exists( 'Inc\\Init' ) ) {
    Inc\Init::register_services();
}

// Update plugin
require dirname(__FILE__) . '/plugin-update-checker/plugin-update-checker.php';

$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/Fichtner21/Elasticsearch',
    __FILE__, //Full path to the main plugin file or functions.php.
    'sputnik-search'
);

//Optional: If you're using a private repository, specify the access token like this:
$myUpdateChecker->setAuthentication('381c6ed87a35f81d4cc34fb2ef6204e072b60950');

//Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');

//Enable realese assets
$myUpdateChecker->getVcsApi()->enableReleaseAssets();