<?php
/**
 * Created by Gorlum 10.08.2016 18:05
 */

/**
 * Created by Gorlum 10.08.2016 12:40
 */
interface IEntityContainer {
  /**
   * IEntityContainer constructor.
   */
  public function __construct();

  /**
   * Set properties data from external source
   *
   * 'propertyName' => array(
   *    P_DB_FIELD => 'fieldNameInDb',
   * )
   *
   * @param array $properties
   */
  public function setProperties($properties);

  /**
   * Clears only properties which declared in $properties array
   */
  public function clearProperties();

  /**
   * Import DB row state into object properties
   *
   * @param array $row
   */
  public function importRow($row);

  /**
   * Exports object properties to DB row state WITHOUT ID
   *
   * Useful for INSERT operations
   *
   * @return array
   */
  public function exportRow();

  /**
   * @return bool
   */
  public function isEmpty();

  /**
   * @return bool
   */
  public function isNew();

}
