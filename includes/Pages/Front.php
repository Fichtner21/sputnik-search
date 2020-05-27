<?php
/**
* @package Interakcjo
*/
namespace Inc\Pages;

use \Inc\Base\BaseController;

class Front extends BaseController {
    protected $search_filename = 'search';
    protected $search_pagination_template = 'search-pagination';
    protected $search_react_template = 'search-react';

    public function register() {
        add_action( 'init', array( $this, 'create_files' ) );
        // Flush rewrite rules
        add_action( 'init', 'flush_rewrite_rules' );
    }

    public function create_files() {
        // Condition if we want PHP or React
        if(get_option('display_version') == 'php') {
            // Create files with template
            $archive_filename = ABSPATH . 'wp-content/themes/' . get_option('stylesheet') . '/' . $this->search_filename . '.php';
            $archive_content = $this->plugin_path . "templates/$this->search_filename" . '.php';
            // Get content form template file & create in active theme
            $archive_template = file_get_contents($archive_content);

            if(!$this->compareFiles($archive_filename, $archive_content)) {
                file_put_contents($archive_filename, $archive_template);
            }

            // Create files with template
            $pagination_filename = ABSPATH . 'wp-content/themes/' . get_option('stylesheet') . '/' . $this->search_pagination_template . '.php';
            $pagination_content = $this->plugin_path . "templates/$this->search_pagination_template" . '.php';
            // Get content form template file & create in active theme
            $pagination_template = file_get_contents($pagination_content);

            if(!$this->compareFiles($pagination_filename, $pagination_content)) {
                file_put_contents($pagination_filename, $pagination_template);
            }
        } elseif(get_option('display_version') == 'react') {
            // Create files with template
            $react_filename = ABSPATH . 'wp-content/themes/' . get_option('stylesheet') . '/' . $this->search_filename . '.php';
            $react_content = $this->plugin_path . "templates/$this->search_react_template" . '.php';
            // Get content form template file & create in active theme
            $react_template = file_get_contents($react_content);

            if(!$this->compareFiles($react_filename, $react_content)) {
                file_put_contents($react_filename, $react_template);
            }
        }
    }
}