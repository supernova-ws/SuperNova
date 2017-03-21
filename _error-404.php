/* <?php
$redirectFrom = !empty($_SERVER["REDIRECT_URL"]) ? $_SERVER["REDIRECT_URL"] : '';
// $serverProtocol = !empty($_SERVER["SERVER_PROTOCOL"]) ? $_SERVER["SERVER_PROTOCOL"] : 'HTTP/1.0';

// header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
// @header("{$serverProtocol} 200 OK");
echo "File {$redirectFrom} not found on server. Contact administration if you think that this is error";
die();
?> */
