<?php

$lang['fl_title'] 				= 'Fleets';
$lang['fl_expttl'] 				= 'Expedition';
$lang['fl_id'] 					= '№';
$lang['fl_mission'] 				= 'Mission';
$lang['fl_count'] 				= 'Number of';
$lang['fl_count_short']			= 'Quantity of';
$lang['fl_from'] 					= 'From';
$lang['fl_from_t']				= 'Return';
$lang['fl_start_t'] 				= 'Time';
$lang['fl_dest'] 					= 'Where';
$lang['fl_dest_t'] 				= 'Arrival';
$lang['fl_back_t'] 				= 'Return';
$lang['fl_back_in'] 				= 'Left';
$lang['fl_order'] 				= 'Order';
$lang['fl_get_to'] 				= '(Т)';
$lang['fl_get_to_ttl'] 			= 'Roundtrip';
$lang['fl_back_to'] 				= '(О)';
$lang['fl_back_to_ttl'] 		= 'Back';
$lang['fl_associate'] 			= 'Combat Union';
$lang['fl_noslotfree'] 			= 'No fleet slots!';
$lang['fl_notback'] 				= 'Fleet cannot return!';
$lang['fl_onlyyours'] 			= 'You can only return your fleets!';
$lang['fl_isback']	 			= 'Fleet returns';
$lang['fl_sback'] 				= 'Ago';
$lang['fl_error'] 				= 'Error';
$lang['fl_new_miss'] 			= 'New task: choose ships';
$lang['fl_fleet_typ'] 			= 'Ship type';
$lang['fl_fleet_disp'] 			= 'Number of';
$lang['fl_noplanetrow'] 		= 'System error is! ))';
$lang['fl_fleetspeed'] 			= 'Speed: ';
$lang['fl_selmax'] 				= 'Max.';
$lang['fl_sur'] 					= 'from';
$lang['fl_continue'] 			= 'Futher';
$lang['fl_noships'] 				= 'No ships orbiting the planet';
$lang['fl_unselectall'] 		= 'unselect all';
$lang['fl_selectall'] 			= 'Select all';
$lang['fl_orbiting'] 			= 'In orbit';
$lang['fl_to_fly']	 			= 'Send';
$lang['fl_no_flying_fleets']	= 'No Fleets out';

//	
$lang['fl_floten1_ttl'] 		= 'Administration of fleet';
$lang['fl_noenought']			= 'Little ships!';
$lang['fl_speed'] 			   = 'Speed';
$lang['fl_planet'] 			   = 'Planet';
$lang['fl_ruins'] 			   = 'Field debris';
$lang['fl_moon'] 			      = 'Moon';
$lang['fl_dist'] 					= 'Distance';
$lang['fl_fltime'] 				= 'Duration(one way)';
$lang['fl_time_go'] 				= 'Sent (time)';
$lang['fl_time_back'] 			= 'Return (time)';
$lang['fl_deute_need'] 			= 'Fuel consumption';
$lang['fl_speed_max'] 			= 'Maximum speed';
$lang['fl_shortcut'] 			= 'Shortcut';
$lang['fl_shortlnk'] 			= 'Edit shortcuts';
$lang['fl_shrtcup1'] 			= '(П)';
$lang['fl_shrtcup2'] 			= '(О)';
$lang['fl_shrtcup3'] 			= '(Л)';
$lang['fl_planettype1'] 	   = 'Planet';
$lang['fl_planettype2'] 	   = 'Field debris';
$lang['fl_planettype3']       = 'Moon';
$lang['fl_noshortc'] 			= 'No shortcuts';
$lang['fl_myplanets']			= 'Planet(s)';
$lang['fl_nocolonies'] 			= 'No Planets';
$lang['fl_noacss'] 				= 'No Combat Unions';
$lang['fl_grattack'] 			= 'Military Alliances';

// floten2.php
$lang['fl_ressources'] 			= 'Raw Materialsё';
$lang['fl_allressources'] 		= 'All resource';
$lang['fl_space_left'] 			= 'Space left';

