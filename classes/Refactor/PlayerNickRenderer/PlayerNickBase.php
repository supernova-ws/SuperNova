<?php
/** Created by Gorlum 24.01.2026 16:02 */

namespace Refactor\PlayerNickRenderer;

use DateTime;
use Refactor\ContextPipeline\AbstractPipelineStep;
use SN;

final class PlayerNickBase extends AbstractPipelineStep {
  /**
   * @param PlayerNickRenderContext $context
   *
   * @return array
   */
  public function apply($context): array {
    $options = &$context->options;
    $user    = &$context->user;

    if (!empty($options['birthday']) && !empty($user['user_birthday'])) {
      $birthday = DateTime::createFromFormat('Y-m-d', $user['user_birthday']);
      $today    = new DateTime();

      if ($birthday && $birthday->format('m-d') === $today->format('m-d')) {
        $result[NICK_BIRTHDAY] = '';
      }
    }

    if (!empty($options['gender']) && isset($user['gender'])) {
      $result[NICK_GENDER] = $user['gender'] == GENDER_UNKNOWN ? 'unknown' : ($user['gender'] == GENDER_FEMALE ? 'female' : 'male');
    }

    if (!empty($options['vacancy']) && !empty($user['vacation'])) {
      $result[NICK_VACATION] = $user['vacation'];
    }

    if (!empty($options['player_rank']) && isset($user['total_points'])) {
      $result[NICK_RANK] = SN::$gc->playerLevelHelper->getPointLevel($user['total_points'], $user['authlevel']);
    }

    if (!empty($options[NICK_RANK_NO_TEXT])) {
      $result[NICK_RANK_NO_TEXT] = $options[NICK_RANK_NO_TEXT];
    }

    if (!empty($options['color']) && ($user_auth_level = $user['authlevel'])) {
      $result[NICK_AUTH_LEVEL] = $user_auth_level;
    }

    if (!empty($options['class'])) {
      $result[NICK_CLASS] = (isset($options[NICK_CLASS]) ? ' ' . $options[NICK_CLASS] : '') . $options['class'];
    }

    if (!empty($user['ally_tag']) && !empty($options['ally'])) {
      $result[NICK_ALLY] = $user['ally_tag'];
    }

    $result[NICK_NICK] = $user['username'];

    return $context->result = $result;
  }

}
