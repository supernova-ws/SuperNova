<?php

/*
#############################################################################
#  Filename: system.mo.php
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2009 Gorlum for Project "SuperNova.WS"
#  Copyright © 2009 MSW
#############################################################################
*/

/**
*
* @package language
* @system [Spanish]
* @version #46a158#
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE'))
{
  exit;
}

// System-wide localization

global $config;

$a_lang_array = [
  'sys_birthday' => 'Cumpleaños',
  'sys_birthday_message' => '¡%1$s! La administración de SuperNova te felicita cordialmente por tu cumpleaños, que cayó en %2$s y te regala %3$d %4$s. ¡Te deseamos mucho éxito en el juego y altos rangos en las estadísticas! Puede que este saludo llegue tarde, pero mejor tarde que nunca.',

  'adm_err_denied' => 'Acceso denegado. No tienes los permisos necesarios para usar esta página de la interfaz de administración del servidor',

  'sys_empire'          => 'Imperio',
  'VacationMode'			=> "Tu producción está cerrada porque estás de vacaciones",
  'sys_moon_destruction_report' => "Informe de destrucción de luna",
  'sys_moon_destroyed' => "¡Tus Estrellas de la Muerte han producido una poderosa onda gravitacional que ha destruido la luna!",
  'sys_rips_destroyed' => "Tus Estrellas de la Muerte han producido una poderosa onda gravitacional, pero no fue suficiente para destruir una luna de este tamaño. Sin embargo, la onda gravitacional rebotó en la superficie lunar y destruyó tu flota.",
  'sys_rips_come_back' => "Tus Estrellas de la Muerte no tienen suficiente energía para dañar esta luna. Tu flota regresa sin destruir la luna.",
  'sys_chance_moon_destroy' => "Probabilidad de destrucción lunar: ",
  'sys_chance_rips_destroy' => "Probabilidad de destrucción por onda gravitacional: ",

  'sys_impersonate' => 'Personificar',
  'sys_impersonate_done' => 'Dejar de personificar',
  'sys_impersonated_as' => '¡ATENCIÓN! Actualmente estás personificando al jugador %1$s. No olvides que en realidad eres %2$s. Puedes dejar de personificar seleccionando la opción correspondiente en el menú.',

  'menu_admin_mining'          => 'Minería de jugadores',
  'menu_admin_units'          => 'Unidades',
  'menu_admin_ube_balance'          => 'Balance UBE',

  'sys_day' => "días",
  'sys_hrs' => "horas",
  'sys_min' => "minutos",
  'sys_sec' => "segundos",
  'sys_day_short' => "d",
  'sys_hrs_short' => "h",
  'sys_min_short' => "m",
  'sys_sec_short' => "s",

  'sys_ask_admin' => 'Enviar preguntas y sugerencias a',

  'sys_wait'      => 'Solicitud en proceso. Por favor, espera.',

  'sys_fleets'       => 'Flotas',
  'sys_expeditions'  => 'Expediciones',
  'sys_fleet'        => 'Flota',
  'sys_expedition'   => 'Expedición',
  'sys_event_next'   => 'Próximo evento:',
  'sys_event_arrive' => 'llegará',
  'sys_event_stay'   => 'terminará la misión',
  'sys_event_return' => 'regresará',

  'sys_total'           => "TOTAL",
  'sys_need'				=> 'Necesita',
  'sys_register_date'   => 'Fecha de registro',

  'sys_attacker' 		=> "Atacante",
  'sys_defender' 		=> "Defensor",

  'COE_combatSimulator' => "Simulador de combate",
  'COE_simulate'        => "Ejecutar simulador",
  'COE_fleet'           => "Flota",
  'COE_defense'         => "Defensa",
  'sys_coe_combat_start'=> "Las flotas rivales se han encontrado",
  'sys_coe_combat_end'  => "Resultados del combate",
  'sys_coe_round'       => "Ronda",

  'sys_coe_attacker_turn'=> 'El atacante dispara con una potencia total de %1$s. Los escudos del defensor absorben %2$s disparos<br />',
  'sys_coe_defender_turn'=> 'El defensor dispara con una potencia total de %1$s. Los escudos del atacante absorben %2$s disparos<br /><br /><br />',
  'sys_coe_outcome_win'  => '¡El defensor ha ganado la batalla!<br />',
  'sys_coe_outcome_loss' => '¡El atacante ha ganado la batalla!<br />',
  'sys_coe_outcome_loot' => 'Obtiene %1$s de metal, %2$s de cristal, %3$s de deuterio<br />',
  'sys_coe_outcome_draw' => 'La batalla terminó en empate.<br />',
  'sys_coe_attacker_lost'=> 'El atacante perdió %1$s unidades.<br />',
  'sys_coe_defender_lost'=> 'El defensor perdió %1$s unidades.<br />',
  'sys_coe_debris_left'  => 'Ahora en estas coordenadas espaciales hay %1$s de metal y %2$s de cristal.<br /><br />',
  'sys_coe_moon_chance'  => 'La probabilidad de que aparezca una luna es del %1$s%%<br />',
  'sys_coe_rw_time'      => 'Tiempo de generación de la página: %1$s segundos<br />',

  'sys_resources'       => "Recursos",
  'sys_ships'           => "Naves",
  'sys_metal'          => "Metal",
  'sys_metal_sh'       => "M",
  'sys_crystal'        => "Cristal",
  'sys_crystal_sh'     => "C",
  'sys_deuterium'      => "Deuterio",
  'sys_deuterium_sh'   => "D",
  'sys_energy'         => "Energía",
  'sys_energy_sh'      => "E",
  'sys_dark_matter'    => "Materia Oscura",
  'sys_dark_matter_sh' => "MO",
  'sys_metamatter'     => "Metamateria",
  'sys_metamatter_sh'  => "MM",

  'sys_reset'           => "Reiniciar",
  'sys_send'            => "Enviar",
  'sys_characters'      => "caracteres",
  'sys_back'            => "Atrás",
  'sys_return'          => "Volver",
  'sys_delete'          => "Eliminar",
  'sys_writeMessage'    => "Escribir mensaje",
  'sys_hint'            => "Sugerencia",

  'sys_alliance'        => "Alianza",
  'sys_player'          => "Jugador",
  'sys_coordinates'     => "Coordenadas",

  'sys_online'          => "En línea",
  'sys_offline'         => "Desconectado",
  'sys_status'          => "Estado",

  'sys_universe'        => "Universo",
  'sys_goto'            => "Ir a",

  'sys_time'            => "Tiempo",
  'sys_temperature'		=> 'Temperatura',

  'sys_no_task'         => "sin tarea",

  'sys_affilates'       => "Jugadores invitados",

  'sys_fleet_arrived'   => "Flota llegada",

  'sys_planet_type' => [
    PT_PLANET => 'Planeta',
    PT_DEBRIS => 'Campo de escombros',
    PT_MOON   => 'Luna',
  ],

  'sys_planet_type_sh' => [
    PT_PLANET => '(P)',
    PT_DEBRIS => '(E)',
    PT_MOON   => '(L)',
  ],

  'sys_planet_expedition' => 'espacio inexplorado',

  'sys_capacity' 			=> 'Capacidad de carga',
  'sys_cargo_bays' 		=> 'Bodegas',

  'sys_supernova' 		=> 'SuperNova',
  'sys_server' 			=> 'Servidor',

  'sys_unbanned'			=> 'Desbloqueado',

  'sys_date_time'			=> 'Fecha y hora',
  'sys_from_person'	   => 'De',
  'sys_from_speed'	   => 'desde',

  'sys_from'		  => 'de',
  'tp_on'            => 'en',

// Resource page
  'res_planet_production' => 'Producción de recursos en el planeta',
  'res_basic_starting_resources' => 'Recursos iniciales en el planeta',
  'res_basic_income' => 'Producción natural',
  'res_basic_storage_size' => 'Tamaño de los almacenes',
  'res_total' => 'TOTAL',
  'res_calculate' => 'Calcular',
  'res_hourly' => 'Por hora',
  'res_daily' => 'Por día',
  'res_weekly' => 'Por semana',
  'res_monthly' => 'Por mes',
  'res_storage_fill' => 'Llenado de almacenes',
  'res_hint' => '<ul><li>Una producción de recursos <100% significa falta de energía. Construye plantas de energía adicionales o reduce la producción de recursos<li>Si tu producción es 0%, probablemente acabas de salir de vacaciones y necesitas activar todas las fábricas<li>Para ajustar la producción de todas las fábricas a la vez, usa el menú desplegable en el encabezado de la tabla. Es especialmente útil después de salir de vacaciones</ul>',

// Build page
  'bld_destroy' => 'Destruir',
  'bld_create'  => 'Construir',
  'bld_research' => 'Investigar',
  'bld_hire' => 'Contratar',

// Imperium page
  'imp_imperator' => "Emperador",
  'imp_overview' => "Resumen del Imperio",
  'imp_fleets' => "Flotas en vuelo",
  'imp_production' => "Producción",
  'imp_name' => "Nombre",
  'imp_research' => "Investigaciones",
  'imp_exploration' => "Expediciones",
  'imp_imperator_none' => "¡No existe tal Emperador en el Universo!",
  'sys_fields' => "Sectores",

// Cookies
  'err_cookie' => "¡Error! No se puede autenticar al usuario con la información de la cookie.<br />Borra las cookies del navegador y luego intenta <a href='login" . DOT_PHP_EX . "'>iniciar sesión</a> en el juego o <a href='reg" . DOT_PHP_EX . "'>registrarte</a>.",

// Supported languages
  'ru'              	  => 'Ruso',
  'en'              	  => 'Inglés',

  'sys_vacation'        => 'Estás de vacaciones hasta',
  'sys_vacation_leave'  => '¡Ya he descansado - salir de vacaciones!',
  'sys_vacation_in'     => 'De vacaciones',
  'sys_level'           => 'Nivel',
  'sys_level_short'     => 'Nv',
  'sys_level_max'       => 'Nivel máximo',

  'sys_yes'             => 'Sí',
  'sys_no'              => 'No',

  'sys_on'              => 'Activado',
  'sys_off'             => 'Desactivado',

  'sys_confirm'         => 'Confirmar',
  'sys_save'            => 'Guardar',
  'sys_create'          => 'Crear',
  'sys_write_message'   => 'Escribir mensaje',

// top bar
  'top_of_year' => 'año',
  'top_online'			=> 'Jugadores',

  'sys_first_round_crash_1'	=> 'Se perdió el contacto con la flota atacada.',
  'sys_first_round_crash_2'	=> 'Esto significa que fue destruida en la primera ronda de batalla.',

  'sys_ques' => [
    QUE_STRUCTURES => 'Edificios',
    QUE_HANGAR     => 'Astillero',
    SUBQUE_DEFENSE => 'Defensa',
    QUE_RESEARCH   => 'Investigaciones',
  ],

  'navbar_button_expeditions_short' => 'Exped',
  'navbar_button_fleets' => 'Flotas',
  'navbar_button_quests' => 'Misiones',
  'navbar_font' => 'Fuente',
  'navbar_font_normal' => 'Normal',
  'sys_que_structures' => 'Edificios',
  'sys_que_hangar' => 'Astillero',
  'sys_que_defense' => 'Defensa',
  'sys_que_research' => 'Investigaciones',
  'sys_que_research_short' => 'Ciencia',

  'eco_que' => 'Cola',
  'eco_que_empty' => 'La cola está vacía',
  'eco_que_clear' => 'Limpiar cola',
  'eco_que_trim'  => 'Cancelar último',
  'eco_que_artifact'  => 'Usar Artefacto',

  'sys_cancel' => 'Cancelar',

  'sys_overview'			=> 'Resumen',
  'mod_marchand'			=> 'Mercader',
  'sys_galaxy'			=> 'Galaxia',
  'sys_system'			=> 'Sistema',
  'sys_planet'			=> 'Planeta',
  'sys_planet_title'			=> 'Tipo de planeta',
  'sys_planet_title_short'			=> 'Tipo',
  'sys_moon'			=> 'Luna',
  'sys_error'			=> 'Error',
  'sys_done'				=> 'Hecho',
  'sys_no_vars'			=> 'Error al inicializar variables, ¡contacta con la administración!',
  'sys_attacker_lostunits'		=> 'El atacante perdió %s unidades.',
  'sys_defender_lostunits'		=> 'El defensor perdió %s unidades.',
  'sys_gcdrunits' 			=> 'Ahora en estas coordenadas espaciales hay %s %s y %s %s.',
  'sys_moonproba' 			=> 'La probabilidad de que aparezca una luna es: %d %% ',
  'sys_moonbuilt' 			=> '¡Gracias a la enorme energía, grandes trozos de metal y cristal se unen y forman una nueva luna %s %s!',
  'sys_attack_title'    		=> '%s. Ocurrió una batalla entre las siguientes flotas:',
  'sys_attack_attacker_pos'      	=> 'Atacante %s [%s:%s:%s]',
  'sys_attack_techologies' 	=> 'Armamento: %d %% Escudos: %d %% Blindaje: %d %% ',
  'sys_attack_defender_pos' 	=> 'Defensor %s [%s:%s:%s]',
  'sys_ship_type' 			=> 'Tipo',
  'sys_ship_count' 		=> 'Cantidad',
  'sys_ship_weapon' 		=> 'Armamento',
  'sys_ship_shield' 		=> 'Escudos',
  'sys_ship_armour' 		=> 'Blindaje',
  'sys_ship_speed' 		=> 'Velocidad',
  'sys_ship_consumption' 		=> 'Consumo',
  'sys_ship_capacity' 		=> 'Bodega/Tanque',
  'sys_destroyed' 			=> 'destruido',
  'sys_attack_attack_wave' 	=> 'El atacante dispara con una potencia total de %s al defensor. Los escudos del defensor absorben %s disparos.',
  'sys_attack_defend_wave'		=> 'El defensor dispara con una potencia total de %s al atacante. Los escudos del atacante absorben %s disparos.',
  'sys_attacker_won' 		=> '¡El atacante ha ganado la batalla!',
  'sys_defender_won' 		=> '¡El defensor ha ganado la batalla!',
  'sys_both_won' 			=> '¡La batalla terminó en empate!',
  'sys_stealed_ressources' 	=> 'Obtiene %s de metal %s %s de cristal %s y %s de deuterio.',
  'sys_rapport_build_time' 	=> 'Tiempo de generación de la página: %s segundos',
  'sys_mess_tower' 		=> 'Transporte',
  'sys_coe_lost_contact' 		=> 'Se perdió el contacto con tu flota',
  'sys_spy_activity' => 'Se observa actividad de espionaje cerca de tus planetas',
  'sys_spy_materials' 		=> 'Materiales en',
  'sys_spy_fleet' 			=> 'Flota',
  'sys_spy_defenses' 		=> 'Defensa',
  'sys_mess_qg' 			=> 'Comando de flota',
  'sys_mess_spy_report' 		=> 'Informe de espionaje',
  'sys_mess_spy_lostproba' 	=> 'Margen de error de la información obtenida por el satélite %d %% ',
  'sys_mess_spy_detect_chance' 	=> 'Probabilidad de detección de tu flota de reconocimiento: %d%%',
  'sys_mess_spy_detect_chance_no_percent' 	=> 'Probabilidad de detección de tu flota de reconocimiento',
  'sys_mess_spy_control' 		=> 'Contraespionaje',
  'sys_mess_spy_activity' 		=> 'Actividad de espionaje',
  'sys_mess_spy_enemy_fleet' 	=> 'Flota enemiga del planeta',
  'sys_mess_spy_seen_at'		=> 'fue detectada cerca del planeta',
  'sys_mess_spy_destroyed'		=> 'La flota de reconocimiento fue destruida',
  'sys_mess_spy_destroyed_enemy'		=> 'Flota espía enemiga destruida',
  'sys_object_arrival'		=> 'Llegó al planeta',
  'sys_stay_mess_stay' => 'Reubicación de flota',
  'sys_stay_mess_start' 		=> 'Tu flota ha llegado al planeta',
  'sys_stay_mess_back'		=> 'Tu flota ha regresado ',
  'sys_stay_mess_end'		=> ' y ha entregado:',
  'sys_stay_mess_bend'		=> ' y ha entregado los siguientes recursos:',
  'sys_adress_planet' 		=> '[%s:%s:%s]',
  'sys_stay_mess_goods' 		=> '%s : %s, %s : %s, %s : %s',
  'sys_colo_mess_from' 		=> 'Colonización',
  'sys_colo_mess_report' 		=> 'Informe de colonización',
  'sys_colo_defaultname' 		=> 'Colonia',
  'sys_colo_arrival' 		=> 'La flota alcanza las coordenadas ',
  'sys_colo_maxcolo' 		=> ', pero no se puede colonizar el planeta, se ha alcanzado el número máximo de colonias para tu nivel de colonización',
  'sys_colo_allisok' 		=> ', y los colonos comienzan a establecerse en el nuevo planeta.',
  'sys_colo_badpos'  			=> ', y los colonos encontraron el entorno poco favorable para tu imperio. La misión de colonización regresa al planeta de origen.',
  'sys_colo_notfree' 			=> ', y los colonos no encontraron un planeta en esas coordenadas. Se ven obligados a regresar completamente desconcertados.',
  'sys_colo_no_colonizer'     => 'No hay colonizador en la flota',
  'sys_colo_planet'  		=> ' ¡Planeta colonizado!',
  'sys_expe_report' 		=> 'Informe de expedición',
  'sys_recy_report' 		=> 'Información del sistema',
  'sys_expe_blackholl_1' 		=> '¡Tu flota entró en un agujero negro y se perdió parcialmente!',
  'sys_expe_blackholl_2' 		=> '¡Tu flota entró en un agujero negro y se perdió por completo!',
  'sys_expe_nothing_1' 		=> '¡Tus investigadores fueron testigos de una SuperNova! Y tus acumuladores lograron capturar parte de la energía liberada.',
  'sys_expe_nothing_2' 		=> '¡Tus investigadores no encontraron nada!',
  'sys_expe_found_goods' 		=> '¡Tus investigadores encontraron un planeta rico en recursos!<br>Obtuviste %s %s, %s %s y %s %s',
  'sys_expe_found_ships' 		=> '¡Tus investigadores encontraron una flota en perfecto estado!<br>Obtuviste: ',
  'sys_expe_back_home' 		=> 'Tu flota regresa.',
  'sys_mess_transport' 		=> 'Transporte',
//  'sys_tran_mess_owner' 		=> 'Una de tus flotas llega al planeta %s %s y entrega %s %s, %s  %s y %s %s.',
  'sys_tran_mess_user'  		=> 'Una flota del planeta %s %s llegó a %s %s y entregó %s %s, %s %s y %s %s.',
  'sys_relocate_mess_user'  		=> 'También se reubicaron las siguientes unidades de combate en el planeta:<br />',
  'sys_mess_fleetback' 		=> 'Regreso',
  'sys_tran_mess_back' 		=> 'Una de tus flotas regresa al planeta %s %s.',
  'sys_recy_gotten' 		=> 'Una de tus flotas ha recolectado %s %s y %s %s. Regresa al planeta.',
  'sys_notenough_money' 		=> 'No tienes suficientes recursos para construir: %s. Actualmente tienes: %s %s , %s %s y %s %s. Para construir necesitas: %s %s , %s %s y %s %s.',
  'sys_nomore_level'		=> 'No puedes mejorar esto más. Ha alcanzado el nivel máximo ( %s ).',
  'sys_buildlist' 			=> 'Lista de construcciones',
  'sys_buildlist_fail' 		=> 'sin construcciones',
  'sys_gain' 			=> 'Recolección: ',
  'sys_debris' 			=> 'Escombros: ',
  'sys_noaccess' 			=> 'Acceso denegado',
  'sys_noalloaw' 			=> '¡No tienes acceso a esta zona!',
  'sys_governor'        => 'Gobernador',

  'flt_error_duration_wrong' => 'No se puede enviar la flota - no hay intervalos disponibles para el retraso. Estudia más niveles de Astrocartografía',
  'flt_stay_duration' => 'Tiempo',

  'flt_mission_expedition' => [
    'msg_sender' => 'Informe de expedición',
    'msg_title' => 'Informe de expedición',

    'found_dark_matter_new' => 'MO obtenida:',
    'found_resources_new' => "Recursos encontrados:",
    'found_fleet_new' => "Naves encontradas:",
    'lost_fleet_new' => "Se perdieron las siguientes naves:",

    'found_dark_matter' => 'Obtenidas %1$d unidades de MO',
    'found_resources' => "Recursos encontrados:\r\n",
    'found_fleet' => "Naves encontradas:\r\n",
    'lost_fleet' => "Se perdieron las siguientes naves:\r\n",
    'outcomes' => [
      FLT_EXPEDITION_OUTCOME_NONE => [
        'messages' => [
          'Tus investigadores no encontraron nada',
        ],
      ],

      FLT_EXPEDITION_OUTCOME_LOST_FLEET => [
        'messages' => [
          'La flota entró en un agujero negro y se perdió parcialmente',
        ],
      ],

      FLT_EXPEDITION_OUTCOME_LOST_FLEET_ALL => [
        'messages' => [
          'Si solo lo hubieras visto... ¡Es tan hermoso... Te llama... (se perdió el contacto con la flota)',
          // 'Informe de la flota %1$s. Hemos completado la exploración del sector. La tripulación está descontenta ¡Eh, ¿qué haces en el puente?! (se perdió el contacto con la flota)',
          'Informe de la flota %1$s. Todo tranquilo (interferencias) (se perdió el contacto con la flota)',
          '¡AAAAAA! ¿QUÉ ES ESO? ¿DE DÓNDE SALIÓ (se perdió el contacto con la flota)',
          'Objeto desconocido detectado. No responde a los protocolos estándar. Enviamos una sonda para investigar (se perdió el contacto con la flota)',
        ],
      ],

      FLT_EXPEDITION_OUTCOME_FOUND_FLEET => [
        'no_result' => 'Desafortunadamente, la potencia combinada de todas las computadoras de la flota no fue suficiente ni para controlar la nave más pequeña. Intenta enviar más naves y/o naves más grandes',
        'messages' => [
          0 => [
            'Encontraste una flota completamente nueva',
          ],
          1 => [
            'Encontraste una flota',
          ],
          2 => [
            'Encontraste una flota usada',
          ],
        ],
      ],

      FLT_EXPEDITION_OUTCOME_FOUND_RESOURCES => [
        'no_result' => 'Las bodegas de tu flota no pudieron contener ni un solo contenedor de recursos. Intenta enviar una flota con más transportes',
        'messages' => [
          0 => [
            'Encontraste un tesoro pirata con recursos. ¿Cuántas naves fueron destruidas para acumular tanto botín?',
          ],
          1 => [
            'Encontraste una base abandonada en un asteroide. ¿I wonder, ¿adónde fueron sus habitantes? Al investigar las ruinas, encontraste algunos almacenes intactos con recursos',
          ],
          2 => [
            'Te topaste con un convoy de transporte destruido. Al revisar las bodegas de las naves destruidas, encontraste algunos recursos',
          ],
        ],
      ],

      FLT_EXPEDITION_OUTCOME_FOUND_DM => [
        'no_result' => 'Desafortunadamente, todos los acumuladores de la flota no fueron suficientes para recolectar ni una sola MO. Intenta enviar una flota más grande',
        'messages' => 'Tu flota fue testigo del nacimiento de una SuperNova',
        // 'messages' => array(
        //   'Tu flota fue testigo del nacimiento de una SuperNova 1',
        //   'Tu flota fue testigo del nacimiento de una SuperNova 2',
        //   'Tu flota fue testigo del nacimiento de una SuperNova 3',
        // ),
      ],

    ],
  ],

  // News page & a bit of imperator page
  'news_fresh'      => 'Noticias recientes',
  'news_all'        => 'Todas las noticias',
  'news_title'      => 'Noticias',
  'news_none'       => 'No hay noticias',
  'news_new'        => 'NUEVO',
  'news_future'     => 'AVANCE',
  'news_more'       => 'Más...',
  'news_hint'       => 'Para ocultar la lista de noticias recientes, léelas todas haciendo clic en el título "[ Noticias ]"',

  'news_date'       => 'Fecha',
  'news_announce'   => 'Contenido',
  'news_detail_url' => 'Enlace a detalles',
  'news_mass_mail'  => 'Enviar noticia a todos los jugadores',

  'news_total'      => 'Total de noticias: ',

  'news_add'        => 'Agregar noticia',
  'news_edit'       => 'Editar noticia',
  'news_copy'       => 'Copiar noticia',
  'news_mode_new'   => 'Nueva',
  'news_mode_edit'  => 'Edición',
  'news_mode_copy'  => 'Copia',

  'sys_administration' => 'Administración del servidor',

  'note_add'        => 'Agregar nota',
  'note_del'        => 'Eliminar nota',
  'note_edit'        => 'Editar nota',

  // Shortcuts
  'shortcut_title'     => 'Marcadores',
  'shortcut_none'      => 'No hay marcadores',
  'shortcut_new'       => 'NUEVO',
  'shortcut_text'      => 'Texto',

  'shortcut_add'       => 'Agregar marcador',
  'shortcut_edit'      => 'Editar marcador',
  'shortcut_copy'      => 'Copiar marcador',
  'shortcut_mode_new'  => 'Nuevo',
  'shortcut_mode_edit' => 'Edición',
  'shortcut_mode_copy' => 'Copia',

  // Missile-related
  'mip_h_launched'			=> 'Lanzamiento de misiles interplanetarios',
  'mip_launched'				=> '¡Misiles interplanetarios lanzados: <b>%s</b>!',

  'mip_no_silo'				=> 'Nivel insuficiente de silos de misiles en el planeta <b>%s</b>.',
  'mip_no_impulse'			=> 'Se requiere investigación del motor de impulso.',
  'mip_too_far'				=> 'El misil no puede volar tan lejos.',
  'mip_planet_error'			=> 'Error - más de un planeta en una coordenada',
  'mip_no_rocket'				=> 'No hay suficientes misiles en el silo para realizar el ataque.',
  'mip_hack_attempt'			=> ' ¿Eres un hacker? Un truco más como este y serás baneado. He registrado tu dirección IP y nombre de usuario.',

  'mip_all_destroyed' 		=> 'Todos los misiles interplanetarios fueron destruidos por misiles interceptores<br>',
  'mip_destroyed'				=> '%s misiles interplanetarios fueron destruidos por misiles interceptores.<br>',
  'mip_defense_destroyed'	=> 'Se destruyeron las siguientes defensas:<br />',
  'mip_recycled'				=> 'Reciclado de escombros de defensa: ',
  'mip_no_defense'			=> '¡No había defensas en el planeta atacado!',

  'mip_sender_amd'			=> 'Tropas de misiles espaciales',
  'mip_subject_amd'			=> 'Ataque con misiles',
  'mip_body_attack'			=> 'Ataque con misiles interplanetarios (%1$s unidades) desde el planeta %2$s <a href="galaxy.php?mode=3&galaxy=%3$d&system=%4$d&planet=%5$d">[%3$d:%4$d:%5$d]</a> al planeta %6$s <a href="galaxy.php?mode=3&galaxy=%7$d&system=%8$d&planet=%9$d">[%7$d:%8$d:%9$d]</a><br><br>',

  // Misc
  'sys_game_rules' => 'Reglas del juego',
  'sys_game_documentation' => 'Descripción del juego',
  'sys_banned_msg' => 'Estás baneado. Para obtener información, visita <a href="banned.php">aquí</a>. Fecha de finalización del bloqueo de la cuenta: ',
  'sys_total_time' => 'Tiempo total',
  'sys_total_time_short' => 'Cola',
  'eco_que_finish' => 'Finalización',

  // Universe
  'uni_moon_of_planet' => 'del planeta',

  // Combat reports
  'cr_view_title'  => "Ver informes de combate",
  'cr_view_button' => "Ver informe",
  'cr_view_prompt' => "Ingresa el código",
  'cr_view_my'     => "Mis informes de combate",
  'cr_view_hint'   => '<ul><li>Puedes ver tus informes de combate haciendo clic en el enlace "Mis informes de combate" en el encabezado</li><li>El código del informe de combate se encuentra en su última línea y es una secuencia de 32 dígitos y caracteres del alfabeto latino</li></ul>',

  // Fleet
  'flt_gather_all'    => 'Recoger recursos',

  // Ban system
  'ban_title'      => 'Lista negra',
  'ban_name'       => 'Nombre',
  'ban_reason'     => 'Razón del bloqueo',
  'ban_from'       => 'Fecha de bloqueo',
  'ban_to'         => 'Duración del bloqueo',
  'ban_by'         => 'Emitido por',
  'ban_no'         => 'No hay jugadores bloqueados',
  'ban_thereare'   => 'Total',
  'ban_players'    => 'bloqueados',
  'ban_banned'     => 'Jugadores bloqueados: ',


  // Records page
  'rec_title'  => 'Récords del Universo',
  'rec_build'  => 'Edificios',
  'rec_specb'  => 'Edificios especiales',
  'rec_playe'  => 'Jugador',
  'rec_defes'  => 'Defensa',
  'rec_fleet'  => 'Flota',
  'rec_techn'  => 'Tecnologías',
  'rec_level'  => 'Nivel',
  'rec_nbre'   => 'Cantidad',
  'rec_rien'   => '-',

  // Credits page
  'cred_link'    => 'Internet',
  'cred_site'    => 'Sitio web',
  'cred_forum'   => 'Foro',
  'cred_credit'  => 'Autores',
  'cred_creat'   => 'Director',
  'cred_prog'    => 'Programador',
  'cred_master'  => 'Líder',
  'cred_design'  => 'Diseñador',
  'cred_web'     => 'Webmaster',
  'cred_thx'     => 'Agradecimientos',
  'cred_based'   => 'Base para la creación de XNova',
  'cred_start'   => 'Lugar de debut de XNova',

  // Built-in chat
  'chat_common'   => 'Chat general',
  'chat_ally'     => 'Chat de Alianza',
  'chat_history'  => 'Historial del chat',
  'chat_message'  => 'Mensaje',
  'chat_send'     => 'Enviar',
  'chat_page'     => 'Página',
  'chat_timeout'  => 'Chat desactivado por inactividad. Actualiza la página.',

  // Interface of Jump Gate
  'gate_start_moon' => 'Luna de inicio',
  'gate_dest_moon'  => 'Luna de destino',
  'gate_use_gate'   => 'Usar puerta',
  'gate_ship_sel'   => 'Seleccionar naves',
  'gate_ship_dispo' => 'disponible',
  'gate_jump_btn'   => '¡Realizar salto!',
  'gate_jump_done'  => '¡Las puertas están en proceso de recarga!<br>Las puertas estarán listas para usar en: ',
  'gate_wait_dest'  => '¡El destino de las puertas está en preparación! Las puertas estarán listas para usar en: ',
  'gate_no_dest_g'  => 'No se encontraron puertas de salto en el destino',
  'gate_no_src_ga'  => 'No hay puertas de salto disponibles',
  'gate_wait_star'  => '¡Las puertas están en proceso de recarga!<br>Las puertas estarán listas para usar en: ',
  'gate_wait_data'  => 'Error: no hay datos para el salto',
  'gate_vacation'   => 'Error: no puedes realizar un salto porque estás en modo vacaciones',
  'gate_ready'      => 'Puertas listas para saltar',

  // quests
  'qst_quests'               => 'Misiones',
  'qst_msg_complete_subject' => 'Misión completada',
  'qst_msg_complete_body'    => 'Has completado la misión "%s".',
  'qst_msg_your_reward'      => 'Tu recompensa:',

  // Messages
  'msg_from_admin' => 'Administración del Universo',
  'msg_class' => [
    MSG_TYPE_OUTBOX => 'Mensajes enviados',
    MSG_TYPE_SPY => 'Informes de espionaje',
    MSG_TYPE_PLAYER => 'Mensajes de jugadores',
    MSG_TYPE_ALLIANCE => 'Mensajes de Alianza',
    MSG_TYPE_COMBAT => 'Informes de combate',
    MSG_TYPE_RECYCLE => 'Informes de reciclaje',
    MSG_TYPE_TRANSPORT => 'Llegada de flota',
    MSG_TYPE_ADMIN => 'Mensajes de Administración',
    MSG_TYPE_EXPLORE => 'Informes de expediciones',
    MSG_TYPE_QUE => 'Mensajes de cola de construcción',
    MSG_TYPE_NEW => 'Todos los mensajes',
  ],

  'msg_que_research_from'    => 'Instituto de investigación',
  'msg_que_research_subject' => 'Nueva tecnología',
  'msg_que_research_message' => 'Se ha investigado una nueva tecnología \'%s\'. Nuevo nivel - %d',

  'msg_que_planet_from'    => 'Gobernador',

  'msg_que_hangar_subject' => 'Trabajo en el astillero completado',
  'msg_que_hangar_message' => "El astillero en %s ha completado su trabajo",

  'msg_que_built_subject'   => 'Trabajos planetarios completados',
  'msg_que_built_message'   => "Se ha completado la construcción del edificio '%2\$s' en %1\$s. Niveles construidos: %3\$d",
  'msg_que_destroy_message' => "Se ha completado la demolición del edificio '%2\$s' en %1\$s. Niveles demolidos: %3\$d",

  'msg_personal_messages' => 'Mensajes personales',

  'sys_opt_bash_info'    => 'Configuración del sistema anti-bashing',
  'sys_opt_bash_attacks' => 'Número de ataques en una oleada',
  'sys_opt_bash_interval' => 'Intervalo entre oleadas',
  'sys_opt_bash_scope' => 'Período de cálculo del bashing',
  'sys_opt_bash_war_delay' => 'Moratoria después de declarar la guerra',
  'sys_opt_bash_waves' => 'Número de oleadas por período',
  'sys_opt_bash_disabled'    => 'Sistema anti-bashing desactivado',

  'sys_id' => 'ID',
  'sys_identifier' => 'Identificador',

  'sys_email'   => 'Correo electrónico',
  'sys_ip' => 'IP',

  'sys_max' => 'Máx',
  'sys_maximum' => 'Máximo',
  'sys_maximum_level' => 'Nivel máximo',

  'sys_user_name' => 'Nombre de usuario',
  'sys_player_name' => 'Nombre del jugador',
  'sys_user_name_short' => 'Nombre',

  'sys_planets' => 'Planetas',
  'sys_moons' => 'Lunas',

  'sys_quantity' => 'Cantidad',
  'sys_quantity_maximum' => 'Cantidad máxima',
  'sys_qty' => 'Cant',
  'sys_quantity_total' => 'Cantidad total',

  'sys_buy_for' => 'Comprar por',
  'sys_buy' => 'Comprar',
  'sys_for' => 'por',

  'sys_eco_lack_dark_matter' => 'Falta Materia Oscura',

  'time_local' => 'Hora del jugador',
  'time_server' => 'Hora del servidor',

  'sys_result' => [
    'error_dark_matter_not_enough' => 'No hay suficiente Materia Oscura para completar la operación',
    'error_dark_matter_change' => 'Error al cambiar la cantidad de Materia Oscura. Intenta de nuevo. Si el error persiste, informa a la Administración del servidor',
  ],

  // Arrays
  'sys_build_result' => [
    BUILD_ALLOWED => 'Se puede construir',
    BUILD_REQUIRE_NOT_MEET => 'Requisitos no cumplidos',
    BUILD_AMOUNT_WRONG => 'Demasiados',
    BUILD_QUE_WRONG => 'Cola inexistente',
    BUILD_QUE_UNIT_WRONG => 'Cola incorrecta',
    BUILD_INDESTRUCTABLE => 'No se puede destruir',
    BUILD_NO_RESOURCES => 'Faltan recursos',
    BUILD_NO_UNITS => 'No hay unidades',
    BUILD_UNIT_BUSY => [
      0 => 'Edificio ocupado',
      STRUC_LABORATORY => 'Investigación en curso',
      STRUC_LABORATORY_NANO => 'Investigación en curso',
    ],
    BUILD_QUE_FULL => 'Cola llena',
    BUILD_SILO_FULL => 'Silo de misiles lleno',
    BUILD_MAX_REACHED => 'Ya has construido y/o puesto en cola la cantidad máxima de unidades de este tipo',
    BUILD_SECTORS_NONE => 'No hay sectores libres',
    BUILD_AUTOCONVERT_AVAILABLE => 'Autoconversión disponible',
    BUILD_HIGHSPOT_NOT_ACTIVE => 'Evento no activo',
  ],

  'sys_game_mode' => [
    GAME_SUPERNOVA => 'SuperNova',
    GAME_OGAME     => 'oGame',
    GAME_BLITZ     => 'Servidor Blitz',
  ],

  'months' => [
     1 =>'enero',
     2 =>'febrero',
     3 =>'marzo',
     4 =>'abril',
     5 =>'mayo',
     6 =>'junio',
     7 =>'julio',
     8 =>'agosto',
     9 =>'septiembre',
    10 =>'octubre',
    11 =>'noviembre',
    12 =>'diciembre'
  ],

  'weekdays' => [
    0 => 'Domingo',
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado'
  ],

  'user_level' => [
    0 => 'Jugador',
    1 => 'Moderador',
    2 => 'Operador',
    3 => 'Administrador',
    4 => 'Desarrollador',
  ],

  'user_level_shortcut' => [
    0 => 'J',
    1 => 'M',
    2 => 'O',
    3 => 'A',
    4 => 'D',
  ],

  'sys_lessThen15min'   => '&lt; 15 min',

  'sys_no_points'         => '¡No tienes suficiente <span class="dark_matter">Materia Oscura</span>!',
  'sys_dark_matter_obtain_header' => 'Cómo obtener <span class="dark_matter">Materia Oscura</span>',
  'sys_dark_matter_desc' => 'La Materia Oscura es una materia no bariónica indetectable por métodos estándar, que constituye el 23% de la masa del Universo. Se puede extraer una cantidad increíble de energía de ella. Debido a esto, y a las dificultades asociadas con su extracción, la Materia Oscura es muy valiosa.',
  'sys_dark_matter_hint' => 'Con esta sustancia puedes contratar oficiales y comandantes.',

  'sys_dark_matter_what_why_how' => 'Qué es <span class="dark_matter">Materia Oscura</span> y <span class="metamatter">Metamateria</span>',
  'sys_dark_matter_what_header' => 'Qué es <span class="dark_matter">Materia Oscura</span>',
  'sys_dark_matter_description_header' => 'Para qué sirve <span class="dark_matter">Materia Oscura</span>',
  'sys_dark_matter_description_text' => '<span class="dark_matter">Materia Oscura</span> es un recurso dentro del juego que te permite realizar diversas operaciones:
    <ul>
      <li>Comprar <a href="index.php?page=premium"><span class="link">Cuenta Premium</span></a></li>
      <li>Reclutar <a href="officer.php?mode=600"><span class="link">Mercenarios</span></a> en el Imperio </li>
      <li>Contratar Gobernadores y comprar sectores adicionales <a href="overview.php?mode=manage"><span class="link">en los planetas</span></a></li>
      <li>Comprar <a href="officer.php?mode=1100"><span class="link">Planos</span></a></li>
      <li>Comprar <a href="artifacts.php"><span class="link">Artefactos</span></a></li>
      <li>Usar <a href="market.php"><span class="link">Mercado Negro</span></a>: Intercambiar un tipo de recurso por otro; vender naves; comprar naves usadas, etc.</li>
      <li>...y mucho más</li>
    </ul>',
  'sys_dark_matter_obtain_text' => 'Obtienes <span class="metamatter">Materia Oscura</span> durante el juego: ganando experiencia por incursiones exitosas en otros planetas, investigando nuevas tecnologías, y también por construir y destruir edificios.
    A veces, las expediciones de investigación también pueden traer <span class="metamatter">MO</span>.',

  'sys_dark_matter_obtain_text_convert' => '<br />Si no tienes suficiente <span class="dark_matter">Materia Oscura</span>, adquiere <span class="metamatter">Metamateria</span>. En caso de falta de <span class="dark_matter">MO</span>, se usará la cantidad necesaria de <span class="metamatter">Metamateria</span> en su lugar',

  'sys_msg_err_update_dm' => 'Error al actualizar la cantidad de MO!',

  'sys_na' => 'No disponible',
  'sys_na_short' => 'N/D',

  'sys_ali_res_title' => 'Recursos de la Alianza',

  'sys_bonus' => 'Bonus',

  'sys_of_ally' => 'de la Alianza',

  'sys_hint_player_name' => 'La búsqueda de jugadores se puede realizar por ID o nombre. Si el nombre del jugador contiene caracteres no legibles o solo números, se debe usar el ID para la búsqueda',
  'sys_hint_ally_name' => 'La búsqueda de Alianzas se puede realizar por ID, etiqueta o nombre. Si la etiqueta o el nombre de la Alianza contienen caracteres no legibles o solo números, se debe usar el ID para la búsqueda',

  'sys_fleet_and' => '+ flotas',

  'sys_on_planet' => 'En el planeta',
  'fl_on_stores' => 'En almacén',

  'sys_ali_bonus_members' => 'Tamaño mínimo de la Alianza para obtener el bonus',

  'sys_premium' => 'Premium',

  'mrc_period_list' => [
    PERIOD_MINUTE    => '1 minuto',
    PERIOD_MINUTE_3  => '3 minutos',
    PERIOD_MINUTE_5  => '5 minutos',
    PERIOD_MINUTE_10 => '10 minutos',
    PERIOD_DAY       => '1 día',
    PERIOD_DAY_3     => '3 días',
    PERIOD_WEEK      => '1 semana',
    PERIOD_WEEK_2    => '2 semanas',
    PERIOD_MONTH     => '30 días',
    PERIOD_MONTH_2   => '60 días',
    PERIOD_MONTH_3   => '90 días',
  ],

  'sys_sector_buy' => 'Comprar 1 sector',

  'sys_select_confirm' => 'Confirmar selección',

  'sys_capital' => 'Capital',

  'sys_result_operation' => 'Mensajes',

  'sys_password' => 'Contraseña',
  'sys_password_length' => 'Longitud de la contraseña',
  'sys_password_seed' => 'Caracteres utilizados',

  'sys_msg_ube_report_err_not_found' => 'Informe de combate no encontrado. Verifica la clave. También es posible que el informe haya sido eliminado por antigüedad',

  'sys_mess_attack_report' 	=> 'Informe de combate',
  'sys_perte_attaquant' 		=> 'El atacante perdió',
  'sys_perte_defenseur' 		=> 'El defensor perdió',

  'ube_report_info_page_header' => 'Informe de combate',
  'ube_report_info_page_header_cypher' => 'Código de acceso',
  'ube_report_info_main' => 'Información principal del combate',
  'ube_report_info_date' => 'Fecha y hora',
  'ube_report_info_location' => 'Ubicación',
  'ube_report_info_rounds_number' => 'Número de rondas',
  'ube_report_info_outcome' => 'Resultado del combate',
  'ube_report_info_outcome_win' => 'El atacante ganó el combate',
  'ube_report_info_outcome_loss' => 'El atacante perdió el combate',
  'ube_report_info_outcome_draw' => 'El combate terminó en empate',
  'ube_report_info_link' => 'Enlace al informe de combate',
  'ube_report_info_bbcode' => 'BBCode para insertar en el chat',
  'ube_report_info_sfr' => 'El combate terminó en una ronda con la derrota del atacante<br />Posible FMR',
  'ube_report_info_debris' => 'Escombros en órbita',
  'ube_report_info_debris_simulator' => '(sin contar la creación de la Luna)',
  'ube_report_info_loot' => 'Botín',
  'ube_report_info_loss' => 'Pérdidas de combate',
  'ube_report_info_generate' => 'Tiempo de generación de la página',

  'ube_report_moon_was' => 'Este planeta ya tenía una luna',
  'ube_report_moon_chance' => 'Probabilidad de formación de luna',
  'ube_report_moon_created' => 'En la órbita del planeta se formó una luna con un diámetro de',

  'ube_report_moon_reapers_none' => 'Todas las naves con motores gravitacionales fueron destruidas durante el combate',
  'ube_report_moon_reapers_wave' => 'Las naves del atacante crearon una onda gravitacional enfocada',
  'ube_report_moon_reapers_chance' => 'Probabilidad de destrucción de la luna',
  'ube_report_moon_reapers_success' => 'Luna destruida',
  'ube_report_moon_reapers_failure' => 'La potencia de la onda no fue suficiente para destruir la luna',

  'ube_report_moon_reapers_outcome' => 'Probabilidad de explosión de motores',
  'ube_report_moon_reapers_survive' => 'La compensación precisa de los campos gravitacionales del sistema permitió disipar el retroceso de la destrucción de la luna',
  'ube_report_moon_reapers_died' => 'Al no poder compensar los campos gravitacionales adicionales del sistema, la flota fue destruida',

  'ube_report_side_attacker' => 'Atacante',
  'ube_report_side_defender' => 'Defensor',

  'ube_report_round' => 'Ronda',
  'ube_report_unit' => 'Unidad de combate',
  'ube_report_attack' => 'Ataque',
  'ube_report_shields' => 'Escudos',
  'ube_report_shields_passed' => 'Penetración',
  'ube_report_armor' => 'Armadura',
  'ube_report_damage' => 'Daño',
  'ube_report_loss' => 'Pérdidas',

 
  'ube_report_info_restored' => 'Estructuras defensivas restauradas',
  'ube_report_info_loss_final' => 'Pérdidas finales de unidades',
  'ube_report_info_loss_resources' => 'Pérdidas en recursos',
  'ube_report_info_loss_dropped' => 'Pérdidas de recursos por reducción de capacidad de almacenamiento',
  'ube_report_info_loot_lost' => 'Recursos saqueados del planeta',
  'ube_report_info_loss_gained' => 'Pérdidas por saqueo de recursos',
  'ube_report_info_loss_in_metal' => 'Pérdidas totales en metal',

  'ube_report_msg_body_common' => 'La batalla tuvo lugar %s en la órbita %s [%d:%d:%d] %s<br />%s<br /><br />',
  'ube_report_msg_body_debris' => 'Como resultado de la batalla, se generaron escombros en la órbita del planeta:<br />',
  'ube_report_msg_body_sfr' => 'Se perdió contacto con la flota',

  'ube_report_capture' => 'Captura de planeta',
  'ube_report_capture_result' => [
    UBE_CAPTURE_DISABLED => 'La captura de planetas está desactivada',
    UBE_CAPTURE_NON_PLANET => 'Solo se pueden capturar planetas',
    UBE_CAPTURE_NOT_A_WIN_IN_1_ROUND => 'Para capturar un planeta, la batalla debe terminar en victoria en el primer asalto',
    UBE_CAPTURE_TOO_MUCH_FLEETS => 'Solo puede participar una flota de captura y la flota planetaria',
    UBE_CAPTURE_NO_ATTACKER_USER_ID => 'ERROR INTERNO - ¡No hay ID de atacante! ¡Informa a los desarrolladores!',
    UBE_CAPTURE_NO_DEFENDER_USER_ID => 'ERROR INTERNO - ¡No hay ID de defensor! ¡Informa a los desarrolladores!',
    UBE_CAPTURE_CAPITAL => 'No se puede capturar la capital',
    UBE_CAPTURE_TOO_LOW_POINTS => 'Solo puedes capturar planetas de jugadores cuyo total de puntos sea al menos el doble que el tuyo',
    UBE_CAPTURE_NOT_ENOUGH_SLOTS => 'No hay más espacios para capturar planetas',
    UBE_CAPTURE_SUCCESSFUL => 'El planeta ha sido capturado por el atacante',
  ],

  'sys_kilometers_short' => 'km',

  'ube_simulation' => 'Simulación',

  'sys_hire_do' => 'Contratar',

  'sys_captains' => 'Capitanes',

  'sys_fleet_composition' => 'Composición de la flota',

  'sys_continue' => 'Continuar',

  'uni_planet_density_types' => [
    PLANET_DENSITY_NONE => 'No existe',
    PLANET_DENSITY_ICE_HYDROGEN => 'Hielo de hidrógeno',
    PLANET_DENSITY_ICE_METHANE => 'Hielo de metano',
    PLANET_DENSITY_ICE_WATER => 'Hielo de agua',
    PLANET_DENSITY_CRYSTAL_RAW => 'Cristal crudo',
    PLANET_DENSITY_CRYSTAL_SILICATE => 'Silicato',
    PLANET_DENSITY_CRYSTAL_STONE => 'Piedra',
    PLANET_DENSITY_STANDARD => 'Estándar',
    PLANET_DENSITY_METAL_ORE => 'Mineral',
    PLANET_DENSITY_METAL_PERIDOT => 'Olivino',
    PLANET_DENSITY_METAL_RAW => 'Metal crudo',
  ],

  'sys_planet_density' => 'Densidad',
  'sys_planet_density_units' => 'kg/m&sup3;',
  'sys_planet_density_core' => 'Tipo de núcleo',

  'sys_change' => 'Cambiar',
  'sys_show' => 'Mostrar',
  'sys_hide' => 'Ocultar',
  'sys_close' => 'Cerrar',
  'sys_unlimited' => 'Sin límites',

  'ov_core_type_current' => 'Tipo de núcleo actual',
  'ov_core_change_to' => 'Cambiar a',
  'ov_core_err_none' => 'El tipo de núcleo del planeta ha sido cambiado de "%s" a "%s".<br />Nueva densidad del planeta: %d kg/m3',
  'ov_core_err_not_a_planet' => 'Solo puedes cambiar la densidad del núcleo en un planeta',
  'ov_core_err_denisty_type_wrong' => 'Tipo de núcleo incorrecto',
  'ov_core_err_same_density' => 'El nuevo tipo de núcleo no es diferente al actual - no hay cambios',
  'ov_core_err_no_dark_matter' => 'No tienes suficiente Materia Oscura para cambiar el tipo de núcleo',

  'sys_color'    => "Color",

  'topnav_imp_attack' => '¡Tu Imperio está bajo ataque!',
  'topnav_user_rank' => 'Tu posición actual en las estadísticas',
  'topnav_users' => 'Total de jugadores registrados',
  'topnav_users_online' => 'Jugadores en línea actualmente',

  'topnav_refresh_page' => 'Recargar página',

  'sys_colonies' => 'Colonias',
  'sys_radio' => 'Radio "Espacio"',

  'sys_auth_provider_list' => [
    ACCOUNT_PROVIDER_NONE => 'Tabla USERS',
    ACCOUNT_PROVIDER_LOCAL => 'Tabla ACCOUNT',
    ACCOUNT_PROVIDER_CENTRAL => 'Tabla central ACCOUNT',
  ],

  'sys_login_messages' => [
    LOGIN_UNDEFINED => 'El proceso de inicio de sesión no ha comenzado',
    LOGIN_SUCCESS => 'Inicio de sesión exitoso',
    LOGIN_ERROR_USERNAME_EMPTY => 'El nombre de usuario no puede estar vacío',
    LOGIN_ERROR_USERNAME_RESTRICTED_CHARACTERS => 'El nombre de usuario no puede contener caracteres restringidos: ',
    LOGIN_ERROR_USERNAME => 'No se encontró ningún jugador con ese nombre',
    LOGIN_ERROR_USERNAME_ALLY_OR_BOT => 'Este nombre pertenece a una Alianza o bot. No puedes iniciar sesión con él... al menos por ahora',
    LOGIN_ERROR_PASSWORD_EMPTY => 'La contraseña no puede estar vacía',
    LOGIN_ERROR_PASSWORD_TRIMMED => 'La contraseña no puede comenzar o terminar con espacios, tabulaciones o saltos de línea',
    LOGIN_ERROR_PASSWORD => 'Contraseña incorrecta',
    //    LOGIN_ERROR_COOKIE => '',

    REGISTER_SUCCESS => 'Registro completado con éxito',
    REGISTER_ERROR_BLITZ_MODE => 'El registro de nuevos jugadores está desactivado en el modo Blitz',
    REGISTER_ERROR_USERNAME_WRONG => 'Nombre de usuario incorrecto',
    REGISTER_ERROR_ACCOUNT_NAME_EXISTS => 'El nombre de cuenta ya está en uso. Intenta iniciar sesión con este nombre y tu contraseña o restablecer tu contraseña',
    REGISTER_ERROR_PASSWORD_INSECURE => 'Contraseña incorrecta. La contraseña debe tener al menos ' . PASSWORD_LENGTH_MIN . ' caracteres',
    REGISTER_ERROR_USERNAME_SHORT => 'Nombre demasiado corto. Debe tener al menos ' . LOGIN_LENGTH_MIN. ' caracteres',
    REGISTER_ERROR_PASSWORD_DIFFERENT => 'La contraseña y la confirmación no coinciden. Verifica los datos',
    REGISTER_ERROR_EMAIL_EMPTY => 'El correo electrónico no puede estar vacío',
    REGISTER_ERROR_EMAIL_WRONG => 'El correo electrónico ingresado no es válido. Verifica la dirección o usa otra',
    REGISTER_ERROR_EMAIL_EXISTS => 'Este correo electrónico ya está registrado. Si ya te registraste en el juego, intenta restablecer tu contraseña. De lo contrario, usa otro correo',

    PASSWORD_RESTORE_ERROR_EMAIL_NOT_EXISTS => 'No hay ningún jugador con este correo electrónico principal',
    PASSWORD_RESTORE_ERROR_TOO_OFTEN => 'Solo puedes solicitar un código de recuperación cada 10 minutos. Si no recibiste el correo, revisa la carpeta de SPAM o contacta a la Administración del servidor al correo <span class="ok">' . $config->server_email . '</span> desde la dirección que usaste al registrarte',
    PASSWORD_RESTORE_ERROR_SENDING => 'Error al enviar el correo. Contacta a la Administración del servidor al correo <span class="ok">' . $config->server_email . '</span>',
    PASSWORD_RESTORE_SUCCESS_CODE_SENT => 'Correo con código de recuperación enviado con éxito',

    PASSWORD_RESTORE_ERROR_CODE_EMPTY => 'El código de recuperación no puede estar vacío',
    PASSWORD_RESTORE_ERROR_CODE_WRONG => 'Código de recuperación incorrecto',
    PASSWORD_RESTORE_ERROR_CODE_TOO_OLD => 'El código de recuperación ha expirado. Solicita uno nuevo',
    PASSWORD_RESTORE_ERROR_CODE_OK_BUT_NO_ACCOUNT_FOR_EMAIL => 'El código de recuperación es correcto, pero no se encontró ninguna cuenta con este correo. Puede que haya sido eliminada o hubo un error interno. Contacta a la Administración del servidor',
    PASSWORD_RESTORE_SUCCESS_PASSWORD_SENT => 'Contraseña restablecida con éxito. Se te ha enviado un correo con la nueva contraseña',
    PASSWORD_RESTORE_SUCCESS_PASSWORD_SEND_ERROR => 'Error al enviar el correo con la nueva contraseña. Solicita un nuevo código de recuperación e inténtalo de nuevo',

    REGISTER_ERROR_PLAYER_NAME_TRIMMED => 'El nombre del jugador no puede comenzar o terminar con espacios, tabulaciones o saltos de línea',
    REGISTER_ERROR_PLAYER_NAME_EMPTY => 'El nombre del jugador no puede estar vacío',
    REGISTER_ERROR_PLAYER_NAME_RESTRICTED_CHARACTERS => 'El nombre del jugador contiene caracteres no permitidos',
    REGISTER_ERROR_PLAYER_NAME_SHORT => 'El nombre del jugador no puede tener menos de ' . LOGIN_LENGTH_MIN . ' caracteres',
    REGISTER_ERROR_PLAYER_NAME_EXISTS => 'Este nombre de jugador ya está en uso. Por favor, elige otro',

    // Errores internos
    AUTH_ERROR_INTERNAL_PASSWORD_CHANGE_ON_RESTORE => '¡ERROR INTERNO! ¡INFORMA A LA ADMINISTRACIÓN! Error al cambiar la contraseña. Por favor, informa a la Administración del Universo sobre este error',
    PASSWORD_RESTORE_ERROR_ADMIN_ACCOUNT => 'No se permite restablecer la contraseña para el Equipo del servidor. Contacta al Administrador',
    REGISTER_ERROR_ACCOUNT_CREATE => '¡Error al crear la cuenta! Por favor, informa a la Administración',
    LOGIN_ERROR_SYSTEM_ACCOUNT_TRANSLATION => 'ERROR DEL SISTEMA - FALLO EN LA TABLA DE TRADUCCIÓN DE PROVEEDORES! ¡Informa a la Administración del servidor!',
    PASSWORD_RESTORE_ERROR_ACCOUNT_NOT_EXISTS => 'Error interno - no se encontró la cuenta al cambiar la contraseña! ¡Informa a la Administración!',
    AUTH_PASSWORD_RESET_INSIDE_ERROR_NO_ACCOUNT_FOR_CONFIRMATION => '¡ERROR INTERNO! No hay cuentas para restablecer la contraseña con el código de confirmación correcto! Por favor, informa a la Administración del Universo sobre este error',
    LOGIN_ERROR_NO_ACCOUNT_FOR_COOKIE_SET => '¡ERROR INTERNO! ¡INFORMA A LA ADMINISTRACIÓN! No se estableció la cuenta en cookie_set()! Por favor, informa a la Administración del Universo sobre este error',
  ],

  'log_reg_email_title' => "Tu registro en el servidor %1\$s del juego SuperNova",
  'log_reg_email_text' => "Confirmación de registro para %3\$s\r\n\r\n
  Este correo contiene tus datos de registro en el servidor %1\$s del juego SuperNova\r\n
  Guarda estos datos en un lugar seguro\r\n\r\n
  Dirección del servidor: %2\$s\r\n
  Tu nombre de usuario: %3\$s\r\n
  Tu contraseña: %4\$s\r\n\r\n
  ¡Gracias por registrarte en nuestro servidor! ¡Te deseamos mucha suerte en el juego!\r\n
  Administración del servidor %1\$s %2\$s\r\n\r\n
  El servidor funciona con el motor libre 'Project SuperNova.WS'. Enciende tu SuperNova http://supernova.ws/",

  'log_lost_email_title' => 'SuperNova, Universo %s: Restablecimiento de contraseña',
  'log_lost_email_code' => "Alguien (puede que tú) ha solicitado restablecer la contraseña en el Universo %1\$4 del juego SuperNova. Si no solicitaste esto, ignora este correo.\r\n\r\nPara restablecer la contraseña, visita \r\n%1\$s?password_reset_confirm=1&password_reset_code=%2\$s#tab_password_reset\r\n o ingresa el código de confirmación \"%2\$s\" (¡SIN COMILLAS!) en la página %1\$s#tab_password_reset\r\n\r\nEste código será válido hasta %3\$s. Después de esta fecha, deberás solicitar un nuevo código",
  'log_lost_email_pass' => "Has restablecido tu contraseña en el servidor %1\$s del juego 'SuperNova'.\r\n\r\nTu nombre de usuario:\r\n%2\$s\r\n\r\nTu nueva contraseña:\r\n%3\$s\r\n\r\n¡Memorízala!\r\n\r\nPuedes iniciar sesión en el juego en " . SN_ROOT_VIRTUAL . "login.php usando el nombre y contraseña anteriores",

  'login_player_register_player_name' => 'Nombre del jugador',
  'login_player_register_description' => '¡Solo un paso más! Elige un nombre de jugador - el nombre que otros jugadores verán en este Universo',
  'login_player_register_do' => 'Elegir nombre',
  'login_player_register_logout' => 'Iniciar sesión con otra cuenta',
  'login_player_register_logout_description' => 'Si deseas iniciar sesión con otra cuenta, haz clic en el botón',

  'sys_password_reset_message_body' => "Has restablecido tu contraseña para acceder al juego en este Universo.\r\n\r\nTu nueva contraseña:\r\n\r\n%1\$s\r\n\r\n¡Memorízala!\r\n\r\nPuedes cambiar tu contraseña en cualquier momento en el menú 'Configuración'.",

  'sys_login_password_show' => 'Mostrar contraseña',
  'sys_login_password_hide' => 'Ocultar contraseña',
  'sys_password_repeat' => 'Repite la contraseña',

  'sys_game_disable_reason' => [
    GAME_DISABLE_NONE => 'Juego activado',
    GAME_DISABLE_REASON => 'Juego desactivado. Los jugadores verán un mensaje',
    GAME_DISABLE_UPDATE => 'El juego se está actualizando',
    GAME_DISABLE_STAT => 'Se está recalculando la estadística',
    GAME_DISABLE_INSTALL => 'El juego aún no está configurado',
    GAME_DISABLE_MAINTENANCE => 'Mantenimiento de la base de datos del servidor',
    GAME_DISABLE_EVENT_BLACK_MOON => '¡Luna Negra!',
    GAME_DISABLE_EVENT_OIS => 'Objetos en el espacio',
  ],

  'sys_sector_purchase_log' => 'El usuario {%2$d} {%1$s} compró 1 sector en el planeta {%5$d} {%3$s} tipo "%4$s" por %6$d TM',

  'sys_notes' => 'Notas',
  'sys_notes_priorities' => [
    0 => 'No importante',
    1 => 'Poco importante',
    2 => 'Normal',
    3 => 'Importante',
    4 => 'Muy importante',
  ],

  'sys_milliseconds' => 'milisegundos',

  'sys_gender' => 'Género',
  'sys_gender_list' => [
    GENDER_UNKNOWN => 'Decidirá cuando crezca',
    GENDER_MALE => 'Masculino',
    GENDER_FEMALE => 'Femenino',
  ],

  'imp_stat_header' => 'Gráfico de cambios en las estadísticas',
  'imp_stat_types' => [
    'TOTAL_RANK' => 'Posición en la estadística general',
    'TOTAL_POINTS' => 'Puntos totales',
    // 'TOTAL_COUNT' => 'Recursos totales',
    'TECH_RANK' => 'Posición en Investigación',
    'TECH_POINTS' => 'Puntos de Investigación',
    // 'TECH_COUNT' => 'Niveles',
    'BUILD_RANK' => 'Posición en Construcciones',
    'BUILD_POINTS' => 'Puntos de Construcciones',
    // 'BUILD_COUNT' => '',
    'DEFS_RANK' => 'Posición en Defensa',
    'DEFS_POINTS' => 'Puntos de Defensa',
    //'DEFS_COUNT' => '',
    'FLEET_RANK' => 'Posición en Flotas',
    'FLEET_POINTS' => 'Puntos de Flotas',
    //'FLEET_COUNT' => '',
    'RES_RANK' => 'Posición en Recursos libres',
    'RES_POINTS' => 'Puntos de Recursos libres',
    //'RES_COUNT' => '',
  ],

  'sys_date' => 'Fecha',

  'sys_blitz_global_button' => 'Servidor Blitz',
  'sys_blitz_page_disabled' => 'Esta página no está disponible en el modo Blitz',
  'sys_blitz_registration_disabled' => 'El registro en el servidor Blitz está desactivado',
  'sys_blitz_registration_no_users' => 'No hay jugadores registrados',
  'sys_blitz_registration_player_register' => 'Registrarse para jugar',
  'sys_blitz_registration_player_register_un' => 'Cancelar registro',
  'sys_blitz_registration_closed' => 'El registro está cerrado. Inténtalo más tarde',
  'sys_blitz_registration_player_generate' => 'Generar nombres y contraseñas',
  'sys_blitz_registration_player_import_generated' => 'Importar cadena generada',
  'sys_blitz_registration_player_name' => 'Tu nombre de usuario para Blitz:',
  'sys_blitz_registration_player_password' => 'Tu contraseña para Blitz:',
  'sys_blitz_registration_server_link' => 'Enlace al servidor Blitz',
  'sys_blitz_registration_player_blitz_name' => 'Nombre en Blitz',
  'sys_blitz_registration_price' => 'Costo de la solicitud',
  'sys_blitz_registration_mode_list' => [
    BLITZ_REGISTER_DISABLED => 'Registro desactivado',
    BLITZ_REGISTER_OPEN => 'Registro abierto',
    BLITZ_REGISTER_CLOSED => 'Registro cerrado',
    BLITZ_REGISTER_SHOW_LOGIN => 'Nombres y contraseñas visibles',
    BLITZ_REGISTER_DISCLOSURE_NAMES => 'Resultados finales',
  ],

  'survey' => 'Encuesta',
  'survey_questions' => 'Opciones',
  'survey_questions_hint' => '1 opción por línea',
  'survey_questions_hint_edit' => 'Editar la encuesta reiniciará los resultados',
  'survey_until' => 'Duración de la encuesta (1 día por defecto)',

  'survey_votes_total_none' => 'Nadie ha votado aún... ¡Sé el primero!',
  'survey_votes_total_voted' => 'Votos totales:',
  'survey_votes_total_voted_join' => '¡Vota o pierde!',
  'survey_votes_total_voted_has_answer' => 'Ya has votado. Votos totales:',

  'survey_lasts_until' => 'La encuesta estará activa hasta',

  'survey_select_one' => 'Elige una opción y haz clic en',
  'survey_confirm' => '¡Votar!',
  'survey_result_sent' => 'Tu voto ha sido registrado. Actualiza la página o usa el enlace <a class="link" href="announce.php">Noticias</a> para ver los resultados',
  'survey_complete' => 'Encuesta completada',

  'player_option_fleet_ship_sort' => [
    PLAYER_OPTION_SORT_DEFAULT => 'Estándar',
    PLAYER_OPTION_SORT_NAME => 'Por nombre',
    PLAYER_OPTION_SORT_ID => 'Por ID',
    PLAYER_OPTION_SORT_SPEED => 'Por velocidad',
    PLAYER_OPTION_SORT_COUNT => 'Por cantidad',
  ],

  'player_option_building_sort' => [
    PLAYER_OPTION_SORT_DEFAULT => 'Estándar',
    PLAYER_OPTION_SORT_NAME => 'Por nombre',
    PLAYER_OPTION_SORT_ID => 'Por ID',
    PLAYER_OPTION_SORT_CREATE_TIME_LENGTH => 'Por tiempo de construcción',
  ],

  'sys_sort' => 'Ordenar',
  'sys_sort_inverse' => 'En orden inverso',

  'sys_blitz_reward_log_message' => 'Servidor Blitz %1$d lugar "%2$s"',
  'sys_blitz_registration_view_stat' => 'Ver estadísticas del servidor Blitz',

  'sys_login_register_message_title' => "Tu nombre y contraseña para iniciar sesión",
  'sys_login_register_message_body' => "Tu nombre de usuario (login)\r\n%1\$s\r\n\r\nTu contraseña\r\n%2\$s\r\n\r\n¡Guarda o memoriza estos datos!",

  'auth_provider_list' => [
    ACCOUNT_PROVIDER_NONE => 'Tabla users',
    ACCOUNT_PROVIDER_LOCAL => 'Tabla account',
    ACCOUNT_PROVIDER_CENTRAL => 'Almacenamiento central',
  ],

  'bld_autoconvert' => 'Conversión automática al crear en el planeta %3$s la unidad {%1$d} "%4$s" en cantidad %2$d con costo "%5$s". Debug: $resource_got = "%6$s", $exchange = %7$s""',

  'news_show_rest' => 'Mostrar texto completo',

  'wiki_requrements' => 'Requisitos',
  'wiki_grants' => 'Proporciona',

  'que_slot_length' => 'Espacios',
  'que_slot_length_long' => 'Espacios en cola',

  'sys_buy_doing' => 'Estás comprando',
  'sys_planet_sector' => 'sector',
  'sys_planet_on' => 'en',

  'sys_purchase_confirm' => 'Confirmar compra',

  'sys_confirm_action_title' => 'Confirma tu acción',
  'sys_confirm_action' => '¿Realmente deseas hacer esto?',

  'sys_system_speed_original' => 'Velocidad original',
  'sys_system_speed_for_action' => 'Como parte de la promoción',

  'menu_info_best_battles' => 'Mejores batallas',

  'sys_cost' => 'Costo',
  'sys_price' => 'Precio',

  'sys_governor_none' => 'Gobernador no contratado',
  'sys_governor_hire' => 'Contratar Gobernador',
  'sys_governor_upgrade_or_change' => 'Mejorar o cambiar Gobernador',

  'tutorial_prev' => '<< Anterior',
  'tutorial_next' => 'Siguiente >>',
  'tutorial_finish' => 'Finalizar',
  'tutorial_window' => 'Abrir en ventana',
  'tutorial_window_off' => 'Volver a la página',

  'tutorial_error_load' => "Error al cargar el tutorial - ¡inténtalo de nuevo! Si persiste, informa a la Administración",
  'tutorial_error_next' => "Error: No existe la siguiente página del tutorial - informa a la Administración",
  'tutorial_error_prev' => "Error: No existe la página anterior del tutorial - informa a la Administración",

  'sys_click_here_to_continue' => 'Haz clic aquí para continuar',

  'sys_module_error_not_found' => '¡El módulo de recompensas "%1$s" no se encuentra o está desactivado!',

  'rank_page_title' => 'Rangos militares',
  'rank' => 'Rango',
  'ranks' => [
    0  => 'Cadete',
    1  => 'Recluta',
    2  => 'Soldado',
    3  => 'Cabo',
    4  => 'Cabo Mayor',
    5  => 'Sargento',
    6  => 'Sargento Mayor',
    7  => 'Guardiamarina',
    8  => 'Alférez',
    9  => 'Subteniente',
    10 => 'Teniente',
    11 => 'Capitán',
    12 => 'Mayor',
    13 => 'Teniente Coronel',
    14 => 'Coronel',
    15 => 'Contralmirante',
    16 => 'Vicealmirante',
    17 => 'Almirante',
    18 => 'Almirante de Flota',
    19 => 'Mariscal',
    20 => 'Generalísimo',
  ],

];