<?php
/**
* @package Sputnik Search
*/
namespace Inc\Pages;

use \Inc\Base\BaseController;
use \Inc\Base\Login;
use \Inc\Base\CreateIndex;
use \Inc\Base\PostsHooks;

class Admin extends BaseController {
    public function register() {
        add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
        add_action('wp_ajax_index_post_in_es', array( $this, 'index_post_in_es' ) );
        add_action('wp_ajax_index_attachment_in_es', array( $this, 'index_attachment_in_es' ) );
    }

    public function add_admin_pages() {
        add_menu_page( 'Sputnik Search', 'Sputnik Search', 'manage_options', 'sputnik-search', array( $this, 'admin_index' ), $this->plugin_url . 'assets/admin/sputnik-icon.svg', 1);
    }

    public function admin_index() {
        require_once $this->plugin_path . 'templates/admin.php';

        $blog_id = get_current_blog_id();
        $search_index = new CreateIndex;
        $search_index->createindex($blog_id);
    }
    
    public function index_post_in_es() {
        $posts_arr = get_posts(array('posts_per_page' => 1, 'post_status' => 'publish', 'post_type'=> array('post'), 'offset' => $_POST['id']));
    
        echo print_r($posts_arr, true);
    
        if(count($posts_arr) > 0) {
            $login = new Login;
            $login->register();

            $posts_hooks = new PostsHooks;
    
            $res = $posts_hooks->synchronize_with_ES($posts_arr[0]->ID);

            $file_path = $this->plugin_path . DIRECTORY_SEPARATOR . 'synchronize-dump.log';

            $time = date('Y-m-d H:i:s');

            $message = print_r($res, true);
            
            $log_line = "$time\t{$message}\n";

            if(!file_put_contents($file_path, $log_line, FILE_APPEND)){
                throw new Exception("Plik dziennika '{$file_path}' nie może zostać otwary ani utworzony. Sprawdź uprawnienia.");
            }
    
            status_header($res['info']['http_code']);
        } else {
            status_header(400);
        }
    
        wp_die();
    }

    
    public function index_attachment_in_es() {
        $args = array(
            'post_type' => 'attachment',
            'numberposts' => 1,
            'post_mime_type' => array('application/pdf', "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "application/vnd.openxmlformats-officedocument.presentationml.presentation", "application/vnd.oasis.opendocument.text"),
            'offset' => $_POST['id']
        );
    
        $attachments = get_posts($args);
    
        if(count($attachments) > 0) {
            $login = new Login;
            $login->register();
    
            $res = add_attachment_func($attachments[0]->ID);
    
            status_header($res['info']['http_code']);
    
            echo print_r($res, true);
        } else {
            status_header(400);
        }
    
        wp_die();
    }
}