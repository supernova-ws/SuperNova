<?php
/**
 * search.php
 *
 * 1.3 copyright (c) 2009-2010 by Gorlum for http://supernova.ws
 *   [%] Fixed search of players without alliance
 * 1.2 - Security checks & tests by Gorlum for http://supernova.ws
 * @version 1.1
 * @copyright 2009 by angelus_ira for Project. XNova
 * @copyright 2008 by ??????? for XNova
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));

$searchtext = mysql_real_escape_string($_POST['searchtext']);
$type = sys_get_param_str('type');
$dpath = (!$user["dpath"]) ? DEFAULT_SKINPATH : $user["dpath"];

includeLang('search');
$i = 0;

//creamos la query
switch($type){
  case "playername":
    $table = gettemplate('search_user_table');
    $row = gettemplate('search_user_row');
    $sql = "SELECT
        u.id as uid, u.username, u.ally_id, u.id_planet,
        p.*, p.name as planet_name,
        s.total_points, s.total_rank,
        a.ally_tag, a.ally_name
      FROM {{table}}planets as p,
        {{table}}statpoints as s,
        {{table}}users as u LEFT JOIN {{table}}alliance as a ON a.id = u.ally_id
      WHERE
        username LIKE '%{$searchtext}%'
        AND p.id_owner = u.id AND p.id=u.id_planet
        AND s.id_owner = u.id AND stat_type = 1 AND stat_code = 1 LIMIT 30;";
    $search = doquery($sql, '');
  break;
  case "planetname":
    $table = gettemplate('search_user_table');
    $row = gettemplate('search_user_row');
    $sql = "SELECT
        p.*, p.name as planet_name,
        u.id as uid, u.username, u.ally_id, u.id_planet,
        s.total_points, s.total_rank,
        a.ally_tag, a.ally_name
      FROM {{table}}planets as p,
        {{table}}users as u,
        {{table}}alliance as a,
        {{table}}statpoints as s
      WHERE
        name LIKE '%{$searchtext}%'
        AND u.id=p.id_owner
        AND a.id = u.ally_id
        AND s.id_owner = p.id_owner AND stat_type = 1 AND stat_code = 1 LIMIT 30;";
    $search = doquery($sql, '');
  break;
  case "allytag":
    $table = gettemplate('search_ally_table');
    $row = gettemplate('search_ally_row');
    $search = doquery("SELECT * FROM {{table}}alliance inner join {{table}}statpoints on {{table}}alliance.`id`={{table}}statpoints.`id_owner` WHERE ally_tag LIKE '%{$searchtext}%' AND STAT_type=2 LIMIT 30",'');
  break;
  case "allyname":
    $table = gettemplate('search_ally_table');
    $row = gettemplate('search_ally_row');
    $search = doquery("SELECT * FROM {{table}}alliance inner join {{table}}statpoints on {{table}}alliance.`id`={{table}}statpoints.`id_owner` WHERE ally_name LIKE '%{$searchtext}%' AND STAT_type=2 LIMIT 30",'');
  break;
  default:
    $table = gettemplate('search_user_table');
    $row = gettemplate('search_user_row');
    $sql = "SELECT
        u.id as uid, u.username, u.ally_id, u.id_planet,
        p.*, p.name as planet_name,
        s.total_points, s.total_rank,
        a.ally_tag, a.ally_name
      FROM {{table}}users as u,
        {{table}}planets as p,
        {{table}}alliance as a,
        {{table}}statpoints as s
      WHERE
        username LIKE '%{$searchtext}%'
        AND p.id_owner = u.id AND p.id=u.id_planet
        AND a.id = u.ally_id
        AND s.id_owner = u.id AND stat_type = 1 AND stat_code = 1 LIMIT 30;";
    $search = doquery($sql, '');
}

if(isset($searchtext) && isset($type)){
  while($r = mysql_fetch_assoc($search)){
    if($type=='playername'||$type=='planetname'){
      $s=$r;
      //para obtener el nombre del planeta
      $s['ally_name'] = ($s['ally_name']!='')?"<a href=\"alliance.php?mode=ainfo&tag={$s['ally_tag']}\">{$s['ally_name']}</a>":'';
      $s['position'] = "<a href=\"stat.php?start=".$s['total_rank']."\">".$s['total_rank']."</a>";
      $s['dpath'] = $dpath;
      $s['coordinated'] = "{$s['galaxy']}:{$s['system']}:{$s['planet']}";
      $s['buddy_request'] = $lang['buddy_request'];
      $s['write_a_messege'] = $lang['write_a_messege'];
      $s['u_id'] = $s[uid];
      $result_list .= parsetemplate($row, $s);
    }elseif($type=='allytag'||$type=='allyname'){
      $s=$r;
      $s['ally_points'] = pretty_number($s['total_points']);
      $s['ally_tag'] = "<a href=\"alliance.php?mode=ainfo&tag={$s['ally_tag']}\">{$s['ally_tag']}</a>";
      $result_list .= parsetemplate($row, $s);
    }
  }
  if($result_list!=''){
    $lang['result_list'] = $result_list;
    $search_results = parsetemplate($table, $lang);
  }
}
//el resto...
$lang['type_playername'] = ($type == "playername") ? " SELECTED" : "";
$lang['type_planetname'] = ($type == "planetname") ? " SELECTED" : "";
$lang['type_allytag'] = ($type == "allytag") ? " SELECTED" : "";
$lang['type_allyname'] = ($type == "allyname") ? " SELECTED" : "";
$lang['searchtext'] = $searchtext;
$lang['u_id'] = $r[uid];
$lang['search_results'] = $search_results;
//esto es algo repetitivo ... w
$page = parsetemplate(gettemplate('search_body'), $lang);
display($page,$lang['Search']);
?>