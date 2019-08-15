<?php
/**
 * Created by Gorlum 14.08.2019 1:39
 */

namespace Pm;


use DBAL\RecordV2;
use DBAL\StorageSqlV2;

/**
 * Class RecordPlayerIgnore
 *
 * @property int|string $playerId  - Player ID which ignores other player
 * @property int|string $ignoredId - Ignored player ID
 * @property int        $pm        - Flag to ignore Personal Messages
 *
 *
 * @package Pm
 */
class RecordPlayerIgnore extends RecordV2 {
  /**
   * @var StorageSqlV2|null $storage
   */
  // TODO - replace with IStorage
  protected static $storage = null;

  protected static $_tableName = 'player_ignore';

  protected static $_fieldsToProperties = [
    'player_id'  => 'playerId',
    'ignored_id' => 'ignoredId',
  ];


}
