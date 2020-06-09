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
        add_action( 'wp_enqueue_scripts', array( $this, 'load_jquery' ) );
    }

    function enqueue_admin_assets() {
        wp_enqueue_style( 'codermirror_css', $this->plugin_url . 'assets/admin/codemirror.css' );
        wp_enqueue_style( 'sputnik_search_admin_styles', $this->plugin_url . 'assets/admin/admin-style.css' );

        wp_enqueue_script( 'sputnik_search_admin_scripts', $this->plugin_url . 'assets/admin/main.js' );
        wp_enqueue_script( 'codermirror_js', $this->plugin_url . 'assets/admin/codemirror.js' );
    }

    function enqueue_public_assets() {
        if(get_option('styles_option') == 'plugin-styles' || get_option('styles_option') == false) {
            wp_enqueue_style( 'sputnik_search_form_styles', $this->plugin_url . 'assets/public/searchform-styles.css' );
            wp_enqueue_style( 'sputnik_search_styles_option', $this->plugin_url . 'assets/public/results-styles.css' );
        }
        if(get_option('custom_css')) {
            wp_enqueue_style( 'sputnik_search_custom_css', $this->plugin_url . 'assets/public/custom-css.css' );
        }
        if(get_option('display_version') == 'react' || get_option('display_version') == false) {
            wp_enqueue_script( 'sputnik_search_react_scripts', $this->plugin_url . 'react/sputnik-wordpress-search.build.js' );
        }
        wp_enqueue_script( 'sputnik_search_public_scripts', $this->plugin_url . 'assets/public/main.js' );
    }

    function load_jquery() {
        if ( ! wp_script_is( 'jquery', 'enqueued' ) ) {
            //Enqueue
            wp_enqueue_script( 'jquery' );
        }
    }
}