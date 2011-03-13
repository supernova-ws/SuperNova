<?php
if (!defined('INSIDE')) {
	die("attempt hacking!");
}

$lang['Login'] = 'Login';
$lang['User_name'] = 'Username:';
$lang['Password'] = 'Password:';
$lang['Please_Login'] = 'You are welcome <a href="login.php" target="_main">login...</a>';
$lang['Please_Wait'] = 'Please wait';
$lang['Remember_me'] = 'Remember me';
$lang['Register'] = 'Register';
$lang['Login_Error'] = 'Login error';
$lang['PleaseWait'] = 'Please wait';
$lang['PasswordLost'] = 'Lost password?';

$lang['Login_Ok'] = 'Successfully connected, <a href="./"><blink>redirection...</blink></a><br><center><img src="design/images/progressbar.gif"></center>';
$lang['Login_FailPassword'] = 'Incorrect name and/or password<br /><a href="login.php" target="_top">Back</a>';
$lang['Login_FailUser'] = 'This player does not exist.<br><a href=login.php>Back</a>';

$lang['log_univ'] = 'Universe log!';
$lang['log_reg'] = 'Register';
$lang['log_reg_main'] = 'Rules!';
$lang['log_menu'] = 'Menu';
$lang['log_stat_menu'] = 'stat menu';
$lang['log_enter'] = 'Login';
$lang['log_cred'] = 'About server';
$lang['log_faq'] = 'FAQ on the game';
$lang['log_forums'] = 'Forum';
$lang['log_contacts'] = 'Contact';
$lang['log_desc'] = '<strong>Supernova is a browser based online multiplayer space strategy.</strong> Thousands of players are simultaneously against one another. For the game you need only a browser.';
$lang['log_toreg'] = 'Sign up now!';
$lang['log_online'] = 'Players Online';
$lang['log_lastreg'] = 'Newbie';
$lang['log_numbreg'] = 'Total accounts';
$lang['log_welcome'] = 'Welcome to';
$lang['vacation_mode'] = 'Your in vacation Mode<br> You can turn off vacation mode ';
$lang['hours'] = ' Hours';
$lang['vacations'] = 'Vacation Mode';

$lang['log_rules'] = "Rules of the game";
$lang['log_banned'] = 'List of currently banned';

$lang['log_see_you'] = 'Hope to see you again at the expanse of our universe. Good luck!<br><a href="login.php">Go to the login page in the game</a>';
$lang['log_session_closed'] = "Session closed.";

// Registration form	
$lang['registry']        	  = 'Registration';
$lang['form']             	 = 'Registration form';
$lang['Register']         	 = 'Information about the error';
$lang['Undefined']        	 = '- undetermined -';
$lang['Male']             	 = 'Male';
$lang['Female']           	 = 'Female';
$lang['Multiverse']       	 = 'XNova';
$lang['E-Mail']          	  = 'E-Mail address';
$lang['MainPlanet']      	  = 'The name of your planet';
$lang['GameName']        	  = 'Game name';
$lang['Sex']             	  = 'Sex';
$lang['accept']          	  = 'Accept';
$lang['reg_i_agree']         = 'I have read and agree with';
$lang['reg_with_rules']      = 'Rules of the game';


$lang['signup']          	  = 'Register';
$lang['neededpass']      	  = 'Password';
$lang['Languese']        	  = 'Language';
$lang['log_reg_text0']    	  = 'Before registering please read';
$lang['log_reg_text1']    	  = 'Registration means that you have read and fully agree with all points of the rules. If you do not agree with any paragraph rules-please register.';

// In order to play you need to register. Type <strong>User Name</strong>, <strong>Password</strong> and <strong>E-Mail Address</strong>.';

// Sent by mail
$lang['mail_welcome']		= 'Thanks for registering {gameurl}\n Your password: {password}\n\n Luck!\n{gameurl}';
$lang['mail_title']		= 'Your registration OGame';
$lang['thanksforregistry'] 	= "Congratulations on your successful registration! Now you can <a href=overview." . PHP_EX . "><u>start the game!</u></a>";
$lang['welcome_to_universe']	= 'Welcome to OGame!!!';
$lang['your_password']		= 'Your password';
$lang['please_click_url']	= 'In order to use the account, you must activate it by clicking on this link';
$lang['regards']		= "Good luck!";

