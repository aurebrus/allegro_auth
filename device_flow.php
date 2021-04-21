<?php
define('CLIENT_ID', ''); // pamietaj aby zarejestrowac klienta typu DEVICE
define('CLIENT_SECRET', '');
define('CODE_URL', 'https://allegro.pl/auth/oauth/device');
define('TOKEN_URL', 'https://allegro.pl/auth/oauth/token');

function getCurl($url, $headers, $content) {
	$ch = curl_init();
	curl_setopt_array($ch, array(
		CURLOPT_URL => $url,
		CURLOPT_HTTPHEADER => $headers,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => $content
	));
	return $ch;
}


function getCode(){
    $authorization = base64_encode(CLIENT_ID.':'.CLIENT_SECRET);
	$headers = array("Authorization: Basic {$authorization}","Content-Type: application/x-www-form-urlencoded");
	$content = "client_id=" .CLIENT_ID;
    $ch = getCurl(CODE_URL, $headers, $content);
    $result = curl_exec($ch);
    $resultCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($result === false || $resultCode !== 200) {
        exit ("Something went wrong:  $resultCode $result");
    }
    return json_decode($result);
}

function getAccessToken($device_code) {
	$authorization = base64_encode(CLIENT_ID.':'.CLIENT_SECRET);
	$headers = array("Authorization: Basic {$authorization}","Content-Type: application/x-www-form-urlencoded");
	$content = "grant_type=urn%3Aietf%3Aparams%3Aoauth%3Agrant-type%3Adevice_code&device_code=${device_code}";
	$ch = getCurl(TOKEN_URL, $headers, $content);
	$tokenResult = curl_exec($ch);
	curl_close($ch);
	return json_decode($tokenResult);
}

function main(){
    $result = getCode();
    echo "Użytkowniku, otwórz ten adres w przeglądarce: \n" . $result->verification_uri_complete ."\n";
    $accessToken = false;
    $interval = (int)$result->interval;
     do {
         sleep($interval);
         $device_code = $result->device_code;
         $resultAccessToken = getAccessToken($device_code);
          if (isset($resultAccessToken->error)) {
               if ($resultAccessToken->error == 'access_denied') {
                   break; 
              } elseif ($resultAccessToken->error == 'slow_down') {
                   $interval++; 
                }
            } else {
                $accessToken = $resultAccessToken->access_token;
                echo "access_token = ", $accessToken;
            }
        } while ($accessToken == false);

    }

main();

?>
