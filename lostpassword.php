<?php

$id_ref = $_GET['id_ref'] ? intval($_GET['id_ref']) : 0;
$id_ref = $id_ref ? '?id_ref=' . $id_ref : '';

header('HTTP/1.1 301 Moved Permanently');
header("Location: login.php{$id_ref}#tab_password_reset");
