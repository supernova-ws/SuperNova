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

$searchtext = sys_get_param_str('searchtext');
$type = sys_get_param_str('type');
$dpath = (!$user["dpath"]) ? DEFAULT_SKINPATH : $user["dpath"];

lng_include('search');
$i = 0;

//creamos la query
switch($type){
  case "planetname":
    $table = gettemplate('search_user_table');
    $row = gettemplate('search_user_row');
    $sql = "SELECT
        p.*, p.name as planet_name,
        u.id as uid, u.username, u.ally_id, u.id_planet,
        u.total_points, u.total_rank,
        u.ally_tag, u.ally_name
      FROM
        {{planets}} AS p
        LEFT JOIN {{users}} AS u ON u.id = p.id_owner
      WHERE
        name LIKE '%{$searchtext}%'
      LIMIT 30;";
    $search = doquery($sql);
  break;
  case "ally":
    $table = gettemplate('search_ally_table');
    $row = gettemplate('search_ally_row');
    $search = doquery("SELECT * FROM {{alliance}} WHERE ally_tag LIKE '%{$searchtext}%' OR ally_name LIKE '%{$searchtext}%' LIMIT 30");
  break;
  case "playername":
  default:
    $table = gettemplate('search_user_table');
    $row = gettemplate('search_user_row');
    $sql = "SELECT
        u.id as uid, u.username, u.ally_id, u.id_planet, u.total_points, u.total_rank,
        p.*, p.name as planet_name,
        u.ally_tag, u.ally_name
      FROM 
        {{users}} AS u
        LEFT JOIN {{planets}} AS p ON p.id_owner = u.id AND p.id=u.id_planet
      WHERE
        username LIKE '%{$searchtext}%'
      LIMIT 30;";
    $search = doquery($sql);
  break;
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
    }elseif($type=='ally'){
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
$lang['type_ally'] = ($type == "ally") ? " SELECTED" : "";
$lang['searchtext'] = $searchtext;
$lang['u_id'] = $r[uid];
$lang['search_results'] = $search_results;
//esto es algo repetitivo ... w
$page = parsetemplate(gettemplate('search_body'), $lang);
display($page,$lang['Search']);
?>