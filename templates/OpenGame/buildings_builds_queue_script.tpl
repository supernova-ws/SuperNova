<script src="scripts/cnt.js" type="text/javascript"></script>
<div id="btc" class="z"></div><script language="JavaScript">
	pp = '{time}';
	pk = '{building_id}';
	pl = '{id}';
	ps = 'buildings.php';
	t();
</script>
<script type="text/javascript">
v  = new Date();
p  = 0;
g  = {b_building_queue};
s  = 0;
hs = 0;
of = 1;
c  = new Array({c}'');
b  = new Array({b}'');
a  = new Array({a}'');
aa = '{completed}';

function t() {
	if ( hs == 0 ) {
		xd();
		hs = 1;
	}
	n = new Date();
	s = c[p]-g-Math.round((n.getTime()-v.getTime())/1000.);
	s = Math.round(s);
	m = 0;
	h = 0;
	if ( s < 0 ) {
		a[p]--;
		xd();
		if ( a[p] <= 0 ) {
			p++;
			xd();
		}
		g = 0;
		v = new Date();
		s = 0;
	}
	if ( s > 59 ) {
		m = Math.floor( s / 60 );
		s = s - m * 60;
	}
	if ( m > 59 ) {
		h = Math.floor( m / 60 );
		m = m - h * 60;
	}
	if ( s < 10 ) {
		s = "0" + s;
	}
	if ( m < 10 ) {
		m = "0" + m;
	}
	if (p > b.length - 2) {
		document.getElementById("bx").innerHTML=aa ;
	} else {
		document.getElementById("bx").innerHTML= b[p] + " " + h + ":" + m + ":" + s;
	}
	window.setTimeout("t();", 200);
}

function xd() {
	while (document.Atr.auftr.length > 0) {
		document.Atr.auftr.options[document.Atr.auftr.length-1] = null;
	}
	if ( p > b.length - 2 ) {
		document.Atr.auftr.options[document.Atr.auftr.length] = new Option(aa);
	}
	for (iv = p; iv <= b.length - 2; iv++) {
		if ( a[iv] < 2 ) {
			ae = " ";
		} else {
			ae = " ";
		}
		if ( iv == p ) {
			act = " ({in_working})";
		} else {
			act = "";
		}
		document.Atr.auftr.options[document.Atr.auftr.length] = new Option( a[iv] + ae + " \"" + b[iv] + "\"" + act, iv + of );
	}
}

window.onload = t;
</script>

<center>
<br>
<form name="Atr" method="get" action="buildings.php">
	<input type="hidden" name="mode" value="fleet">
	<table width="530">
	<tr>
		<td class="c" >{work_todo}</td>
	</tr><tr>
		<th ><select name="auftr" size="10"></select></th>
	</tr><tr>
		<td class="c" ></td>
	</tr>
	</table>
</form>
{total_left_time}

{pretty_time_b_hangar}
<br>
</center>