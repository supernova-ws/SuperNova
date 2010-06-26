<br />
<script type="text/javascript"><!--
var originalColor;

function reCalc(){
  var inz = 0;

  si = document.getElementsByName('exchangeTo')[0].selectedIndex;
  sinp = document.getElementsByName('spend[' + si + ']')[0];

  for (i=0;i<=3;i++){
    document.getElementById('course' + i).innerHTML = rates[i] / rates[si];

    inp = document.getElementsByName('spend[' + i + ']')[0];
    if(i == si){
      inp.disabled = true;
      inp.style.backgroundColor = "#00FF00";
    }else{
      if (originalColor == undefined)
        originalColor = inp.style.backgroundColor;
      inp.style.backgroundColor = originalColor;

      inp.disabled = false;
      inz = inz + inp.value * rates[i] / rates[si];
      inp.value = parseFloat(0 + inp.value);
    }
  }
  sinp.value = parseFloat(inz);
}
//--></script>

<script type="text/javascript"><!--
var rates = Array ( {rpg_exchange_metal}, {rpg_exchange_crystal}, {rpg_exchange_deuterium}, {rpg_exchange_darkMatter});
//--></script>

<form action="" method="POST">
  <table>
    <tr><td class="c" colspan=4>{L_eco_mrk_trader}</td></tr>
    <tr align="center">
      <td class="c">{L_sys_resources}</td>
      <td class="c">{L_Ressverf}</td>
      <td class="c">{L_eco_mrk_exchange}</td>
      <td class="c">{L_eco_mrk_course}</td>
    </tr>

    <tr>
      <th>{L_Metal}</th>
      <th>{avail_metal}</th>
      <th><input name="spend[0]" value="{spend0}" onKeyUp="javascript:reCalc();"></th>
      <th><span id="course0"></span></th>
    </tr>
    
    <tr>
      <th>{L_Crystal}</th>
      <th>{avail_crystal}</th>
      <th><input name="spend[1]" value="{spend1}" onKeyUp="javascript:reCalc();"></th>
      <th><span id="course1"></span></th>
    </tr>
    
    <tr>
      <th>{L_Deuterium}</th>
      <th>{avail_deuterium}</th>
      <th><input name="spend[2]" value="{spend2}" onKeyUp="javascript:reCalc();"></th>
      <th><span id="course2"></span></th>
    </tr>
    
    <tr>
      <th>{L_dark_matter}</th>
      <th>{avail_darkMatter}</th>
      <th><input name="spend[3]" value="{spend3}" onKeyUp="javascript:reCalc();"></th>
      <th><span id="course3"></span></th>
    </tr>
    
    <tr><td class="c" colspan=4 align=center><input type="submit" name="exchange" value="Обменять на">
      <select name="exchangeTo" onChange="javascript:reCalc();">
        <option value="0">{L_Metal}
        <option value="1">{L_Crystal}
        <option value="2">{L_Deuterium}
      </select></td></tr>
    <tr><td class="c" colspan=4 align=center>{L_eco_mrk_trader_cost}{rpg_cost_trader} {L_eco_mrk_dark_matter_short}</td></tr>
  </table>
  <input type="hidden" name="mode" value="{mode}">
</form>

<script type="text/javascript"><!--
varTemp = '{exchangeTo}';

if(varTemp == '')
  varTemp = 0;

document.getElementsByName('exchangeTo')[0].selectedIndex = varTemp;
reCalc();
//--></script>
