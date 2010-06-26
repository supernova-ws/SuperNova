<br />
<script type="text/javascript"><!--
function reCalc(){
  var inz = 0;

  si = document.getElementsByName('exchangeTo')[0].selectedIndex;
  sinp = document.getElementsByName('spend[' + si + ']')[0];

  for (i=0;i<=2;i++){
    inp = document.getElementsByName('spend[' + i + ']')[0];
    if(i == si){
      inp.disabled = true;
    }else{
      inp.disabled = false;
      inz = inz + inp.value * rates[i] / rates[si];
    }
  }
  sinp.value = inz;
}
//--></script>

<script type="text/javascript"><!--
var rates = Array ( {rpg_exchange_metal}, {rpg_exchange_crystal}, {rpg_exchange_deuterium});
//--></script>

<form action="" method="POST">
  <table>
    <tr><td class="c" colspan=4>{L_eco_mrk_exchanger}</td></tr>
    <tr align="center"><td class="c">Ресурс</td><td class="c">Отдать</td><td class="c">Доступно</td></tr>
    <tr>
      <th>{L_Metal}</th>
      <th><input name="spend[0]" value="{spend0}" onKeyUp="javascript:reCalc();"></th>
      <th>{avail_metal}</th>
    </tr>
    
    <tr>
      <th>{L_Crystal}</th>
      <th><input name="spend[1]" value="{spend1}" onKeyUp="javascript:reCalc();"></th>
      <th>{avail_crystal}</th>
    </tr>
    
    <tr>
      <th>{L_Deuterium}</th>
      <th><input name="spend[2]" value="{spend2}" onKeyUp="javascript:reCalc();"></th>
      <th>{avail_deuterium}</th>
    </tr>
    
    <tr><td class="c" colspan=4 align=center><input type="submit" name="exchange" value="Обменять на"> <select name="exchangeTo" onChange="javascript:reCalc();"><option value="0">{L_Metal}<option value="1">{L_Crystal}<option value="2">{L_Deuterium}</select></td></tr>
  </table>
  <input type="hidden" name="mode" value="{mode}">
</form>

<script type="text/javascript"><!--
document.getElementsByName('exchangeTo')[0].selectedIndex = {exchangeTo};
reCalc();
//--></script>
