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
    public function register() {
        add_action('save_post', 'synchronize_with_ES');
        add_action('add_attachment', 'add_attachment_func', 11);
        add_action('edit_attachment', 'edit_attachment_func', 11);
        add_action('delete_attachment', 'delete_attachment_func', 11);
        add_action('delete_blog', 'delete_blog_action', 10, 6);
        add_action('wpmu_new_blog', 'wporg_wpmu_new_blog_example', 10, 6);
    }

    public function synchronize_with_ES($post_id) {
        $login = new Login;
        $login->register();

        $post = get_post($post_id);

        $response = array();

        if($login->token && $post->post_status != "auto-draft") {
            $blog_id = get_current_blog_id();
            $headers = array("Authorization: $login->token");

            $response = array();

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

            $existsResponse = $this->method("GET", "documents/$blog_id/doc-id/$post_id/_exists", null, $headers);

            $response = $existsResponse;
            
            $is_post = $post->post_type == 'post';
            $is_page = $post->post_type == 'page';
            $is_komunikaty = $post->post_type == 'komunikaty';
            $is_galerie = $post->post_type == 'galerie';
            $is_sesja_rady = $post->post_type == 'sesja_rady';
            $is_sport_object = $post->post_type == 'sport_object';
            $is_club = $post->post_type == 'club';
            $is_sciezki_rowerowe = $post->post_type == 'sciezki_rowerowe';
            $is_spacer_po_miescie = $post->post_type == 'spacer_po_miescie';
            $is_culture = $post->post_type == 'culture';
            $is_przyroda = $post->post_type == 'przyroda';
            $is_zabytki_i_koscioly = $post->post_type == 'zabytki_i_koscioly';
            $is_komisje = $post->post_type == 'komisje';
            $is_radni = $post->post_type == 'radni';
            $is_adresy = $post->post_type == 'adresy';
            $is_band = $post->post_type == 'band';
            $is_uep = $post->post_type == 'uep';
            $is_pracownicy = $post->post_type == 'pracownicy';
            $is_event = $post->post_type == 'event';
            $is_wydarzenia = $post->post_type == 'wydarzenia';

            $is_correct_type = $is_post || $is_page || $is_komunikaty || $is_galerie || $is_sesja_rady || $is_sport_object || $is_club || $is_sciezki_rowerowe || $is_spacer_po_miescie || $is_culture || $is_przyroda || $is_zabytki_i_koscioly || $is_komisje || $is_radni || $is_adresy || $is_band || $is_uep || $is_pracownicy || $is_event || $is_service || $is_wydarzenia;

            if($post->post_status == "publish" && $is_correct_type) {
                if($existsResponse['info']['http_code'] == 200) {
                    $response = $this->method("POST", "documents/$blog_id/doc-id/$post_id", $data, $headers);
                } else if($existsResponse['info']['http_code'] == 404) {
                    $response = $this->method("PUT", "documents/$blog_id/doc-id/$post_id", $data, $headers);
                }
            } else {
                if($existsResponse['info']['http_code'] == 200) {
                    $response = $this->method("DELETE", "documents/$blog_id/doc-id/$post_id", null, $headers);
                }
            }
        }

        return $response;
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
            $blog_id = get_current_blog_id();
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
            
            $response = $this->method("PUT", "attachments/$blog_id/doc-id/$file_id", $data, $headers);

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
            $blog_id = get_current_blog_id();
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
            
            $response = $this->method("POST", "attachments/$blog_id/doc-id/$file_id", $data, $headers);
        }

        return $response;
    }

    public function delete_attachment_func($file_id) {
        $login = new Login;
        $login->register();

        $blog_id = get_current_blog_id();

        if($login->token) {
            $headers = array("Authorization: $login->token");
            $response = $this->method("DELETE", "attachments/$blog_id/doc-id/$file_id", null, $headers);
        }
    }

    public function wporg_wpmu_new_blog_example($blog_id, $user_id, $domain, $path, $site_id, $meta) {
        $create_index = new CreateIndex;
        $create_index->createindex($blog_id);
    }

    public function delete_blog_action($blog_id, $drop) {
        $delete_index = new DeleteIndex;
        $delete_index->deleteindex($blog_id);
    }
}