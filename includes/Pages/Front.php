<?php
/**
* @package Interakcjo
*/
namespace Inc\Pages;

use \Inc\Base\BaseController;

class Front extends BaseController {
    protected $search_archive_template = 'search';
    protected $search_pagination_template = 'search-pagination';

    public function register() {
        add_action( 'init', array( $this, 'create_files' ) );
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    public function create_files() {
        // Create files with template
        $archive_filename = ABSPATH . 'wp-content/themes/' . get_option('stylesheet') . '/' . $this->search_archive_template . '.php';
        $archive_content = $this->plugin_path . "templates/$this->search_archive_template" . '.php';
        // Get content form template file & create in active theme
        $archive_template = file_get_contents($archive_content);
        file_put_contents($archive_filename, $archive_template);

        // Create files with template
        $pagination_filename = ABSPATH . 'wp-content/themes/' . get_option('stylesheet') . '/' . $this->search_pagination_template . '.php';
        $pagination_content = $this->plugin_path . "templates/$this->search_pagination_template" . '.php';
        // Get content form template file & create in active theme
        $pagination_template = file_get_contents($pagination_content);
        file_put_contents($pagination_filename, $pagination_template); 
    }
}