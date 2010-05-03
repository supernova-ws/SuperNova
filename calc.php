<?php

/*
 *    Calculadora de Puntos v0.6c
 *   
 *     Autor:
 *     - oriol clariana <oriol_clariana@hotmail.com>
 *
 *    Agradecimientos:
 *    - Pikat0: Por su gran ayuda con recomendaciones
 *      como precio de algunas investigaciones
 *    - _ZydRick_: Por su gran ayuda en la implementacion de
 *      porcentajes a la calculadora.
 */

$v = '0.6c'; /* Version de la Calculadora */

$edff = array();
$invv = array();
$hann = array();
$deff = array();

$edf = GetInFile('edf.txt');
$edf_v = array_fill(0, count($edf), array(0, 0, 0, 0));

$inv = GetInFile('inv.txt');
$inv_v = array_fill(0, count($inv), array(0, 0, 0, 0));

$han = GetInFile('han.txt');
$han_v = array_fill(0, count($han), array(0, 0, 0, 0));

$def = GetInFile('def.txt');
$def_v = array_fill(0, count($def), array(0, 0, 0, 0));

LoadCookies();

if (!empty($_POST['reset'])) {
    foreach($_COOKIE as $elm => $con) {
        setcoookie($elm, '');
    }

    $_POST = array();
}

if (!empty($_POST['suma'])) {
    if (!empty($_POST['edf'])) setcoookie('edf', serialize($_POST['edf']), time()+60*60*24*30);
    if (!empty($_POST['inv'])) setcoookie('inv', serialize($_POST['inv']), time()+60*60*24*30);
    if (!empty($_POST['han'])) setcoookie('han', serialize($_POST['han']), time()+60*60*24*30);
    if (!empty($_POST['def'])) setcoookie('def', serialize($_POST['def']), time()+60*60*24*30);

    LoadCookies();

    for($i = 0; $i < count($edf); $i++) {
        if (!empty($edff)) {
            $edf_v[$i][0] = og_pow($edf[$i][1], abs($edff[$i]), $edf[$i][4]);
            $edf_v[$i][1] = og_pow($edf[$i][2], abs($edff[$i]), $edf[$i][4]);
            $edf_v[$i][2] = og_pow($edf[$i][3], abs($edff[$i]), $edf[$i][4]);

            $edf_v[$i][3] = round(($edf_v[$i][0] + $edf_v[$i][1] + $edf_v[$i][2]) / 1000);
        }
    }
   
    for($i = 0; $i < count($inv); $i++) {
        if (!empty($invv)) {
            $inv_v[$i][0] = og_pow($inv[$i][1], abs($invv[$i]), $inv[$i][4]);
            $inv_v[$i][1] = og_pow($inv[$i][2], abs($invv[$i]), $inv[$i][4]);
            $inv_v[$i][2] = og_pow($inv[$i][3], abs($invv[$i]), $inv[$i][4]);
           
            $inv_v[$i][3] = round(($inv_v[$i][0] + $inv_v[$i][1] + $inv_v[$i][2]) / 1000);
        }
    }
   
    for($i = 0; $i < count($han); $i++) {
        if (!empty($hann)) {
            $han_v[$i][0] = $han[$i][1] * abs($hann[$i]);
            $han_v[$i][1] = $han[$i][2] * abs($hann[$i]);
            $han_v[$i][2] = $han[$i][3] * abs($hann[$i]);
           
            $han_v[$i][3] = round(($han_v[$i][0] + $han_v[$i][1] + $han_v[$i][2]) / 1000);
        }
    }

    for($i = 0; $i < count($def); $i++) {
        if (!empty($deff)) {
            $def_v[$i][0] = $def[$i][1] * abs($deff[$i]);
            $def_v[$i][1] = $def[$i][2] * abs($deff[$i]);
            $def_v[$i][2] = $def[$i][3] * abs($deff[$i]);
           
            $def_v[$i][3] = round(($def_v[$i][0] + $def_v[$i][1] + $def_v[$i][2]) / 1000);
        }
    }
}

function og_pow($v, $lvl, $x) {
    $t = 0;

    if ($lvl != 1) {
        for ($i = 0; $i < $lvl; $i++) {
            if ($i == 0) {
                $t += $v;
                continue;
            }
           
            $v *= $x;
            $t += $v;
        }
    } else {
        $t = $v;
    }
   
    return round($t);
}

function GetInFile($f) {
    $f = file($f);
   
    $ar = array();
   
    foreach ($f as $linea) {
        $tmp = array();
   
        $exp = explode(':', $linea);
        $tmp[] = $exp[0];
       
        $x = explode(' ', trim($exp[1]));
        foreach ($x as $y) {
            $tmp[] = $y;
        }
       
        $ar[] = $tmp;
    }
   
    return $ar;
}

