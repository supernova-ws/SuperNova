<?php
define('INSIDE'  , true);
define('INSTALL' , false);

$ugamela_root_path = './../';
include($ugamela_root_path . 'extension.inc');
include($ugamela_root_path . 'common.' . $phpEx);

pr();
print("\n");

print("  \$resources = array(\n");

function p_s($str, $sameline = false)
{
  global $space;

  if(!$sameline)
    for($i == 0; $i < 4 + $space; $i++)
      print(' ');
  print("{$str}");
  if(!$sameline)
    print("\n");
}

foreach($resource as $id => $name)
{
  if($id<100) $space1  = ' '; else $space1 = '';
  if($id<10)  $space1 .= ' ';
  p_s("{$id}{$space1} => array(");
  $space+=2;

  p_s("'name' => '{$resource[$id]}',");
  if($requeriments[$id])
  {
    $str_temp = "'require' => array(";
    foreach($requeriments[$id] as $id_require => $level)
    {
      $str_temp .= "{$id_require} => {$level}, ";
    }
    $str_temp = substr($str_temp, 0, -2);
    p_s("{$str_temp}),");
  }

//  $str_temp = "'cost' => array(";
  if($id>=600 && $id<700)
  {
//    $str_temp .= "'dark_matter' => 3, 'factor' => 1, ";
    p_s("'dark_matter' => 3,");
    p_s("'factor' => 1,");

  }
  foreach($pricelist[$id] as $field_name => $field_value)
  {
    p_s("'$field_name' => {$field_value},");
//    $str_temp .= "'$field_name' => {$field_value}, ";
  }
//  $str_temp = substr($str_temp, 0, -2);
//  p_s("{$str_temp}),");

  if($CombatCaps[$id])
  {
//    $str_temp = "'combat' => array(";
    foreach($CombatCaps[$id] as $field_name => $field_value)
    {
      if($field_name == 'sd' || $field_name == 'amplify')
      {
        $str_temp = "'{$field_name}' => array(";
        foreach($field_value as $field_name2 => $field_value2)
        {
          $str_temp .= "{$field_name2} => {$field_value2}, ";
        }
        $str_temp = substr($str_temp, 0, -2);
        p_s("{$str_temp}),");
      }
      else
      {
        // $str_temp .= "'{$field_name}' => {$field_value}, ";
        p_s("'{$field_name}' => {$field_value},");
      }
    }
//    $str_temp = substr($str_temp, 0, -2);
//    p_s("{$str_temp}),");
  }

  if($ProdGrid[$id])
  {
    foreach($ProdGrid[$id] as $field_name => $field_value)
    {
      if($field_name == 'formule')
      {
        foreach($field_value as $field_name2 => $field_value2)
        {
          p_s("'{$field_name2}_perhour' => '{$field_value2}',");
        }
      }
      else
      {
      }
    }
  }


  $space-=2;
  p_s("),\n");
}
print('  );');
?>
