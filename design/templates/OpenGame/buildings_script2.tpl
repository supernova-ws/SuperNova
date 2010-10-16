<table width="100%" border=0>
<tr>
<td align=center><form border=0 name="h_Atr" method="get" action="buildings.php">
В производстве: <div id=h_bx class=z></div><br>
<script  type="text/javascript">
hv  = new Date();
hp  = 0;
hg  = {h_b_hangar_id_plus};
hs  = 0;
h_hs = 0;
hof = 1;
h_c  = new Array({h_c}'');
h_b  = new Array({h_b}'');
h_a  = new Array({h_a}'');
haa = '{completed}';

function h_t() {
	if ( h_hs == 0 ) {
		h_xd();
		h_hs = 1;
	}
	n = new Date();
	hs = h_c[hp] - hg - Math.round((n.getTime() - hv.getTime()) / 1000.);
	hs = Math.round(hs);
	m = 0;
	h = 0;
	if ( hs < 0 ) {
		h_a[hp]--;
		h_xd();
		if ( h_a[hp] <= 0 ) {
			hp++;
			h_xd();
		}
		hg = 0;
		hv = new Date();
		hs = 0;
	}
	if ( hs > 59 ) {
		m = Math.floor(hs / 60);
		hs = hs - m * 60;
	}
	if ( m > 59 ) {
		h = Math.floor(m / 60);
		m = m - h * 60;
	}
	if ( hs < 10 ) {
		hs = "0" + hs;
    }
    if (m < 10) {
      m = "0" + m;
	}
	if ( hp > h_b.length - 2 ) {
		document.getElementById("h_bx").innerHTML=haa ;
    } else {
		document.getElementById("h_bx").innerHTML=h_b[hp]+" "+h+":"+m+":"+hs;
    }
	window.setTimeout("h_t();", 200);
}

function h_xd() {
	while (document.h_Atr.h_auftr.length > 0) {
		document.h_Atr.h_auftr.options[document.h_Atr.h_auftr.length-1] = null;
	}
	if ( hp > h_b.length - 2 ) {
		document.h_Atr.h_auftr.options[document.h_Atr.h_auftr.length] = new Option(haa);
	}
	for ( iv = hp; iv <= h_b.length - 2; iv++ ) {
		if ( h_a[iv] < 2 ) {
			ae = " ";
		} else {
			ae = " ";
		}
		if ( iv == hp ) {
			act = " ({in_working})";
		} else {
			act = "";
		}
		document.h_Atr.h_auftr.options[document.h_Atr.h_auftr.length] = new Option( h_a[iv] + ae + " \"" + h_b[iv] + "\"" + act, iv + hof );
	}
}

window.onload = h_t;
</script>
<!-- <br /> -->
<input type="hidden" name="mode" value="fleet">
<input type="hidden" name="action" value="cancelqueue">
	<div class="h_c" ><div align="center"><input type="submit" value="Отменить строительство" /></div>{work_todo}</div></td>
	<td><select name="h_auftr" size="3"></select><br><b>Вся очередь: {h_total_left_time} {h_pretty_time_b_hangar}</b></td>
<!--<td class="h_c" ></td>-->
</tr>
</table>
</form>
</center>
<!--
<br />
 {h_total_left_time} {h_pretty_time_b_hangar}<br>
<br />-->