var ajax = new sack();

function showInfo(id) {
	printMessage(id, 'infotext');
	document.getElementById('statustext').innerHTML = "";
}

function wait() {
	return;
}

checkUsername.oldname = "";
checkUsername.lastcheck = "103";
function checkUsername() {
	var username = document.forms[0].elements[0].value;
	if (username.length > 2 && username.length < 20) {
		if (username != checkUsername.oldname) {
			checkUsername.oldname = username;
			remoteCheckUsername();
			return checkUsername.lastcheck;
		} else {
			return checkUsername.lastcheck;
		}
	} else {
		checkUsername.lastcheck = "103";
		return "103";
	}
}


checkEmail.oldmail = "";
checkEmail.lastcheck = "104";
function checkEmail() {
	var email = document.forms[0].elements[1].value;
	if (email.length >= 3 && email.length < 64) {
		if (email != checkEmail.oldmail) {
			checkEmail.oldmail = email;
			m = email.match(/[a-zA-Z0-9]+@+[a-zA-Z0-9]+[.]+[a-zA-Z0-9]{2,4}/);
			if (m) {
				remoteCheckEmail();
			} else {
				checkEmail.lastcheck = "104";
				return checkEmail.lastcheck;
			}
		} else {
			return checkEmail.lastcheck;
		}
	} else {
		checkEmail.lastcheck = "104";
		return "104";
	}
}

function remoteCheckUsername() {
	var username = document.forms[0].elements[0].value;
	ajax.requestFile = "check_registration.php";

	// turn on its execute flag
    ajax.runResponse = whenResponse;
    ajax.execute = true;

    ajax.setVar("action", "check_username");
    ajax.setVar("username", username);
    ajax.runAJAX();
}

function remoteCheckEmail() {
	var email = document.forms[0].elements[1].value;
	ajax.requestFile = "check_registration.php";

	// turn on its execute flag
    ajax.runResponse = whenResponse;
    ajax.execute = true;

    ajax.setVar("action", "check_email");
    ajax.setVar("email", email);
    ajax.runAJAX();
}

function checkAGB() {
	if (document.forms[0].elements[2].checked == true) {
		return 0;
	} else{
		return 1;
	}
}

function checkData() {
	//document.getElementById('debug').innerHTML = checkUsername() + " " + checkEmail() + " " + checkAGB();
	if (checkUsername.lastcheck == "0"
		&& checkEmail.lastcheck == "0"
		&& !checkAGB()) {
		document.forms[0].elements[4].disabled = false;
		
	} else {
		document.forms[0].elements[4].disabled = true;
			//document.write(document.forms[0].elements[3].value);
	}
	
}

function pollUsername() {
	pollUsername.interval = setInterval("printMessage(checkUsername())", 1000);
	clearInterval(pollEmail.interval);
}

function stopPollingUsername() {
	clearInterval(pollUsername.interval);
}

function pollEmail() {
	pollEmail.interval = setInterval("printMessage(checkEmail())", 1000);
	clearInterval(pollUsername.interval);
}

function stopPollingEmail() {
	clearInterval(pollEmail.interval);
}


function whenLoading(){
}
      
function whenLoaded(){
}
      
function whenInteractive(){
}

function whenResponse(){
	retVals = this.response.split(" ");
	switch (retVals[0]) {
		case "1": // check username
			checkUsername.lastcheck = retVals[1];
			break;
		case "2": // check email
			checkEmail.lastcheck = retVals[1];
			//checkEmail();
			break;
	}
}
