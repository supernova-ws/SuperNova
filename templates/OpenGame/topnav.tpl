<style type="text/css" media="screen">
input.frameles
{
  border               : #ff0000;
  text-align           : center;
  font-size            : 11px;
}
</style>

<!-- <div id="header_top" align="center" width="auto" style="background-color: transparent;"> -->
<table align=center width="722" cellpadding="0" cellspacing="0" class="header" id="resources" style="width: 722px;" padding-right="30"><tbody>
  <tr class="header">
    <td class="header" align="center" width="150"><select size="1" onChange="eval('location=\''+this.options[this.selectedIndex].value+'\'');">{planetlist}</select></td>
    <td class="header" align="center" width="150"><A HREF="resources.php"><img src="{dpath}images/metall.gif" border="0" height="22" width="42"></A></td>
    <td class="header" align="center" width="150"><A HREF="resources.php"><img src="{dpath}images/kristall.gif" border="0" height="22" width="42"></A></td>
    <td class="header" align="center" width="150"><A HREF="resources.php"><img src="{dpath}images/deuterium.gif" border="0" height="22" width="42"></A></td>
    <td class="header" align="center" width="150"><A HREF="resources.php"><img src="{dpath}images/energie.gif" border="0" height="22" width="42"></A></td>
    <td class="header" align="center" width="140"><A HREF="resources.php"><img src="{dpath}images/dm_klein_2.jpg" border="0" height="22" width="42"></A></td>
    <td class="header" align="center" width="140"><A HREF="messages.php"><img src="{dpath}images/message.gif" border="0" height="22" width="42"></A></td>
  </tr>
  <tr class="header">
    <td class="header" align="center" width="150"><b><font color="#ffffff"><span id="localTime">00.00.0000 00:00:00</span></font></b></td>
    <td class="header" align="center" width="150"><b><font color="#FFFF00"><A HREF="resources.php">{Metal}</A></font></td>
    <td class="header" align="center" width="150"><b><font color="#FFFF00"><A HREF="resources.php">{Crystal}</A></font></b></td>
    <td class="header" align="center" width="150"><b><font color="#FFFF00"><A HREF="resources.php">{Deuterium}</A></font></b></td>
    <td class="header" align="center" width="150"><b><font color="#FFFF00"><A HREF="resources.php">{Energy}</A></font></b></td>
    <td class="header" align="center" width="140"><b><font color="#ffff00"><A HREF="resources.php">Темная материя</A></font></b></td>
    <td class="header" align="center" width="140"><b><font color="#FFFF00"><A HREF="messages.php">{Message}</A></font></b></td>
  </tr>
  <tr class="header" align=center valign=middle><form name='ressources'> <center>
    <td class="header" align="center" width="150"><b><font color="#FFFF00">{Ressverf}</font></b></td>
    <td class="header" align="center" width="150"><span id='onPlanetMetal'>{metalh}</span></td>
    <td class="header" align="center" width="150"><span id='onPlanetCrystal'>{crystalh}</span></td>
    <td class="header" align="center" width="150"><span id='onPlanetDeuterium'>{deuteriumh}</span></td>
    <td class="header" align="center" width="150"><font>{energy_total}</font></td>
    <td class="header" align="center" width="140" rowspan="2"><font color="green"><b>{dark_matter}</b></font></td>
    <td class="header" align="center" valign=middle width="140" rowspan="2" style="white-space: nowrap;">{message}</td>
  </form></tr>
  <tr class="header">
    <td class="header" align="center" width="150"><b><font color="#FFFF00">{Store_max}</font></b></td>
    <td class="header" align="center" width="150">{metal_max}</td>
    <td class="header" align="center" width="150">{crystal_max}</td>
    <td class="header" align="center" width="150">{deuterium_max}</td>
    <td class="header" align="center" width="150"><font color="#00ff00">{energy_max}</font></td>
  </tr>
</tbody></table>
<!-- </div> -->

<script LANGUAGE='JavaScript'>
<!--
var now = new Date();
var event = new Date();
var seconds = (now - event);
var val = 0;
var val2 = 0;
var val3 = 0;

var d_m = document.getElementById('onPlanetMetal');
var d_c = document.getElementById('onPlanetCrystal');
var d_d = document.getElementById('onPlanetDeuterium');

function update() {
  now = new Date();
  seconds = (now - event) /1000;

  val = ( {metal_perhour} /3600.0) * seconds + {metalh};
  if( val >= {metal_mmax} ) {
    val = {metalh};
    d_m.style.color = '#FF0000';
    d_m.style.fontWeight = 'bold';
  }
  d_m.innerHTML = number_format( val ,2);

  val = ( {crystal_perhour} /3600.0) * seconds + {crystalh};
  if( val >= {crystal_mmax} ) {
    val = {crystalh};
    d_c.style.color = '#FF0000';
    d_c.style.fontWeight = 'bold';
  }
  d_c.innerHTML = number_format( val ,2);

  val = ( {deuterium_perhour} /3600.0) * seconds + {deuteriumh};
  if( val >= {deuterium_mmax} ) {
    val = {deuteriumh};
    d_d.style.color = '#FF0000';
    d_d.style.fontWeight = 'bold';
  }
  d_d.innerHTML = number_format( val ,2);

  ID=window.setTimeout('update();',1000);
}

update();

function number_format(number,laenge) {
  number = Math.round( number * Math.pow(10, laenge) ) / Math.pow(10, laenge);
  str_number = number+'';
  arr_int = str_number.split('.');
  if(!arr_int[0]) arr_int[0] = '0';
  if(!arr_int[1]) arr_int[1] = '';
  if(arr_int[1].length < laenge){
    nachkomma = arr_int[1];
    for(i=arr_int[1].length+1; i <= laenge; i++){  nachkomma += '0';  }
    arr_int[1] = nachkomma;
  }
  if(arr_int[0].length > 3){
    Begriff = arr_int[0];
    arr_int[0] = '';
    for(j = 3; j < Begriff.length ; j+=3){
      Extrakt = Begriff.slice(Begriff.length - j, Begriff.length - j + 3);
      arr_int[0] = '.' + Extrakt +  arr_int[0] + '';
    }
    str_first = Begriff.substr(0, (Begriff.length % 3 == 0)?3:(Begriff.length % 3));
    arr_int[0] = str_first + arr_int[0];
  }
  return arr_int[0]+','+arr_int[1];
}
// --></script>