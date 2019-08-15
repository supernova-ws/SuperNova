<?php
/**
 * Created by Gorlum 13.11.2018 14:07
 */

namespace DBAL;


use Common\AccessLoggedTranslatedV2;
use Core\GlobalContainer;
use Exception;
use SN;

/**
 * RecordV2 interface for access Storage data
 *
 * Classes starting from this one and above inheritance tree made for translation DB field names to properties with all bells and whistles
 *
 * Class itself represents single table where derived object represents single record
 * Object  handles insert/delete/update with single ::save() function based on it's internal state
 *
 *
 * @property int|string $id
 *
 * @package DBAL
 */
class RecordV2 extends AccessLoggedTranslatedV2 {
  const _INDEX_FIELD = 'id';
  const _PREFIX1 = 'Record';
  const _PREFIX2 = 'RecordV2';

  /**
   * @var StorageSqlV2|null $storage
   */
  // TODO - replace with IStorage
  protected static $storage = 0;

  protected static $_tableName = '';
  protected static $_indexField = self::_INDEX_FIELD;


  protected $isNew = true;
  protected $toDelete = false;
  protected $deleted = false;

  public static function tableName() {
    if (empty(static::$_tableName)) {
      if (strpos(static::class, self::_PREFIX1) !== 0) {
        static::$_tableName = substr(static::class, strlen(self::_PREFIX1));
      } elseif (strpos(static::class, self::_PREFIX2) !== 0) {
        static::$_tableName = substr(static::class, strlen(self::_PREFIX2));
      } else {
        static::$_tableName = static::class;
      }
    }

    return static::$_tableName;
  }

  public static function indexField() {
    return static::$_indexField;
  }

  public function __construct(GlobalContainer $services = null) {
    parent::__construct($services);

    static::setStorage($services->storageSqlV2);

    if (empty(static::$_properties)) {
      $fields = static::$storage->fields(static::tableName());

      $this->importFieldDefinitions($fields);
    }
  }

  /**
   * @param StorageSqlV2 $storage
   */
  public static function setStorage(StorageSqlV2 $storage) {
    if (static::$storage === 0) {
      die('CRITICAL! No "static::$storage" property declared in "' . get_called_class() . '" class! Report to developer!');
    } elseif (empty(static::$storage)) {
      static::$storage = $storage;
    }
  }

  /**
   * @return StorageSqlV2|null
   */
  public static function getStorage() {
    return static::$storage;
  }


  /**
   * Handles saving object to storage
   *
   * @throws Exception
   */
  public function save() {
    if ($this->toDelete) {
      $this->delete();
    } elseif ($this->isEmpty()) {
      // Object is empty - no valuable info here
      if ($this->isNew) {
        /*
        Do nothing
       TODO - Exception ?
       Basically it's throw-out instance of class which can be forgotten
       May be it's was made for access to some non-static property. Or for something else - it's doesn't matters
       What matters that this instance shouldn't (and can't) be stored in Storage
        */
      } else {
        // Deleting empty object
        $this->delete();
      }
    } else {
      if ($this->isNew) {
        $this->insert();
      } else {
        $this->update();
      }
    }
  }

  protected function delete() {
    if (!empty($this->id)) {
      static::$storage->delete(
        static::tableName(),
        [static::$_properties[self::_INDEX_FIELD]->field => $this->id]
      );
    } else {
      $this->id = null;
    }

    $this->accept();

    $this->toDelete = true;
    $this->isNew    = true;
  }

  public function toFieldArray($useDefaults = false, $skipId = false) {
    $array = parent::toFieldArray($useDefaults);
    if ($skipId) {
      unset($array[static::$_properties[self::_INDEX_FIELD]->field]);
    }

    return $array;
  }

  /**
   * @return int|null|string
   *
   * @throws Exception
   */
  protected function insert() {

    // Filling absent property as default
    // TODO - add `mandatory` field that only them was filled with defaults
//    foreach (static::$_properties as $name => $description) {
//      if (!isset($this->$name)) {
//        $this->$name = static::$_defaults[$name];
//      }
//    }

    // Inserting records
    $fieldValues = $this->toFieldArray(false, true);

    $id = static::$storage->insert(static::tableName(), $fieldValues);
    if (!empty($id)) {
      $this->id = $id;
      $this->accept();

      $this->isNew = false;

      $this->findById($id);
    }

    return $id;
  }

  protected function update() {
    static::$storage->update(
      static::tableName(),
      [static::$_properties[self::_INDEX_FIELD]->field => $this->id],
      $this->changesToFields($this->_changes),
      $this->changesToFields($this->_deltas)
    );

    $this->accept();

    $this->isNew = false;

    $this->findById($this->id);
  }

  /**
   * @param int|string $id
   *
   * @return $this
   */
  public function findById($id) {
    $array = static::$storage->findFirst(static::tableName(), [static::$_properties[self::_INDEX_FIELD]->field => $id]);

    $this->replaceFromFieldArray($array);

    return $this;
  }

  protected function replaceFromFieldArray(array $array) {
    $this->clear();
    if (!empty($array)) {
      $this->fromFieldArray($array);

      $this->accept();

      $this->isNew = false;
    }
  }

  public function clear() {
    parent::clear();

    $this->isNew    = true;
    $this->toDelete = false;
  }

  public function isDeleted() {
    return $this->toDelete && $this->isNew;
  }

  /**
   * Record is empty if one of the mandatory field(s) is empty
   *
   * @return bool
   */
  public function isEmpty() {
    $isEmpty = false;

    foreach (static::$_mandatory as $name => $cork) {
      if (!isset($this->$name)) {
        $isEmpty = true;
        break;
      }
    }

    return $isEmpty;
  }


  public function markDelete($mark = true) {
    $this->toDelete = $mark;
  }


  /**
   * @param $id
   *
   * @return null|static
   */
  public static function findByIdStatic($id) {
    $record = new static(SN::$gc);
    $record->findById($id);

    return $record->isNew ? null : $record;
  }


  /**
   * @param GlobalContainer $services
   *
   * @return $this[]
   */
  public static function findAllStatic(GlobalContainer $services) {
    $result = [];

    static::setStorage($services->storageSqlV2);

    $iter = static::$storage->findIterator(static::tableName(), []);

    foreach ($iter as $fields) {
      $promo = new static($services);
      $promo->fromFieldArray($fields);
      $promo->isNew = false;
      $result[]     = $promo;
    }

    return $result;
  }

  /**
   * @return bool
   */
  public function isNew() {
    return $this->isNew;
  }
}
