// ==UserScript==
// @name           OGame Screen of Resources
// @namespace      http://www.midgar.com.ar/grease/ogame/
// @description    Graficzny interfejs surowcýw
// @include        http://ogame*.de/game/*
// @include        http://*.gfsrv.net/game/*
// ==/UserScript==
   function formatNmb(numero)
   {
	  var nNmb = String(numero); 
    var sRes = "";
    for (var j, i = nNmb.length - 1, j = 0; i >= 0; i--, j++)
     sRes = nNmb.charAt(i) + ((j > 0) && (j % 3 == 0)? ".": "") + sRes;
    return sRes;
   }
   
if (document.location.href.indexOf('/game/resources.php') == -1) return;
//GM_log('Init: '+document.location.href);

var T_Recursos = document.getElementsByTagName("td");
var Metal = T_Recursos[18].innerHTML.replace(/\./,'');
var Crystal = T_Recursos[19].innerHTML.replace(/\./,'');
var Deut = T_Recursos[20].innerHTML.replace(/\./,'');

Metal = Metal.replace(/\./g,'');
Crystal = Crystal .replace(/\./g,'');
Deut = Deut.replace(/\./g,'');




var PMetal = T_Recursos[40].innerHTML.replace(/\./g,'');
var PCrystal = T_Recursos[41].innerHTML.replace(/\./g,'');
var PDeut = T_Recursos[42].innerHTML.replace(/\./g,'');

PMetal = PMetal.replace(/\./g,'');
PCrystal = PCrystal .replace(/\./g,'');
PDeut = PDeut.replace(/\./g,'');


var AlmM = T_Recursos[35].innerHTML.replace(/\./g,'');
var AlmC = T_Recursos[36].innerHTML.replace(/\./g,'');
var AlmD = T_Recursos[37].innerHTML.replace(/\./g,'');

AlmM = AlmM.replace(/k/,'000');
AlmC = AlmC.replace(/k/,'000');
AlmD = AlmD.replace(/k/,'000');

AlmM = AlmM.replace(/\./g,'');
AlmC = AlmC.replace(/\./g,'');
AlmD = AlmD.replace(/\./g,'');


if (Metal.indexOf('<font color')!=-1) {
	Metal = Metal.substring(22, Metal.indexOf('</font'));
}
if (Crystal.indexOf('<font color')!=-1) {
	Crystal = Crystal.substring(22, Crystal.indexOf('</font'));
}
if (Deut.indexOf('<font color')!=-1) {
	Deut = Deut.substring(22, Deut.indexOf('</font'));
}

if (PMetal.indexOf('<font color')!=-1) {
	PMetal = PMetal.substring(22, PMetal.indexOf('</font'));
}
if (PCrystal.indexOf('<font color')!=-1) {
	PCrystal = PCrystal.substring(22, PCrystal.indexOf('</font'));
}
if (PDeut.indexOf('<font color')!=-1) {
	PDeut = PDeut.substring(22, PDeut.indexOf('</font'));
}

if (AlmM.indexOf('<font color')!=-1) {
	AlmM = AlmM.substring(22, AlmM.indexOf('</font'));
}
if (AlmC.indexOf('<font color')!=-1) {
	AlmC = AlmC.substring(22, AlmC.indexOf('</font'));
}
if (AlmD.indexOf('<font color')!=-1) {
	AlmD = AlmD.substring(22, AlmD.indexOf('</font'));
}

var XMetal = new Array(3);
var XCrystal = new Array(3);
var XDeut = new Array(3);

XMetal[0] = PMetal * 24;
XCrystal[0] = PCrystal * 24;
XDeut[0] = PDeut *24;
XMetal[1] = PMetal * 168;
XCrystal[1] = PCrystal * 168;
XDeut[1] = PDeut * 168;
XMetal[2] = PMetal * 720;
XCrystal[2] = PCrystal * 720;
XDeut[2] = PDeut * 720;

// Buscar Formulario de Recursos

var ResFormC, T_Form, ResForm;
ResFormC = document.getElementsByTagName('table');

for (var i = 0; i < ResFormC.length; i++) {
	
	T_Form = ResFormC[i];
	if (T_Form.getAttribute('width') == '550') {
		ResForm = T_Form;
	}
}

// Buscar Factor de Produccion
var T_Factor = /Wsp\u00F3\u0142czynnik produkcji(.)*\:(.)*[0-9.]/gi.exec(document.body.innerHTML);

var Factor, FactorPorc;
if (T_Factor.length) {
	Factor=T_Factor[0].split(":");
	Factor=parseFloat(Factor[1]) * 100;
	FactorPorc=parseInt(parseFloat(Factor) * 2.5);
}

