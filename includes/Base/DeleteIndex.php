<?php
/**
* @package Sputnik Search
*/
namespace Inc\Base;

use \Inc\Base\BaseController;
use \Inc\Base\Login;

class DeleteIndex extends BaseController {
    public $existsResponse = array();

    public function deleteindex($id) {
        $login = new Login;
        $login->register();

        if($login->token) {
            $headers = array("Authorization: $login->token");
    
            $this->existsResponse = $this->method("GET", "indices/$id", null, $headers);
    
            if($this->existsResponse['info']['http_code'] == 200) {
                $response = $this->method("DELETE", "indices/$id", null, $headers);
            }
        }
    }
}