// floten3.php
$lang['fl_attack_error'] = array(
  ATTACK_ALLOWED         => 'Fleet sent successfully',
  ATTACK_NO_TARGET       => 'The specified destination does not exist',
  ATTACK_OWN             => 'cannnot attack own planet',
  ATTACK_WRONG_MISSION   => 'The task cannot be performed in the specified destination',
  ATTACK_NO_ALLY_DEPOSIT => 'No alliance depot on planet',
  ATTACK_NO_DEBRIS       => 'No debris field here',
  ATTACK_VACANCY         => 'You cannot attack a player in Vacation Mode',
  ATTACK_SAME_IP         => 'Cannot trade with players with same IP!<br>Interaction with players with the same IP could be banned',
  ATTACK_BUFFING         => 'Injection - transfer of resources from weak to strong player - is forbidden by the rules',
  ATTACK_ADMIN           => 'You cannot attack the administrator',
  ATTACK_NOOB            => 'This player is too weak for you.',
  ATTACK_OWN_VACATION    => 'You are in Vacation Mode',
  ATTACK_NO_SILO         => 'Too low of missile silo',
  ATTACK_NO_MISSILE      => 'You cannot attack without missiles',
  ATTACK_NO_FLEET        => 'No fleet selected',
  ATTACK_NO_SLOTS        => 'No fleet slots',
  ATTACK_NO_SHIPS        => 'No ships',
  ATTACK_NO_RECYCLERS    => 'No recyclers',
  ATTACK_NO_SPIES        => 'No spy probes',
  ATTACK_NO_COLONIZER    => 'No colonyship',
  ATTACK_MISSILE_TOO_FAR => 'Your missiles cannot reach this far',
  ATTACK_WRONG_STRUCTURE => 'You can only attack defence structures',
  ATTACK_NO_FUEL			 => 'Not enough fuel to launch fleet',
  ATTACK_NO_RESOURCES    => 'There is not enough resources for transport',
  ATTACK_NO_ACS          => 'No ACS',
  ATTACK_ACS_MISSTARGET  => 'Does not match the destination and the purpose of ACS',
  ATTACK_WRONG_SPEED     => 'Incorrect speed',
  ATTACK_ACS_TOO_LATE    => 'Fleet to slow - it will not catch with the Group ACS',
);

$lang['fl_fleet_err']			= 'Error!';
$lang['fl_unknow_target'] 		= '<li>The specified destination does not exist!</li>';
$lang['fl_nodebris'] 			= '<li>No Debris!</li>';
$lang['fl_nomoon'] 				= '<li>The moon does not exist!</li>';
$lang['fl_vacation_ttl']		= 'Vacation Mode';
$lang['fl_vacation_pla'] 		= 'Player is in Vacation!';
$lang['fl_noob_title'] 			= 'Newbie protection';
$lang['fl_noob_mess_n'] 		= 'The player is too low for you to attack!';
$lang['fl_bad_planet01']		= '<li>This planet is already colonized!</li>'; // !G-
$lang['fl_colonized']			= '<li>Planet colonized by!</li>';
$lang['fl_dont_stay_here']		= 'You cannot land on the enemy planet!'; // !G-
$lang['fl_no_allydeposit']		= 'No alliance depot on the planet!';
$lang['fl_no_self_attack'] 	= '<li>You cannot attack your own planet!</li>';
$lang['fl_no_self_spy'] 		= '<li>You cannot spy on your own planet!</li>';
$lang['fl_only_stay_at_home']	= '<li>Нельзя передислоцировать флот на чужую планету!</li>';
$lang['fl_cheat_speed']			= 'Trying to cheat! Administration has been messaged!';
$lang['fl_cheat_origine']		= 'Trying to cheat! Administration has been messaged!';
$lang['fl_limit_planet']		= '<li>Bad planet !</li>';
$lang['fl_limit_system']		= '<li>Incorrect system !</li>';
$lang['fl_limit_galaxy']		= '<li>Irregular galaxy !</li>';
$lang['fl_ownpl_err'] 			= '<li>You cannot attack a planet!</li>';
$lang['fl_no_planet_type']		= '<li>Incorrect point designation!</li>';
$lang['fl_fleet_err_pl']		= '<li>Planets destination is buged!</li>';
$lang['fl_bad_mission']			= '<li>Setting is not specified or specified Mission is impossible!</li>';
$lang['fl_no_fleetarray']		= '<li>Something wrong with the fleet!</li>';
$lang['fl_noenoughtgoods'] 	= '<li>Attempt to send empty fleet on a mission &quot;Transport&quot;!</li>';
$lang['fl_expe_notech'] 		= '<li>Fleet was not sent, no Astrophysics Technology!</li>';
$lang['fl_expe_max'] 			= '<li>You cannot send another expedition. Develop Astrophysics technology</li>';
$lang['fl_no_deuterium']		= 'Not enough deuterium! ';
$lang['fl_no_resources']		= 'Not enough resources!';
$lang['fl_nostoragespa']		= 'Not enough storeage space! ';
$lang['fl_fleet_send'] 			= 'Fleet sent';
$lang['fl_expe_warning'] 		= 'Attention, you may lose ships during the expedition!';
$lang['fl_expe_staytime'] 		= 'holding time';
$lang['fl_expe_hours'] 			= 'hours';
$lang['fl_adm_attak'] 			= 'You cannot attack the Administrator';
$lang['fl_warning'] 				= 'Warning';
$lang['fl_page0_hint']			= '<ul><li>To create, edit, and remove something  &quot;shortcut&quot; in the left menu<li>What would join the war with the alliance, please click on the title of any available you union</ul>';
$lang['fl_page1_hint']			= '<ul><li>Flight time includes the time for takeoff/landing fleet binding component of any flight,how near or far &quot;shortcut&quot; in the left menu <li>What would join the war with the alliance, please click on the title of any available you union</ul>';
$lang['fl_page5_hint']			= 
'<ul>
  <li>Check the colony, with which you want to dispose of resources for the current planet
  <li>A check mark in the title bar allows you to put or delete markers directly on all colonies
  <li>In the transport resources are only transport ships: small ships, large transport and super transport
  <li>Vessels are loaded in descending size hold
