<?php
/**
 * Created by Gorlum 04.12.2017 4:20
 */

global $nickSorting;
$nickSorting = [
  NICK_HTML,

  NICK_FIRST,

  NICK_RANK,

  NICK_BIRTHDAY,
  NICK_VACATION,
  NICK_PREMIUM,
  NICK_AUTH_LEVEL,

  NICK_HIGHLIGHT,
  NICK_CLASS,
  NICK_NICK_CLASS,
  NICK_NICK,
  NICK_NICK_CLASS_END,
  NICK_ALLY_CLASS,
  NICK_ALLY,
  NICK_ALLY_CLASS_END,
  NICK_CLASS_END,
  NICK_HIGHLIGHT_END,

  NICK_GENDER,
  NICK_RACE,
  NICK_AWARD,

  NICK_LAST,
];
/*
 // Old nick sorting
 $nickSorting = [
  NICK_HTML,

  NICK_FIRST,

  NICK_RACE,
  NICK_GENDER,
  NICK_AWARD,
  NICK_VACATION,
  NICK_BIRTHDAY,
  NICK_PREMIUM,
  NICK_AUTH_LEVEL,

  NICK_RANK,
  NICK_HIGHLIGHT,
  NICK_CLASS,
  NICK_NICK_CLASS,
  NICK_NICK,
  NICK_NICK_CLASS_END,
  NICK_ALLY_CLASS,
  NICK_ALLY,
  NICK_ALLY_CLASS_END,
  NICK_CLASS_END,
  NICK_HIGHLIGHT_END,

  NICK_LAST,
];
*/


/**
 * Ordering nick parts according to predefined part order
 *
 * @param array      $array
 * @param null|array $options
 *
 * @return array
 */
function playerNickOrder($array, $options = null) {
  global $nickSorting;

  $currentSort = is_array($options[NICK_SORT]) ? $options[NICK_SORT] : $nickSorting;

  $result = [];
  // Rearranging nick parts according to sort array
  foreach ($currentSort as $nickPartId) {
    if (array_key_exists($nickPartId, $array)) {
      $result[$nickPartId] = $array[$nickPartId];
      unset($array[$nickPartId]);
    }
  }

  // Adding what left of nick parts to resulting array
  return array_merge_recursive_numeric($result, $array);
}

// Может принимать: (array)$user, $nick_render_array, $nick_render_array_html, $nick_render_string_compact
function player_nick_render_to_html($result, $options = false) {
  $result = player_nick_uncompact($result);

  if (is_array($result)) {
    if (isset($result['id'])) {
      $result = player_nick_render_current_to_array($result, $options);
    }
    if (!isset($result[NICK_HTML])) {
      $result = player_nick_render_array_to_html($result);
    }
    unset($result[NICK_HTML]);
    $result = implode('', playerNickOrder($result, $options));
  }

  return $result;
}

/**
 * Pack nick parts to string
 *
 * @param array $nick_array
 *
 * @return string
 */
function player_nick_compact($nick_array) {
  return json_encode(playerNickOrder($nick_array));
}

/**
 * Unpacks nick parts from string - if necessary
 *
 * @param array|string $nick_string
 *
 * @return array|string
 */
function player_nick_uncompact($nick_string) {
  $result = $nick_string;
  if (is_string($nick_string) && (strpos($nick_string, '{"') === 0 || strpos($nick_string, '["') === 0)) {
    $result = json_decode($nick_string, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
      $result = $nick_string;
    }
  }

  return $result;
}

function player_nick_render_array_to_html($nick_array) {
  $result = null;

  return sn_function_call('player_nick_render_array_to_html', array($nick_array, &$result));
}

/**
 * @param array $nick_array
 * @param array $result
 *
 * @return array
 */
