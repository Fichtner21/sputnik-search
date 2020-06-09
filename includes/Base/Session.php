<?php
/**
* @package Sputnik Search
*/
namespace Inc\Base;

use \Inc\Base\BaseController;

class Session extends BaseController {
    public function register() {
        add_action('init', array( $this, 'initSession' ) );
    }

    public function initSession() {
        if( ! session_id() ) {
            session_start();
        }
        if(isset($_GET['dev']) && $_GET['dev'] == 'logout') {
            session_destroy();

            header('Location: ' . get_home_url() . '/wp-admin/admin.php?page=sputnik-developer');

            exit();
        }
    }
}