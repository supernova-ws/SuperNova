<?php

/**
 * notes.php
 *
 * 1.0s - Security checks by Gorlum for http://supernova.ws
 * @version 1.0
 * @copyright 2008 by ??????? for XNova
 */

include('common.' . substr(strrchr(__FILE__, '.'), 1));
// TODO: Rewrote notes
$GET_a = intval($_GET['a']);
$n = intval($_GET['n']);
$POST_s = intval($_POST["s"]);
$priority = intval($_POST["u"]);
$title = sys_get_param_str('title', $lang['NoTitle']);
$text = sys_get_param_str('text', $lang['NoText']);
$id = intval($_POST["n"]);

$lang['Please_Wait'] = "Patientez...";

//lenguaje
includeLang('notes');

$lang['PHP_SELF'] = 'notes.'. PHP_EX;

if($POST_s == 1 || $POST_s == 2){//Edicion y agregar notas

  $time = time();

  if($POST_s ==1){
    doquery("INSERT INTO {{notes}} SET owner={$user['id']}, time=$time, priority=$priority, title='$title', text='$text'");
    message($lang['NoteAdded'], $lang['Please_Wait'],'notes.'. PHP_EX,"3");
  }elseif($POST_s == 2){
    /*
      pequeño query para averiguar si la nota que se edita es del propio jugador
    */
    $note_query = doquery("SELECT * FROM {{notes}} WHERE id=$id AND owner=".$user["id"]);

    if(!$note_query){ error($lang['notpossiblethisway'],$lang['Notes']); }

    doquery("UPDATE {{notes}} SET time=$time, priority=$priority, title='$title', text='$text' WHERE id=$id");
    message($lang['NoteUpdated'], $lang['Please_Wait'], 'notes.'. PHP_EX, "3");
  }

}
elseif($_POST){//Borrar

  foreach($_POST as $a => $b){
    /*
      Los checkbox marcados tienen la palabra delmes seguido del id.
      Y cada array contiene el valor "y" para compro
    */
    if(preg_match("/delmes/i",$a) && $b == "y"){

      $id = str_replace("delmes","",$a);
      $note_query = doquery("SELECT * FROM {{notes}} WHERE id=$id AND owner={$user['id']}");
      //comprobamos,
      if($note_query){
        $deleted++;
        doquery("DELETE FROM {{notes}} WHERE `id`=$id;");// y borramos
      }
    }
  }
  if($deleted){
    $mes = ($deleted == 1) ? $lang['NoteDeleted'] : $lang['NoteDeleteds'];
    message($mes,$lang['Please_Wait'],'notes.'.PHP_EX,"3");
  }else{header("Location: notes." . PHP_EX);}

}else{//sin post...
  if($GET_a == 1){//crear una nueva nota.
    /*
      Formulario para crear una nueva nota.
    */

    $parse = $lang;

    $parse['c_Options'] = "<option value=2 selected=selected>{$lang['Important']}</option>
        <option value=1>{$lang['Normal']}</option>
        <option value=0>{$lang['Unimportant']}</option>";

    $parse['cntChars'] = '0';
    $parse['TITLE'] = $lang['Createnote'];
    $parse['text'] = '';
    $parse['title'] = '';
    $parse['inputs'] = '<input type=hidden name=s value=1>';

    $page .= parsetemplate(gettemplate('notes_form'), $parse);

    display($page,$lang['Notes'],false);

  }
  elseif($GET_a == 2){//editar
    /*
      Formulario donde se puestra la nota y se puede editar.
    */
    $note = doquery("SELECT * FROM {{notes}} WHERE owner={$user['id']} AND id=$n",'',true);

    if(!$note){ message($lang['notpossiblethisway'],$lang['Error']); }

    $cntChars = strlen($note['text']);

    $SELECTED[$note['priority']] = ' selected="selected"';

    $parse = array_merge($note,$lang);

    $parse['c_Options'] = "<option value=2{$SELECTED[2]}>{$lang['Important']}</option>
        <option value=1{$SELECTED[1]}>{$lang['Normal']}</option>
        <option value=0{$SELECTED[0]}>{$lang['Unimportant']}</option>";

    $parse['cntChars'] = $cntChars;
    $parse['TITLE'] = $lang['Editnote'];
    $parse['inputs'] = '<input type=hidden name=s value=2><input type=hidden name=n value='.$note['id'].'>';

    $page .= parsetemplate(gettemplate('notes_form'), $parse);

    display($page,$lang['Notes'],false);

  }
  else{//default

    $notes_query = doquery("SELECT * FROM {{notes}} WHERE owner={$user['id']} ORDER BY time DESC");
    //Loop para crear la lista de notas que el jugador tiene
    $count = 0;
    $parse=$lang;
    while($note = mysql_fetch_assoc($notes_query)){
      $count++;
      //Colorea el titulo dependiendo de la prioridad
      if($note["priority"] == 0){ $parse['NOTE_COLOR'] = "lime";}//Importante
      elseif($note["priority"] == 1){ $parse['NOTE_COLOR'] = "yellow";}//Normal
      elseif($note["priority"] == 2){ $parse['NOTE_COLOR'] = "red";}//Sin importancia

      //fragmento de template
      $parse['NOTE_ID'] = $note['id'];
      $parse['NOTE_TIME'] = date(FMT_DATE_TIME,$note["time"]);
      $parse['NOTE_TITLE'] = $note['title'];
      $parse['NOTE_TEXT'] = strlen($note['text']);

      $list .= parsetemplate(gettemplate('notes_body_entry'), $parse);

    }

    if($count == 0){
      $list .= "<tr><th colspan=4>{$lang['ThereIsNoNote']}</th>\n";
    }

    $parse = $lang;
    $parse['BODY_LIST'] = $list;
    //fragmento de template

  display(parsetemplate(gettemplate('notes_body'), $parse), $lang['Notes']);
  }
}
?>