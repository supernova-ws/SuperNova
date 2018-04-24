<?php
/**
 * Created by Gorlum 21.04.2018 16:13
 */

namespace Core;

use Common\Interfaces\IContainer;
use Exception;
use Fleet\Fleet;
use Planet\Planet;
use SN;

/**
 * Entity repository
 *
 * Caches Entities for further use
 *
 * Support synonyms to hold class parents along with a children
 * Support transactions
 * DOES NOT support locking
 *
 * @package Core
 */
class RepoV2 implements IContainer {

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
   * @var int[][] $versionId
   */
  protected $versionId = [];

  /**
   * Core\Repository constructor.
   *
   * @param GlobalContainer $gc
   */
  public function __construct(GlobalContainer $gc) { }

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
   * @param array $name - [entityClassName, dbId]
   *
   * @return EntityDb|null
   */
  public function __get($name) {
    return $this->__isset($name) ? $this->repo[reset($name)][end($name)] : null;
  }

  /**
   * Get entity verion
   *
   * @param array $name - [entityClassName, dbId]
   *
   * @return int
   */
  protected function version($name) {
    return !empty($version = $this->versionId[$this->getClassName($name)][$this->getDbId($name)]) ? $version : -1;
  }

  /**
   * Retrieve entity from repository or load it from DB (by entity's own load method)
   *
   * Supports transactions and version tracking (between transactions)
   *
   * @param array $name - [entityClassName, dbId]
   *
   * @return EntityDb|null
   *
   * @throws Exception
   */
  public function getOrLoad($name) {
    $className = $this->getClassName($name);
    $dbId = $this->getDbId($name);

    if ($this->__isset($name)) {
      // If entity exists in repo - getting it
      $entity = $this->__get($name);

      // If in transaction and version missmatch - record should be refreshed just for a case
      if (sn_db_transaction_check(false) && $this->version($name) < SN::$transaction_id) {
        // Entity will care by itself - should it be really reloaded or not
        $entity->reload();
      }

      if ($entity->isNew()) {
        $this->__unset($name);
      }
    } else {
      /**
       * @var EntityDb $entity
       */
      $entity = new $className();
      $entity->dbLoadRecord($dbId);

      if (!$entity->isNew()) {
        $this->__set($name, $entity);
      }
    }

    if ($entity->isNew()) {
      unset($entity);
      $entity = null;
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
   * @param int|string $fleetId
   *
   * @return Fleet|EntityDb|null
   *
   * @throws Exception
   */
  public function getFleet($fleetId) {
    return $this->getOrLoad([Fleet::class, $fleetId]);
  }

  /**
   * Writes entity to repository
   *
   * Entity should not be already set - otherwise exception raised
   *
   * @param array    $name - [entityClassName, dbId]
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
    $this->versionId[$className][$dbId] = SN::$transaction_id;
  }

  /**
   * @param array $name - [entityClassName, dbId]
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
   * @param array $name - [entityClassName, dbId]
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
   * @param array $name - [entityClassName, dbId]
   *
   * @return void
   */
  public function __unset($name) {
    if (!($className = $this->getClassName($name))) {
      return;
    }

    $dbId = $this->getDbId($name);
    unset($this->repo[$className][$dbId]);
    unset($this->versionId[$className][$dbId]);
  }

  /**
   * Is container contains no data
   *
   * @return bool
   */
  public function isEmpty() {
    return empty($this->repo) && empty($this->versionId);
  }

  /**
   * Clears container contents
   */
  public function clear() {
    $this->repo = [];
    $this->versionId = [];
  }

}
