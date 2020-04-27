<?php
/**
* @package Sputnik Search
*/
namespace Inc\Base;

use \Inc\Base\BaseController;

class Login extends BaseController {
    public function register() {
        $data = array(
            'userName' => $this->ESUserName,
            'password' => $this->ESPassword
        );

        $response = $this->method("POST", 'auth', $data);

        if($response['info']['http_code'] == 200) {
            $this->token = $response['res'];

            return $this->token;
        }
    }
}
