<?php
/**
* @package Sputnik Search
*/
namespace Inc\Base;

use \Inc\Base\BaseController;
use \Inc\Base\Login;
use \Inc\Base\CreateIndex;
use \Inc\Base\DeleteIndex;

class PostsHooks extends BaseController {
    public $response = array();

    public function register() {
        add_action('save_post', array( $this, 'synchronize_with_ES' ) );
        add_action('add_attachment', array( $this, 'add_attachment_func' ), 11);
        add_action('edit_attachment', array( $this, 'edit_attachment_func' ), 11);
        add_action('delete_attachment', array( $this, 'delete_attachment_func' ), 11);
        add_action('delete_blog', array( $this, 'delete_blog_action' ), 10, 6);
        add_action('wpmu_new_blog', array( $this, 'wporg_wpmu_new_blog_example' ), 10, 6);
    }

    public function synchronize_with_ES($post_id) {
        $login = new Login;
        $login->register();

        $post = get_post($post_id);

        if($login->token && $post->post_status != "auto-draft") {
            $headers = array("Authorization: $login->token");

            $categories = array();
            $post_categories = get_the_category($post_id);

            foreach ($post_categories as $cat) {
                $categories[] = $cat->term_id;
            }

            $is_service = $post->post_type == 'service';

            $data = array(
                "title" => $post->post_title,
                "content" => strip_tags($post->post_content),
                "date" => get_the_date("Y-m-d", $post_id),
                "categories" => $categories,
                "thumbnail" => get_the_post_thumbnail_url($post_id),
                "url" => $is_service ? get_post_meta($post_id, 'service_url', true) : get_the_permalink($post_id)
            );

            $existsResponse = $this->method("GET", "documents/$this->blog_id/doc-id/$post_id/_exists", null, $headers);

            $this->response = $existsResponse;

            $get_post_types = get_post_types(array( 'public' => true ));
            $post_types = array();

            foreach($get_post_types as $post_type) {
                array_push($post_types, $post_type);
            }

            foreach($post_types as $post_type) {
                if($post->post_status == "publish" && $post->post_type == $post_type) {
                    if($existsResponse['info']['http_code'] == 200) {
                        $this->response = $this->method("POST", "documents/$this->blog_id/doc-id/$post_id", $data, $headers);
                    } elseif($existsResponse['info']['http_code'] == 404) {
                        $this->response = $this->method("PUT", "documents/$this->blog_id/doc-id/$post_id", $data, $headers);
                    }
                } else {
                    if($existsResponse['info']['http_code'] == 200) {
                        $this->response = $this->method("DELETE", "documents/$this->blog_id/doc-id/$post_id", null, $headers);
                    }
                }
            }
        }

        return $this->response;
    }

    public function add_attachment_func($file_id) {
        $login = new Login;
        $login->register();

        $file = get_post($file_id);

        $mime = $file->post_mime_type;

        $docx = $mime == "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
        $xlsx = $mime == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
        $pptx = $mime == "application/vnd.openxmlformats-officedocument.presentationml.presentation";
        $odt = $mime == "application/vnd.oasis.opendocument.text";
        $pdf = $mime == "application/pdf";

        $response = array();

        if($login->token && ($docx || $odt || $pdf || $xlsx || $pptx)) {
            $headers = array("Authorization: $login->token");

            $response = array();

            $categories = array();
            $post_categories = get_the_category($file_id);

            foreach ($post_categories as $cat) {
                $categories[] = $cat->term_id;
            }

            $file_url = get_attached_file($file_id);

            $file_content = file_get_contents($file_url);

            $data = array(
                "title" => $file->post_title,
                "data" => base64_encode($file_content),
                "date" => get_the_date("Y-m-d", $file_id),
                "categories" => $categories,
                "thumbnail" => "",
                "url" => wp_get_attachment_url($file->ID)
            );
            
            $response = $this->method("PUT", "attachments/$this->blog_id/doc-id/$file_id", $data, $headers);

            if($response['info']['http_code'] == 409 || $response['info']['http_code'] == '409'){
                $response = edit_attachment_func($file_id);
            }
        }

        return $response;
    }

    public function edit_attachment_func($file_id) {
        $login = new Login;
        $login->register();

        $file = get_post($file_id);

        $mime = $file->post_mime_type;

        $docx = $mime == "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
        $xlsx = $mime == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
        $pptx = $mime == "application/vnd.openxmlformats-officedocument.presentationml.presentation";
        $odt = $mime == "application/vnd.oasis.opendocument.text";
        $pdf = $mime == "application/pdf";

        $response = array();

        if($login->token && ($docx || $odt || $pdf || $xlsx || $pptx)) {
            $headers = array("Authorization: $login->token");

            $response = array();

            $categories = array();
            $post_categories = get_the_category($file_id);

            foreach ($post_categories as $cat) {
                $categories[] = $cat->term_id;
            }

            $file_url = get_attached_file($file_id);
            $file_content = file_get_contents($file_url);

            $data = array(
                "title" => $file->post_title,
                "data" => base64_encode($file_content),
                "date" => get_the_date("Y-m-d", $file_id),
                "categories" => $categories,
                "thumbnail" => "",
                "url" => wp_get_attachment_url($file->ID)
            );
            
            $response = $this->method("POST", "attachments/$this->blog_id/doc-id/$file_id", $data, $headers);
        }

        return $response;
    }

    public function delete_attachment_func($file_id) {
        $login = new Login;
        $login->register();

        if($login->token) {
            $headers = array("Authorization: $login->token");
            $response = $this->method("DELETE", "attachments/$this->blog_id/doc-id/$file_id", null, $headers);
        }
    }

    public function wporg_wpmu_new_blog_example($blog_id, $user_id, $domain, $path, $site_id, $meta) {
        $create_index = new CreateIndex;
        $create_index->createindex($this->blog_id);
    }

    public function delete_blog_action($blog_id, $drop) {
        $delete_index = new DeleteIndex;
        $delete_index->deleteindex($this->blog_id);
    }
}