<?php
// This example demonstarte function wrapping (for functions, that support wrapping)
// Uncomment next line to override standard 'display' function with your own
// global $functions;$functions['display'] = 'display_mine';

function display_mine($page, $title = '', $topnav = true, $metatags = '', $AdminPage = false, $isDisplayMenu = true)
{
  print('MEH!');
  sn_display($page, $title, $topnav, $metatags, $AdminPage, $isDisplayMenu);
}

?>
