<?php
/**
* @package Sputnik Search
*/
namespace Inc\Base;

use \Inc\Base\BaseController;

class SearchForm extends BaseController { 
    public function register() {
        add_shortcode( 'sputnik_search_form', array($this, 'search_form_template' ) );
    }

    public function search_form_template() {
        ob_start();

        require_once $this->plugin_path . '/templates/search-form.php';

        $output = ob_get_clean();

        return $output;
    }
}