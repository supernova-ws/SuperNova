<?php
/**
 * buddy.php
 *   Friend system
 */

use Buddy\BuddyView;

include('common.' . substr(strrchr(__FILE__, '.'), 1));

lng_include('buddy');


$model = new \V2Fleet\V2FleetModel(classSupernova::$gc);
$fleet = $model->loadById(8);
var_dump($fleet);

die();

/**
 * @var array $user
 */
global $user;
$paramBuddy = new \Buddy\BuddyParams($user);
$view = new BuddyView();
display($view->makeTemplate(classSupernova::$gc, $paramBuddy));
