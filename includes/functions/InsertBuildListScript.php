<?php

/**
 * InsertBuildListScript.php
 *
 * @version 1.0
 * @copyright 2008 by Chlorel for XNova
 */

function InsertBuildListScript ( $CallProgram, $prefix = "", $autoRefresh = true ) {
  global $lang;

  $BuildListScript  = "<script type=\"text/javascript\">\n";
  $BuildListScript .= "<!--\n";
  $BuildListScript .= "function t{$prefix}() {\n";
  $BuildListScript .= " v           = new Date();\n";
  $BuildListScript .= " var blc     = document.getElementById('blc{$prefix}');\n";
  $BuildListScript .= " var timeout = 1;\n";
  $BuildListScript .= " n           = new Date();\n";
  $BuildListScript .= " ss          = pp{$prefix};\n";
  $BuildListScript .= " aa          = Math.round( (n.getTime() - v.getTime() ) / 1000. );\n";
  $BuildListScript .= " s           = ss - aa;\n";
  $BuildListScript .= " m           = 0;\n";
  $BuildListScript .= " h           = 0;\n\n";
  $BuildListScript .= " if ( (ss + 3) < aa ) {\n";
  $BuildListScript .= "   blc.innerHTML = \"". $lang['completed'] ."<br>\" + \"<a href=". $CallProgram .".php?planet=\" + pl{$prefix} + \">". $lang['continue'] ."</a>\";\n";
  $BuildListScript .= "   if ((ss + 6) >= aa) {\n";
  if($autoRefresh)
    $BuildListScript .= "     window.setTimeout('document.location.href=\"". $CallProgram .".php?planet=' + pl{$prefix} + '\";', 3500);\n";
  $BuildListScript .= "   }\n";
  $BuildListScript .= " } else {\n";
  $BuildListScript .= "   if ( s < 0 ) {\n";
  $BuildListScript .= "     if (1) {\n";
  $BuildListScript .= "       blc.innerHTML = \"". $lang['completed'] ."<br>\" + \"<a href=". $CallProgram .".php?planet=\" + pl{$prefix} + \">". $lang['continue'] ."</a>\";\n";
  if($autoRefresh)
    $BuildListScript .= "       window.setTimeout('document.location.href=\"". $CallProgram .".php?planet=' + pl{$prefix} + '\";', 2000);\n";
  $BuildListScript .= "     } else {\n";
  $BuildListScript .= "       timeout = 0;\n";
  $BuildListScript .= "       blc.innerHTML = \"". $lang['completed'] ."<br>\" + \"<a href=". $CallProgram .".php?planet=\" + pl{$prefix} + \">". $lang['continue'] ."</a>\";\n";
  $BuildListScript .= "     }\n";
  $BuildListScript .= "   } else {\n";
  $BuildListScript .= "     if ( s > 59) {\n";
  $BuildListScript .= "       m = Math.floor( s / 60);\n";
  $BuildListScript .= "       s = s - m * 60;\n";
  $BuildListScript .= "     }\n";
  $BuildListScript .= "     if ( m > 59) {\n";
  $BuildListScript .= "       h = Math.floor( m / 60);\n";
  $BuildListScript .= "       m = m - h * 60;\n";
  $BuildListScript .= "     }\n";
  $BuildListScript .= "     if ( s < 10 ) {\n";
  $BuildListScript .= "       s = \"0\" + s;\n";
  $BuildListScript .= "     }\n";
  $BuildListScript .= "     if ( m < 10 ) {\n";
  $BuildListScript .= "       m = \"0\" + m;\n";
  $BuildListScript .= "     }\n";
  $BuildListScript .= "     if (1) {\n";
  $BuildListScript .= "       blc.innerHTML = h + \":\" + m + \":\" + s + \"<br><a href=". $CallProgram .".php?listid=\" + pk{$prefix} + \"&cmd=\" + pm{$prefix} + \"&planet=\" + pl{$prefix} + \">". $lang['DelFirstQueue'] ."</a>\";\n";
  $BuildListScript .= "     } else {\n";
  $BuildListScript .= "       blc.innerHTML = h + \":\" + m + \":\" + s + \"<br><a href=". $CallProgram .".php?listid=\" + pk{$prefix} + \"&cmd=\" + pm{$prefix} + \"&planet=\" + pl{$prefix} + \">". $lang['DelFirstQueue'] ."</a>\";\n";
  $BuildListScript .= "     }\n";
  $BuildListScript .= "   }\n";
  $BuildListScript .= "   pp{$prefix} = pp{$prefix} - 1;\n";
  $BuildListScript .= "   if (timeout == 1) {\n";
  $BuildListScript .= "     window.setTimeout(\"t{$prefix}();\", 999);\n";
  $BuildListScript .= "   }\n";
  $BuildListScript .= " }\n";
  $BuildListScript .= "}\n";
  $BuildListScript .= "//-->\n";
  $BuildListScript .= "</script>\n";

  return $BuildListScript;
}

function InsertCounterLaunchScript($RestTime, $PlanetID, $prefix = ""){
  $Build = "<br /><div id=\"blc{$prefix}\" class=\"z\">". pretty_time( $RestTime ) ."</div>";
  $Build .= "\n<script language=\"JavaScript\">";
  $Build .= "\n pp{$prefix} = \"". $RestTime ."\";\n";  // temps necessaire (a compter de maintenant et sans ajouter time() )
  $Build .= "\n pk{$prefix} = \"". 1 ."\";\n";          // id index (dans la liste de construction)
  $Build .= "\n pm{$prefix} = \"cancel\";\n";           // mot de controle
  $Build .= "\n pl{$prefix} = \"". $PlanetID ."\";\n";  // id planete
  $Build .= "\n t{$prefix}();\n";
  $Build .= "\n</script>\n";
  return $Build;
}
?>