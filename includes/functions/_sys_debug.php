<?php
// Copyright (c) 2009 Gorlum for oGame.Triolan.com.ua
// Dump variables nicer then var_dump()

function dump_old($value,$level=0,$varname="")
{
  if ($varname) $varname .= " = ";

  if ($level==-1)
  {
    $trans[' ']='&there4;';
    $trans["\t"]='&rArr;';
    $trans["\n"]='&para;;';
    $trans["\r"]='&lArr;';
    $trans["\0"]='&oplus;';
    return strtr(htmlspecialchars($value),$trans);
  }
  if ($level==0) echo '<pre>' . $varname;
  $type= gettype($value);
  echo $type;
  if ($type=='string')
  {
    echo '('.strlen($value).')';
    $value= dump($value,-1);
  }
  elseif ($type=='boolean') $value= ($value?'true':'false');
  elseif ($type=='object')
  {
    $props= get_class_vars(get_class($value));
    echo '('.count($props).') <u>'.get_class($value).'</u>';
    foreach($props as $key=>$val)
    {
      echo "\n".str_repeat("\t",$level+1).$key.' => ';
      dump($value->$key,$level+1);
    }
    $value= '';
  }
  elseif ($type=='array')
  {
    echo '('.count($value).')';
    foreach($value as $key=>$val)
    {
      echo "\n".str_repeat("\t",$level+1).dump($key,-1).' => ';
      dump($val,$level+1);
    }
    $value= '';
  }
  echo " <b>$value</b>";
  if ($level==0) echo '</pre>';
}


function dump($value,$varname = "",$level=0,$dumper = "")
{
  if ($varname) $varname .= " = ";

  if ($level==-1)
  {
    $trans[' ']='&there4;';
    $trans["\t"]='&rArr;';
    $trans["\n"]='&para;;';
    $trans["\r"]='&lArr;';
    $trans["\0"]='&oplus;';
    return strtr(htmlspecialchars($value),$trans);
  }
  if ($level==0) $dumper = '<pre>' . $varname;

  $type = gettype($value);
  $dumper .= $type;

  if ($type=='string')
  {
    $dumper .= '('.strlen($value).')';
    $value = dump($value,"",-1);
  }
  elseif ($type=='boolean') $value= ($value?'true':'false');
  elseif ($type=='object')
  {
    $props= get_class_vars(get_class($value));
    $dumper .= '('.count($props).') <u>'.get_class($value).'</u>';
    foreach($props as $key=>$val)
    {
      $dumper .= "\n".str_repeat("\t",$level+1).$key.' => ';
      $dumper .= dump($value->$key,"",$level+1);
    }
    $value= '';
  }
  elseif ($type=='array')
  {
    $dumper .= '('.count($value).')';
    foreach($value as $key=>$val)
    {
      $dumper .= "\n".str_repeat("\t",$level+1).dump($key,"",-1).' => ';
      $dumper .= dump($val,"",$level+1);
    }
    $value= '';
  }
  $dumper .= " <b>$value</b>";
  if ($level==0) $dumper .= '</pre>';
  return $dumper;
}

function pdump($value,$varname = "",$level=0,$dumper = "")
{
  print(dump($value,$varname,$level,$dumper));
}

function pr($prePrint = false){
  if($prePrint)
    print("<br>");
  print(rand() . "<br>");
}

function pc($prePrint = false){
  global $_PRINT_COUNT_VALUE;
  $_PRINT_COUNT_VALUE++;

  if($prePrint)
    print("<br>");
  print($_PRINT_COUNT_VALUE . "<br>");
}
?>