// Errors
$lang['error_lang']		= 'This language is not supported!<br />';
$lang['error_mail']		= 'Wrong E-Mail !<br />';
$lang['error_planet']		= 'Another planet has the same name !<br />';
$lang['error_hplanetnum']	= 'Name the planet must be written with Latin letters ONLY !<br />';
$lang['error_character']	= 'Incorrect name !<br />';
$lang['error_charalpha']	= 'You can use only letters !<br />';
$lang['error_password']		= 'Password must be at least 4 characters !<br />';
$lang['error_rgt']		= 'You must comply with the rules !<br />';
$lang['error_userexist']	= 'This name is already in use !<br />';
$lang['error_emailexist']	= 'This e-mail is already in use !<br />';
$lang['error_sex'] 	 	= 'Error in the choice of sex !<br />';
$lang['error_mailsend']  	= 'Error in sending the email, your password: ';
$lang['reg_welldone']		= 'Registration complete! Your password was specified when registering the mailbox. Here it is again just in case';
$lang['error_captcha']		= 'The wrong graphic code !<br/>';
$lang['error_v']		= 'Try it again !<br />';

// Menu
$lang['log_menu']	 = 'Menu';
$lang['log_stat_menu']	 = 'Statistics';
$lang['log_cred']	 = 'About server';
$lang['log_faq'] 	 = 'FAQ';
$lang['log_forums']	 = 'Forum';
$lang['log_contacts'] 	 = 'Administration';

$lang['log_login_page'] 	 = 'Enter the game';
$lang['log_reg_already'] = 'Already have a registration? ';
$lang['log_reg_already_lost'] = 'Forgot password?';

// "Lost password" text strings
$lang['log_lost_header']       = 'Password recovery';
$lang['log_lost_description1'] = 'Enter the email address you registered your account. It will be sent an email with a verification code to reset your password';
$lang['log_lost_send_mail']    = 'Send Mail';

$lang['log_lost_code']         = 'Confirmation code';
$lang['log_lost_description2'] = 'If you have a confirmation code, please enter it below and click "Reset password". An e-mail will be sent an email with a new password';
$lang['log_lost_reset_pass']   = 'Reset password';

$lang['log_lost_sent_code']    = 'Email sent to this email with further instructions on resetting your password';
$lang['log_lost_sent_pass']    = 'Just sent to your email message with the new password';
                              
$lang['log_lost_email_title']  = "{$lang['sys_supernova']}, Server {$config->game_name}: Password reset";
$lang['log_lost_email_code']   = "Someone (possibly you) has requested a reset password on server {$config->game_name} Games '{$lang['sys_supernova']}'. If you did not request reset password-then just ignore this email.\r\n For password reset, go to the address %1\$s?confirm=%2\$s or enter the confirmation code \"%2\$s\" (WITHOUT THE DOUBLE QUOTES!) on the page %1\$s\r\n This code will be valid up to %3\$s. After the password reset you will need to request a new confirmation code";
$lang['log_lost_email_pass']   = "You changed your password on the server {$config->game_name} Games '{$lang['sys_supernova']}'. The following line shows your new password:\r\n%s\r\n Remember it!";
                              
$lang['log_lost_err_email']    = 'This email is not registered in the database. This could mean one of the following:<br>You miss typed the email. Return to the previous page and try again<br>Your account has been deleted due to inactivity. Register a new<br>You are trying to enter the wrong Gaming Universe. Double check the name of the current Universe and on Error Go To correct Universe';
$lang['log_lost_err_sending']  = 'Error sending message to the specified by email. Notify Administrator of the error';
$lang['log_lost_err_code']     = 'The verification code is not registered in the database. This could mean one of the following:<br>You mistype the confirmation code. Return to the previous page and enter the code<br>You are trying to enter the confirmation code in the wrong Universe for which it was generated. Double check the name of the current Universe and on Error Go To correct Universe<br>Your account has been deleted due to inactivity. Register a new<br>Expired confirmation code. Check the expiration date of code in the letter. If it passed, request a new confirmation code';
$lang['log_lost_err_admin']    = 'The members server command (moderators, operators, administrators, etc) may not use the password reset function. Contact your server administrator to change the password';
$lang['log_lost_err_change']   = 'Error changing the password in the database. Notify Administrator of error';

?>
