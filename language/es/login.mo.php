<?php

/*
#############################################################################
#  Filename: login.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [Russian]
* @version 46d0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

global $config;

$a_lang_array = array(
  'Login' => 'Iniciar sesión',
  'User_name' => 'Nombre:',
  'Authorization' => 'Autorización',
  'Please_Login' => 'Por favor <a href="login.php" target="_main">ingrese...</a>',
  'Please_Wait' => 'Espere',
  'Remember_me' => 'Recordarme',
  'Register' => 'Información de error',
  'Login_Error' => 'Error',
  'PleaseWait' => 'Espere',
  'PasswordLost' => 'Recuperar contraseña',
  'Login_Ok' => 'Conexión exitosa, <a href="./"><blink>redireccionando...</blink></a><br><center><img src="design/images/progressbar.gif"></center>',
  'Login_FailPassword' => 'Nombre y/o contraseña incorrectos<br /><a href="login.php" target="_top">Volver</a>',
  'Login_FailUser' => 'El jugador no existe.<br><a href=login.php>Volver</a>',
  'log_univ' => '¡Bienvenido a nuestro Universo!',
  'log_reg' => 'Registrarse',
  'log_reg_main' => 'Registrarse',
  'log_menu' => 'Menú',
  'log_stat_menu' => 'Estadísticas',
  'log_enter' => 'Entrar',
  'log_news' => 'Noticias del servidor',
  'log_cred' => 'Acerca del servidor',
  'log_faq' => 'FAQ del juego',
  'log_forums' => 'Foro',
  'log_contacts' => 'Administración',
  'log_desc' => '<strong>SuperNova es una estrategia espacial multijugador en línea.</strong> Miles de jugadores compiten simultáneamente. Solo necesitas un navegador web.',
  'log_toreg' => '¡Regístrate ahora!',
  'log_online' => 'Jugadores en línea',
  'log_lastreg' => 'Nuevo jugador',
  'log_numbreg' => 'Cuentas totales',
  'log_welcome' => 'Bienvenido a',
  'vacation_mode' => 'Estás en modo vacaciones<br>puedes desactivarlo en ',
  'hours' => ' horas',
  'vacations' => 'Modo vacaciones',
  'log_scr1' => 'Captura de pantalla del astillero, donde se construyen y ordenan naves en el planeta actual. Haz clic para ampliar.',
  'log_scr2' => 'Captura de estadísticas, muestra tu ranking entre otros jugadores. Haz clic para ampliar.',
  'log_scr3' => 'Captura del universo, muestra tu planeta en el universo. Haz clic para ampliar.',
  'log_rules' => 'Reglas del juego',
  'log_banned' => 'Lista de baneados',
  'log_see_you' => 'Esperamos verte de nuevo en nuestro Universo. ¡Buena suerte!<br><a href="login.php">Ir a la página de inicio</a>',
  'log_session_closed' => 'Sesión cerrada.',
  'registry' => 'Registro',
  'form' => 'Formulario de registro',
  'Undefined' => '- indefinido -',
  'Male' => 'Masculino',
  'Female' => 'Femenino',
  'Multiverse' => 'XNova',
  'E-Mail' => 'Correo electrónico',
  'MainPlanet' => 'Nombre del planeta principal',
  'GameName' => 'Nombre',
  'gender' => 'Género',
  'accept' => 'Acepto las reglas',
  'reg_i_agree' => 'He leído y acepto los',
  'reg_with_rules' => 'términos del juego',
  'signup' => 'Registrarse',
  'Languese' => 'Idioma',
  'log_reg_text0' => 'Antes de registrarte, lee las',
  'log_reg_text1' => 'El registro implica que has leído y aceptado todas las reglas. Si no estás de acuerdo con algún punto, por favor no te registres.',
  'thanksforregistry' => '¡Felicidades por tu registro! Serás redirigido a tu planeta en 10 segundos. Si no ocurre, haz clic <a href=overview.php><u>aquí</u></a>',
  'welcome_to_universe' => '¡Bienvenido a OGame!!!',
  'please_click_url' => 'Para activar tu cuenta, haz clic en este enlace',
  'regards' => '¡Buena suerte!',
  'error_lang' => '¡Este idioma no es soportado!<br />',
  'error_mail' => '¡Correo electrónico inválido!<br />',
  'error_planet' => '¡Otro planeta ya tiene ese nombre!<br />',
  'error_hplanetnum' => '¡El nombre del planeta solo puede contener letras latinas!<br />',
  'error_character' => '¡Nombre inválido!<br />',
  'error_charalpha' => '¡Solo puedes usar letras latinas!<br />',
  'error_password' => '¡La contraseña debe tener al menos 4 caracteres!<br />',
  'error_rgt' => '¡Debes aceptar las reglas!<br />',
  'error_userexist' => '¡Ese nombre ya está en uso!<br />',
  'error_emailexist' => '¡Ese correo ya está registrado!<br />',
  'error_sex' => '¡Error en la selección de género!<br />',
  'error_mailsend' => 'Error al enviar el correo, tu contraseña: ',
  'reg_welldone' => '¡Registro completado! Tu contraseña ha sido enviada a tu correo. Aquí está de nuevo por si acaso:<br>',
  'error_captcha' => '¡Código gráfico incorrecto!<br/>',
  'error_v' => '¡Inténtalo de nuevo!<br />',
  'log_login_page' => 'Ingresar al juego',
  'log_reg_already' => '¿Ya tienes cuenta? Haz clic ',
  'log_reg_already_lost' => '¿Olvidaste tu contraseña? Haz clic ',

  'log_lost_header' => 'Restablecer contraseña',
  'log_lost_code' => 'Código de verificación',
  'log_lost_description2' => 'Si tienes un código de verificación, ingrésalo y haz clic en "Restablecer contraseña". Recibirás un correo con tu nueva contraseña.<br /><br />
    Si no ves el correo, revisa tu carpeta de spam. Nuestro mensaje pudo ser marcado como no deseado.<br /><span style="color: red;">¡ATENCIÓN! mail.ru bloquea nuestros correos. Contacta al administrador.</span><br /><br />
    Si aún no lo encuentras, escribe al administrador: <span class="ok">' . $config->server_email . '</span>',
  'log_lost_reset_pass' => 'Restablecer contraseña',
  'log_lost_send_mail' => 'Enviar código de verificación',
  'log_lost_sent_code' => 'Se ha enviado un correo con instrucciones para restablecer tu contraseña',
  'log_lost_sent_pass' => 'También se ha enviado un correo con tu nueva contraseña',

  'log_lost_err_email' => 'El correo no está registrado. Posibles causas:<br>Error al escribir el correo. Intenta nuevamente<br>Tu cuenta fue eliminada por inactividad. Regístrate de nuevo<br>Estás intentando acceder al universo incorrecto',
  'log_lost_err_sending' => 'Error al enviar el correo. Contacta al administrador',
  'log_lost_err_code' => 'Código de verificación inválido. Posibles causas:<br>Error al ingresar el código<br>Estás usando el código en el universo equivocado<br>Tu cuenta fue eliminada<br>El código ha expirado',
  'log_lost_err_admin' => 'El equipo del servidor no puede usar esta función. Contacta al administrador',
  'log_lost_err_change' => 'Error al cambiar la contraseña. Contacta al administrador',

  'log_lost_description1' => 'Ingresa el correo electrónico asociado a tu cuenta para recibir un código de verificación',
  'login_register_offer' => 'Haz clic aquí para registrarte',
  'login_password_restore_offer' => 'Haz clic aquí para restablecer tu contraseña',

  'login_register_email_hint' => 'Usa un correo válido - el dueño del correo es considerado dueño de la cuenta<br />
    <span style="color: red;">¡ATENCIÓN! No uses correos de @mail.ru - no recibirás nuestros mensajes!</span>',

  'login_account_name_or_email' => 'Е-мейл',

);
