<?php

$allow_anonymous = true;
include('common.' . substr(strrchr(__FILE__, '.'), 1));
$template = gettemplate('dark_matter', true);
$template->assign_var('URL_DARK_MATTER', $config->url_dark_matter);
display(parsetemplate($template, $parse), $lang['sys_dark_matter']);

?>
