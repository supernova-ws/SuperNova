<?php

include('common.' . substr(strrchr(__FILE__, '.'), 1));

$template = gettemplate('viewreport', true);
$template->assign_var('PAGE_HINT', $lang['cr_view_hint']);

display($template, $lang['cr_view_title']);
