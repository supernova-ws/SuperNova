<?php
/**
 * Created by Gorlum 13.11.2018 15:50
 */

namespace DBAL;

/**
 * Class PropertyDescription
 *
 * @package DBAL
 */
class PropertyDescription {
  const SQL_DATE_TIME = 'Y-m-d H:i:s';

  /**
   * Field name
   *
   * @var string $field
   */
  public $field = '';
  /**
   * Property name
   *
   * @var string $name
   */
  public $name = '';

  /**
   * Property type
   *
   * @var string $type
   */
  public $type = '';
  public $default = null;
  /**
   * Is value in this field is mandatory
   *
   * @var bool $mandatory
   */
  public $mandatory = false;

  /**
   * Callback to convert from field to property
   *
   * function (mixed $value) : mixed;
   *
   * @var callable $toProperty
   */
  public $toProperty = null;
  /**
   * Callback to convert from property to field
   *
   * function (mixed $value) : mixed;
   *
   * @var callable $fromProperty
   */
  public $fromProperty = null;

  /**
   * Callback to convert from user input to property
   *
   * If no $description specified - some defaults are applied
   *
   * function (mixed $value, PropertyDescription $description = null) : mixed;
   *
   * @var callable $fromUser
   */
  public $fromUser = null;

//  /**
//   * function (string $name, mixed $value) : [string $ptlName, mixed $ptlValue];
//   *
//   * @var callable $toPtl
//   * @deprecated
//   */
//  public $toPtl = null;

  /**
   * Callback to add extra information/convert data from property for PTL
   *
   * function (string $name, mixed $value, array $ptlResult) : array $ptlResult;
   *
   * @var callable $toPtl
   */
  public $toPtl = null;

  /**
   * @param string $propertyName
   *
   * @return $this
   */
  public function setName($propertyName) {
    $this->name = $propertyName;

    return $this;
  }

  /**
   * @param DbFieldDescription $fieldDescription
   */
  public function fromDbFieldDescription(DbFieldDescription $fieldDescription) {
    $this->field = $fieldDescription->Field;
    $this->setName($fieldDescription->Field);

    $nonMandatory = false;

    switch (true) {
      case strpos($fieldDescription->Type, 'int') === 0:
      case strpos($fieldDescription->Type, 'tinyint') === 0:
        $this->type       = 'integer';
        $this->default    = !empty($fieldDescription->Default) ? intval($fieldDescription->Default) : 0;
        $this->toProperty = [self::class, 'toInteger'];
        $this->fromUser   = [self::class, 'intFromUser'];
      break;

      /** @noinspection PhpMissingBreakStatementInspection */
      case strpos($fieldDescription->Type, 'mediumtext') === 0:
        $nonMandatory = true;
      case strpos($fieldDescription->Type, 'varchar') === 0:
        $this->type    = 'string';
        $this->default = !empty($fieldDescription->Default) || $fieldDescription->Default === null
          ? $fieldDescription->Default : '';
      break;

      case strpos($fieldDescription->Type, 'datetime') === 0:
        $this->type = 'datetime';
//        $this->default      = !empty($fieldDescription->Default) || $fieldDescription->Default === null
//          ? $fieldDescription->Default : '0000-00-00 00:00:00';
        // TODO - current timestamp ????
        if ($fieldDescription->Default === null) {
          $this->default = null;
        } elseif (!empty($fieldDescription->Default)) {
          $this->default = strtotime($fieldDescription->Default);
        } else {
          $this->default = 0;
        }
        $this->toProperty   = [self::class, 'toUnixTime'];
        $this->fromProperty = [self::class, 'fromUnixTime'];
        $this->fromUser     = [self::class, 'datetimeFromUser'];
//        $this->toPtl        = [self::class, 'ptlUnixTime'];
      break;

      default:
        die("Unsupported field type '{$fieldDescription->Type}' in " . get_called_class());
      break;
    }

    // Main index is non mandatory
    if ($fieldDescription->Extra === 'auto_increment' && $fieldDescription->Key === 'PRI') {
      $nonMandatory = true;
    }


    // If this field is not null and default value is not set - then this field is mandatory
    if ($fieldDescription->Null === 'NO' && $fieldDescription->Default === null && !$nonMandatory) {
      $this->mandatory = true;
    }

  }

  /**
   * @param string|null $value
   *
   * @return int
   */
  public static function toInteger($value) {
    return $value === null ? null : intval($value);
  }
  public static function intFromUser($value, PropertyDescription $description = null) {
    if ($value === null) {
      $value = $description->default;
    } else {
      $value = intval($value);
    }

    return $value;
  }

  public static function toUnixTime($value) {
    return $value !== null ? strtotime($value) : $value;
  }

  public static function fromUnixTime($value) {
    return $value !== null ? date(self::SQL_DATE_TIME, $value) : $value;
  }

  /**
   * Converts input from user to datetime (unix timestamp internally)
   *
   * @param mixed                    $value
   * @param PropertyDescription|null $description
   *
   * @return mixed
   */
  public static function datetimeFromUser($value, PropertyDescription $description = null) {
    $value = strtotime($value);
    if ($value === null) {
      $value = $description->default;
    }

    return $value;
  }

  public static function jsonDecode($value) {
    $value = json_decode($value, true);

    return $value;
  }

  public static function jsonEncode($value) {
    $value = json_encode($value);

    return $value;
  }

//  /**
//   * @param string $name
//   * @param mixed  $value
//   *
//   * @return array
//   */
//  public static function ptlUnixTime($name, $value) {
//    return [$name . '_STRING', empty($value) ? '' : date(self::SQL_DATE_TIME, $value)];
//  }

}