// Agregar tabla de factor de produccion
if (ResForm) {
	// Buscar Produccion Real

	

	// Procesar Tablas
	var ProdFact = document.createElement('div');

	ProdFact.innerHTML = '<table width="500"><tr>'+
'<th>Wsp\u00F3\u0142czynnik produkcji</th>'+
'<th>'+Factor+'%</th>'+
'<th width="250"><div style="border: 1px solid #FFFFFF; width: 250px;"><div style="background-color: '+(Factor < 100 ? '#C00000' : '#00C000' )+'; width: '+FactorPorc+'px;">&nbsp;</div></div></th>'+
'</tr></table><br />';
	
	var CuentaRec = document.createElement('div');

	CuentaRec.innerHTML = '<br /><table width="500">'+
'<tr><td class="c" colspan="4">Wydoycie</td></tr>'+
'<tr>'+
'<td class="c">&nbsp;</td>'+
'<th>24h</th>'+
'<th>Tydzien</th>'+
'<th>Miesi\u0105c</th>'+
'</tr>'+
'<tr>'+
'<td class="c">Metal</td>'+
'<th><font color="#00ff00">'+formatNmb(XMetal[0])+'</font></th>'+
'<th><font color="#00ff00">'+formatNmb(XMetal[1])+'</font></th>'+
'<th><font color="#00ff00">'+formatNmb(XMetal[2])+'</font></th>'+
'</tr>'+
'<tr>'+
'<td class="c">Cristal</td>'+
'<th><font color="#00ff00">'+formatNmb(XCrystal[0])+'</font></th>'+
'<th><font color="#00ff00">'+formatNmb(XCrystal[1])+'</font></th>'+
'<th><font color="#00ff00">'+formatNmb(XCrystal[2])+'</font></th>'+
'</tr>'+
'<tr>'+
'<td class="c">Deut</td>'+
'<th><font color="#00ff00">'+formatNmb(XDeut[0])+'</font></th>'+
'<th><font color="#00ff00">'+formatNmb(XDeut[1])+'</font></th>'+
'<th><font color="#00ff00">'+formatNmb(XDeut[2])+'</font></th>'+
'</tr>'+
'</table><br />';

	var EAlmM=(Metal / AlmM) * 100;
	var EAlmMPorc=parseInt((Metal / AlmM) * 250);
	var EAlmC=(Crystal / AlmC) * 100;
	var EAlmCPorc=parseInt((Crystal / AlmC) * 250);
	var EAlmD=(Deut / AlmD) * 100;
	var EAlmDPorc=parseInt((Deut / AlmD) * 250);

	EAlmM = Math.round(EAlmM);
	EAlmC = Math.round(EAlmC);
	EAlmD = Math.round(EAlmD);




	CuentaRec.innerHTML += '<table width="500">'+
'<tr><td class="c" colspan="3">Magazyny</td></tr>'+
'<tr>'+
'<th>Metal</th>'+
'<th>'+EAlmM+'%</th>'+
'<th width="250"><div style="border: 1px solid #FFFFFF; width: 250px;"><div style="background-color: '+(EAlmM > 100 ? '#C00000' : '#00C000' )+'; width: '+(EAlmMPorc > 250 ? 250 : EAlmMPorc)+'px;">&nbsp;</div></div></th>'+
'</tr>'+
'<tr>'+
'<th>Kryszta\u0142</th>'+
'<th>'+EAlmC+'%</th>'+
'<th width="250"><div style="border: 1px solid #FFFFFF; width: 250px;"><div style="background-color: '+(EAlmC > 100 ? '#C00000' : '#00C000' )+'; width: '+(EAlmCPorc > 250 ? 250 : EAlmCPorc)+'px;">&nbsp;</div></div></th>'+
'</tr>'+
'<tr>'+
'<th>Deuter</th>'+
'<th>'+EAlmD+'%</th>'+
'<th width="250"><div style="border: 1px solid #FFFFFF; width: 250px;"><div style="background-color: '+(EAlmD > 100 ? '#C00000' : '#00C000' )+'; width: '+(EAlmDPorc > 250 ? 250 : EAlmDPorc)+'px;">&nbsp;</div></div></th>'+
'</tr>'+
'</table><br />';


	ResForm.parentNode.insertBefore(CuentaRec, ResForm.nextSibling);
	ResForm.parentNode.insertBefore(ProdFact, ResForm);
	//document.body.innerHTML = document.body.innerHTML.replace(/Fattore di produzione(.)+n\:(.)*[0-9.]/gi,'');

}