function t() {
	v = new Date();
	n = new Date();
	o = new Date();
	for (cn = 1; cn <= anz; cn++) {
		bxx = document.getElementById('bxx' + cn);
		ss  = bxx.title;
		s   = ss - Math.round((n.getTime() - v.getTime()) / 1000.);
		m   = 0;
		h   = 0;
		if (s < 0) {
			bxx.innerHTML = "-";
		} else {
			if (s > 59) {
				m = Math.floor(s/60);
				s = s - m * 60;
			}
			if (m > 59) {
				h = Math.floor(m / 60);
				m = m - h * 60;
			}
			if (s < 10) {
				s = "0" + s;
			}
			if (m < 10) {
				m = "0" + m;
			}
		bxx.innerHTML = h + ":" + m + ":" + s + "";
		}
		bxx.title = bxx.title - 1;
	}
	window.setTimeout("t();", 999);
}
