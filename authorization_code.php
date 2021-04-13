<?php

define('CLIENT_ID', '');
define('CLIENT_SECRET', '');
define('REDIRECT_URI', '');
define('AUTH_URL', 'https://allegro.pl/auth/oauth/authorize');
define('TOKEN_URL', 'https://allegro.pl/auth/oauth/token');


function getAuthorizationCode() {
	$authorization_redirect_url = AUTH_URL . "?response_type=code&client_id=" . CLIENT_ID . "&redirect_uri=" . REDIRECT_URI;
	?>
	<html>
	<body>
	<a href="<?php echo $authorization_redirect_url; ?>">Login to Allegro</a>
	</body>
	</html>
	<?php
}

function getCurl($headers, $content) {
	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_URL => TOKEN_URL,
		CURLOPT_HTTPHEADER => $headers,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => $content
	));
	return $ch;
}

function getAccessToken($authorization_code) {
	$authorization = base64_encode(CLIENT_ID.':'.CLIENT_SECRET);
	$headers = array("Authorization: Basic {$authorization}","Content-Type: application/x-www-form-urlencoded");
	$content = "grant_type=authorization_code&code=$authorization_code&redirect_uri=" . REDIRECT_URI;
	$ch = getCurl($headers, $content);
	$tokenResult = curl_exec($ch);
	$resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

    if ($tokenResult === false || $resultCode !== 200) {
        exit ("Something went wrong");
    }

	return json_decode($tokenResult)->access_token;
}

function main(){
    if ($_GET["code"]) {
        $access_token = getAccessToken($_GET["code"]);
		echo "access_token = ", $access_token;
    } else {
		
        getAuthorizationCode();
    }
}

main();

?>
