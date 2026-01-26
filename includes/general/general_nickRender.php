<?php
/**
 * Created by Gorlum 04.12.2017 4:20
 */

use Refactor\PlayerNickRenderer\PlayerNickRenderContext;
use Refactor\PlayerNickRenderer\PlayerNickRenderContextHtml;
use Refactor\PlayerNickRenderer\PlayerNickRenderPipeline;

global $nickSorting;
$nickSorting = [
//  NICK_HTML,

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

/**
 * @param string     $jsonNickArray JSON-encoded array of nick render parts
 * @param array|bool $options
 *
 * @return array|string
 */
function player_nick_render_to_html_chat($jsonNickArray, $options = false) {
  $jsonNickArray = @json_decode($jsonNickArray, true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    // If supplied JSON string is invalid or broken
    $jsonNickArray = [];
  }

//  $result = player_nick_render_array_to_html($result);
  $jsonNickArray = playerNickRenderArrayToHtml($jsonNickArray);

//  unset($jsonNickArray[NICK_HTML]);

  return implode('', playerNickOrder($jsonNickArray, $options));
}

/**
 * @param array $nickArray is a user record
 * @param       $options
 *
 * @return array|string
 */
function player_nick_render_to_html_user($nickArray, $options = false) {
  $nickArray = playerNickRenderToArray($nickArray, $options);

//  $result = player_nick_render_array_to_html($result);
  $nickArray = playerNickRenderArrayToHtml($nickArray);

//  unset($nickArray[NICK_HTML]);

  return implode('', playerNickOrder($nickArray, $options));
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

///**
// * Unpacks nick parts from string - if necessary
// *
// * @param array|string $nick_string
// *
// * @return array|string
// */
//function player_nick_uncompact($nick_string) {
//  $result = $nick_string;
//  if (is_string($nick_string) && (strpos($nick_string, '{"') === 0 || strpos($nick_string, '["') === 0)) {
//    $result = json_decode($nick_string, true);
//    if (json_last_error() !== JSON_ERROR_NONE) {
//      $result = $nick_string;
//    }
//  }
//
//  return $result;
//}

/**
 * @param array $nick_array
 *
 * @return array
 */
function playerNickRenderArrayToHtml($nick_array) {
  return PlayerNickRenderPipeline::singleton()->renderArrayToHtml(new PlayerNickRenderContextHtml($nick_array));
}

//function player_nick_render_array_to_html($nick_array) {
//  $result = null;
//
//  /** @see sn_player_nick_render_array_to_html */
//  return sn_function_call('player_nick_render_array_to_html', array($nick_array, &$result));
//}
//
///**
// * @param array $nick_array
// * @param array $result
// *
// * @return array
// */
//function sn_player_nick_render_array_to_html($nick_array, &$result) {
//  static $iconCache = array();
//
//  if (empty($iconCache['gender_' . $nick_array[NICK_GENDER]])) {
//    $iconCache['gender_' . $nick_array[NICK_GENDER]] = SN::$gc->skinModel->getImageCurrent("gender_{$nick_array[NICK_GENDER]}|html");
//    $iconCache['icon_vacation'] = SN::$gc->skinModel->getImageCurrent('icon_vacation|html');
//    $iconCache['icon_birthday'] = SN::$gc->skinModel->getImageCurrent('icon_birthday|html');
//  }
//
//  // ALL STRING ARE UNSAFE!!!
//  if (isset($nick_array[NICK_BIRTHDAY])) {
//    $result[NICK_BIRTHDAY] = $iconCache['icon_birthday'];
//  }
//
//  if (isset($nick_array[NICK_VACATION])) {
//    $result[NICK_VACATION] = $iconCache['icon_vacation'];
//  }
//
//  if (isset($nick_array[NICK_RANK])) {
//    $result[NICK_RANK] = SN::$gc->playerLevelHelper->renderRank($nick_array[NICK_RANK], $nick_array[NICK_RANK_NO_TEXT]);
//  }
//
//  if (isset($nick_array[NICK_GENDER])) {
//    $result[NICK_GENDER] = $iconCache['gender_' . $nick_array[NICK_GENDER]];
//  }
//
//  $highlight = null;
//  if (isset($nick_array[NICK_PREMIUM])) {
//    $highlight = SN::$config->chat_highlight_premium;
//  }
//
//  if (isset($nick_array[NICK_AUTH_LEVEL])) {
//    switch ($nick_array[NICK_AUTH_LEVEL]) {
//      case 4:
//        $highlight = SN::$config->chat_highlight_developer;
//      break;
//
//      case 3:
//        $highlight = SN::$config->chat_highlight_admin;
//      break;
//
//      case 2:
//        $highlight = SN::$config->chat_highlight_operator;
//      break;
//
//      case 1:
//        $highlight = SN::$config->chat_highlight_moderator;
//      break;
//    }
//
//  }
//
//  if ($highlight) {
//    list($result[NICK_HIGHLIGHT], $result[NICK_HIGHLIGHT_END]) = explode('$1', $highlight);
//  }
//
//  if (isset($nick_array[NICK_CLASS])) {
//    $result[NICK_CLASS] = '<span ' . $nick_array[NICK_CLASS] . '>';
//    $result[NICK_CLASS_END] = '</span>';
//  }
//
//  $result[NICK_NICK] = sys_safe_output($nick_array[NICK_NICK]);
//
//  if (isset($nick_array[NICK_ALLY])) {
//    $result[NICK_ALLY] = '[' . sys_safe_output($nick_array[NICK_ALLY]) . ']';
//  }
//
//  $result[NICK_HTML] = true;
//
//  return $result;
//}

/**
 * @psalm-type PlayerNickRenderOptionArray = array{
 *    color:bool,
 *    ally:bool,
 *    icons:bool,
 *    gender:bool,
 *    birthday:bool,
 *    vacancy:bool,
 *    player_rank:bool,
 *    race:bool,
 *    award:bool,
 * }
 *
 * @param array                            $user
 * @param PlayerNickRenderOptionArray|bool $options
 *
 * @return array
 */
function playerNickRenderToArray(array $user, $options = false) {
  return PlayerNickRenderPipeline::singleton()->renderToArray(new PlayerNickRenderContext($user, $options));
}

