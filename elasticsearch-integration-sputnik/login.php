<?php

add_action('admin_menu', 'login'); 

function login() {
	global $token, $ESUserName, $ESPassword;

	$data = array(
		'userName' => $ESUserName,
		'password' => $ESPassword
	);


	$response = method("POST", 'auth', $data);
	// if($response['info']['http_code'] == 200) {
	// 	// $data = '4250be160e5e02d352925bd43493649696fa5c9ea0494a5ac26deadd7b2541b8edcdd3eb621e608cbc35df602d307d7f6886f5a126433c3ddad0c0a28691e4437013c033fd48b85437b064b4f193788ea9a866d4df166e644bfab376dca59f00fd2c8249eaf12c41b6712d8a1d885c393c33fcdad42a8937e2336d4012db45d7b12355adac51b3fd873d5a95f6a6ad83';
	// 	$method = 'AES-256-CBC';
	// 	// $password = '857be747281d37d4b0a6e0082c7516f51697f81d952a41c996d41a623de63bed6afc102cbf9ad816359d3894e479ee903c4f13331360aeb2f02b66730d808c63';
	// 	$key = 'pX2a1Sd3k0LKs2BN';
	// 	$crypto = new Crypto();
	// 	echo "<div style='margin-left:170px;'>";
	// 	echo "Method: " . $method . "\n";
	// 	$encrypted = $crypto->encrypt($response['res'], $key, $method);
	// 	echo "Encrypted: ". $encrypted . "\n";
	// 	$token = $crypto->decrypt($encrypted, $key, $method);
	// 	echo "Decrypted: ".  $token . "\n";
	// 	echo "</div>";
	// 	// $len = mb_strlen($token);
	// 	// $pad = ord( $token[$len - 1] );
		
	// 	return $token;
	// }

	// $data = 'plain text or binary data';

	// // ECB encrypts each block of data independently and 
	// // the same plaintext block will result in the same ciphertext block.
	// //$method = 'AES-256-ECB';

	// // CBC has an IV and thus needs randomness every time a message is encrypted
	// $method = 'AES-128-CBC';

	// // simple password hash
	// $password = 'secret-password-as-string';
	// $key = 'pX2a1Sd3k0LKs2BN';

	// // Most secure
	// // You must store this secret random key in a safe place of your system.
	// //$key = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
	// $cr = new Crypto();
	// echo "<div style='margin-left: 170px'>";
	// echo "Method: " . $method . "\n";
	// $encrypted = $cr->decrypt($data, $key, $method);
	// echo "Decrypted: ". $encrypted . "\n";
	// // $decrypted = $cr->decrypt($encrypted, $key, $method);
	// // echo "Decrypted: ".  $decrypted . "\n"; // plain text
	// echo "</div>";
	// // if($response['info']['http_code'] == 200) {
	// // 	$crypto = new Crypto();
	// // 	//$token = $crypto->decrypt($response['res']);
	// // 	$token = $crypto->decrypt($response['res']);		
	// // 	echo "<div style='margin-left: 170px'>";
	// // 	echo '<pre>';
	// // 	var_dump($token);
	// // 	echo '</pre>';
	// // 	echo "</div>";
	// // 	return $token;
		
	// // }

	if($response['info']['http_code'] == 200) {
		$crypto = new Crypto();

		$token = $response['res'];

		return $token;
	}

}

