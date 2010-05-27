<?php

        if (isset($POST_newleader)) {
            doquery("UPDATE {{table}} SET `ally_rank_id`='0' WHERE `id`={$user['id']} ", 'users');
            doquery("UPDATE {{table}} SET `ally_owner`='" . mysql_escape_string(strip_tags($POST_newleader)) . "' WHERE `id`={$user['ally_id']} ", 'alliance');
            doquery("UPDATE {{table}} SET `ally_rank_id`='0' WHERE `id`='" . mysql_escape_string(strip_tags($POST_newleader)) . "' ", 'users');
            header('Location: alliance.php');
            exit;
        }
        // проверить лицензию
        if ($ally['ally_owner'] != $user['id']) {
            message($lang['Denied_access'], $lang['Members_list']);
        } else {
        // У нас есть массив из рядов
        $rangos_alianza = unserialize($ally['ally_ranks']);
        // Armamar опять список пользователей альянс
        $listuser = doquery("SELECT * FROM {{table}} WHERE `ally_id` ='{$user['ally_id']}'", 'users');
        // подсчет количества пользователей.
        $i = 0;
        // Как и в обычай. строка шаблона
        $template1 = gettemplate('alliance_admin_transfer_row');
        $page_list1 = '';
        // Мы делаем пользователю пользователь при проверке их ранга
        while ($u = mysql_fetch_array($listuser)) {
            $i=$i+1;
            $u['i'] = $i;
            if ($ally['ally_owner'] == $u['id']) {
            // Если он является ведущим союзником смысла, который autotransfiera Линия
            } elseif ($u['ally_rank_id'] == 0 ) {
            // Если вы новичок никогда не позволят правую руку
            } else {
                if ($rangos_alianza[$u['ally_rank_id']-1]['rechtehand'] == 1){
                // Вот, вооруженных вариант для пользователей, с разрешения его правой рукой
                $righthand['righthand'] .= "\n<option value=\"" . $u['id'] . "\"";
                $righthand['righthand'] .= ">";
                $righthand['righthand'] .= "".$u['username'];
                $righthand['righthand'] .= " [".$rangos_alianza[$u['ally_rank_id']-1]['name'];
                $righthand['righthand'] .= "]  </option>";
                }

            }
            // ПМА отправки данных, необходимых язык
            $righthand['transfer_to'] = $lang['transfer_to'];
            $righthand['transfer'] = $lang['transfer'];
            $righthand["dpath"] = $dpath;

        }
        // Наконец объединяет все в правильных шаблонов
        $page_list1 .= parsetemplate($template1, $righthand);
        $parse1 = $lang;
        $parse1['s'] = $s;
        $parse1['list'] = $page_list1;

        $page .= parsetemplate(gettemplate('alliance_admin_transfer'), $parse1);

        display($page, $lang['Members_list']);
    }
    }
    { // Конец передачи в режиме альянса

    // Default *falta revisar...*
    if ($ally['ally_owner'] != $user['id']) {
      $ally_ranks = unserialize($ally['ally_ranks']);
    }
    // Imagen de la alianza
    if ($ally['ally_ranks'] != '') {
      $ally['ally_ranks'] = "<tr><td colspan=2><img src=\"{$ally['ally_image']}\"></td></tr>";
    }
    // temporalmente...
    if ($ally['ally_owner'] == $user['id']) {
      $range = ($ally['ally_owner_range'] != '')?$lang['Founder']:$ally['ally_owner_range'];
    } elseif ($user['ally_rank_id'] != 0 && isset($ally_ranks[$user['ally_rank_id']-1]['name'])) {
      $range = $ally_ranks[$user['ally_rank_id']-1]['name'];
    } else {
      $range = $lang['member'];
    }
    // Link de la lista de miembros
    if ($ally['ally_owner'] == $user['id'] || $user_can_watch_memberlist) {
      $lang['members_list'] = " (<a href=\"?mode=memberslist\">{$lang['Members_list']}</a>)";
    } else {
      $lang['members_list'] = '';
    }
    // El link de adminstrar la allianza
    if ($ally['ally_owner'] == $user['id'] || $user_admin) {
      $lang['alliance_admin'] = " (<a href=\"?mode=admin&edit=ally\">{$lang['ally_admin']}</a>)";
    } elseif ($user_can_kick && $ally['ally_owner'] != $user['id'] && !$user_admin ){
      $lang['alliance_admin'] = " (<a href=\"?mode=admin&edit=members\">{$lang['members_admin']}</a>)";
    }else{
      $lang['alliance_admin'] = '';
    }
    // El link de enviar correo circular
    if ($ally['ally_owner'] == $user['id'] || $user_can_send_mails) {
      $lang['send_circular_mail'] = "<tr><th>{$lang['Circular_message']}</th><th><a href=\"?mode=circular\">{$lang['Send_circular_mail']}</a></th></tr>";
    } else {
      $lang['send_circular_mail'] = '';
    }
    // El link para ver las solicitudes
    $lang['requests'] = '';
    $request = doquery("SELECT id FROM {{table}} WHERE `ally_request` ='{$ally['id']}'", 'users');
    $request_count = mysql_num_rows($request);
    if ($request_count != 0) {
      if ($ally['ally_owner'] == $user['id'] || $user_can_see_applications || $user_admin_applications)
        $lang['requests'] = "<tr><th>{$lang['Requests']}</th><th><a href=\"alliance.php?mode=admin&edit=requests\">{$request_count} {$lang['XRequests']}</a></th></tr>";
    }
    if ($ally['ally_owner'] != $user['id']) {
      $lang['ally_owner'] .= MessageForm($lang['Exit_of_this_alliance'], "", "?mode=exit", $lang['Continue']);
    } else {
      $lang['ally_owner'] .= '';
    }
    // La imagen de logotipo
    $lang['ally_image'] = ($ally['ally_image'] != '')?
    "<tr><th colspan=2><img src=\"{$ally['ally_image']}\"></td></tr>":'';
    // $ally_image =
    $lang['range'] = $range;
    // codigo raro
    $patterns[] = "#\[fc\]([a-z0-9\#]+)\[/fc\](.*?)\[/f\]#Ssi";
    $replacements[] = '<font color="\1">\2</font>';
    $patterns[] = '#\[img\](.*?)\[/img\]#Smi';
    $replacements[] = '<img src="\1" alt="\1" style="border:0px;" />';
    $patterns[] = "#\[fc\]([a-z0-9\#\ \[\]]+)\[/fc\]#Ssi";
    $replacements[] = '<font color="\1">';
    $patterns[] = "#\[/f\]#Ssi";
    $replacements[] = '</font>';
    $ally['ally_description'] = preg_replace($patterns, $replacements, $ally['ally_description']);
    $lang['ally_description'] = nl2br($ally['ally_description']);

    $ally['ally_text'] = preg_replace($patterns, $replacements, $ally['ally_text']);
    $lang['ally_text'] = nl2br($ally['ally_text']);

    $lang['ally_web'] = $ally['ally_web'];
    $lang['ally_tag'] = $ally['ally_tag'];
    $lang['ally_members'] = $ally['ally_members'];
    $lang['ally_name'] = $ally['ally_name'];

    if ($game_config['OverviewClickBanner'] != '') {
      $parse['ClickBanner'] = stripslashes( $game_config['OverviewClickBanner'] );
    }

    display(parsetemplate(gettemplate('alliance_frontpage'), $lang), $lang['your_alliance']);

?>