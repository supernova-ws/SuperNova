function keypress(e){
    if(e.keyCode == 13) {
        submitLogin();    
    }
}

function submitLogin(){
    changeAction('login');
    document.loginForm.submit();
}

function openLink(value) {
	if(value != "" && value != 0) {
		var valarr = value.split(/\|/);
		var link = "http://nwl.gameforge.de/index.php?type=redirect&lang=org&zone=ogame&target=" + valarr[1];
		if(valarr[0] == "new_window") {
			window.open(link);
		}
		else {
			top.location.href = link;
		}
	}
}


function checkCookie() {
    var uni = document.loginForm.universe.value;
    getUniFromCookie(uni);
}

function setCookie() {
    var uni = document.loginForm.universe.value;
    var user = document.loginForm.login.value;
    var pw = document.loginForm.pass.value;
    var str = user + "," + pw;
    var date = new Date();
    date.setTime(date.getTime()+(365*24*60*60*1000));
    cookie_setzen(uni, str, date);
}


function getCookie(name) {
   var i=0;  //Suchposition im Cookie
   var suche = name + "=";
   while (i<document.cookie.length) {
      if (document.cookie.substring(i, i + suche.length)
      == suche) {
         var ende = document.cookie.indexOf(";", i
         + suche.length);
         ende = (ende > -1) ? ende :
         document.cookie.length;
         var cook = document.cookie.substring(i
            + suche.length, ende);
         return unescape(cook);
      }
      i++;
   }
   return "";
}


function cookie_setzen() {
  var anzParameter = cookie_setzen.arguments.length;
  var parameter = cookie_setzen.arguments;
  // 1. Cookie-Name
  var name = parameter[0];
  // 2. Cookie-Wert
  var value = (anzParameter >= 2) ? parameter[1] : "";
  value = escape(value); // URL-Codierung
  // 3. Haltbarkeitsdatum
  var expires = (anzParameter >= 3) ? parameter[2] : null;
  if (expires != null) {
    if (expires.toGMTString) {
      expires = expires.toGMTString();
    }
  }
  // 4. Domain
  var domain = (anzParameter >= 4) ? parameter[3] : null;
  // 5. Pfad
  var path = (anzParameter >= 5) ? parameter[4] : null;

  if (path != null) {
    path = escape(path); // Sonderzeichen umwandeln
  }
  // 6. Sicherheitsstufe
  var secure = (anzParameter >= 6) ? parameter[5] : null;
  // Zusammensetzen des Cookies
  var c = name + "=" + escape(value);
  if (expires != null) {
    c += "; expires=" + expires;
  }
  if (domain != null) {

    c += "; domain=" + domain;
  }
  if (path != null) {
    c += "; path=" + path;
  }
  if (secure) {
    c += "; secure";
  }
  // Cookie setzen
  document.cookie = c;
}

function cookie_lesen(name) {
  var i = document.cookie.indexOf(name + "=");
  var c = "";
  if (i > -1) {
    var ende = document.cookie.indexOf("; ",
               i+name.length+1);
    if (ende == -1) {
      ende = document.cookie.length;
    }
    c = document.cookie.substring(i+name.length+1, ende);
  }
  return unescape(c);
}

function getUniFromCookie(uni) {
    var str = cookie_lesen(uni);
    str = unescape(str);
    
    
    if(str.length > 0) {
        var trennzeichenPos = str.search(',');
        var user = str.substring(0, trennzeichenPos);
        var pw = str.substring(trennzeichenPos, str.length);
        
        document.loginForm.login.value = user;
        document.loginForm.pass.value = pw;
    }
    else {
        document.loginForm.login.value = "";
        document.loginForm.pass.value = "";
    }
    
}