<?php

include('common.' . substr(strrchr(__FILE__, '.'), 1));

$template = gettemplate('viewreport', true);
$template->assign_var('PAGE_HINT', classLocale::$lang['cr_view_hint']);

display($template, classLocale::$lang['cr_view_title']);
