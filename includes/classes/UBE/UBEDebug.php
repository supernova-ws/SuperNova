<?php

/**
 * Class UBEDebug
 */
class UBEDebug {

  /**
   *
   *
   * @version 41a6.49
   */
  public static function unit_dump_header() {
    if(!defined('DEBUG_UBE')) {
      return;
    }

    print('<table border="1">');
    print('<tr>');
    print('<th>desc</th>');
    print('<th>unit_id</th>');
    print('<th colspan="2">count</th>');
//  print('<th>type</th>');
//  print('<th>attack_bonus</th>');
//  print('<th>shield_bonus</th>');
//  print('<th>armor_bonus</th>');
//  print('<th>unit_randomized_attack</th>');
//  print('<th>unit_randomized_shield</th>');
//  print('<th>unit_randomized_armor</th>');
    print('<th colspan="2">units_destroyed</th>');
//  print('<th>pool_attack</th>');
    print('<th colspan="2">pool_shield</th>');
    print('<th colspan="2">pool_armor</th>');
    print('<th colspan="2">boom</th>');
    print('<th colspan="2">attack_income</th>');
//  print('<th>units_lost</th>');
//  print('<th>units_restored</th>');
//  print('<th>capacity</th>');
    print('<th>armor_share</th>');
    print('</tr>');
  }

  /**
   * @param UBEUnit      $unit
   * @param string       $desc
   * @param UBEUnit|null $before
   *
   * @version 41a6.49
   */
  public static function unit_dump(UBEUnit $unit, $desc = '', UBEUnit $before = null) {
    if(!defined('DEBUG_UBE')) {
      return;
    }

    $classLocale = classLocale::$lang;

    print('<tr align="right">');
    print("<td>{$desc}</td>");
    print("<td>[{$unit->unitId}]{$classLocale['tech_short'][$unit->unitId]}</td>");
//  print("<td>" . unit_dump_delta($current, 'count', $before) . "</td>");
    static::unit_dump_delta($unit, 'count', $before);
//  print("<td>" . $this->type . "</td>");
//  print("<td>" . $this->attack_bonus . "</td>");
//  print("<td>" . $this->shield_bonus . "</td>");
//  print("<td>" . $this->armor_bonus . "</td>");
//  print("<td>" . $this->unit_randomized_attack . "</td>");
//  print("<td>" . $this->unit_randomized_shield . "</td>");
//  print("<td>" . $this->unit_randomized_armor . "</td>");
    static::unit_dump_delta($unit, 'units_destroyed', $before);
//  $this->unit_dump_delta($this, 'pool_attack', $before);
    static::unit_dump_delta($unit, 'pool_shield', $before);
    static::unit_dump_delta($unit, 'pool_armor', $before);
    static::unit_dump_delta($unit, 'unit_count_boom', $before);
    static::unit_dump_delta($unit, 'attack_income', $before);
//  print("<td>" . $this->units_lost . "</td>");
//  print("<td>" . $this->units_restored . "</td>");
//  print("<td>" . $this->capacity . "</td>");
    print("<td>" . round($unit->share_of_side_armor, 4) . "</td>");
    print('</tr>');
  }


  /**
   *
   *
   * @version 41a6.49
   */
  public static function unit_dump_footer() {
    if(!defined('DEBUG_UBE')) {
      return;
    }

    print('</table><br>');
  }

  /**
   * @param UBEUnit $attacking_unit_pool
   * @param UBEUnit $defending_unit_pool
   * @param int     $defending_fleet_id
   *
   * @return UBEUnit
   */
  public static function unit_dump_defender(UBEUnit $attacking_unit_pool, UBEUnit $defending_unit_pool, $defending_fleet_id) {
    if(!defined('DEBUG_UBE')) {
      return null;
    }

    $classLocale = classLocale::$lang;

    print("[{$attacking_unit_pool->unitId}]{$classLocale['tech'][$attacking_unit_pool->unitId]}" .
      ' attacks ' .
      $defending_fleet_id . '@' . "[{$defending_unit_pool->unitId}]{$classLocale['tech'][$defending_unit_pool->unitId]}" .
      ' with ' . pretty_number($defending_unit_pool->attack_income) .
      '<br>'
    );
    $before = clone $defending_unit_pool;
    static::unit_dump($defending_unit_pool, 'before');

    return $before;
  }

  /**
   * @param UBEUnit      $unit
   * @param string       $field
   * @param UBEUnit|null $before
   *
   * @version 41a6.49
   */
  public static function unit_dump_delta(UBEUnit $unit, $field, UBEUnit $before = null) {
    if(!defined('DEBUG_UBE')) {
      return;
    }

//  print("<td" . ($before != null ? ' colspan=2' : '') . ">");
    print("<td>");
    print(pretty_number($unit->$field));
    print("</td>");
    print("<td>");
    if(!empty($before)) {
      print('' . pretty_number($unit->$field - $before->$field) . '');
    }
    print("</td>");
  }

}
