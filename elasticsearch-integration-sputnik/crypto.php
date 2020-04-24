<?php

function hextobin($hexstr) {
	$n = strlen($hexstr);
	$sbin = "";
	$i = 0;

	while($i < $n) {       
		$a = substr($hexstr, $i, 2);		
		$c = strtr(rtrim(base64_encode(pack('H*', sprintf('%u', CRC32($a)))), '='), '+/', '-_');		
		//$c = pack("H*", $a);

		if ($i == 0){
			$sbin = $c;
		} else {
			$sbin .= $c;
		}

		$i += 2;

	}
	//var_dump($sbin);
	return $sbin;
} 

class Crypto {
	private $encryptKey = 'pX2a1Sd3k0LKs2BN';
	private $iv = '1234567890123456';
	private $blocksize = 16;
 
	// public function decrypt($data){
	// 	//var_dump($data);
	// 	//return $this->unpad(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->encryptKey, hextobin($data), MCRYPT_MODE_CBC, $this->iv), $this->blocksize);
	// 	//return $this->unpad(openssl_decrypt(hextobin($data), 'AES-256-CBC', $this->encryptKey, OPENSSL_RAW_DATA, $this->iv), $this->blocksize);
	// 	//$check = $this->unpad(openssl_decrypt($data, 'AES-128-CBC', $this->encryptKey,  OPENSSL_RAW_DATA, $this->iv), $this->blocksize);
	// 	//return $this->unpad(openssl_decrypt(hextobin($data), 'AES-128-CBC', $encryptKey, OPENSSL_RAW_DATA, $iv), $this->blocksize);
	// 	return openssl_decrypt('123', 'AES-256-CBC', 'password', OPENSSL_RAW_DATA, $iv);		
	// 	//return $check;
	// }	
	// function decrypt($data, $key, $method)
	// {
	//     $data = base64_decode($data);
	//     $ivSize = openssl_cipher_iv_length($method);
	//     $iv = substr($data, 0, $ivSize);
	//     $data = unpad(openssl_decrypt(substr($data, $ivSize), $method, $key, OPENSSL_RAW_DATA, $iv));

	//     return $data;
	// }

	// public function encrypt($data){
	// 	// $pad = $this->blocksize - (strlen($data) % $this->blocksize);
	// 	// $data = $data . str_repeat(chr($pad), $pad);
	// 	// echo "<div style='margin-left:170px;'>";
	// 	// echo "encrypt $data: " ."\n";
	// 	// echo "</div>";
	// 	//return bin2hex(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->encryptKey, $data, MCRYPT_MODE_CBC, $this->iv));
	// 	//return bin2hex(openssl_encrypt( $data,'AES-128-CBC', $this->encryptKey, OPENSSL_RAW_DATA, $this->iv));
	// 	//return bin2hex(openssl_encrypt('AES-256-CBC', $this->encryptKey, $data, OPENSSL_RAW_DATA, $this->iv));
	// 	return openssl_encrypt($data, 'AES-256-CBC', $this->encryptKey, OPENSSL_RAW_DATA, $this->iv);
		
	// }

	// public function encrypt($data, $key, $method)
	// {
	//     $ivSize = openssl_cipher_iv_length($method);
	//     $iv = openssl_random_pseudo_bytes($ivSize);

	//     $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
	    
	//     // For storage/transmission, we simply concatenate the IV and cipher text
	//     $encrypted = base64_encode($iv . $encrypted);

	//     return $encrypted;
	// }

	//public function decrypt($data)	{
		// $plaintext = $data;
		// $cipher = "AES-128-CBC";
		// $key = 'pX2a1Sd3k0LKs2BN';
		// if (in_array($cipher, openssl_get_cipher_methods()))
		// {
		//     $ivlen = openssl_cipher_iv_length($cipher);
		//     $iv = openssl_random_pseudo_bytes($ivlen);
		//     $ciphertext = openssl_encrypt($plaintext, $cipher, $key, $options=0, $iv, $tag);
		//     //store $cipher, $iv, and $tag for decryption later
		//     $original_plaintext = openssl_decrypt($ciphertext, $cipher, $key, $options=0, $iv, $tag);
		//     return $original_plaintext;
		// }
		// $data = hextobin($data);
	 //    //$data = base64_decode($data);
	 //    $method = 'AES-128-CBC';
	 //    $key = 'pX2a1Sd3k0LKs2BN';
	 //    $ivSize = openssl_cipher_iv_length($method);
	 //    //$ivSize = 16;
	 //    $iv = substr($data, 0, $ivSize);
	 //  	$data = openssl_decrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
	 //  	// return $this->unpad(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->encryptKey, hextobin($data), MCRYPT_MODE_CBC, $this->iv), $this->blocksize);

	    // return $data;
	//}
	function decrypt(string $data, string $key, string $method): string
{
	
    $data = base64_decode($data);
    $ivSize = openssl_cipher_iv_length($method);
    $iv = substr($data, 0, $ivSize);
    $data = openssl_decrypt(substr($data, $ivSize), $method, $key, OPENSSL_RAW_DATA, $iv);

    return $data;
}

	function encrypt(string $data, string $key, string $method): string
{
    $ivSize = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($ivSize);

    $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
    
    // For storage/transmission, we simply concatenate the IV and cipher text
    $encrypted = base64_encode($iv . $encrypted);

    return $encrypted;
}





	public function unpad($str, $blocksize){
		$len = mb_strlen($str);
		$pad = ord( $str[$len - 1] );

		if ($pad && $pad < $blocksize) {
			$pm = preg_match('/' . chr($pad) . '{' . $pad . '}$/', $str);
			if( $pm ) {
				return mb_substr($str, 0, $len - $pad);
			}
		}

		return $str;
	}
}



