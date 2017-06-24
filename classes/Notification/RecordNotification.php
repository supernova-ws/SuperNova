<?php

/**
 * Created by Gorlum 12.06.2017 14:47
 */

namespace Notification;

use DBAL\ActiveRecord;

/**
 * Class RecordNotification
 *
 * @property string     $createdSql
 * @property int|string $ownerId
 * @property string     $text
 *
 * @package Notification
 *
 */
class RecordNotification extends ActiveRecord {
  protected static $_tableName = 'notifications';

  protected static $_fieldsToProperties = [
    'timestamp' => 'createdSql',
    'owner'     => 'ownerId',
    'text'      => 'text',
  ];

}
