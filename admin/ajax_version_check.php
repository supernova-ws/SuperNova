<?php

include('../includes/init.' . substr(strrchr(__FILE__, '.'), 1));

$url = 'http://supernova.ws/version_check.php?db=' . DB_VERSION . '&release=' . SN_RELEASE . '&version=' . SN_VERSION;

print(sn_get_url_contents($url));

?>
