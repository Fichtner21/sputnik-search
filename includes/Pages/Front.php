<?php
/**
* @package Interakcjo
*/
namespace Inc\Pages;

use \Inc\Base\BaseController;

class Front extends BaseController {
    protected $search_archive_template = 'search';

    public function register() {
        add_action( 'init', array( $this, 'create_files' ) );
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    public function create_files() {
        // Create files with template
        $filename = ABSPATH . 'wp-content/themes/' . get_option('stylesheet') . '/' . $this->search_archive_template . '.php';
        $content = $this->plugin_path . "templates/$this->search_archive_template" . '.php';
        // Get content form template file & create in active theme
        $template = file_get_contents($content);
        file_put_contents($filename, $template);    
    }
}