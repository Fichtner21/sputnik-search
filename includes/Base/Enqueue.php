<?php
/**
* @package Sputnik Search
*/
namespace Inc\Base;

use \Inc\Base\BaseController;

class Enqueue extends BaseController {
    public function register() {
        add_action ( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action ( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
    }

    function enqueue_admin_assets() {
        wp_enqueue_style( 'sputnik_search_admin_styles', $this->plugin_url . 'assets/admin/admin-style.css' );
        wp_enqueue_script( 'sputnik_search_admin_scripts', $this->plugin_url . 'assets/admin/main.js' );

        wp_enqueue_style( 'codermirror_css', $this->plugin_url . 'assets/admin/codemirror.css' );
        wp_enqueue_script( 'codermirror_js', $this->plugin_url . 'assets/admin/codemirror.js' );
    }

    function enqueue_public_assets() {
        // Condition if we want PHP or React
        wp_enqueue_style( 'sputnik_search_public_styles', $this->plugin_url . 'assets/public/public-style.css' );
        wp_enqueue_script( 'sputnik_search_public_scripts', $this->plugin_url . 'assets/public/main.js' );
        if(get_option('display_version') == 'react') {
            wp_enqueue_script( 'sputnik_search_react_scripts', $this->plugin_url . 'react/sputnik-wordpress-search.build.js' );
        }
        if(get_option('custom_css')) {
            if(!function_exists('custom_styles')) {
                echo '<style>';
                echo get_option('custom_css');
                echo '</style>';
            }
            add_action('wp_head', 'custom_styles', 20);
        }
    }
}