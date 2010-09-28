<?php
if (!defined('INSIDE')) {
	die("Защита от взлома!");
}

$lang['Login'] = 'Логин';
$lang['User_name'] = 'Имя:';
$lang['Password'] = 'Пароль:';
$lang['Please_Login'] = 'Пожалуйста <a href="login.php" target="_main">войдите...</a>';
$lang['Please_Wait'] = 'Подождите';
$lang['Remember_me'] = 'Запомнить меня';
$lang['Register'] = 'Регистрация';
$lang['Login_Error'] = 'Ошибка';
$lang['PleaseWait'] = 'Подождите';
$lang['PasswordLost'] = 'Забыли пароль?';

$lang['Login_Ok'] = 'Успешное подключение, <a href="./"><blink>перенаправление...</blink></a><br><center><img src="images/progressbar.gif"></center>';
$lang['Login_FailPassword'] = 'Неверное имя и/или пароль<br /><a href="login.php" target="_top">Назад</a>';
$lang['Login_FailUser'] = 'Такого игрока не существует.<br><a href=login.php>Назад</a>';

$lang['log_univ'] = 'Добро пожаловать в нашу Вселенную!';
$lang['log_reg'] = 'Регистрация';
$lang['log_reg_main'] = 'Регистрируйся сейчас!';
$lang['log_menu'] = 'Меню';
$lang['log_stat_menu'] = 'Статистика игроков';
$lang['log_enter'] = 'Войти';
$lang['log_cred'] = 'О сервере';
$lang['log_faq'] = 'FAQ по игре';
$lang['log_forums'] = 'Форум';
$lang['log_contacts'] = 'Администрация';
$lang['log_desc'] = '<strong>Сверхновая — это онлайновая мультиплеерная космическая браузерная стратегия.</strong> Тысячи игроков выступают одновременно против друг друга. Для игры Вам нужен лишь обычный браузер.';
$lang['log_toreg'] = 'Зарегистрируйся сейчас!';
$lang['log_online'] = 'Игроков Онлайн';
$lang['log_lastreg'] = 'Новичок';
$lang['log_numbreg'] = 'Всего аккаунтов';
$lang['log_welcome'] = 'Добро пожаловать в';
$lang['vacation_mode'] = 'Вы в отпуске<br> отключить режим отпуска можно через ';
$lang['hours'] = ' часов';
$lang['vacations'] = 'Режим отпуска';

$lang['log_rules'] = "Правила игры";
$lang['log_banned'] = 'Список забаненных';

$lang['log_see_you'] = 'Надеемся вас снова увидеть на просторах нашей Вселенной. Удачи!<br><a href="login.php">Перейти на страницу входа в игру</a>';
$lang['log_session_closed'] = "Сессия закрыта.";

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
$lang['reg_i_agree']         = 'Я ознакомился и согласен с';
$lang['reg_with_rules']      = 'правилами игры';


$lang['signup']          	  = 'Зарегистрироваться';
$lang['neededpass']      	  = 'Пароль';
$lang['Languese']        	  = 'Язык';
$lang['log_reg_text0']    	  = 'Перед регистрацией ознакомьтесь с';
$lang['log_reg_text1']    	  = 'Регистрация означает, что вы полность прочли и согласились со всеми пунктами правил. Если вы не согласны хоть с каким-то пунктом правил - пожалуйста, не регестрируйтесь.';

// Для того чтобы играть вам нужно зарегестрироваться. Введите <strong>Имя пользователя</strong>, <strong>пароль</strong> и <strong>E-Mail адрес</strong>.';

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
$lang['log_stat_menu']	 = 'Статистика';
$lang['log_cred']	 = 'О сервере';
$lang['log_faq'] 	 = 'FAQ по игре';
$lang['log_forums']	 = 'Форум';
$lang['log_contacts'] 	 = 'Администрация';

$lang['log_login_page'] 	 = 'Войти в игру';
$lang['log_reg_already'] = 'Уже есть регистрация? Воспользутесь ссылкой ';
?>