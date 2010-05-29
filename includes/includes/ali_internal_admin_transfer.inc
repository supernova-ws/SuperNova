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
                if ($user_admin){
                // Вот, вооруженных вариант для пользователей, с разрешения его правой рукой
                $righthand['righthand'] .= "\n<option value=\"" . $u['id'] . "\"";
                $righthand['righthand'] .= ">";
                $righthand['righthand'] .= "".$u['username'];
                $righthand['righthand'] .= " [".$ranks[$u['ally_rank_id']]['name'];
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
?>