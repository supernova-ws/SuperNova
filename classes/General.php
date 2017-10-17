<?php
/**
 * Created by Gorlum 12.10.2017 15:19
 */

use \Common\GlobalContainer;

/**
 * Class General
 *
 * Wrapper for /includes/general.php
 * Will make unit testing easier
 *
 */
class General {

  protected $gc;

  /**
   * General constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct(GlobalContainer $gc) {
    $this->gc = $gc;
  }

  /**
   * @param string|string[] $groupNameList
   *
   * @return array|array[]
   */
  public function getGroupsByName($groupNameList) {
    return sn_get_groups($groupNameList);
  }

  /**
   * @param int|string|array $groupIdList
   *
   * @return array|array[]
   */
  public function getGroupsById($groupIdList) {
    if (!is_array($groupIdList)) {
      $groupIdList = [$groupIdList];
    }

    $idToName = $this->getGroupsByName(GROUP_GROUP_ID_TO_NAMES);
    foreach ($groupIdList as &$groupId) {
      if (!empty($idToName[$groupId])) {
        $groupId = $idToName[$groupId];
      }

    }

    return $this->getGroupsByName($groupIdList);
  }

}
