<?php
/**
 * Created by Gorlum 13.06.2017 14:09
 */

namespace Player;


use DBAL\ActiveRecordStatic;

class TablePlayer extends ActiveRecordStatic {
  protected static $_primaryIndexField = 'id';
  protected static $_tableName = 'users';


  public static function updateFromArray($array) {
    // Removing authlevel for
    // a) not to trigger hack warnings
    // b) make to use special functions
    unset($array['authlevel']);
    unset($array['metamatter']);
    unset($array['metamatter_total']);

    return parent::updateFromArray($array);
  }

}
