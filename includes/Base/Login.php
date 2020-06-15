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
            $encrypted_token = $this->encrypt($response['res']);

            $this->token = $this->decrypt($encrypted_token);

            return $this->token;
        }
    }

    public function encrypt($data) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

        $encrypted = openssl_encrypt($data, 'aes-256-cbc', 'pX2a1Sd3k0LKs2BN', 0, $iv);

        return base64_encode($encrypted . '::' . $iv);
    }

    public function decrypt($data) {
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);

        return openssl_decrypt($encrypted_data, 'aes-256-cbc', 'pX2a1Sd3k0LKs2BN', 0, $iv);
    }
}
