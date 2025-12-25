<?php
/** @noinspection HtmlUnknownTarget */

/*
#############################################################################
#  Filename: options.mo
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
* @system [Spanish]
* @version 46d0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = [
  'opt_account' => 'Perfil',
  'opt_int_options' => 'Interfaz',
  'opt_settings_statistics' => 'Estadísticas del jugador',
  'opt_settings_info' => 'Información del jugador',
  'opt_alerts' => 'Notificaciones',
  'opt_common' => 'General',
  'opt_tutorial' => 'Tutorial',

  'opt_birthday' => 'Cumpleaños',

  'opt_header' => 'Configuración de usuario',
  'opt_messages' => 'Notificaciones automáticas',
  'opt_msg_saved' => 'Configuración guardada correctamente',
  'opt_msg_name_changed' => 'Nombre de usuario cambiado correctamente',
  'opt_msg_name_change_err_used_name' => 'Este nombre pertenece a otro usuario',
  'opt_msg_name_change_err_no_dm' => 'No hay suficiente Materia Oscura para cambiar el nombre',

  'username_old' => 'Nombre actual',
  'username_new' => 'Nuevo nombre',
  'username_change_confirm' => 'Cambiar nombre',
  'username_change_confirm_payed' => 'por',

  'opt_msg_pass_changed' => 'Contraseña cambiada correctamente',
  'opt_err_pass_wrong' => 'Contraseña actual incorrecta. La contraseña no se cambió',
  'opt_err_pass_unmatched' => 'Las contraseñas no coinciden. La contraseña no se cambió',
  'changue_pass' => 'Cambiar contraseña',
  'Download' => 'Descargar',
  'userdata' => 'Información',
  'username' => 'Nombre',
  'lastpassword' => 'Contraseña anterior',
  'newpassword' => 'Nueva contraseña<br>(mín. 8 caracteres)',
  'newpasswordagain' => 'Repetir nueva contraseña',
  'emaildir' => 'Dirección de email',
  'emaildir_tip' => 'Esta dirección puede cambiarse en cualquier momento. Se convertirá en la principal si no se modifica durante 7 días.',
  'permanentemaildir' => 'Dirección de email principal',
  'opt_planet_sort_title' => 'Ordenar planetas por',
  'opt_planet_sort_options' => [
    SORT_ID       => 'Fecha de colonización',
    SORT_LOCATION => 'Coordenadas',
    SORT_NAME     => 'Orden alfabético',
    SORT_SIZE     => 'Número de campos',
  ],
  'opt_planet_sort_ascending' => [
    SORT_ASCENDING  => 'Ascendente',
    SORT_DESCENDING => 'Descendente',
  ],

  'opt_navbar_title' => 'Barra de navegación',
  'opt_navbar_description' => 'La barra de navegación (o simplemente "navbar") se encuentra en la parte superior de la pantalla. Esta sección permite configurar su apariencia',
  'opt_navbar_resourcebar_description' => 'Barra de recursos',
  'opt_navbar_buttons_title' => 'Configuración de botones del navbar',
  'opt_player_options' => [
    PLAYER_OPTION_NAVBAR_PLANET_VERTICAL        => 'Barra de recursos vertical',
    PLAYER_OPTION_NAVBAR_PLANET_DISABLE_STORAGE => 'Ocultar capacidad de almacenamiento en la barra de recursos',
    PLAYER_OPTION_NAVBAR_PLANET_OLD             => 'Usar visualización antigua de recursos en tabla',

    PLAYER_OPTION_NAVBAR_RESEARCH_WIDE          => 'Botón de investigación ancho (visualización antigua)',
    PLAYER_OPTION_NAVBAR_DISABLE_RESEARCH       => 'Desactivar botón de investigación',
    PLAYER_OPTION_NAVBAR_DISABLE_PLANET         => 'Desactivar botón de planeta',
    PLAYER_OPTION_NAVBAR_DISABLE_HANGAR         => 'Desactivar botón de astillero',
    PLAYER_OPTION_NAVBAR_DISABLE_DEFENSE        => 'Desactivar botón de defensa',
    PLAYER_OPTION_NAVBAR_DISABLE_EXPEDITIONS    => 'Desactivar botón de expediciones',
    PLAYER_OPTION_NAVBAR_DISABLE_FLYING_FLEETS  => 'Desactivar botón de flotas en vuelo',
    PLAYER_OPTION_NAVBAR_DISABLE_QUESTS         => 'Desactivar botón de misiones',
    PLAYER_OPTION_NAVBAR_DISABLE_META_MATTER    => 'Desactivar botón de MetaMateria',

    PLAYER_OPTION_UNIVERSE_OLD                  => 'Usar visualización antigua del "Universo"',
    PLAYER_OPTION_UNIVERSE_DISABLE_COLONIZE     => 'Desactivar botón de colonización',
    PLAYER_OPTION_DESIGN_DISABLE_BORDERS        => 'Desactivar bordes de imágenes en tablas',
    PLAYER_OPTION_TECH_TREE_TABLE               => 'Página de Tecnologías en formato de tabla (visualización antigua)',
    PLAYER_OPTION_FLEET_SHIP_SELECT_OLD         => 'Cantidad de naves en columna separada (visualización antigua)',
    PLAYER_OPTION_FLEET_SHIP_HIDE_SPEED         => 'No mostrar velocidad de la nave',
    PLAYER_OPTION_FLEET_SHIP_HIDE_CAPACITY      => 'No mostrar capacidad de carga de la nave',
    PLAYER_OPTION_FLEET_SHIP_HIDE_CONSUMPTION   => 'No mostrar consumo de combustible de la nave',
    PLAYER_OPTION_TUTORIAL_DISABLED             => 'Desactivar completamente el tutorial',
    PLAYER_OPTION_TUTORIAL_WINDOWED             => 'Mostrar texto tutorial en ventana emergente (popup)',
    PLAYER_OPTION_TUTORIAL_CURRENT              => 'Reiniciar tutorial - comenzará desde el principio',

    PLAYER_OPTION_PLANET_SORT_INVERSE           => 'En orden inverso',
    PLAYER_OPTION_BUILD_AUTOCONVERT_HIDE        => 'Ocultar botón de autoconversión',

    PLAYER_OPTION_SOUND_ENABLED                 => 'Activar sonidos en el juego',
    PLAYER_OPTION_ANIMATION_DISABLED            => 'Desactivar efectos de animación',
    PLAYER_OPTION_PROGRESS_BARS_DISABLED        => 'Desactivar barras de progreso',
  ],

  'opt_chk_skin' => 'Usar tema visual',
  'opt_adm_title' => 'Opciones de administración',
  'opt_adm_planet_prot' => 'Protección de planetas',
  'thanksforregistry' => 'Gracias por registrarte.<br />Recibirás un mensaje con tu contraseña en unos minutos.',
  'general_settings' => 'Configuración general',
  'skins_example' => 'Tema visual',

  'opt_avatar' => 'Avatar',
  'opt_avatar_search' => 'Buscar en Google',
  'opt_avatar_remove' => 'Eliminar avatar',
  'opt_upload' => 'Subir',

  'opt_msg_avatar_removed' => 'Avatar eliminado',
  'opt_msg_avatar_uploaded' => 'Avatar cambiado correctamente',
  'opt_msg_avatar_error_delete' => 'Error al eliminar el archivo de avatar. Contacta con la Administración del servidor',
  'opt_msg_avatar_error_writing' => 'Error al guardar el archivo de avatar. Contacta con la Administración del servidor',
  'opt_msg_avatar_error_upload' => 'Error al subir la imagen %1. Contacta con la Administración del servidor',
  'opt_msg_avatar_error_unsupported' => 'Formato de imagen no soportado. Solo se admiten archivos JPG, GIF, PNG de hasta 200KB',

  'untoggleip' => 'Desactivar verificación por IP',
  'untoggleip_tip' => 'La verificación por IP significa que no podrás iniciar sesión con tu nombre desde dos IP diferentes. ¡La verificación mejora tu seguridad!',
  'galaxyvision_options' => 'Universo',
  'spy_cant' => 'Cantidad de sondas',
  'spy_cant_tip' => 'Cantidad de sondas que se enviarán cuando espíes a alguien.',
  'tooltip_time' => 'Retraso antes de mostrar tooltips',
  'mess_ammount_max' => 'Cantidad máxima de mensajes de flota',
  'seconds' => 'Segundo(s)',
  'shortcut' => 'Acceso rápido',
  'show' => 'Mostrar',
  'write_a_messege' => 'Escribir mensaje',
  'spy' => 'Espionaje',
  'add_to_buddylist' => 'Añadir a amigos',
  'attack_with_missile' => 'Ataque con misiles',
  'show_report' => 'Ver informe',
  'delete_vacations' => 'Gestionar perfil',
  'mode_vacations' => 'Activar modo vacaciones',
  'vacations_tip' => 'El modo vacaciones protege tus planetas durante tu ausencia.',
  'deleteaccount' => 'Desactivar perfil',
  'deleteaccount_tip' => 'El perfil se eliminará después de 45 días de inactividad.',
  'deleteaccount_on' => 'La eliminación de la cuenta ocurrirá tras inactividad',
  'save_settings' => 'Guardar cambios',
  'exit_vacations' => 'Salir del modo vacaciones',
  'Vaccation_mode' => 'Modo vacaciones activado. Durará hasta: ',
  'You_cant_exit_vmode' => 'No puedes salir del modo vacaciones hasta que pase el tiempo mínimo',
  'Error' => 'Error',
  'cans_resource' => 'Detener producción de recursos en planetas',
  'cans_reseach' => 'Detener investigaciones en planetas',
  'cans_build' => 'Detener construcciones en planetas',
  'cans_fleet_build' => 'Detener construcción de flota y defensas',
  'cans_fly_fleet2' => 'Flota enemiga acercándose... no puedes irte de vacaciones',
  'vacations_exit' => 'Modo vacaciones desactivado... Vuelve a iniciar sesión',
  'select_skin_path' => 'SELECCIONAR',
  'opt_language' => 'Idioma de interfaz',
  'opt_compatibility' => 'Compatibilidad - interfaces antiguos',
  'opt_compat_structures' => 'Interfaz antiguo de construcción de edificios',
  'opt_vacation_err_your_fleet' => 'No puedes irte de vacaciones mientras tengas al menos una flota en vuelo',
  'opt_vacation_err_building' => 'Estás construyendo o investigando en %s y por eso no puedes irte de vacaciones',
  'opt_vacation_err_research' => 'Tus científicos están investigando tecnología y por eso no puedes irte de vacaciones',
  'opt_vacation_err_que' => 'Tienes investigaciones en curso o construcciones en algún planeta y por eso no puedes irte de vacaciones. Usa el enlace "Imperio" para ver las colas de construcción',
  'opt_vacation_err_timeout' => 'Aún no has acumulado suficiente tiempo para vacaciones - el tiempo de espera no ha terminado',
  'opt_vacation_next' => 'Podrás irte de vacaciones después de',
  'opt_vacation_min' => 'mínimo hasta',
  'succeful_changepass' => 'Contraseña cambiada correctamente.<br /><a href="login.php" target="_top">Volver</a>',

  'opt_time_diff_clear' => 'Medir diferencia entre el tiempo del jugador y el del servidor',
  'opt_time_diff_manual' => 'Establecer diferencia de tiempo manualmente',
  'opt_time_diff_explain' => 'Con la diferencia de tiempo correctamente configurada, el reloj "Tiempo del jugador" en el navbar debería estar sincronizado segundo a segundo con el reloj del dispositivo del jugador<br />
  Normalmente el juego establece automáticamente la diferencia correcta. Sin embargo, con una zona horaria incorrecta en el dispositivo del jugador, al jugar desde múltiples dispositivos, o con una conexión muy lenta, a veces es necesario establecer la diferencia manualmente',

  'opt_custom' => [
    'opt_uni_avatar_user' => 'Mostrar avatar del usuario',
    'opt_uni_avatar_ally' => 'Mostrar logo de la Alianza',
    'opt_int_struc_vertical' => 'Cola de construcciones vertical',
    'opt_int_navbar_resource_force' => 'Mostrar siempre la barra de recursos',
    'opt_int_overview_planet_columns' => 'Número de columnas en la lista de planetas',
    'opt_int_overview_planet_columns_hint' => '0 - calcular según el número máximo de filas',
    'opt_int_overview_planet_rows' => 'Número máximo de filas en la lista de planetas',
    'opt_int_overview_planet_rows_hint' => 'Se ignora si se especifica el número de columnas',
  ],

  'opt_mail_optional_description' => 'A esta dirección se envían mensajes privados de otros jugadores y notificaciones de eventos del juego (como informes de expediciones y espionaje)',
  'opt_mail_permanent_description' => 'Esta dirección de email está vinculada a tu cuenta. Solo puede establecerse una vez. Todas las notificaciones del sistema (como cambios de contraseña) se envían aquí',

  'opt_account_name' => 'Tu nombre de usuario<br />Este nombre se usa para iniciar sesión',
  'opt_game_user_name' => 'Nombre en el juego (nick)<br />Con este nombre te verán otros jugadores',

  'opt_universe_title' => 'Universo',

  'option_fleets' => 'Flotas',
  'option_fleet_send' => 'Envío de flota',

  'option_change_nick_disabled' => 'El cambio de nick está desactivado en la configuración del servidor',

  'opt_ignores' => 'Lista de ignorados',
  'opt_unignore_do' => 'Eliminar de lista de ignorados',
  'opt_ignore_list_empty' => 'Tu lista de ignorados está vacía',
];