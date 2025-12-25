<?php

/*
#############################################################################
# Nombre del archivo: admin.mo
# Proyecto: SuperNova.WS
# Sitio web: http://www.supernova.ws
# Descripción: Juego masivo multijugador en línea en el navegador, estrategia espacial
#
# Derechos de autor © 2009-2025 Gorlum para el proyecto "SuperNova.WS" + DeepSeec, GhatGpt,YandexGPT 5 Pro

#############################################################################
*/

/**
*
* Paquete: idioma
* Sistema: Español
* Versión: 45a13
*
*/

/**
* NO CAMBIE
*/

if (!defined('INSIDE')) die();

$a_lang_array = array(
  'menu_admin_ally' => 'Alianzas',

  'adm_tool_md5_header' => 'Generación y cifrado de contraseña (MD5)',
  'adm_tool_md5_hash' => 'Hash MD5',
  'adm_tool_md5_encode' => 'Cifrar',
  'adm_tool_md5_generate' => 'Generar',

  'adm_tool_sql_page_header' => 'Parámetros del servidor SQL',

  'adm_tool_sql_server_version' => 'Versión del servidor',
  'adm_tool_sql_client_version' => 'Versión de la biblioteca',
  'adm_tool_sql_host_info' => 'Método de conexión',

  'adm_confirm_do' => 'Confirmar',

  'adm_tool_sql_table' => array(
    'server' => array(
      'TABLE_HEADER' => 'Servidor SQL',
      'COLUMN_NAME_1' => 'Parámetro',
      'COLUMN_NAME_2' => 'Valor',
      // 'TABLE_FOOTER' => '',
      // 'TABLE_EMPTY' => '',
    ),

    'status' => array(
      'TABLE_HEADER' => 'Estado del servidor SQL',
      'COLUMN_NAME_1' => 'Parámetro',
      'COLUMN_NAME_2' => 'Valor',
      // 'TABLE_FOOTER' => '',
    ),

    'params' => array(
      'TABLE_HEADER' => 'Configuración del servidor SQL',
      'COLUMN_NAME_1' => 'Parámetro',
      'COLUMN_NAME_2' => 'Valor',
      // 'TABLE_FOOTER' => '',
    ),
  ),

  'adm_pl_image' => 'Imagen del planeta',
  'adm_pl_fields_max' => 'Máximo de sectores',
  'adm_pl_temp_min' => 'Temperatura mínima',
  'adm_pl_temp_max' => 'Temperatura máxima',
  'adm_pl_fields_busy' => 'Sectores ocupados',
  'adm_pl_governor' => 'Gobernador',
  'adm_pl_debris_metal' => 'Escombros, metal',
  'adm_pl_debris_crystal' => 'Escombros, cristal',

  'adm_opt_user_settings' => 'Configuraciones de los jugadores',
  'adm_opt_user_birthday_gift' => 'Regalo al jugador por su cumpleaños',
  'adm_opt_user_birthday_gift_disable' => '0 - deshabilitar regalos',
  'adm_opt_user_birthday_range' => 'Rango retroactivo de cumpleaños, en días',
  'adm_opt_user_birthday_range_hint' => 'Qué tan lejos en el pasado puede estar el cumpleaños para que el jugador reciba un regalo. Obviamente, no tiene sentido práctico establecer este valor más de 364 días.',

  'adm_ul_title' => 'Lista de jugadores',
  'adm_ul_title_online' => 'Jugadores en línea',
  'adm_ul_time_registered' => 'Fecha de registro',
  'adm_ul_time_played' => 'Último inicio de sesión',
  'adm_ul_time_banned' => 'Término de bloqueo',
  'adm_ul_delete_confirm' => 'Confirme la eliminación del usuario',
  'adm_ul_referral' => 'Recomendaciones',
  'adm_ul_players' => 'Jugadores',
  'adm_ul_dms' => 'TM',
  'adm_sys_actions' => 'Acciones',
  'adm_sys_write_message' => 'Escribir un mensaje privado',
  'adm_sys_delete_user' => 'Eliminar jugador',
  'adm_done' => 'Se ha completado con éxito',
  'adm_inactive_removed' => '<li>Se eliminaron registros inactivos de jugadores: %d</li>',
  'adm_stat_title' => 'Actualización de estadísticas',
  'adm_maintenance_title' => 'Mantenimiento de la BD',
  'adm_records' => 'registros procesados',
  'adm_cleaner_title' => 'Limpieza de la cola de construcciones',
  'adm_cleaned' => 'Cantidad de tareas eliminadas: ',
  'adm_schedule_none' => 'No hay tareas en el horario actual',



  'Fix' => 'Actualizado',
  'Welcome_to_Fix_section' => 'sección de patches',
  'There_is_not_need_fix' => '¡No se necesita fix!',
  'Fix_welldone' => 'Hecho!',
  'adm_ov_title' => 'Resumen',
  'adm_ov_infos' => 'Información',
  'adm_ov_yourv' => 'Versión actual',
  'adm_ov_lastv' => 'Versión disponible',
  'adm_ov_here' => 'aquí',
  'adm_ov_onlin' => 'En línea',
  'adm_ov_ally' => 'Alianza',
  'adm_ov_point' => 'Puntos',
  'adm_ov_activ' => 'Activo',
  'adm_ov_count' => 'Jugadores en línea',
  'adm_ov_wrtpm' => 'Escribir en MP',
  'adm_ov_altpm' => '[MP]',
  'adm_ov_hint' => '<ul><li>La tabla de usuarios en línea puede ser ordenada por las columnas "ID", "Nombre del jugador", "Alianza", "Puntos" y "Actividad". Para ordenar por una columna específica, haga clic en su encabezado</li></ul>',




  'adm_ul_ttle2' => 'Jugadores listados',
  'adm_ul_id' => 'ID',
  'adm_ul_name' => 'Nombre del jugador',
  'adm_ul_mail' => 'Correo electrónico',
  'adm_ul_adip' => 'IP',
  'adm_ul_regd' => 'Registrado desde',
  'adm_ul_lconn' => 'Último inicio de sesión',
  'adm_ul_bana' => 'Ban',
  'adm_ul_detai' => 'Detalles',
  'adm_ul_actio' => 'Acciones',
  'adm_ul_playe' => 'jugadores',
  'adm_ul_yes' => 'Sí',
  'adm_ul_no' => 'No',
  'adm_pl_title' => 'Planetas activos',
  'adm_pl_activ' => 'Planetas activos',
  'adm_pl_name' => 'Nombre del planeta',
  'adm_pl_posit' => 'Coordenadas',
  'adm_pl_point' => 'Valor',
  'adm_pl_since' => 'Activo',
  'adm_pl_they' => 'Total',
  'adm_pl_apla' => 'planeta(s)',
  'adm_am_plid' => 'ID del planeta',
  'adm_am_done' => 'La adición se completó con éxito',
  'adm_am_ttle' => 'Añadir recursos',
  'adm_am_add' => 'Confirmar',
  'adm_am_form' => 'Formulario de adición de recursos',
  'adm_ban_title' => 'Bloquear jugador',
  'adm_bn_plto' => 'Bloquear jugador',
  'adm_bn_name' => 'Nombre del jugador',
  'adm_bn_reas' => 'Razón del bloqueo',
  'adm_bn_isvc' => 'Con modo de vacaciones',
  'adm_bn_time' => 'Duración del bloqueo',
  'adm_bn_days' => 'Días',
  'adm_bn_hour' => 'Horas',
  'adm_bn_mins' => 'Minutos',
  'adm_bn_secs' => 'Segundos',
  'adm_bn_bnbt' => 'Bloquear',
  'adm_bn_thpl' => 'Jugador',
  'adm_bn_isbn' => 'bloqueado con éxito!',
  'adm_bn_vctn' => 'Modo de vacaciones activado.',
  'adm_bn_errr' => 'Error al bloquear al jugador! Es posible que el apodo %s no se encuentre.',
  'adm_bn_err2' => 'Error al desactivar la producción en los planetas!',
  'adm_bn_plnt' => 'Producción en los planetas desactivada.',
  'adm_ban_msg_issued_date' => 'bloqueó al jugador',
  'adm_unbn_ttle' => 'Desbloqueo',
  'adm_unbn_plto' => 'Desbloquear jugador',
  'adm_unbn_name' => 'Nombre',
  'adm_unbn_bnbt' => 'Desbloquear',
  'adm_unbn_thpl' => 'Jugador',
  'adm_unbn_isbn' => 'desbloqueado!',
  'adm_rz_ttle' => 'Reinicio de la universo',
  'adm_rz_done' => 'Usuario(s) de transferencia(s)',
  'adm_rz_conf' => 'Confirmación',
  'adm_rz_text' => 'Al presionar el botón (reiniciar) eliminará todos los datos de la base de datos. ¿Hizo una copia de seguridad??? ¡Los cuentas no serán eliminadas!..',
  'adm_rz_doit' => 'Reiniciar',
  'adm_ch_ttle' => 'Administración del chat',
  'adm_ch_list' => 'Lista de mensajes',
  'adm_ch_clear' => 'Borrar',
  'adm_ch_idmsg' => 'ID',
  'adm_ch_delet' => 'eliminar',
  'adm_ch_play' => 'Jugador',
  'adm_ch_time' => 'Fecha',
  'adm_ch_chat' => 'Réplica',
  'adm_ch_nbs' => 'mensajes en total...',
  'adm_er_ttle' => 'Entradas del sistema de registros',
  'adm_er_clear' => 'Borrar',
  'adm_er_idmsg' => 'ID',
  'adm_er_type' => '[Código] Título',
  'adm_er_play' => 'Jugador',
  'adm_er_time' => 'Fecha',
  'adm_er_page' => 'Dirección de la página',
  'adm_er_nbs' => 'Entradas en el registro:',
  'adm_er_text' => 'Entrada del registro',
  'adm_er_bktr' => 'Información de depuración',
  'adm_dm_title' => 'Cambio de la cantidad de Materia Oscura',
  'adm_dm_planet' => 'ID, coordenadas o nombre del planeta',
  'adm_dm_oruser' => 'O',
  'adm_dm_user' => 'ID o nombre del jugador de la lista de jugadores',
  'adm_or_caption' => 'O',
  'adm_dm_no_quant' => 'Indique la cantidad de TM (positiva - para acreditación, negativa - para descontar)',
  'adm_dm_no_dest' => 'Indique el ID o el nombre del jugador para cambiar TM',
  'adm_dm_add_err' => 'Parece que hubo un error al acreditar TM',
  'adm_dm_user_none' => 'Error: no se encontró jugador con ID o nombre "%s"',
  'adm_dm_user_added' => 'La cantidad de TM del jugador [%2$d] "%1$s" se cambió exitosamente a %3$s TM',
  'adm_dm_user_conflict' => 'Error: parece que en la BD hay un jugador tanto con ese nombre como con ese ID',
  'adm_dm_planet_none' => 'Error al buscar el planeta: no se encontró planeta con ID, coordenadas o nombre %s',
  'adm_dm_planet_added' => 'La cantidad de TM del jugador ID %1$d (dueño del planeta %4$s %2$s ID %3$d) se cambió exitosamente a %5$d TM.',
  'adm_dm_planet_conflict' => 'Datos no únicos para buscar el planeta.<br>Esto significa que en la BD existen al mismo tiempo',
  'adm_dm_planet_conflict_id' => 'un planeta con nombre "%1$s" y un planeta con ID %1$s .<br>Intente utilizar las coordenadas del planeta.',
  'adm_dm_planet_conflict_name' => 'varios planetas con nombre "%1$s".<br>Intente utilizar coordenadas o ID del planeta.',
  'adm_dm_planet_conflict_coords' => 'un planeta con nombre "%1$s" y un planeta con coordenadas %1$s.<br>Intente utilizar el ID del planeta.',
  'adm_apply' => 'Aplicar',
  'adm_maint' => 'Mantenimiento',
  'adm_backup' => 'Copia de seguridad',
  'adm_tools' => 'Herramientas',
  'adm_tools_reloadConfig' => 'Recalcular la configuración',
  'adm_reason' => 'Razón',
  'adm_opt_title' => 'Configuración del Universo',
  'adm_opt_game_settings' => 'Parámetros del juego',
'adm_opt_game_name' => 'Nombre del Universo',
'adm_opt_multiaccount_enabled' => 'Permitir la interacción de cuentas desde 1 IP',
'adm_opt_speed' => 'Velocidad',
'adm_opt_game_gspeed' => 'Juegos',
'adm_opt_game_fspeed' => 'Flotas',
'adm_opt_game_pspeed' => 'Recolección de recursos',
'adm_opt_colonies_not_counted' => '(sin contar la Capital)',
'adm_opt_colonies_no_restrictions' => '(-1 - sin restricciones)',
'adm_opt_game_speed_normal' => '(1&nbsp;-&nbsp;normal)',
'adm_opt_game_faq' => 'Enlace a Preguntas Frecuentes',
'adm_opt_game_forum' => 'Dirección del foro',
'adm_opt_game_metamatter' => 'Enlace "Comprar Metamateria"',
'adm_opt_game_copyrigh' => 'Copyright',
'adm_opt_game_online' => 'Desactivar el juego. Los usuarios verán el siguiente mensaje:',
'adm_opt_game_offreaso' => 'Mensaje',
'adm_opt_plan_settings' => 'Parámetros de los planetas',
'adm_opt_plan_initial' => 'Tamaño del planeta inicial',
'adm_opt_plan_base_inc' => 'Producción base',
'adm_opt_game_debugmod' => 'Activar modo de depuración',
'adm_opt_geoip_whois_url' => 'URL del servicio WHOIS',
'adm_opt_geoip_whois_url_example' => '(por ejemplo "http://1whois.ru/?ip=")',
'adm_opt_game_counter' => 'Activar contador de visitas',
'adm_opt_game_oth_info' => 'Otros parámetros',
'adm_opt_int_news_count' => 'Número de noticias',
'adm_opt_int_page_imperor' => 'En la página "Emperador"',
'adm_opt_game_zero_disable' => '(0&nbsp;-&nbsp;desactivar)',
'adm_opt_game_advertise' => 'Bloques de publicidad',
'adm_opt_game_oth_adds' => 'Activar bloque de publicidad en el menú izquierdo. Código del banner:',
'adm_opt_game_oth_gala' => 'Galaxia',
'adm_opt_game_oth_syst' => 'Sistema',
'adm_opt_game_oth_plan' => 'Planeta',
'adm_opt_btn_save' => 'Guardar',
'adm_opt_vacation_mode' => 'Desactivar modo de vacaciones',
'adm_opt_sectors' => 'sectores',
'adm_opt_per_hour' => 'por hora',
'adm_opt_saved' => 'Las configuraciones del juego se han guardado correctamente',
'adm_opt_players_online' => 'Jugadores en el servidor',
'adm_opt_vacation_mode_is' => 'Modo de vacaciones',
'adm_opt_game_status' => 'Estado del juego',
'adm_opt_links' => 'Enlaces y banners',
'adm_opt_universe_size' => 'Tamaño del Universo',
'adm_opt_galaxies' => 'Galaxias',
'adm_opt_systems' => 'Sistemas',
'adm_opt_planets' => 'Planetas',
'adm_opt_build_on_research' => 'Construir laboratorio durante la investigación',
'adm_opt_eco_scale_storage' => 'Escalado de almacenes según la velocidad de recolección',
'adm_opt_game_rules' => 'Enlace a las reglas',
'adm_opt_max_colonies' => 'Número de colonias',
'adm_opt_exchange' => 'Tasa de cambio de recursos',
'adm_opt_game_mode' => 'Tipo de Universo',
'adm_opt_chat' => 'Configuración del chat',
'adm_opt_chat_timeout' => 'Tiempo de espera por inactividad',
'adm_opt_allow_buffing' => 'Permitir el buffing',
'adm_opt_ally_help_weak' => 'Permitir el mantenimiento en un aliado débil',
'adm_opt_email_pm' => 'Permitir el envío de MP a correo electrónico',
'adm_opt_player_defaults' => 'Configuración predeterminada del jugador',
'adm_opt_game_default_language' => 'Idioma de la interfaz',
'adm_opt_game_default_skin' => 'Diseño/Piel',
'adm_opt_game_default_template' => 'Plantilla',
'adm_opt_player_change_name' => 'Cambio de nombre del jugador',
'adm_opt_player_change_name_options' => [
    SERVER_PLAYER_NAME_CHANGE_NONE => 'El cambio de nombre está prohibido',
    SERVER_PLAYER_NAME_CHANGE_FREE => 'Cambio de nombre gratuito',
    SERVER_PLAYER_NAME_CHANGE_PAY => 'Cambio de nombre por TM',
],
'adm_opt_player_change_name_cost' => 'Costo en TM por cambio de nombre',
'adm_opt_empire_mercenary_temporary' => 'Mercenarios temporales',
'adm_opt_empire_mercenary_temporary_base' => 'Tiempo base para el reclutamiento, en segundos',
'adm_opt_empire_mercenary_temporary_hint' => 'Al activar la opción, todos los mercenarios se convertirán en temporales con un plazo base. Al desactivar la opción, todos los mercenarios se convertirán en permanentes. Los mercenarios reclutados que no cumplan los requisitos de reclutamiento no podrán ser actualizados, pero seguirán activos.',
'adm_opt_experimental' => 'OPCIONES EXPERIMENTALES! USAR CON CUIDADO!',
'adm_opt_tpl_minifier' => 'Minificador de plantillas',
'adm_opt_tpl_minifier_hint' => 'El minificador comprimirá las plantillas, reemplazando varios caracteres "vacíos" consecutivos (salto de línea, tabulación, espacio) por un solo espacio. Para más información sobre el funcionamiento del minificador, consulte /docs/changelog.txt',
'adm_lm_compensate' => 'Compensar',
'adm_pl_comp_title' => 'Compensación del planeta destruido',
'adm_pl_comp_src' => 'Destruir el planeta',
'adm_pl_comp_dst' => 'Acreditar recursos al planeta',
'adm_pl_comp_bonus' => 'Bonificación del jugador',
'adm_pl_comp_check' => 'Comprobar',
'adm_pl_comp_confirm' => 'Confirmar',
'adm_pl_comp_done' => 'Listo',
'adm_pl_comp_price' => 'Costo de las construcciones',
'adm_pl_comp_got' => 'Se acreditará',
'adm_pl_com_of_plr' => 'del jugador',
'adm_pl_comp_will_be' => 'será',
'adm_pl_comp_destr' => 'destruido.',
'adm_pl_comp_recieve' => 'La cantidad de recursos indicada',
'adm_pl_comp_recieve2' => 'acreditada al planeta',
'adm_pl_comp_err_0' => 'No se encontró el planeta a destruir',
'adm_pl_comp_err_1' => 'El planeta ya ha sido destruido',
  'adm_pl_comp_err_2' => 'No se encontró planeta para acreditar recursos',
  'adm_pl_comp_err_3' => 'Los planetas especificados tienen dueños diferentes. Solo se pueden acreditar recursos en un planeta del mismo jugador',
  'adm_pl_comp_err_4' => 'El planeta no pertenece al jugador especificado',
  'adm_pl_comp_err_5' => 'Los planetas para destruir y acreditar recursos coinciden',
  'adm_ver_versions' => 'Versiones de componentes del servidor',
  'adm_ver_version_sn' => 'Versión del motor',
  'adm_ver_version_db' => 'Versiones de la base de datos',
  'adm_update_force' => 'Forzar actualización desde cero',
  'adm_update_repeat' => 'Repetir actualización anterior',
  'adm_ptl_test' => 'Prueba de phpBB Template Engine',
  'adm_counter_recalc' => 'Recalcular tabla `counter`',
  'adm_lm_planet_edit' => 'Editar',
  'adm_planet_edit' => 'Edición de planeta',
  'adm_planet_id' => 'ID del planeta',
  'adm_name' => 'Nombre',
  'adm_planet_change' => 'Cambio',
  'adm_planet_parent' => 'Planeta principal',
  'adm_planet_active' => 'Planetas activos',
  'adm_planet_edit_hint' => '<ul>    <li>Si introduces un ID de planeta en una página vacía y haces clic en "Confirmar", el motor intentará mostrar información sobre ese planeta: tipo, nombre y coordenadas, así como la cantidad actual de unidades/recursos del tipo seleccionado en el planeta</li>    <li>Para eliminar una cierta cantidad de unidades/recursos del planeta, introduce un número negativo</li>  </ul>',
  'adm_planet_list_title' => 'Lista de planetas',
  'adm_sys_owner' => 'Dueño',
  'adm_sys_owner_id' => 'ID del dueño',
  'addm_title' => 'Añadir luna',
  'addm_addform' => 'Formulario de nueva luna',
  'addm_playerid' => 'ID del planeta anfitrión',
  'addm_moonname' => 'Nombre de la luna',
  'addm_moongala' => 'Especifica galaxia',
  'addm_moonsyst' => 'Especifica sistema',
  'addm_moonplan' => 'Especifica posición',
  'addm_moondoit' => 'Añadir',
  'addm_done' => 'Luna creada',
  'adm_usr_level' => array(
    '0' => 'Jugador',
    '1' => 'Operador',
    '2' => 'Moderador',
    '3' => 'Administrador',
  ),

  'adm_usr_genre' => array(
    GENDER_UNKNOWN => 'No especificado',
    GENDER_MALE => 'Hombre',
    GENDER_FEMALE => 'Mujer',
  ),

  'panel_mainttl' => 'Panel de administración',
  'adm_panel_mnu' => 'Buscar jugador',
  'adm_panel_ttl' => 'Tipo de búsqueda',
  'adm_search_pl' => 'Buscar por nombre',
  'adm_search_ip' => 'Buscar por IP',
  'adm_stat_play' => 'Estadísticas del jugador',
  'adm_mod_level' => 'Nivel de acceso',
  'adm_player_nm' => 'Nombre del jugador',
  'adm_ip' => 'IP',
  'adm_plyer_wip' => 'Jugadores con IP',
  'adm_frm1_id' => 'ID',
  'adm_frm1_name' => 'Nombre',
  'adm_frm1_ip' => 'IP',
  'adm_frm1_mail' => 'e-Mail',
  'adm_frm1_acc' => 'Rango',
  'adm_frm1_gen' => 'Género',
  'adm_frm1_main' => 'ID del planeta',
  'adm_frm1_gpos' => 'Coordenadas',
  'adm_mess_lvl1' => 'Nivel de acceso',
  'adm_mess_lvl2' => '"ahora" ',
  'adm_colony' => 'Colonias',
  'adm_planet' => 'Planeta',
  'adm_moon' => 'Luna',
  'adm_technos' => 'Tecnologías',
  'adm_bt_search' => 'Buscar',
  'adm_bt_change' => 'Cambiar',
  'flt_id' => 'ID',
  'flt_fleet' => 'Flota',
  'flt_ships' => 'Composición',
  'flt_mission' => 'Misión',
  'flt_here' => 'Regreso',
  'flt_there' => 'Ida',
  'flt_here_there' => 'Ida/Regreso',
  'flt_departure' => 'Punto de partida',
  'flt_owner' => 'Dueño',
  'flt_planet' => 'Planeta',
  'flt_time_return' => 'Regreso',
  'flt_e_owner' => 'Destino',
  'flt_time_arrive' => 'Llegada',
  'flt_staying' => 'Tiempo de espera',
  'flt_action' => 'Acción',
  'flt_title' => 'Flotas en vuelo',
  'flt_no_fleet' => 'Actualmente no hay flotas en vuelo',
  'mlst_title' => 'Lista de mensajes',
  'mlst_mess_del' => 'Eliminación de mensajes',
  'mlst_hdr_page' => 'Pág.',
  'mlst_hdr_title' => ' ) mensajes :',
  'mlst_hdr_prev' => '[ &lt;- ]',
  'mlst_hdr_next' => '[ -&gt; ]',
  'mlst_hdr_id' => 'ID',
  'mlst_hdr_type' => 'Tipo de mensajes',
  'mlst_hdr_time' => 'Hora de envío',
  'mlst_hdr_from' => 'De',
  'mlst_hdr_to' => 'Para',
  'mlst_hdr_text' => 'Contenido',
  'mlst_hdr_action' => 'Marcar',
  'mlst_del_mess' => 'Eliminar',
  'mlst_bt_delsel' => 'Eliminar mensajes seleccionados',
  'mlst_bt_deldate' => 'Eliminar',
  'mlst_hdr_delfrom' => 'Eliminar mensajes de este tipo anteriores a',
  'mlst_no_messages' => 'No hay mensajes',
  'mlst_messages_deleted' => 'Mensajes con ID %s eliminados',
  'mlst_messages_deleted_date' => 'Mensajes tipo "%s" hasta %s eliminados (sin incluir mensajes en la fecha especificada)',

  'adm_lng_title' => 'Localización',
  'adm_lng_warning' => '¡ADVERTENCIA! ¡Esta es una versión alpha del editor de localizaciones! ¡Úsalo bajo tu propio riesgo!',
  'adm_lng_domain' => 'Dominio',
  'adm_lng_string_name' => 'Nombre de la cadena',
  'adm_lng_string_add' => 'Añadir cadena',
  'adm_uni_price_galaxy' => 'Costo base para renombrar galaxia',
  'adm_uni_price_system' => 'Costo base para renombrar sistema',

  'adm_opt_ver_check' => 'Verificación de versión',
  'adm_opt_ver_check_hint' => 'En cualquier tipo de verificación de versión solo se envían datos anónimos: versión actual de la BD, número de release y versión del juego. Puedes verificar la versión "manualmente" haciendo clic en "Verificar versión".',
  'adm_opt_ver_check_do' => 'Verificar versión',
  'adm_opt_ver_check_last' => 'Última verificación de versión realizada',
  'adm_opt_ver_check_auto' => 'Verificación automática de versión',
  'adm_opt_ver_check_auto_hint' => 'Puedes activar la verificación automática de versión del juego. La verificación se realizará automáticamente cada cierto tiempo (por defecto, una vez al día). Más detalles en la documentación',

  'adm_opt_ver_response' => array(
    SNC_VER_NEVER => 'No se ha realizado verificación de versión',

    SNC_VER_ERROR_CONNECT => 'Error de verificación de versión. El juego no pudo contactar con el servidor de actualizaciones. Asegúrate de que tienes instalado y activado CURL en PHP o que en la configuración de PHP está permitido el acceso a servidores remotos',
    SNC_VER_ERROR_SERVER => 'Error del servidor de actualizaciones. Comprueba si ha salido una versión más nueva del motor con mejor soporte para el servidor de actualizaciones. En caso contrario, ¡notifica urgentemente al desarrollador!',

    SNC_VER_EXACT => 'Tienes instalada la última versión alpha del próximo release. ¡Gracias por participar en las pruebas!',
    SNC_VER_LESS => 'Estás usando una versión alpha del próximo release. ¡Pero ya hay una alpha más nueva! Actualiza si quieres obtener correcciones de errores y probar nuevas características.',
    SNC_VER_FUTURE => '¡Tienes una versión del juego del futuro! ¡Contacta urgentemente con el desarrollador y pásale esta versión! También prepárate para la visita de la Milicia Temporal por violación del continuo espacio-temporal...',

    SNC_VER_RELEASE_EXACT => 'Tienes la versión más reciente del último release del juego',
    SNC_VER_RELEASE_MINOR => 'Tienes una versión desactualizada del juego - ya hay una actualización del release actual. Probablemente corrige algunos errores de tu versión. Se recomienda actualizar.',
    SNC_VER_RELEASE_MAJOR => 'Tienes una versión muy desactualizada - ya ha salido un nuevo release. Corrección de errores, nuevas características - ¡debes actualizar!',
    SNC_VER_RELEASE_ALPHA => 'Tienes la versión más reciente del release del juego. Pero ya hay una alpha del próximo release. ¿Quizás quieras participar en sus pruebas?',

    SNC_VER_MAINTENANCE => 'El servidor de actualizaciones está en mantenimiento. Inténtalo más tarde',
    SNC_VER_UNKNOWN_RESPONSE => 'El servidor de actualizaciones devolvió una respuesta desconocida. Probablemente significa que hay una versión más nueva del motor con capacidades más avanzadas de actualización',
    SNC_VER_INVALID => 'No puedo entender qué versión tan extraña tienes. Contacta con el desarrollador para diagnosticar el problema.',
    SNC_VER_STRANGE => 'No deberías ver este mensaje. Si lo ves, algo ha ido mal. Contacta con el desarrollador para diagnosticar el problema.',

    SNC_VER_REGISTER_UNREGISTERED => 'Tu servidor no está registrado',
    SNC_VER_REGISTER_ERROR_MULTISERVER => 'Error - ¡tu servidor está registrado múltiples veces! Contacta con el desarrollador para diagnosticar el problema.',
    SNC_VER_REGISTER_ERROR_REGISTERED => 'Error - ¡tu servidor ya está registrado! Comprueba que la clave única y el identificador en la configuración del servidor son correctos.',
    SNC_VER_REGISTER_ERROR_NO_NAME => 'Error - ¡falta el nombre del servidor! Debes asignar un nombre al servidor.',
    SNC_VER_REGISTER_ERROR_WRONG_URL => 'Error - ¡URL incorrecta! La cadena proporcionada no es una URL válida. Quizás intentaste registrar un servidor ejecutándose en localhost - el servidor de actualizaciones no trabaja con esos servidores.',
    SNC_VER_REGISTER_REGISTERED => 'Tu sitio se ha registrado con éxito',

    SNC_VER_ERROR_INCOMPLETE_REQUEST => 'Error - ¡clave o ID de sitio incorrectos! Comprueba que la clave y el ID en la configuración del servidor son correctos.',
    SNC_VER_ERROR_UNKNOWN_KEY => 'Error - ¡clave desconocida! La clave proporcionada no se encuentra en la BD del servidor de actualizaciones. Comprueba que la clave en la configuración del servidor es correcta.',
    SNC_VER_ERROR_MISSMATCH_KEY_ID => 'Error - ¡la clave proporcionada no coincide con el ID proporcionado! Comprueba que la clave y el ID en la configuración del servidor son correctos.',
  ),

  'adm_opt_ver_response_short' => array(
    SNC_VER_NEVER => 'No realizada',

    SNC_VER_ERROR_CONNECT => 'Error de conexión',
    SNC_VER_ERROR_SERVER => 'Error del servidor',

    SNC_VER_EXACT => 'Última alpha',
    SNC_VER_LESS => 'Alpha antigua',
    SNC_VER_FUTURE => 'Alpha del futuro',

    SNC_VER_RELEASE_EXACT => 'Versión actualizada',
    SNC_VER_RELEASE_MINOR => 'Actualización recomendada',
    SNC_VER_RELEASE_MAJOR => 'Actualización necesaria',
    SNC_VER_RELEASE_ALPHA => 'Release actualizado',

    SNC_VER_MAINTENANCE => 'Mantenimiento',
    SNC_VER_UNKNOWN_RESPONSE => 'Respuesta desconocida',
    SNC_VER_INVALID => 'Error de versión',
    SNC_VER_STRANGE => 'Problema inesperado',

    SNC_VER_REGISTER_UNREGISTERED => 'No registrado',
    SNC_VER_REGISTER_ERROR_MULTISERVER => 'Multi-registro',
    SNC_VER_REGISTER_ERROR_REGISTERED => 'Error de clave',
    SNC_VER_REGISTER_ERROR_NO_NAME => 'Error de nombre',
    SNC_VER_REGISTER_REGISTERED => 'Registrado',

    SNC_VER_ERROR_INCOMPLETE_REQUEST => 'Error de clave o ID',
    SNC_VER_ERROR_UNKNOWN_KEY => 'Clave desconocida',
    SNC_VER_ERROR_MISSMATCH_KEY_ID => 'La clave no coincide con el ID',
  ),

  'adm_upd_register' => 'Registro del servidor',

  'adm_upd_register_hint' => '
    El registro del servidor es necesario para ciertas solicitudes al servidor de actualizaciones. Durante el registro se envía la mínima información necesaria para identificar el servidor:
    <ul>
      <li>URL completo del servidor - es decir, dirección HTTP y subdirectorio del servidor. Ejemplo: http://miservidor.com/micarpeta/. Esto es necesario para la identificación primaria del servidor. La ruta completa es necesaria para distinguir múltiples copias de SuperNova instaladas en el mismo IP o dominio.</li>
      <li>Nombre interno del servidor. Se usa para incluir en mensajes.</li>
    </ul>
    ¿Por qué registrar tu servidor? En el futuro se planean varias funciones que solo estarán disponibles para servidores registrados. Entre ellas (ordenadas por plazo estimado de implementación):
    <ul>
      <li>Obtención automática del registro de cambios</li>
      <li>Actualización automatizada del motor</li>
      <li>Participación en el ranking de servidores</li>
      <li>Reportes de errores de administradores</li>
      <li>Chat para administradores de servidores</li>
      <li>Por solicitud - diagnóstico remoto del servidor</li>
      <li>...y mucho más</li>
    </ul>
    ¿Por qué registrar tu servidor ahora?
    <ul>
      <li>Las solicitudes de servidores registrados tienen mayor prioridad en diagnóstico de problemas y procesamiento de reportes.</li>
      <li>Durante el registro, además de una clave única, el servidor recibe un número de identificación único que se usará para ordenar servidores. Cuanto antes registres tu servidor, más arriba aparecerá en el listado general...</li>
    </ul>
  ',

  'adm_upd_register_do' => 'Registrar servidor',
  'adm_upd_register_already' => 'Ya estás registrado en el servidor de actualizaciones. ¡Asegúrate de guardar el ID y clave única de tu servidor!',
  'adm_upd_register_id' => 'Número de registro',
  'adm_upd_register_key' => 'Clave de registro',

  'adm_opt_stats_and_records' => 'Estadísticas y récords',
  'adm_opt_stats_hide_admins' => 'Ocultar administradores',
  'adm_opt_stats_hide_admins_detail' => 'Se ocultarán todas las cuentas con authlevel > 0',
  'adm_opt_stats_hide_player_list' => 'Ocultar jugadores',
  'adm_opt_stats_hide_player_list_detail' => 'Lista de IDs de jugadores a ocultar, separados por comas',
  'adm_opt_stats_schedule' => 'Programación de actualización de estadísticas',
  'adm_opt_stats_schedule_detail' => 'Formato: "[AAAA:[MM:[DD:[HH:[MM:[SS]]]]]][,(...)]"<br />
    Los parámetros cero a la izquierda son opcionales<br />
    Los parámetros vacíos a la derecha se consideran cero<br />
    Ejemplos:<br />
     - "00:00:27:00" significa "ejecución a los 27 minutos de cada hora";<br />
     - "04::" - "ejecución a las 4 AM cada día";<br />
     - "02::,17:00" - "ejecución a las 2 AM cada día y a los 17 minutos de cada hora";<br />
     - "1:4:30:00" - "Ejecución el día 1 de cada mes a las 04:30 AM" etc.',
  'adm_opt_stats_hide_pm_link' => 'Ocultar enlaces a mensajes privados',

  'adm_pay' => 'Pagos',
  'adm_pay_stats' => 'Estadísticas de pagos',
  'adm_pay_th_payer' => 'Pagador',
  'adm_pay_th_payer_id' => 'ID',
  'adm_pay_th_payer_name' => 'Nombre',
  'adm_pay_th_payment' => 'Pago',
  'adm_pay_th_payment_id' => 'ID',
  'adm_pay_th_payment_date' => 'Fecha',
  'adm_pay_th_payment_status' => 'Estado',
  'adm_pay_th_payment_amount' => 'Monto',
  'adm_pay_th_payment_currency' => 'Moneda',
  'adm_pay_th_mm_paid' => 'Pagado',
  'adm_pay_th_mm_gained' => 'Acreditado',
  'adm_pay_th_module' => 'Sistema de pago',
  'adm_pay_th_module_name' => 'Tipo',

  'adm_pay_filter_all' => '-- Todos --',
  'adm_pay_filter_status' => array(
    PAYMENT_STATUS_ALL => '-- Todos --',
    PAYMENT_STATUS_NONE => 'No completado',
    PAYMENT_STATUS_COMPLETE => 'Completado',
  ),
  'adm_pay_filter_test' => array(
    PAYMENT_TEST_ALL => '-- Todos --',
    PAYMENT_TEST_REAL => 'Real',
    PAYMENT_TEST_PROBE => 'Prueba',
  ),
  'adm_pay_filter_stat' => array(
    PAYMENT_FILTER_STAT_NORMAL => '-- Ninguno --',
    PAYMENT_FILTER_STAT_MONTH => 'Por meses',
    PAYMENT_FILTER_STAT_YEAR => 'Por años',
    PAYMENT_FILTER_STAT_ALL => 'Todo el tiempo',
  ),
  'adm_pay_filter_stat_name' => 'Estadísticas',

  'adm_user_stat' => 'Estadísticas de usuarios',
  'adm_user_online' => 'En línea desde %s hasta %s',

  'adm_ban_unban' => 'Ban/Desban',
  'adm_metametter_payment' => 'MM & Pagos',

  'adm_stat_already_started' => 'Las estadísticas ya se están actualizando ahora mismo',

  'adm_dm_change_hint' => 'La búsqueda se realiza primero por ID de jugador, y si no se encuentra, por nombre',

  'adm_matter_change_log_record' => 'A través del panel de administración por el usuario [%3$s] "%4$s" para la cuenta [%1$d] "%2$s" por la razón "%5$s"',

  'adm_game_status' => 'Estado actual del juego',

  'adm_log_delete_update_info' => 'Eliminar información sobre mantenimiento de BD, actualizaciones de estadísticas y motor',

  'admin_tab_status' => 'Estado',
  'admin_tab_game' => 'Juego',
  'admin_tab_universe' => 'Universo',
  'admin_tab_planets' => 'Planetas',
  'admin_tab_stats_and_records' => 'Estadísticas',
  'admin_tab_urls' => 'Enlaces',
  'admin_tab_players' => 'Jugadores',
  'admin_tab_UBE' => 'Batalla',
  'admin_tab_advertise' => 'Publicidad',

  'admin_tab_universe_main' => 'Universo',

  'admin_ptl_test_la_' => "Single'Double\"Zero\0End",

  'admin_title_access_denied' => 'Acceso denegado',

  'menu_admin_modules' => 'Módulos',

  'adm_player' => 'Jugador',
  'adm_planets' => 'Planetas',

  // ------------------ NO LOCALIZADO -------------------------------
  'adm_mm_title'                        => 'Cambiar cantidad de Metamateria',
  'adm_mm_account'                      => 'Cuenta: ID, nombre o email de registro',
  'adm_mm_account_hint'                 => 'La búsqueda de cuenta primero es por ID, luego por nombre, luego por email de registro',
  'adm_mm_player'                       => 'Jugador: ID o nombre de la lista de jugadores',
  'adm_mm_player_hint'                  => 'La búsqueda de jugador primero es por ID, luego por nombre',
  'adm_mm_err_points_empty'             => 'Especifica cantidad de MM (positivo - añadir, negativo - quitar)',
  'adm_mm_err_account_not_found'        => 'Error: no puedo encontrar cuenta con ID, nombre o email "%1$s"',
  'adm_mm_err_player_not_found'         => 'Error: no se encontró jugador con ID o nombre "%1$s"',
  'adm_mm_err_player_no_account'        => 'Error: no puedo encontrar cuenta para el jugador "%1$s"',
  'adm_mm_err_account_and_player_empty' => 'Error: no se especificó ni cuenta ni jugador para cambiar MM',
  'adm_mm_err_mm_change_failed'         => 'Error: error interno al cambiar MM. Contacta al desarrollador',
  'adm_mm_msg_mm_changed'               => 'La cuenta [%2$d] "%1$s" (jugador [%4$s] "%5$s") tuvo su MM cambiada exitosamente a <span class="metamatter">%3$s MM</span>',
  'adm_mm_msg_confirm_mm_change'        => 'Confirma cambiar MM de la cuenta [%2$d] "%1$s" (jugador [%4$s] "%5$s") a <span class="metamatter">%3$s MM</span>',
  'adm_mm_msg_change_mm_log_record'     => 'Admin [%6$s] "%7$s" (jugador [%3$s] "%4$s") razón "%5$s" para [%1$d] "%2$s" (jugador [%8$d] "%9$s")',

  'admin_ally_list' => 'Lista de Alianzas',
);
