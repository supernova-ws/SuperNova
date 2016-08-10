<?php
/**
 * buddy.php
 *   Friend system
 */

use Buddy\BuddyView;

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('buddy');

/**
 * @var array $user
 */
global $user;
$view = new BuddyView();
$cBuddy = new \Buddy\BuddyRoutingParams(classSupernova::$gc, $user);
display($view->makeTemplate($cBuddy));
