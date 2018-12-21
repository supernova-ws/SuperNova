<?php
/**
 * Created by Gorlum 14.11.2018 0:27
 */

namespace Common;

use DBAL\DbFieldDescription;
use DBAL\PropertyDescription;

/**
 * Class AccessLoggedTranslatedV2
 *
 * Class introduces field-to-property translation
 *
 * "Fields" - input data with external namespace and vague types translated to "properties" - internal representation with own namespace and fixed types
 *
 * @package Common
 */
class AccessLoggedTranslatedV2 extends AccessLoggedV2 {
  /**
   * Field name translation to property names
   *
   * @var string[] $_fieldsToProperties
   */
  protected static $_fieldsToProperties = [];

  /**
   * @var PropertyDescription[] $_fields
   */
  protected static $_properties = [];
  /**
   * Default values
   *
   * @var array $_defaults
   */
  protected static $_defaults = [];
  /**
   * Mandatory field list
   *
   * @var array $_mandatory
   */
  protected static $_mandatory = [];

  /**
   * Imports fields definitions from Storage
   *
   * Importing field type, defaults and creating field->property mappings in $_properties array
   *
   * @param DbFieldDescription[] $fields
   */
  protected function importFieldDefinitions($fields) {
    static::$_properties = [];
    static::$_defaults   = [];
    static::$_mandatory  = [];

    foreach ($fields as $fieldName => $fieldDescription) {
      $property = new PropertyDescription();
      $property->fromDbFieldDescription($fieldDescription);

      $propertyName =
        !empty(static::$_fieldsToProperties[$property->field])
          ? static::$_fieldsToProperties[$property->field]
          : $property->field;

      static::$_properties[$propertyName] = $property->setName($propertyName);
      // TODO - DEFAULT SHOULD BE CONVERTED TO PROPERTY!!!!
      static::$_defaults[$propertyName]   = $property->default;

      if ($property->mandatory) {
        static::$_mandatory[$propertyName] = true;
      }
    }

//    var_dump(static::$_properties);
  }


  /**
   * Converts one array (of properties, fields, user inputs, etc) to another with or w/o callback function
   *
   * @param array  $array
   * @param string $callBackName Name of Property callback which will be used. Can be empty for no callback
   * @param bool   $useDefaults  Should defaults used if property/field is absent from input array
   * @param bool   $useFieldName Use field name as array key. If empty - property name will be used
   *
   * @return array
   */
  protected function convertToPropertyWithCallback($array, $callBackName = '', $useDefaults = false, $useFieldName = false) {
    $result = [];

    foreach (static::$_properties as $propName => $property) {
      if (array_key_exists($propName, $array)) {
        $value = $array[$propName];
      } elseif ($useDefaults) {
        $value = static::$_defaults[$propName];
      } else {
        continue;
      }

      if (!empty($callBackName) && is_callable($property->$callBackName)) {
        $call  = $property->$callBackName;
        $value = $call($value, $property);
      }

      $result[$useFieldName ? $property->field : $propName] = $value;
    }

    return $result;
  }


  /**
   *
   * (remark) Here too much to redo is with ::convertToPropertyWithCallback()
   *
   * @param array $fieldArray
   *
   * @return static
   */
  public function fromFieldArray(array $fieldArray) {
    foreach (static::$_properties as $propName => $property) {
      if (!array_key_exists($property->field, $fieldArray)) {
        continue;
      }

      $value = $fieldArray[$property->field];

      if (is_callable($property->toProperty)) {
        $call  = $property->toProperty;
        $value = $call($value);
      }

      $this->$propName = $value;
    }

    return $this;
  }

  /**
   * Converts record to field array
   *
   * Field array is basically ready to be saved to Storage (some kind of DB)
   * Check for mandatory fields also applied
   *
   * @param bool $useDefaults - Use default values if there is no original value
   *
   * @return array - field array to save
   */
  public function toFieldArray($useDefaults = false) {
    $fieldValues = [];

    // Making local copy of mandatory properties list
    $mandatory = static::$_mandatory;

    foreach (static::$_properties as $propName => $property) {
      if (array_key_exists($propName, $this->values)) {
        $value = $this->$propName;
      } elseif ($useDefaults) {
        $value = static::$_defaults[$propName];
      } else {
        continue;
      }

      if (is_callable($property->fromProperty)) {
        $call  = $property->fromProperty;
        $value = $call($value);
      }

      $fieldValues[$property->field] = $value;

      // Removing this property from mandatory list - if it present
      unset($mandatory[$propName]);
    }

    if (!empty($mandatory)) {
      var_dump($mandatory);
      die('NO MANDATORY FIELDS'); // TODO - Exception
    }

    return $fieldValues;
  }

  /**
   * Convert changes and deltas from properties to fields for update
   *
   * @param $propertyArray
   *
   * @return array
   */
  protected function changesToFields($propertyArray) {
    return $this->convertToPropertyWithCallback($propertyArray, 'fromProperty', false, true);
  }

  /**
   * Mass-assign properties from user input
   *
   * Makes internal conversion types
   * DOES NOT fill default values
   * TODO - ADD VALIDATION
   *
   * @param array $propertyArray
   */
  public function assignProperties(array $propertyArray, $validate = false) {
    $properties = $this->convertToPropertyWithCallback($propertyArray, 'fromUser', false, false);

    foreach ($properties as $propName => $value) {
      $this->$propName = $value;
    }
  }

  /**
   * Fills empty field with default values
   */
  public function fillDefaults() {
    foreach (static::$_properties as $propertyName => $propertyDescription) {
      if (!isset($this->$propertyName)) {
        $this->$propertyName = $propertyDescription->default;
      }
    }
  }

}
