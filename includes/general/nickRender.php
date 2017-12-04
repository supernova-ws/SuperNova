<?php
/**
 * Created by Gorlum 04.12.2017 4:20
 */

// TODO - поменять название
// Может принимать: (array)$user, $nick_render_array, $nick_render_array_html, $nick_render_string_compact
function player_nick_render_to_html($result, $options = false) {
  // TODO - обрабатывать разные случаи: $user, $render_nick_array, $string

  if (is_string($result) && strpos($result, ':{i:')) {
    $result = player_nick_uncompact($result);
  }

  if (is_array($result)) {
    if (isset($result['id'])) {
      $result = player_nick_render_current_to_array($result, $options);
    }
    if (!isset($result[NICK_HTML])) {
      $result = player_nick_render_array_to_html($result);
    }
    unset($result[NICK_HTML]);
    // unset($result[NICK_ID]);
    ksort($result);
    $result = implode('', $result);
  }

  return $result;
}

function player_nick_compact($nick_array) {
  ksort($nick_array);

  return serialize($nick_array);
}

function player_nick_uncompact($nick_string) {
  try {
    $result = unserialize($nick_string);
    // ksort($result); // Всегда ksort-ый в player_nick_compact()
  } catch (exception $e) {
    $result = strpos($nick_string, ':{i:') ? null : $nick_string; // fallback if it is already string - for old chat strings, for example
  }

  return $result;
}

function player_nick_render_array_to_html($nick_array) { return sn_function_call('player_nick_render_array_to_html', array($nick_array, &$result)); }

/**
 * @param array $nick_array
 * @param array $result
 *
 * @return array
 */
function sn_player_nick_render_array_to_html($nick_array, &$result) {
  static $iconCache = array();

  if (empty($iconCache['gender_' . $nick_array[NICK_GENDER]])) {
    $iconCache['gender_' . $nick_array[NICK_GENDER]] = classSupernova::$gc->skinModel->getImageCurrent("gender_{$nick_array[NICK_GENDER]}|html");
    $iconCache['icon_vacation'] = classSupernova::$gc->skinModel->getImageCurrent('icon_vacation|html');
    $iconCache['icon_birthday'] = classSupernova::$gc->skinModel->getImageCurrent('icon_birthday|html');
  }
  $iconGender = $iconCache['gender_' . $nick_array[NICK_GENDER]];

  // ALL STRING ARE UNSAFE!!!
  if (isset($nick_array[NICK_BIRTHSDAY])) {
    $result[NICK_BIRTHSDAY] = $iconCache['icon_birthday'];
  }

  if (isset($nick_array[NICK_VACATION])) {
    $result[NICK_VACATION] = $iconCache['icon_vacation'];
  }

  if (isset($nick_array[NICK_GENDER])) {
    $result[NICK_GENDER] = $iconGender;
  }

  if (isset($nick_array[NICK_AUTH_LEVEL]) || isset($nick_array[NICK_PREMIUM])) {
    switch ($nick_array[NICK_AUTH_LEVEL]) {
      case 4:
        $highlight = classSupernova::$config->chat_highlight_developer;
      break;

      case 3:
        $highlight = classSupernova::$config->chat_highlight_admin;
      break;

      case 2:
        $highlight = classSupernova::$config->chat_highlight_operator;
      break;

      case 1:
        $highlight = classSupernova::$config->chat_highlight_moderator;
      break;

      default:
        $highlight = isset($nick_array[NICK_PREMIUM]) ? classSupernova::$config->chat_highlight_premium : '';
    }

    if ($highlight) {
      list($result[NICK_HIGHLIGHT], $result[NICK_HIGHLIGHT_END]) = explode('$1', $highlight);
    }
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
 *   'color' => true,
 *   'icons' => true,
 *   'gender' => true,
 *   'birthday' => true,
 *   'ally' => true,
 * ]
 *
 * @return mixed
 */
function player_nick_render_current_to_array($render_user, $options = false) { return sn_function_call('player_nick_render_current_to_array', array($render_user, $options, &$result)); }

/**
 * @param array      $render_user
 * @param array|bool $options - [
 *   'color' => true,
 *   'icons' => true,
 *   'gender' => true,
 *   'birthday' => true,
 *   'ally' => true,
 * ]
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


  if ($render_user['user_birthday'] && ($options === true || isset($options['icons']) || isset($options['birthday'])) && (date('Y', SN_TIME_NOW) . date('-m-d', strtotime($render_user['user_birthday'])) == date('Y-m-d', SN_TIME_NOW))) {
    $result[NICK_BIRTHSDAY] = '';
  }

  if ($options === true || (isset($options['icons']) && $options['icons']) || (isset($options['gender']) && $options['gender'])) {
    $result[NICK_GENDER] = $render_user['gender'] == GENDER_UNKNOWN ? 'unknown' : ($render_user['gender'] == GENDER_FEMALE ? 'female' : 'male');
  }

  if (($options === true || (isset($options['icons']) && $options['icons']) || (isset($options['vacancy']) && $options['vacancy'])) && $render_user['vacation']) {
    $result[NICK_VACATION] = $render_user['vacation'];
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
    $result[NICK_CLASS] = (isset($result_options[NICK_CLASS]) ? ' ' . $result_options[NICK_CLASS] : '') . $options['class'];
  }

  if ($render_user['ally_tag'] && ($options === true || (isset($options['ally']) && $options['ally']))) {
    $result[NICK_ALLY] = $render_user['ally_tag'];
  }

  $result[NICK_NICK] = $render_user['username'];

  return $result;
}
