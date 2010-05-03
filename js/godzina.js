document.write('<span id="date1"></span>');
function getCurrentDate1()
{       objDate = new Date();

        hours   = objDate.getHours() < 10 ? '0' + objDate.getHours() : objDate.getHours();
        minutes = objDate.getMinutes() < 10 ? '0' + objDate.getMinutes() : objDate.getMinutes();
        seconds = objDate.getSeconds() < 10 ? '0' + objDate.getSeconds() : objDate.getSeconds();

        return currentDate = hours + ':' + minutes + ':' + seconds;
}
function setCurrentDate1()
{ document.getElementById( 'date1' ).innerHTML = getCurrentDate1();}
setCurrentDate1();
setInterval( 'setCurrentDate1()', 1000 );