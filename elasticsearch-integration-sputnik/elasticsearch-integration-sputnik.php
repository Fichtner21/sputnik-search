<?php
/*
Plugin Name: ElastiSearch
*/

//$urlAPI = 'http://185.135.91.33/api/';
$urlAPI = 'http://35.158.146.123:9005/api/';
$token = '';

include_once(__DIR__ . "/access-data.php");

include_once(__DIR__ . "/crypto.php");
include_once(__DIR__ . "/method.php");
include_once(__DIR__ . "/menu.php");
include_once(__DIR__ . "/login.php");


function es_login_action() {
	if(is_admin() && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
		login();
	}
}

add_action('init', 'es_login_action', 9);

include_once(__DIR__ . "/posts_hooks.php");
include_once(__DIR__ . "/create_index.php");
include_once(__DIR__ . "/delete_index.php");

function es_sputnik_enqueue_script() {
	wp_enqueue_script('es-sputnik-enqueue-script', plugins_url('js/sputnik-wordpress-search.js', __FILE__), false);
}

add_action('wp_enqueue_scripts', 'es_sputnik_enqueue_script');