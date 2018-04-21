<?php
/**
 * Created by Gorlum 21.04.2018 16:13
 */

namespace Core;

use Common\Interfaces\IContainer;
use Exception;
use Planet\Planet;
use SN;

/**
 * Entity repository
 *
 *
 *
 *
 * @package Core
 */
class RepoV2 implements IContainer {

//  /**
//   * @var GlobalContainer $gc
//   */
//  protected $gc;
//
//  /**
//   * @var StorageV2 $storage
//   */
//  protected $storage;

  /**
   * List of synonyms
   *
   * @var string[] $synonyms
   */
  protected $synonyms = [];

  /**
   * @var EntityDb[][] $repo
   */
  protected $repo = [];

  /**
   * @var int[][] $version
   */
  protected $version = [];

  /**
   * Core\Repository constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct(GlobalContainer $gc) {
//    $this->gc = $gc;
//    $this->storage = $gc->storageV2;
  }

  /**
   * @param string $className
   * @param string $synonym
   *
   * @return $this
   */
  public function addSynonym($className, $synonym) {
    $this->synonyms[$className] = $synonym;

    return $this;
  }

  /**
   * @param mixed $name
   *
   * @return EntityDb|null
   */
  public function __get($name) {
    return $this->__isset($name) ? $this->repo[reset($name)][end($name)] : null;
  }

  /**
   * @param mixed $name
   *
   * @return EntityDb|null
   *
   * @throws Exception
   */
  public function getOrLoad($name) {
    if ($this->__isset($name)) {
      return $this->__get($name);
    }

    $className = $this->getClassName($name);
    $dbId = $this->getDbId($name);
    /**
     * @var EntityDb $entity
     */
    $entity = new $className();
    $entity->dbLoadRecord($dbId);

    if ($entity->isNew()) {
      unset($entity);
      $entity = null;
    } else {
      $this->__set($name, $entity);
    }

    return $entity;
  }

  /**
   * @param int|string $planetId
   *
   * @return Planet|EntityDb|null
   *
   * @throws Exception
   */
  public function getPlanet($planetId) {
    return $this->getOrLoad([Planet::class, $planetId]);
  }

  /**
   * Writes entity to repository
   *
   * Entity should not be already set - otherwise exception raised
   *
   * @param mixed    $name
   * @param EntityDb $value
   *
   * @return void
   * @throws Exception
   */
  public function __set($name, $value) {
    if (!($className = $this->getClassName($name))) {
      throw new Exception("Mallformed name " . var_export($name, true) . " in RepoV2::__set(). ");
    }

    if ($this->__isset($name) && $this->__get($name) !== $value) {
      throw new Exception("Trying to overwrite entity [" . implode(',', $name) . "] which already set. Unset it first!");
    }

    $dbId = $this->getDbId($name);
    $this->repo[$className][$dbId] = $value;
    $this->version[$className][$dbId] = SN::$transaction_id;
  }

  /**
   * @param array $name
   *
   * @return string
   */
  protected function getClassName($name) {
    if (!$this->isNameValid($name)) {
      return false;
    }

    $className = reset($name);
    if (!empty($this->synonyms[$className])) {
      $className = $this->synonyms[$className];
    }

    return $className;
  }

  /**
   * @param array $name
   *
   * @return mixed
   */
  protected function getDbId($name) {
    $dbId = end($name);

    return $dbId;
  }

  /**
   * @param array $name - [entityClassName, dbId]
   *
   * @return bool
   */
  protected function isNameValid($name) {
    return is_array($name) && count($name) == 2;
  }

  /**
   * Checks if entity with specified DB ID registered in repository
   *
   * Repository also holds entity current version (for transaction support)
   * Repository stores ONLY EXISTING OBJECTS - so it's not caches unsuccessful data retrieves
   *
   * @param array $name - [entityClassName, dbId]
   *
   * @return bool
   */
  public function __isset($name) {
    if (!($className = $this->getClassName($name))) {
      return false;
    }
    $dbId = $this->getDbId($name);

    return is_array($this->repo[$className]) && isset($this->repo[$className][$dbId]);
  }

  /**
   * @param array $name
   *
   * @return void
   */
  public function __unset($name) {
    if (!($className = $this->getClassName($name))) {
      return;
    }

    $dbId = $this->getDbId($name);
    unset($this->repo[$className][$dbId]);
    unset($this->version[$className][$dbId]);
  }

  /**
   * Is container contains no data
   *
   * @return bool
   */
  public function isEmpty() {
    return empty($this->repo) && empty($this->version);
  }

  /**
   * Clears container contents
   */
  public function clear() {
    $this->repo = [];
    $this->version = [];
  }

}
