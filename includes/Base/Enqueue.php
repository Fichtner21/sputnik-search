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
    }

    function enqueue_public_assets() {
        wp_enqueue_style( 'sputnik_search_admin_styles', $this->plugin_url . 'assets/public/public-style.css' );
        wp_enqueue_script( 'sputnik_search_admin_scripts', $this->plugin_url . 'assets/public/main.js' );
    }
}