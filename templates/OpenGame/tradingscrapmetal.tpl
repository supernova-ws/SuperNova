
<script type="text/javascript">

function calc_resources()
{
	var regain_kristal = {crystal} * 1;
	var max_number = {max_spy_probe};
	var num = parseInt(document.getElementById('numscrap').value);

	if (num < 0){
		num = 0;
		document.getElementById('numscrap').value=num;
	}
	if (num > max_number){
		num = max_number;
		document.getElementById('numscrap').value=num;
	}
	
	document.getElementById('scrapkrisvalue').innerHTML = num * regain_kristal; 
	
}

</script>
<br>
<center>
<form action="" method="post">
  <table border="0" cellpadding="0" cellspacing="1" width="519">
   <tr height="20"><td colspan="3" class="c">{Intergalactic_merchant}</td></tr>
   <tbody>
     <tr height="20">
    	<th rowspan="4" align="center" valign="middle"><img src="images/scrap.jpg" width="120" height="180"></th>
    	<th class="1" colspan="2" align="center"><p>{Merchant_text_decript}<br></p></th>
    </tr>
     <tr height="20">
         <th align="center">{How_much_want_exchange}</th>
         <th align="center">
             <input id="numscrap" type="text" name="number_of_probes" alt="{Spionagesonde}" size="6" maxlength="6" value="0" tabindex="1" onKeyup="calc_resources();">
         <span style="color:gray;">/ {max_spy_probe}</span></th>
     </tr>
     <tr height="20">
         <th colspan="2" align="center">{Merchant_give_you}</th>
         </tr>
     <tr height="20" align="center">
         <th colspan="2"><input name="submit" type="submit" value="{Exchange}"></th>
     </tr>
    </tbody></table>
</form>

</center>
</body>
</html>
