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
   * @param string     $className
   * @param int|string $objectId
   *
   * @return EntityDb|null
   */
  public function get($className, $objectId) {
    return $this->is_set($className, $objectId) ? $this->repo[$className][$objectId] : null;
  }

  /**
   * Get entity version
   *
   * @param $className
   * @param $objectId
   *
   * @return int
   */
  protected function version($className, $objectId) {
    return !empty($version = $this->versionId[$className][$objectId]) ? $version : -1;
  }

  /**
   * Retrieve entity from repository or load it from DB (by entity's own load method)
   *
   * Supports transactions and version tracking (between transactions)
   *
   * @param string     $className - fully qualified class name
   * @param int|string $objectId  - entity ID
   *
   * @return EntityDb|null
   *
   * @throws Exception
   */
  public function getOrLoad($className, $objectId) {
    if ($this->is_set($className, $objectId)) {
      // If entity exists in repo - getting it
      $entity = $this->get($className, $objectId);

      // If in transaction and version missmatch - record should be refreshed just for a case
      if (sn_db_transaction_check(false) && $this->version($className, $objectId) < SN::$transaction_id) {
        // Entity will care by itself - should it be really reloaded or not
        $entity->reload();
      }

      if ($entity->isNew()) {
        $this->un_set($className, $objectId);
      }
    } else {
      /**
       * @var EntityDb $entity
       */
      $entity = new $className();
      $entity->dbLoadRecord($objectId);

      if (!$entity->isNew()) {
        $this->set($entity);
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
    return $this->getOrLoad(Planet::class, $planetId);
  }

  /**
   * @param int|string $fleetId
   *
   * @return Fleet|EntityDb|null
   *
   * @throws Exception
   */
  public function getFleet($fleetId) {
    return $this->getOrLoad(Fleet::class, $fleetId);
  }


  /**
   * Writes entity to repository
   *
   * Entity should not be already in registry - otherwise exception raised
   *
   * @param EntityDb $value
   *
   * @return bool
   * @throws Exception
   */
  public function set($value) {
    $className = $this->getClassStoreName(get_class($value));
    $objectId = $value->id;

    if (empty($value->id)) {
      throw new Exception("Trying to add in registry entity [" . implode(',', [$className, $objectId]) . "] with zero objectId");
    }

    if ($this->is_set($className, $objectId) && $this->get($className, $objectId) !== $value) {
      throw new Exception("Trying to overwrite entity [" . implode(',', [$className, $objectId]) . "] which already set. Unset it first!");
    }

    $this->repo[$className][$objectId] = $value;
    $this->versionId[$className][$objectId] = SN::$transaction_id;

    return true;
  }


  /**
   * @param string $className
   *
   * @return string
   */
  protected function getClassStoreName($className) {
    return !empty($this->synonyms[$className]) ? $this->synonyms[$className] : $className;
  }

  /**
   * @param string     $className
   * @param int|string $objectId
   *
   * @return bool
   */
  public function is_set($className, $objectId) {
    return is_array($this->repo[$className]) && isset($this->repo[$className][$objectId]);
  }

  /**
   * @param string     $className
   * @param int|string $objectId
   *
   * @return void
   */
  public function un_set($className, $objectId) {
    unset($this->repo[$className][$objectId]);
    unset($this->versionId[$className][$objectId]);
  }

  /**
   * @param EntityDb $entity
   *
   * @return void
   */
  public function unsetByEntity($entity) {
    $className = $this->getClassStoreName($entity);
    $objectId = $entity->id;

    unset($this->repo[$className][$objectId]);
    unset($this->versionId[$className][$objectId]);
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

  /**
   * @param array    $name - [entityClassName, objectId]
   * @param EntityDb $value
   *
   * @return void
   * @throws Exception
   *
   * @deprecated
   * @see RepoV2::set()
   */
  public function __set($name, $value) {
    throw new Exception("DEPRECATED! Method RepoV2::__set() is DEPRECATED! Use RepoV2::set() instead!");
  }

  /**
   * @param array $name - [entityClassName, objectId]
   *
   * @return void
   * @throws Exception
   * @deprecated
   */
  public function __get($name) {
    throw new Exception("DEPRECATED! Method RepoV2::__get() is DEPRECATED! Use RepoV2::get() instead!");
  }

  /**
   * Checks if entity with specified DB ID registered in repository
   *
   * Repository also holds entity current version (for transaction support)
   * Repository stores ONLY EXISTING OBJECTS - so it's not caches unsuccessful data retrieves
   *
   * @param array $name - [entityClassName, objectId]
   *
   * @return void
   * @throws Exception
   * @deprecated
   */
  public function __isset($name) {
    throw new Exception("DEPRECATED! Method RepoV2::__isset() is DEPRECATED! Use RepoV2::is_set() instead!");
  }

  /**
   * @param array $name - [entityClassName, objectId]
   *
   * @return void
   * @throws Exception
   * @deprecated
   */
  public function __unset($name) {
    throw new Exception("DEPRECATED! Method RepoV2::__unset() is DEPRECATED! Use RepoV2::un_set() instead!");
  }

}
