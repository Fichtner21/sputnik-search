<?php
/**
* @package Sputnik Search
*/
namespace Inc\Base;

use \Inc\Base\BaseController;

class SettingsLinks extends BaseController {
    public function register() {
        add_filter( "plugin_action_links_" . $this->plugin_name, array( $this, 'settings_link' ));
    }

    public function settings_link( $links ) {
        if(is_admin()) {
            $settings_link = '<a href="'. get_home_url() .'/wp-admin/admin.php?page=sputnik-search" title="Ustawienia">Ustawienia</a>';
        }

        array_push( $links, $settings_link );

        return $links;
    }
}