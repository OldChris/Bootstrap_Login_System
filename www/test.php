<?php
// https://www.nevprobusinesssolutions.com/blog/how-to-make-url-encryption-and-decryption-using-php/
function base64_url_encode($input, $secret)

{
	return strtr(base64_encode($secret . $input), '+/=', '-_,');
}

// base64_encode -Encodes data  with MIME base64
function base64_url_decode($input, $secret)
{
	return substr(base64_decode(strtr($input, '-_,', '+/=')),strlen($secret),(strlen($input)-strlen($secret)));
}

// base64_decode -Decodes data encoded with MIME base64.
$input="index.php?id=3";
$secret=bin2hex(random_bytes(20));
echo "<br>secret = " . $secret ;
$encrypted=base64_url_encode($input, $secret);
echo "<br> encrypted= " . $encrypted; 
$decrypted=base64_url_decode($encrypted, $secret);
echo "<br> decrypted= " . $decrypted; 

?>