// ==UserScript==
// @name          Nieaktywni
// @namespace     Ktoska
// @description	  Nieaktywni w galaktyce , podswietlanie
// @include  http://*ogame*.*/galaxy.php*   
// ==/UserScript==

var allElements, thisElement;
allElements = document.getElementsByTagName('span');
for (var i = 0; i < allElements.length; i++) {
    thisElement = allElements[i];    
	if (thisElement.className.substring(0,8)=='inactive')
		{
		thisElement.style.color = "red";
				}
	if (thisElement.className.substring(0,12)=='longinactive')
		{
		thisElement.style.color = "maroon";
				}
}  



