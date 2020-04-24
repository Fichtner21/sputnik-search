<?php
// add_action('admin_menu', 'method');

function method($type, $url, $data, $additionalsHeaders = array()) {
	global $urlAPI;

	$curl = curl_init();
	$headers = array_merge(
		array('Content-Type: application/json'),
		$additionalsHeaders
	);
	

	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_URL, "$urlAPI$url");

	

	if($data){
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
	}

	$response = curl_exec($curl);

	$info = curl_getinfo($curl);

	// echo '<pre>';
	// var_dump($response);
	// echo '</pre>';

	if (!$response) {
		file_put_contents(__DIR__ . '/add_file_response3_', print_r(array("res" => $response, "info" => $info), true));
	    die("Connection Failure.n");
	}

	return array("res" => $response, "info" => $info);
}