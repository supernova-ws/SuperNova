<script type="text/javascript">

function calc_resources()

{
	var regain_met = {schrotti_met};
	var regain_crys = {schrotti_crys};
	var regain_deut = {schrotti_deut};
	var regain_energy = {schrotti_energy};
	var max_ships_sell = {max_ships_to_sell};
	var num = parseInt(document.getElementById('numschrotti').value, 10);

	if (num < 0){
		num = 0;
		document.getElementById('numschrotti').value=num;
	}

	if (num > max_ships_sell){
		num = max_ships_sell;
		document.getElementById('numschrotti').value=num;
	}

	document.getElementById('schrotti_met').innerHTML = num * regain_met;
	document.getElementById('schrotti_crys').innerHTML = num * regain_crys;
	document.getElementById('schrotti_deut').innerHTML = num * regain_deut;
	document.getElementById('schrotti_energy').innerHTML = num * regain_energy;
}

</script>

<br>
<br>
<center>
  <table border="0" cellpadding="0" cellspacing="1" width="600">
   <tr height="20">
		<td colspan="4" class="c">{Intergalactic_merchant}</td>
</tr>

   <tbody>
     
     <tr height="10">
    	<th rowspan="3" align="left" class="c"><img src="{dpath}gebaeude/{image}.gif" width="120" height="120"></th>
    	<th class="1" colspan="2" align="center">{Merchant_text_decript}<br></th>
    	<td class="c" colspan="2" align="center"><br>
				<form name="planets" action="schrotti.php" method="post">
				<select name="shiptypeid" onchange="this.form.submit();">{shiplist}</select>
				</form>
     </tr>

	<form action="" method="post">
     <tr height="20">
         <th align="center">{How_much_want_exchange}</th>
         <td colspan="2" class="c" align="left">
         	<input type="hidden" name="shiptypeid" value="{shiptype_id}">
          <input id="numschrotti" type="text" name="number_ships_sell" alt="{Kleiner_transporter}" size="8" maxlength="6" value="0" tabindex="1" onKeyup="calc_resources();">
         	<span style="color:yellow;"> {von} {max_ships_to_sell}</span></td>
     </tr>

     <tr height="20">
       <th colspan="2" align="center">{Merchant_give_Info}</th>
     <td colspan="2" class="c">
     {Merchant_give_Aluminium}<br>
     {Merchant_give_Silicium}<br>
     {Merchant_give_Deuterium}<br>
     {Merchant_give_Energy}</td>
     </td>

     <tr height="20" align="center">
			<td></td>
			<td></td>
     <td colspan="3" class="c"><input name="submit" type="submit" value="{Exchange}"></td>
     </tr>
    </tbody</table>

</form>

</center>
{ClickBanner}
</body>
</html>