</ul>';

$lang['fl_err_no_ships'] 		= 'No ships In the fleet. Return to the previous page and select the ships for the fleet';

$lang['fl_shrtcup'] = array(
  1 => $lang['fl_shrtcup1'], 
  2 => $lang['fl_shrtcup2'], 
  3 => $lang['fl_shrtcup3']
);

$lang['fl_planettype'] = array(
  1 => $lang['fl_planettype1'], 
  2 => $lang['fl_planettype2'], 
  3 => $lang['fl_planettype3']
);

$lang['fl_aks_invite_message_header']  = 'Invitation to ACS';
$lang['fl_aks_invite_message']         = '<font color="red">Player %s has invited you to join the ACS. you can join the ACS in the page &quot;Fleet&quot;.</font>';
$lang['fl_aks_player_invited']         = '<font color="lime">Player %s was invited to joint attack.</font>';
$lang['fl_aks_player_invited_already'] = '<font color="lime">Player %s already invited. Repeated invitations sent</font>';
$lang['fl_aks_player_error']           = '<font color="red">Error. Player %s was not found.</font>';
$lang['fl_aks_already_in_aks'] 			= 'Fleet in the battle group!';
$lang['fl_aks_adding_error']				= 'Error adding party to fleet:<br>%s';
$lang['fl_aks_hack_wrong_fleet']			= 'Hacking attempt! Manipulation of the alien fleet Message sent to Administrator!';
$lang['fl_aks_too_slow']					= 'Fleet is too slow and could not join the war Union';
$lang['fl_fleet_not_exists'] 				= 'The fleet was not found';
$lang['fl_multi_ip_protection']			= 'Protection against multi-accounts!<br>Unable to send resources to the player with same IP!';

$lang['fl_on_stores']						= 'In stock';
$lang['fl_load_cargo']						= 'Storage';
$lang['fl_rest_on_planet']					= 'Balance';
$lang['fl_none_resources']					= 'Reset';

$lang['fl_planet_resources']				= 'Resources on the planet';

$lang['fl_fleet_data'] 						= 'Current fleet';

$lang['flt_gather_all']    = 'Gather all the resources';
$lang['flt_gather_report'] = 'Gathering report';
$lang['flt_report']        = 'Report';

?>
