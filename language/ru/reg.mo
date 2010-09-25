<?php
if (!defined('INSIDE')) {
	die("Защита от взлома!");
}

// Регистрационная форма	
$lang['registry']        	  = 'Регистрация';
$lang['form']             	 = 'Форма регистрации';
$lang['Register']         	 = 'Информация о ошибке';
$lang['Undefined']        	 = '- неопределённый -';
$lang['Male']             	 = 'Мужской';
$lang['Female']           	 = 'Женский';
$lang['Multiverse']       	 = 'XNova';
$lang['E-Mail']          	  = 'Адрес e-Mail';
$lang['MainPlanet']      	  = 'Имя главной планеты';
$lang['GameName']        	  = 'Имя';
$lang['Sex']             	  = 'Пол';
$lang['accept']          	  = 'Я согласен с правилами';
$lang['signup']          	  = 'Зарегистрироваться';
$lang['neededpass']      	  = 'Пароль';
$lang['Languese']        	  = 'Язык';
$lang['ru']              	  = 'Русский';
$lang['en']              	  = 'Английский';
$lang['log_reg_text']    	  = 'Для того чтобы играть вам необходимо ввести <strong>Имя пользователя</strong>, <strong>пароль</strong> и<strong> E-Mail адрес</strong>, а также <strong> прочитать пользовательское соглашение</strong> до активации галочки о его прочтении.';

// Отсылается на почту
$lang['mail_welcome']		= 'Спасибо за регистрацию {gameurl}\n Ваш пароль: {password}\n\n Удачи!\n{gameurl}';
$lang['mail_title']		= 'Ваша регистрация OGame';
$lang['thanksforregistry'] 	= 'Поздравляем вас с успешной регистрацией! Теперь вы можете войти в игру, использую свои имя и пароль. Удачной игры! <a href=login.php>Перейти на страницу ввода пароля</a>';
$lang['welcome_to_universe']	= 'Добро пожаловать в OGame!!!';
$lang['your_password']		= 'Ваш пароль';
$lang['please_click_url']	= 'Для того чтобы использовать аккаунт, вы должны активировать его нажав на эту ссылку';
$lang['regards']		= "Удачи!";

// Ошибки
$lang['error_mail']		= 'Неверный E-Mail !<br />';
$lang['error_planet']		= 'Другая планета уже имеет то же название !<br />';
$lang['error_hplanetnum']	= 'Название планеты должно быть написано ТОЛЬКО латинскими буквами !<br />';
$lang['error_character']	= 'Неверное имя !<br />';
$lang['error_charalpha']	= 'Вы можете использовать ТОЛЬКО латинские буквы !<br />';
$lang['error_password']		= 'Пароль должен состоять как минимум из 4 знаков !<br />';
$lang['error_rgt']		= 'Вы должны согласиться с правилами !<br />';
$lang['error_userexist']	= 'Такое имя уже используется !<br />';
$lang['error_emailexist']	= 'Такой e-mail уже используется !<br />';
$lang['error_sex'] 	 	= 'Ошибка в выборе пола !<br />';
$lang['error_mailsend']  	= 'Ошибка в отправлении электронной почты, ваш пароль: ';
$lang['reg_welldone']		= 'Регистрация завершена !';
$lang['error_captcha']		= 'Неверный графический код !<br/>';
$lang['error_v']		= 'Повторить еще раз !<br />';

// Меню
$lang['log_menu']	 = 'Меню';
$lang['log_reg'] 	 = 'Регистрация';
$lang['log_stat_menu']	 = 'Статистика';
$lang['log_cred']	 = 'О сервере';
$lang['log_faq'] 	 = 'FAQ по игре';
$lang['log_forums']	 = 'Форум';
$lang['log_contacts'] 	 = 'Администрация';

$lang['log_servername'] = 'OGame Triolan';
?>