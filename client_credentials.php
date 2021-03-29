<?php

define('CLIENT_ID', '');
define('CLIENT_SECRET', '');
define('TOKEN_URL', 'https://allegro.pl/auth/oauth/token');

function getAccessToken() 
{
	$authorization = base64_encode(CLIENT_ID.':'.CLIENT_SECRET);
	$header = array("Authorization: Basic {$authorization}","Content-Type: application/x-www-form-urlencoded");
	$content = "grant_type=client_credentials";
	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_URL => TOKEN_URL,
		CURLOPT_HTTPHEADER => $header,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => $content
	));
	$tokenResult = curl_exec($ch);
    $resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

    if ($tokenResult === false || $resultCode !== 200) {
        exit ("Something went wrong");
    }

	return json_decode($tokenResult)->access_token;
}

function main()
{
    echo "access_token = ", getAccessToken();
}
main();