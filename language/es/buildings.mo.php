<?php

/*
#############################################################################
#  Filename: buildings.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Juego de estrategia espacial masivo multijugador en línea
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [Spanish]
* @version 46a158
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = array(
  'built' => 'Construido',
  'Fleet' => 'Flota',
  'fleet' => 'flota',
  'Defense' => 'Defensa',
  'defense' => 'defensa',
  'Research' => 'Investigación',
  'level' => 'Nivel',
  'dispo' => 'Disponible',
  'load_det' => 'Haz clic en la imagen para ver el modelo 3D',
  'off_det' => 'Haz clic nuevamente para ocultar el modelo 3D',
  'allowed_aya' => 'Disponible',
  'allowed_ye' => 'Disponibles',
  'allowed_yi' => 'Disponible',
  'mech_info' => 'Especificaciones técnicas',
  'fst_bld_load' => 'Procesando pedido.<br>Por favor espera...',
  'fst_bld' => 'Pedido rápido:',
  'price' => 'Costo',
  'builds' => 'Construcciones',
  'destroy_price' => 'Costo de demolición',
  'no_fields' => 'No hay campos disponibles en el planeta',
  'can_build' => 'Se puede construir: ',
  'Requirements' => 'Requisitos: ',
  'Requires' => 'Recursos necesarios ',
  'Rest_ress' => 'Recursos restantes ',
  'Rest_ress_fleet' => 'Incluyendo flotas en camino',
  'Rechercher' => 'Investigar',
  'ConstructionTime' => 'Tiempo de construcción ',
  'DestructionTime' => 'Tiempo de demolición ',
  'ResearchTime' => 'Tiempo de investigación ',
  'Construire' => 'Construir',
  'BuildFirstLevel' => 'Construir',
  'BuildNextLevel' => 'Construir siguiente nivel ',
  'completed' => 'Completado',
  'in_working' => 'Ocupado',
  'work_todo' => 'Ocupado',
  'total_left_time' => 'Tiempo restante',
  'only_one' => 'Solo puedes construir un escudo.',
  'b_no_silo_space' => 'El silo de misiles está lleno.',
  'que_full' => '¡La cola de construcción está llena!',
  'Build_lab' => 'Error de construcción',
  'NoMoreSpace' => '¡Planeta lleno!',
  'InBuildQueue' => 'En cola de construcción',
  'bld_usedcells' => 'Campos ocupados',
  'bld_theyare' => 'Quedan',
  'bld_cellfree' => 'campos libres',
  'DelFromQueue' => 'cancelar',
  'DelFirstQueue' => 'Pausar',
  'cancel' => 'Cancelar',
  'continue' => 'Continuar',
  'ready' => 'Esperar',
  'destroy' => 'Demoler',
  'on' => 'en',
  'attention' => '¡Atención! Se detectó un intento de hackeo. ¡La acción ha sido registrada!',
  'no_laboratory' => '¡Laboratorio de investigación no construido!',
  'need_hangar' => '¡Astillero no construido!',
  'labo_on_update' => '¡Laboratorio en actualización!',
  'fleet_on_update' => '¡Astillero en modernización!',
  'Total_techs' => 'Total de investigaciones',
  'eco_bld_page_hint' => '<ul><li>Pasa el cursor sobre la imagen para ver información de la unidad</li>
  <li>Haz clic para seleccionar la unidad. Otro clic en la misma unidad la deseleccionará</li>
  <li>Haz clic en el icono azul "i" para ver características detalladas</li>
  <li>Construye haciendo clic en el "+" (esquina superior derecha) o en el enlace "Construir"</li>
  <li>Demuele haciendo clic en el "-" (esquina superior izquierda) o en el enlace correspondiente</li></ul>',
  'eco_price' => 'Precio',
  'eco_left' => 'Restante',
  'eco_bld_resources_not_enough' => 'No hay suficientes recursos para construir las unidades pedidas',

  'eco_bld_msg_err_research_in_progress' => 'Los científicos del Imperio ya están investigando',
  'eco_bld_msg_err_not_research' => 'Solo se pueden investigar tecnologías en los laboratorios',
  'eco_bld_msg_err_requirements_not_meet' => 'No se cumplen los requisitos para la investigación',
  'eco_bld_msg_err_laboratory_upgrading' => 'Los laboratorios están siendo modificados y no pueden investigar.<br/><br/>Durante la construcción o modificación de Laboratorios o Nano-laboratorios en cualquier planeta del Imperio (incluso si están en cola), la investigación no está disponible<br/><br/>Para iniciar una investigación, elimina todos los Laboratorios y Nano-laboratorios de todas las colas de construcción en todos los planetas',

  'eco_bld_unit_info_extra_show' => 'Mostrar información adicional',
  'eco_bld_unit_info_extra_hide' => 'Ocultar información adicional',
  'eco_bld_unit_info_extra_none' => 'Sin información adicional',

  'eco_bld_autoconvert' => 'Autoconversión',
  'eco_bld_autoconvert_explain' => 'Los recursos faltantes para construcción/investigación se convertirán automáticamente de los recursos disponibles (metal, cristal, deuterio), y luego se añadirán a la cola.\r\n\r\n',
  'eco_bld_autoconvert_dark_matter_none' => 'Faltan {0} de Materia Oscura para construcción con autoconversión.',
  'eco_bld_autoconvert_confirm' => 'Esta operación costará {0} de Materia Oscura.\r\n\r\n¿Continuar?',

  'eco_que_clear_dialog_title' => 'Confirmar limpieza de cola',
  'eco_que_clear_dialog_text' => '¡Esta acción borrará toda la cola!<br /><br />Todas las construcciones/investigaciones no completadas se cancelarán y el tiempo invertido se perderá.<br />Los recursos serán devueltos al planeta.<br /><br />¿Estás seguro de continuar?',

  'eco_que_artifact_dialog_title' => 'Usar {0}',
  'eco_que_artifact_dialog_text' => "Se usará el Artefacto \"{0}\" para acelerar la construcción/investigación actual.<br /><br />Si queda más de una hora: el tiempo se reducirá a la mitad<br />Si queda menos de una hora: se completará inmediatamente<br /><br />No se puede usar si queda menos de un minuto",

  'eco_bld_research_page_name' => 'Investigación de tecnologías',
  'eco_bld_research_page_novapedia' => 'Lista de tecnologías en Novapedia',
);