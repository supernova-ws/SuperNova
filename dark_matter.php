<?php

$allow_anonymous = true;
$skip_ban_check = true;
include('common.' . substr(strrchr(__FILE__, '.'), 1));

display(parsetemplate(gettemplate('dark_matter', true), $parse), $lang['sys_dark_matter']);

?>
