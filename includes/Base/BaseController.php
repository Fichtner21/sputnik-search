<?php
/**
* @package Sputnik Search
*/
namespace Inc\Base;

class BaseController {
    public $plugin_path;
    public $plugin_url;
    public $plugin_name;

    public $ESUserName;
    public $ESPassword;
    
    public $apiURL;
    public $token;

    public $blog_id;

    public function __construct() {
        $this->plugin_path = plugin_dir_path( dirname( __FILE__, 2 ) );
        $this->plugin_url = plugin_dir_url( dirname( __FILE__, 2 ) );
        $this->plugin_name = plugin_basename( dirname( __FILE__, 3 ) . '/sputnik-search.php' );

        $this->ESUserName = get_option('es_username');
        $this->ESPassword = get_option('es_password');

        $this->apiURL = 'http://35.158.146.123:9005/api/';
        $this->token = '';

        $this->blog_id = get_current_blog_id();
    }

    public function method($type, $url, $data, $additionalsHeaders = array()) {
        $curl = curl_init();
        $headers = array_merge(
            array('Content-Type: application/json'),
            $additionalsHeaders
        );
    
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, "$this->apiURL$url");
    
        if($data){
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }
    
        $response = curl_exec($curl);
    
        $info = curl_getinfo($curl);
    
        if (!$response) {
            file_put_contents($this->plugin_path . '/add_file_response_', print_r(array("res" => $response, "info" => $info), true));
            die("Connection Failure.n");
        }
    
        return array("res" => $response, "info" => $info);
    }
}