function sn_player_nick_render_array_to_html($nick_array, &$result) {
  static $iconCache = array();

  if (empty($iconCache['gender_' . $nick_array[NICK_GENDER]])) {
    $iconCache['gender_' . $nick_array[NICK_GENDER]] = SN::$gc->skinModel->getImageCurrent("gender_{$nick_array[NICK_GENDER]}|html");
    $iconCache['icon_vacation'] = SN::$gc->skinModel->getImageCurrent('icon_vacation|html');
    $iconCache['icon_birthday'] = SN::$gc->skinModel->getImageCurrent('icon_birthday|html');
  }

  // ALL STRING ARE UNSAFE!!!
  if (isset($nick_array[NICK_BIRTHDAY])) {
    $result[NICK_BIRTHDAY] = $iconCache['icon_birthday'];
  }

  if (isset($nick_array[NICK_VACATION])) {
    $result[NICK_VACATION] = $iconCache['icon_vacation'];
  }

  if (isset($nick_array[NICK_RANK])) {
    $result[NICK_RANK] = SN::$gc->playerLevelHelper->renderRank($nick_array[NICK_RANK], $nick_array[NICK_RANK_NO_TEXT]);
  }

  if (isset($nick_array[NICK_GENDER])) {
    $result[NICK_GENDER] = $iconCache['gender_' . $nick_array[NICK_GENDER]];
  }

  $highlight = null;
  if (isset($nick_array[NICK_PREMIUM])) {
    $highlight = SN::$config->chat_highlight_premium;
  }

  if (isset($nick_array[NICK_AUTH_LEVEL])) {
    switch ($nick_array[NICK_AUTH_LEVEL]) {
      case 4:
        $highlight = SN::$config->chat_highlight_developer;
      break;

      case 3:
        $highlight = SN::$config->chat_highlight_admin;
      break;

      case 2:
        $highlight = SN::$config->chat_highlight_operator;
      break;

      case 1:
        $highlight = SN::$config->chat_highlight_moderator;
      break;
    }

  }

  if ($highlight) {
    list($result[NICK_HIGHLIGHT], $result[NICK_HIGHLIGHT_END]) = explode('$1', $highlight);
  }

  if (isset($nick_array[NICK_CLASS])) {
    $result[NICK_CLASS] = '<span ' . $nick_array[NICK_CLASS] . '>';
    $result[NICK_CLASS_END] = '</span>';
  }

  $result[NICK_NICK] = sys_safe_output($nick_array[NICK_NICK]);

  if (isset($nick_array[NICK_ALLY])) {
    $result[NICK_ALLY] = '[' . sys_safe_output($nick_array[NICK_ALLY]) . ']';
  }

  $result[NICK_HTML] = true;

  return $result;
}

/**
 * @param array      $render_user
 * @param array|bool $options - [
 *                            'color' => true,
 *                            'icons' => true,
 *                            'gender' => true,
 *                            'birthday' => true,
 *                            'ally' => true,
 *                            ]
 *
 * @return mixed
 */
function player_nick_render_current_to_array($render_user, $options = false) {
  $result = null;

  return sn_function_call('player_nick_render_current_to_array', array($render_user, $options, &$result));
}

/**
 * @param array      $render_user
 * @param array|bool $options - [
 *                            'color' => true,
 *                            'icons' => true,
 *                            'gender' => true,
 *                            'birthday' => true,
 *                            'ally' => true,
 *                            ]
 * @param array      $result
 *
 * @return mixed
 */
function sn_player_nick_render_current_to_array($render_user, $options = false, &$result) {
  /*
  $options = $options !== true ? $options :
    array(
      'color' => true,
      'icons' => true,
      'gender' => true,
      'birthday' => true,
      'ally' => true,
    );
  */


  if (($options === true || isset($options['icons']) || isset($options['birthday'])) && (date('Y', SN_TIME_NOW) . date('-m-d', strtotime($render_user['user_birthday'])) == date('Y-m-d', SN_TIME_NOW))) {
    if(!empty($render_user['user_birthday'])) {
      $result[NICK_BIRTHDAY] = '';
    }
  }

  if ($options === true || (isset($options['icons']) && $options['icons']) || (isset($options['gender']) && $options['gender'])) {
    if (isset($render_user['gender'])) {
      $result[NICK_GENDER] = $render_user['gender'] == GENDER_UNKNOWN ? 'unknown' : ($render_user['gender'] == GENDER_FEMALE ? 'female' : 'male');
    }
  }

  if (($options === true || (isset($options['icons']) && $options['icons']) || (isset($options['vacancy']) && $options['vacancy'])) && $render_user['vacation']) {
    if(isset($render_user['vacation'])) {
      $result[NICK_VACATION] = $render_user['vacation'];
    }
  }

  if ($options === true || !empty($options['icons']) || !empty($options['player_rank'])) {
    if(isset($render_user['total_points'])) {
      $result[NICK_RANK] = SN::$gc->playerLevelHelper->getPointLevel($render_user['total_points'], $render_user['authlevel']);
    }
  }

  if (!empty($options[NICK_RANK_NO_TEXT])) {
    $result[NICK_RANK_NO_TEXT] = $options[NICK_RANK_NO_TEXT];
  }

  if ($options === true || (isset($options['color']) && $options['color'])) {
    if ($user_auth_level = $render_user['authlevel']) {
      $result[NICK_AUTH_LEVEL] = $user_auth_level;
    }
    if ($user_premium = mrc_get_level($render_user, false, UNIT_PREMIUM)) {
      $result[NICK_PREMIUM] = $user_premium;
    }
  }

  if ((isset($options['class']) && $options['class'])) {
//    $result[NICK_CLASS] = (isset($result_options[NICK_CLASS]) ? ' ' . $result_options[NICK_CLASS] : '') . $options['class'];
    $result[NICK_CLASS] = (isset($options[NICK_CLASS]) ? ' ' . $options[NICK_CLASS] : '') . $options['class'];
  }

  if ($render_user['ally_tag'] && ($options === true || (isset($options['ally']) && $options['ally']))) {
    if(isset($render_user['ally_tag'])) {
      $result[NICK_ALLY] = $render_user['ally_tag'];
    }
  }

  $result[NICK_NICK] = $render_user['username'];

  return $result;
}

