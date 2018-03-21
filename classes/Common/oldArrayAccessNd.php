<?php

namespace Common;
use ArrayAccess;

/**
 * Класс упрощает операции с многомерными индексами для ArrayAccess - старая версия
 * Многомерные индексы могут передаваться в $offset в виде массива
 * Например: array('test', 1, 2, 3) будет соответствовать обращению test[1][2][3]
 *
 * Таким образом работа с многомерными массивами может быть спроецирована на любой объект, который умеет в пары Ключ-Значение и поддерживает стандартные magic methods __isset, __get, __set и __unset
 *
 * Если объект-потомок поддерживает отложенную запись - ему нужно реализовать так же функцию __flush()
 *
 */
abstract class oldArrayAccessNd implements ArrayAccess {

  abstract public function __get($offset);

  abstract public function __set($offset, $value = null);

  abstract public function __isset($offset);

  abstract public function __unset($offset);

  public function __flush() {
    return true;
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Whether a offset exists
   * @link http://php.net/manual/en/arrayaccess.offsetexists.php
   *
   * @param mixed $offset <p>
   * An offset to check for.
   * </p>
   *
   * @return boolean true on success or false on failure.
   * </p>
   * <p>
   * The return value will be casted to boolean if non-boolean was returned.
   */
  public function offsetExists($offset) {
    !is_array($offset) ? $offset = array($offset) : false;

    $current_leaf = $this->__get(reset($offset));
    while (($leaf_index = next($offset)) !== false) {
      if (!isset($current_leaf) || !is_array($current_leaf) || !isset($current_leaf[$leaf_index])) {
        unset($current_leaf);
        break;
      }
      $current_leaf = $current_leaf[$leaf_index];
    }

    return isset($current_leaf);
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to retrieve
   * @link http://php.net/manual/en/arrayaccess.offsetget.php
   *
   * @param mixed $offset <p>
   * The offset to retrieve.
   * </p>
   *
   * @return mixed Can return all value types.
   */
  public function offsetGet($offset) {
    $result = null;

    !is_array($offset) ? $offset = array($offset) : false;

    if ($this->offsetExists($offset)) {
      $result = $this->__get(reset($offset));
      while (($leaf_index = next($offset)) !== false) {
        $result = $result[$leaf_index];
      }
    }

    return $result;
  }


  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to set
   * @link http://php.net/manual/en/arrayaccess.offsetset.php
   *
   * @param mixed $offset <p>
   * The offset to assign the value to.
   * </p>
   * @param mixed $value <p>
   * The value to set.
   * </p>
   *
   * @return void
   */
  public function offsetSet($offset, $value = null) {
    // Если нет никакого индекса - значит нечего записывать
    if (!isset($offset) || (is_array($offset) && empty($offset))) {
      return;
    }

    // Если в массиве индекса только один элемент - значит это просто индекс
    if (is_array($offset) && count($offset) == 1) {
      // Разворачиваем его в индекс
      $offset = array(reset($offset) => $value);
      unset($value);
      // Дальше будет использоваться стандартный код для пары $option, $value
    }

    // Адресация многомерного массива через массив индексов в $option
    if (is_array($offset) && isset($value)) {
      // TODO - а не переделать ли это всё на __isset() ??
      // Вытаскиваем корневой элемент
      $root = $this->__get(reset($offset));
      $current_leaf = &$root;
      while (($leaf_index = next($offset)) !== false) {
        !is_array($current_leaf[$leaf_index]) ? $current_leaf[$leaf_index] = array() : false;
        $current_leaf = &$current_leaf[$leaf_index];
      }
      if ($current_leaf != $value) {
        $current_leaf = $value;
        // Сохраняем данные с корня
        $this->__set(reset($offset), $root);
      }
    } else {
      // Пакетная запись из массива ключ -> значение
      !is_array($offset) ? $offset = array($offset => $value) : false;

      foreach ($offset as $key => $value) {
        $this->__get($key) !== $value ? $this->__set($key, $value) : false;
      }
    }

    $this->__flush(); // Сбрасывем кэш - если есть его поддержка
  }

  /**
   * (PHP 5 &gt;= 5.0.0)<br/>
   * Offset to unset
   * @link http://php.net/manual/en/arrayaccess.offsetunset.php
   *
   * @param mixed $offset <p>
   * The offset to unset.
   * </p>
   *
   * @return void
   */
  public function offsetUnset($offset) {
    // Если нет никакого индекса - значит нечего записывать
    if (!isset($offset) || (is_array($offset) && empty($offset))) {
      return;
    }

    !is_array($offset) ? $offset = array($offset) : false;

    if ($this->offsetExists($offset)) {
      // Перематываем массив в конец
      $key_to_delete = end($offset);
      $parent_offset = $offset;
      array_pop($parent_offset);
      if (!count($parent_offset)) {
        // В массиве был один элемент - мы удаляем в корне. Просто удаляем элемент
        $this->__unset($key_to_delete);
      } else {
        // Получаем родительское дерево
        $parent_element = $this->offsetGet($parent_offset);
        // Удаляем из него элемент
        unset($parent_element[$key_to_delete]);
        // Записываем измененное родительское дерево назад
        $this->offsetSet($parent_offset, $parent_element);
      }
    }

    $this->__flush(); // Сбрасывем кэш - если есть его поддержка
  }
}
