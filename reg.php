<?php

$id_ref = isset($_GET['id_ref']) ? intval($_GET['id_ref']) : 0;
$id_ref = $id_ref ? '?id_ref=' . $id_ref : '';

header('HTTP/1.1 301 Moved Permanently');
header("Location: login.php{$id_ref}#tab_register");
