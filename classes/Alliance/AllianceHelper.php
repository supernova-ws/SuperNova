<?php
/**
 * Created by Gorlum 11.11.2018 4:57
 */

namespace Alliance;

class AllianceHelper {
  protected static $patterns = [
//    "#\[fc\]([a-z0-9\#]+)\[/fc\](.*?)\[/f\]#Ssi" => '<font color="\1">\2</font>',
    "#\[fc\]([a-z0-9\#]+)\[/fc\](.*?)\[/f\]#Ssi" => '<span style="color:\1">\2</span>',
    '#\[img\](.*?)\[/img\]#Smi'                  => '<img src="\1" alt="\1" style="border:0;" />',
//    "#\[fc\]([a-z0-9\#\ \[\]]+)\[/fc\]#Ssi"      => '<font color="\1">',
//    "#\[/f\]#Ssi"                                => '</font>',
    "#\[fc\]([a-z0-9\#\ \[\]]+)\[/fc\]#Ssi"      => '<span style="color:\1">',
    "#\[/f\]#Ssi"                                => '</span>',
  ];

  public static function formatText($text) {
    return nl2br(preg_replace(array_keys(self::$patterns), self::$patterns, $text));
  }

}
