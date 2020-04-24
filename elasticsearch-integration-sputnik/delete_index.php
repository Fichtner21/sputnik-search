<?php


function deleteIndex($id) {
	global $token;

	if($token) {
		$headers = array("Authorization: $token");

		$existsResponse = method("GET", "indices/$id", null, $headers);

		if($existsResponse['info']['http_code'] == 200) {
			$response = method("DELETE", "indices/$id", null, $headers);
		}
	}
}