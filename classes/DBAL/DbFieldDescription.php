<?php
/**
 * Created by Gorlum 04.10.2017 8:55
 */

namespace DBAL;

/**
 * Class DbFieldDescription
 * @package DBAL
 *
 * Objects of this class contains MySql field description
 */
class DbFieldDescription {
  public $Field;
  public $Type;
  public $Collation;
  public $Null;
  public $Key;
  public $Default;
  public $Extra;
  public $Privileges;
  public $Comment;

  /**
   * @param array $mySqlDescription
   */
  public function fromMySqlDescription($mySqlDescription) {
    $this->Field = isset($mySqlDescription['Field']) ? $mySqlDescription['Field'] : null;
    $this->Type = isset($mySqlDescription['Type']) ? $mySqlDescription['Type'] : null;
    $this->Collation = isset($mySqlDescription['Collation']) ? $mySqlDescription['Collation'] : null;
    $this->Null = isset($mySqlDescription['Null']) ? $mySqlDescription['Null'] : null;
    $this->Key = isset($mySqlDescription['Key']) ? $mySqlDescription['Key'] : null;
    $this->Default = isset($mySqlDescription['Default']) ? $mySqlDescription['Default'] : null;
    $this->Extra = isset($mySqlDescription['Extra']) ? $mySqlDescription['Extra'] : null;
    $this->Privileges = isset($mySqlDescription['Privileges']) ? $mySqlDescription['Privileges'] : null;
    $this->Comment = isset($mySqlDescription['Comment']) ? $mySqlDescription['Comment'] : null;

    return $this;
  }

}
