var pp;

function t() {
	var v  = new Date();
	var btc = document.getElementById('btc');
	var n  = new Date();
	var s  = pp - Math.round((n.getTime() - v.getTime()) / 1000.);
	var m  = 0;
	var h  = 0;
	if ( s < 0 ) {
		btc.innerHTML = "Termin&eacute;<br>" + "<a href=?cp=" + pl + ">Continuer</a>"
	} else {
		if ( s > 59 ) {
		m = Math.floor( s / 60 );
		s = s - m * 60
		}
		if ( m > 59 ) {
			h = Math.floor( m / 60 );
			m = m - h * 60
		}
		if ( s < 10 ) {
			s = "0" + s
		}
		if ( m < 10 ) {
			m = "0" + m
		}
		btc.innerHTML = h + ":" + m + ":" + s + "<br><a href=" + ps + "?cmd=" + pk + "&cp=" + pl + ">Annuler</a>"
	}
	pp = pp - 1;
	window.setTimeout("t();", 999);
}
