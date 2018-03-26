<?php

function sn_ube_combat_helper_round_header($round) {
  $header = array(
    'ИД1',
    'ИД2',
    'Ат',
    '%',
    'Ат%',
    'Залп',
    'АтЗа',
    'Щит',
    'Щит-',
    'ЩитО',
    'АтО',
    'Бр',
    'Бр-',
    'Пос',
    'Пос%',
    'Rand',
    'Boom',
    'БрО',
    'Юнит',
  );

  print($round);
  print('<table border=1>');
  print('<tr align="left">');
  foreach ($header as $key => $value) {
    if (is_array($value)) {
      continue;
    }
    print('<th>');
    print($value);
    print('</th>');
  }
  print('</tr>');
}

function sn_ube_combat_helper_round_footer($round = 0) {
  print('</table>');
}

function sn_ube_combat_helper_round_row(&$debug_unit_crossfire_result) {
  $SN = array(
    SHIP_CARGO_SMALL     => 'МаТр', SHIP_CARGO_BIG => 'БоТр', SHIP_CARGO_SUPER => 'СуТр', SHIP_CARGO_HYPER => 'ГпТр',
    SHIP_SATTELITE_SOLAR => 'СоСп',
    SHIP_COLONIZER       => 'Коло', SHIP_RECYCLER => 'Пере', SHIP_SPY => 'Шпио', SHIP_SATTELITE_SLOTH => 'Лень',

    SHIP_SMALL_FIGHTER_LIGHT => 'ЛгИс', SHIP_SMALL_FIGHTER_WRATH => 'Гнев', SHIP_SMALL_FIGHTER_HEAVY => 'ТяИс', SHIP_SMALL_FIGHTER_ASSAULT => 'Штур',

    SHIP_MEDIUM_DESTROYER   => 'Эсми', SHIP_MEDIUM_BOMBER_ENVY => 'Зави', SHIP_LARGE_BOMBER => 'Бомб',
    SHIP_CARGO_GREED        => 'Жадн', SHIP_LARGE_CRUISER => 'Крей', SHIP_LARGE_BATTLESHIP => 'Линк', SHIP_LARGE_BATTLESHIP_PRIDE => 'Горд',
    SHIP_LARGE_DESTRUCTOR   => 'Уник', SHIP_HUGE_DEATH_STAR => 'ЗвСм', SHIP_HUGE_SUPERNOVA => 'Нова',
    UNIT_DEF_TURRET_MISSILE => 'Раке', UNIT_DEF_TURRET_LASER_SMALL => 'ЛеЛа', UNIT_DEF_TURRET_LASER_BIG => 'ТяЛа', UNIT_DEF_TURRET_GAUSS => 'Гаус', UNIT_DEF_TURRET_ION => 'Ионн', UNIT_DEF_TURRET_PLASMA => 'Плаз', UNIT_DEF_SHIELD_SMALL => 'МалЩ', UNIT_DEF_SHIELD_BIG => 'БолЩ', UNIT_DEF_SHIELD_PLANET => 'ПлаЩ'
  );

  print('<tr align="right">');
  foreach ($debug_unit_crossfire_result as $key => $value) {
    if (is_array($value)) {
      continue;
    }
    print('<td>');
    print(($key === 'attack_unit_id' || $key === 'defend_unit_id') ? $SN[$value] : $value);
    print('</td>');
  }
  print('</tr>');
}