function SumaTodo() {
    global $edf_v, $inv_v, $han_v, $def_v;
    $t = 0;

    foreach (array_merge($edf_v, $inv_v, $han_v, $def_v) as $e) {
        foreach($e as $a) {
            $t += $a;
        }
    }
   
    return round($t / 1000);
}

function LoadCookies() {
    global $edff, $invv, $hann, $deff;

    if (!empty($_COOKIE['edf'])) $edff = unserialize($_COOKIE['edf']);
    if (!empty($_COOKIE['inv'])) $invv = unserialize($_COOKIE['inv']);
    if (!empty($_COOKIE['han'])) $hann = unserialize($_COOKIE['han']);
    if (!empty($_COOKIE['def'])) $deff = unserialize($_COOKIE['def']);
}

function setcoookie($a, $b, $c = '') {
    @setcookie($a, $b, $c);

    $_COOKIE[$a] = $b;
}

?>
<html>

    <head>
        <title>OGame Clone</title>
        <link rel="stylesheet" type="text/css" href="http://mysqldb2.my.funpic.de/formate.css">
    </head>

    <center>
        

        <body>
            <form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
<?php

$tot = SumaTodo();
$to = array_fill(0, 4, 0);

for ($a = 0; $a < 4; $a++) {
    switch ($a) {
        case 0:
            $titulo = 'Geb&auml;de';
            $loc = true;
           
            $c = 'edf'; $d = 'edff';
            $t = $edf;
            $t_v = $edf_v;
           
            break;
        case 1:
            $titulo = 'Forschung';
            $loc = true;
           
            $c = 'inv'; $d = 'invv';
            $t = $inv;
            $t_v = $inv_v;
           
            break;
        case 2:
            $titulo = 'Flotte';
            $loc = false;
           
            $c = 'han'; $d = 'hann';
            $t = $han;
            $t_v = $han_v;
           
            break;
        case 3:
            $titulo = 'Flotte';
            $loc = false;
           
            $c = 'def'; $d = 'deff';
            $t = $def;
            $t_v = $def_v;
           
            break;
    }

?>
                <table border="0" cellpadding="2" cellspacing="1" width="90%">
                    <tr>
                        <td class="c" style="width: 30%"><b><?=$titulo?></b></td>
                        <td class="c" style="width: 10%"><b><?=$loc == true ? 'Stufe' : 'Anzahl'?></b></td>
                        <td class="c" style="width: 15%"><b><span style="color: green">Metall</span></b></td>
                        <td class="c" style="width: 15%"><b><span style="color: blue">Kristall</span></b></td>
                        <td class="c" style="width: 15%"><b><span style="color: darkred">Deuterium</span></b></td>
                        <td class="c" style="width: 10%"><b>Punkte</b></td>
                    </tr>
<?php
    for($i = 0; $i < count($t); $i++) {
?>
                    <tr>
                        <th style="width: 30%"><?=$t[$i][0]?></td>
                        <th style="text-align: center; width: 10%"><input type="text" name="<?=$c?>[]" value="<?=!empty(${$d}[$i]) ? ${$d}[$i]: 0;?>" style="width: 30px;"></th>
                        <th style="width: 15%"><span style="color: green"><?=number_format($t_v[$i][0], 0, ',', '.');?></span></th>
                        <th style="width: 15%"><span style="color: blue"><?=number_format($t_v[$i][1], 0, ',', '.');?></span></th>
                        <th style="width: 15%"><span style="color: darkred"><?=number_format($t_v[$i][2], 0, ',', '.');?></span></th>
                        <th style="width: 10%"><?=number_format($t_v[$i][3], 0, ',', '.');?> (<?=@round((100 * $t_v[$i][3]) / $tot)?>%)</th>
                    </tr>
<?php
   
        $to[$a] += $t_v[$i][3];
    }
?>
                    <tr>
                        <td colspan="4">&nbsp;</td>
                        <td class="c">Summe: </th>
                        <th><?=$to[$a]?> (<?=@round((100 * $to[$a]) / $tot)?>%)</th>
                    </tr>
                </table><br />
<?php
}

?>
               
                <input type="submit" name="suma" value="Berechen">
                <input type="submit" name="reset" value="Zur&uuml;cksetzen">

                <br /><br />
                Punkte: <?=number_format($tot, 0, '.', ''); ?>
               
            </form>
           


        </center>
    </body>
</html>