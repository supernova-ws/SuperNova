<?php
/**
 * Created by Gorlum 12.07.2017 12:34
 */

namespace DBAL\Tests\Fixtures;


use DBAL\ActiveRecordAbstract;
use DBAL\DbFieldDescription;

class RecordActiveAbstractObject extends ActiveRecordAbstract {
  protected static $_tableName = '';
  protected static $_fieldsToProperties = [
    'timestamp_current' => 'timestampCurrent',
  ];

  protected static function dbGetFieldsDescription() {
    $result = [];
    foreach([
      'id'                =>
        [
          'Field'      => 'id',
          'Type'       => 'bigint(20) unsigned',
          'Collation'  => null,
          'Null'       => 'NO',
          'Key'        => 'PRI',
          'Default'    => null,
          'Extra'      => 'auto_increment',
          'Privileges' => 'select,insert,update,references',
          'Comment'    => '',
        ],
      'timestamp_current' =>
        [
          'Field'      => 'timestamp_current',
          'Type'       => 'timestamp',
          'Collation'  => null,
          'Null'       => 'NO',
          'Key'        => '',
          'Default'    => 'CURRENT_TIMESTAMP',
          'Extra'      => 'on update CURRENT_TIMESTAMP',
          'Privileges' => 'select,insert,update,references',
          'Comment'    => '',
        ],
      'varchar'           =>
        [
          'Field'      => 'varchar',
          'Type'       => 'varchar(32)',
          'Collation'  => 'utf8_general_ci',
          'Null'       => 'YES',
          'Key'        => 'UNI',
          'Default'    => '',
          'Extra'      => '',
          'Privileges' => 'select,insert,update,references',
          'Comment'    => '',
        ],
      'null'              =>
        [
          'Field'      => 'null',
          'Type'       => 'varchar(32)',
          'Collation'  => 'utf8_general_ci',
          'Null'       => 'YES',
          'Key'        => 'UNI',
          'Default'    => null,
          'Extra'      => '',
          'Privileges' => 'select,insert,update,references',
          'Comment'    => '',
        ],
//        'owner'             =>
//          [
//            'Field'      => 'ally_owner',
//            'Type'       => 'bigint(20) unsigned',
//            'Collation'  => null,
//            'Null'       => 'YES',
//            'Key'        => 'MUL',
//            'Default'    => null,
//            'Extra'      => '',
//            'Privileges' => 'select,insert,update,references',
//            'Comment'    => '',
//          ],
//        'unix_time'         =>
//          [
//            'Field'      => 'ally_register_time',
//            'Type'       => 'int(11)',
//            'Collation'  => null,
//            'Null'       => 'NO',
//            'Key'        => '',
//            'Default'    => '0',
//            'Extra'      => '',
//            'Privileges' => 'select,insert,update,references',
//            'Comment'    => '',
//          ],
//        'medium_text'       =>
//          [
//            'Field'      => 'ally_description',
//            'Type'       => 'mediumtext',
//            'Collation'  => 'utf8_general_ci',
//            'Null'       => 'YES',
//            'Key'        => '',
//            'Default'    => null,
//            'Extra'      => '',
//            'Privileges' => 'select,insert,update,references',
//            'Comment'    => '',
//          ],
    ] as $fieldData) {
      $dbf = new DbFieldDescription();
      $result[$fieldData['Field']] = $dbf->fromMySqlDescription($fieldData);
    }

    return $result;
  }

  /**
   * @return bool
   */
  protected function dbInsert() {
    // TODO: Implement dbInsert() method.
  }

  /**
   * Asks DB for last insert ID
   *
   * @return int|string
   */
  protected function dbLastInsertId() {
    // TODO: Implement dbLastInsertId() method.
  }

  /**
   * @return bool
   */
  protected function dbUpdate() {
    // TODO: Implement dbUpdate() method.
  }

}
