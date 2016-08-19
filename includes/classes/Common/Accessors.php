<?php
/**
 * Created by Gorlum 19.08.2016 20:35
 */

namespace Common;

/**
 * Accessors storage
 *
 * TODO - make magic method access to accessors ????????
 *
 * @package Common
 */
class Accessors {

  /**
   * Array of accessors - getters/setters/etc
   *
   * @var callable[][]
   */
  protected $accessors = array();


  /**
   * @param string   $varName
   * @param string   $processor
   * @param callable $callable
   *
   * @throws \Exception
   */
  public function setAccessor($varName, $processor, $callable) {
    if (empty($callable)) {
      return;
    }

    if (is_callable($callable)) {
      $this->accessors[$varName][$processor] = $callable;
    } else {
      throw new \Exception('Error assigning callable in ' . get_called_class() . '! Callable typed [' . $processor . '] is not a callable or not accessible in the scope');
    }
  }

  /**
   * @param string $varName
   * @param string $processor
   *
   * @return callable|null
   */
  public function getAccessor($varName, $processor) {
    return isset($this->accessors[$varName][$processor]) ? $this->accessors[$varName][$processor] : null;
  }

  /**
   * @param string $varName
   * @param string $processor
   *
   * @return bool
   */
  public function haveAccessor($varName, $processor) {
    return isset($this->accessors[$varName][$processor]);
  }

  // TODO

//  /**
//   * @param \Entity\EntityModel     $model
//   * @param \Entity\EntityContainer $container
//   * @param string           $processor
//   */
//  protected function processRow($model, $container, $processor) {
//    foreach ($model->getProperties() as $propertyName => $propertyData) {
//      $fieldName = !empty($propertyData[P_DB_FIELD]) ? $propertyData[P_DB_FIELD] : '';
//      if ($this->haveAccessor($propertyName, $processor)) {
//        call_user_func_array($this->getAccessor($propertyName, $processor), array($container, $propertyName, $fieldName));
//      } elseif ($fieldName) {
//        if ($processor == P_CONTAINER_IMPORT) {
//          $container->$propertyName = isset($container->row[$fieldName]) ? $container->row[$fieldName] : null;
//        } else {
//          $container->row += array($fieldName => $container->$propertyName);
//        }
//      }
//      // Otherwise it's internal field - filled and used internally
//    }
//  }

  /**
   * @param $varName
   * @param $processor
   * @param array $params
   *
   * @return mixed
   * @throws \Exception
   */
  public function invokeProcessor($varName, $processor, $params) {
    if(!$this->haveAccessor($varName, $processor)) {
      throw new \Exception('No processor found '); // TODO - add more sense
    }

    return call_user_func_array($this->getAccessor($varName, $processor), $params);
  }

}
