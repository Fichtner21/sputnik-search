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
        wp_enqueue_style( 'codermirror_css', $this->plugin_url . 'assets/admin/codemirror.css', array(), $this->plugin_version );
        wp_enqueue_style( 'sputnik_search_admin_styles', $this->plugin_url . 'assets/admin/admin-style.css', array(), $this->plugin_version );

        wp_enqueue_script( 'sputnik_search_admin_scripts', $this->plugin_url . 'assets/admin/main.js', array(), $this->plugin_version );
        wp_enqueue_script( 'codermirror_js', $this->plugin_url . 'assets/admin/codemirror.js', array(), $this->plugin_version );
    }

    function enqueue_public_assets() {
        wp_enqueue_style( 'sputnik_search_form_styles', $this->plugin_url . 'assets/public/style.css', array(), $this->plugin_version );

        if(get_option('custom_css')) {
            wp_enqueue_style( 'sputnik_search_custom_css', $this->plugin_url . 'assets/public/custom-css.css', array(), $this->plugin_version );
        }
        if(get_option('display_version') == 'react' || get_option('display_version') == false) {
            wp_enqueue_script( 'sputnik_search_react_scripts', $this->plugin_url . 'react/sputnik-wordpress-search.build.js', array(), $this->plugin_version );
        }
        wp_enqueue_script( 'sputnik_search_public_scripts', $this->plugin_url . 'assets/public/main.js', array(), $this->plugin_version );
    }

    function load_jquery() {
        if ( ! wp_script_is( 'jquery', 'enqueued' ) ) {
            //Enqueue
            wp_enqueue_script( 'jquery' );
        }
    }
}