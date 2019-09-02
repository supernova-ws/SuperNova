<?php

include('common.' . substr(strrchr(__FILE__, '.'), 1));

$template = SnTemplate::gettemplate('viewreport', true);
$template->assign_var('PAGE_HINT', $lang['cr_view_hint']);

SnTemplate::display($template, $lang['cr_view_title']);
