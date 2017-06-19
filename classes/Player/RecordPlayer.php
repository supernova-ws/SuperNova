<?php
/**
 * Created by Gorlum 13.06.2017 14:09
 */

namespace Player;


use DBAL\ActiveRecord;


/**
 * Class RecordPlayer
 *
 * @package Player
 *
 * @property $id
 * @property $username
 * @property $authlevel
 * @property $vacation
 * @property $banaday
 * @property $dark_matter
 * @property $dark_matter_total
 * @property $player_rpg_explore_xp
 * @property $player_rpg_explore_level
 * @property $ally_id
 * @property $ally_tag
 * @property $ally_name
 * @property $ally_register_time
 * @property $ally_rank_id
 * @property $lvl_minier
 * @property $xpminier
 * @property $player_rpg_tech_xp
 * @property $player_rpg_tech_level
 * @property $lvl_raid
 * @property $xpraid
 * @property $raids
 * @property $raidsloose
 * @property $raidswin
 * @property $new_message
 * @property $mnl_alliance
 * @property $mnl_joueur
 * @property $mnl_attaque
 * @property $mnl_spy
 * @property $mnl_exploit
 * @property $mnl_transport
 * @property $mnl_expedition
 * @property $mnl_buildlist
 * @property $msg_admin
 * @property $bana
 * @property $deltime
 * @property $news_lastread
 * @property $total_rank
 * @property $total_points
 * @property $password
 * @property $salt
 * @property $email
 * @property $email_2
 * @property $lang
 * @property $avatar
 * @property $sign
 * @property $id_planet
 * @property $galaxy
 * @property $system
 * @property $planet
 * @property $current_planet
 * @property $user_lastip
 * @property $user_last_proxy
 * @property $user_last_browser_id
 * @property $register_time
 * @property $onlinetime
 * @property $que_processed
 * @property $template
 * @property $skin
 * @property $design
 * @property $noipcheck
 * @property $options
 * @property $user_as_ally
 * @property $metal
 * @property $crystal
 * @property $deuterium
 * @property $user_birthday
 * @property $user_birthday_celebrated
 * @property $player_race
 * @property $vacation_next
 * @property $metamatter
 * @property $metamatter_total
 * @property $admin_protection
 * @property $user_bot
 * @property $gender
 * @property $immortal
 * @property $parent_account_id
 * @property $parent_account_global
 * @property $server_name
 *
 */
class RecordPlayer extends ActiveRecord {
  protected static $_tableName = 'users';

}
