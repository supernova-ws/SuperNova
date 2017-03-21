/* <?php
// header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
echo "File {$_SERVER["REDIRECT_URL"]} not found on server. Contact administration if you think that this is error";
var_dump($_SERVER["SERVER_PROTOCOL"]);
?> */
