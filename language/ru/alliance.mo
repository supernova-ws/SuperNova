<?php
if (!defined('INSIDE')) {
	die("Обнаружена попытка взлома!");
}

$lang['ali_dip_title'] = 'Дипломатия';
$lang['ali_dip_negotiate'] = 'Переговоры';

$lang['ali_adm_msg_subject']   = 'Рассылка Альянса';

$lang['ali_dip_offers_your']   = 'Ваши предложения';
$lang['ali_dip_offers_to_you'] = 'Предложения вам';
$lang['ali_dip_offer_none']    = 'Нет предложений';
$lang['ali_dip_offer']         = 'Предложение';
$lang['ali_dip_offers']        = 'Предложения';
$lang['ali_dip_offer_new']     = 'Вступить в переговоры';
$lang['ali_dip_offer_to_ally'] = 'Предложить Альянсу';
$lang['ali_dip_offer_make']    = 'Начать переговоры';
$lang['ali_dip_offer_answer']      = 'Альянс отклонил ваше предложение';
$lang['ali_dip_offer_deny_reason'] = 'Вы отклонили предложение'; // . Причина отказа:
$lang['ali_dip_offer_to']          = 'Альянсу';
$lang['ali_dip_offer_from']        = 'От Альянса';

$lang['ali_dip_offer_deny']          = 'Отклонить предложение';
$lang['ali_dip_offer_accept']        = 'Принять предложение';
$lang['ali_dip_offer_delete']        = 'Отозвать предложение';

$lang['ali_dip_err_no_ally']          = 'Нет такого Альянса';
$lang['ali_dip_err_wrong_offer']      = 'Нельзя сделать ТАКОЕ предложение';
$lang['ali_dip_err_offer_none']       = 'Нет такого предложения';
$lang['ali_dip_err_offer_same']       = 'Вы уже находитесь с этим Альянсом в отношениях %s';
$lang['ali_dip_err_offer_alien']      = 'Это предложение делали не вам!'; // hack
$lang['ali_dip_err_offer_accept_own'] = 'Нельзя принять за другого свое предложение!'; // hack
$lang['ali_dip_err_offer_empty']      = 'Не указано предложение'; // hack

$lang['ali_dip_relation_none']    = 'Нет отношений';
$lang['ali_dip_relation_change']  = 'Мы приняли предложение Альянса';
$lang['ali_dip_relation_change_to']  = 'изменить отношения на';
$lang['ali_dip_relation_accept']  = 'принял наше предложение изменить отношения на';

$lang['ali_dip_relations'] = array(
  ALLY_DIPLOMACY_NEUTRAL       => 'Нейтралитет',
  ALLY_DIPLOMACY_WAR           => 'Война',
  ALLY_DIPLOMACY_PEACE         => 'Мир',
  ALLY_DIPLOMACY_CONFEDERATION => 'Конфедерация',
  ALLY_DIPLOMACY_FEDERATION    => 'Федерация',
  ALLY_DIPLOMACY_UNION         => 'Объединение',
  ALLY_DIPLOMACY_MASTER        => 'Ведущий',
  ALLY_DIPLOMACY_SLAVE         => 'Ведомый'
);

$lang['ali_lessThen15min']    = '&lt; 15 м';

$lang['ali_confirm']          = 'Подтвердить';
$lang['ali_confirmation']     = 'Подтверждение';

$lang['ali_adm_disband']      = 'Распустить Альянс';
$lang['ali_adm_options']      = 'Настройки Альянса';
$lang['ali_adm_transfer']     = 'Передать Альянс игроку';
$lang['ali_adm_return']       = 'Вернуться к управлению Альянсом';
$lang['ali_adm_kick']         = 'Исключить игрока из Альянса';
$lang['ali_adm_kick_confirm'] = 'Вы уверенны что хотите исключить игрока из Альнса?';
$lang['ali_adm_requests']     = 'Заявки';
$lang['ali_adm_newLeader']    = 'ВЫБЕРИТЕ ИГРОКА';
$lang['ali_adm_lastRank']     = 'Нельзя удалить единственное звание!';

$lang['ali_adm_rights_title']       = 'Настройка прав доступа';
$lang['ali_adm_rights_rank_new']    = 'Новое звание';
$lang['ali_adm_rights_rank_delete'] = 'Удалить звание';
$lang['ali_adm_rights_rank_none']   = 'Нет званий';
$lang['ali_adm_rights_rank_name']   = 'Звание';
$lang['ali_adm_rights_mass_mail']   = 'Сообщение всему Альянсу';
$lang['ali_adm_rights_view_online'] = 'Просмотр on-line статуса участников';
$lang['ali_adm_rights_helper']      = 'Помощник главы (Для передачи необходим ранг основателя)';
$lang['ali_adm_rights_legend']      = 'Права Альянса';

$lang['ali_leaderRank']       = 'Глава Альянса';
$lang['ali_defaultRankName']  = 'Новичок';

$lang['ali_make_title']       = 'Создание Альянса';
$lang['ali_make_tag_length']  = '(от 3 до 8 символов)';
$lang['ali_make_name_length'] = '(до 35 символов)';
$lang['ali_make_confirm']     = 'Создать Альянс';

$lang['ali_req_cancel']       = 'Удалить заявку';
$lang['ali_req_candidate']    = 'Кандидат';
$lang['ali_req_characters']   = 'символов';
$lang['ali_req_date']         = 'Дата подачи заявки';
$lang['ali_req_deny_msg']     = 'Ваша заявка на вступление в Альянс [%s] была отклонена.<br>Причина отказа: "%s".<br>Вы можете удалить заявку и попробовать позже или вступить в другой Альянс.';
$lang['ali_req_deny_admin']   = '<font color=red>Запрос уже отклонен</font>. Однако, пока пользователь не удалил запрос на вступление, вы можете изменить свое решение';
$lang['ali_req_deny_reason']  = 'Ваш запрос на вступление отклонен';
$lang['ali_req_emptyList']    = 'Нет заявок для рассмотрения';
$lang['ali_req_inAlly']       = 'Вы уже являетесь участником Альянса.';
$lang['ali_req_make']         = 'Подать заявку';
$lang['ali_req_not_allowed']  = 'НЕТ ПРИЕМА';
$lang['ali_req_otherRequest'] = 'Вы уже подали заявку в другой Альянс.';
$lang['ali_req_template']     = 'Прошу принять меня в ваш Альянс';
$lang['ali_req_text']         = 'Текст заявки';
$lang['ali_req_title']        = 'Подача заявки в Альянс';
$lang['ali_req_waiting']      = 'Ваше заявка на вступление в Альянс [%s] будет расмотренно главой Альянса.<br>Вас оповестят о принятом решении.';
$lang['ali_req_check']        = 'Управление заявками';
$lang['ali_req_requestCount'] = 'Заявок';
$lang['ali_req_admin_title']  = 'Обзор заявок';
$lang['ali_req_accept']       = 'Принять заявку';
$lang['ali_req_deny']         = 'Отклонить заявку';

$lang['ali_search_title']       = 'Поиск Альянса';
$lang['ali_search_action']      = 'Искать';
$lang['ali_search_tip']         = 'Поиск можно производить по части имени или обозначения Альянса';
$lang['ali_search_result_none'] = 'Не найдено Альянсов, соответствующих вашему запросу.';
$lang['ali_search_result_tip']  = 'Кликните на имени или обозначении Альянса, что бы посмотреть информацию о нем.<br>Кликните "Вступить", что бы послать запрос о вступлении.';

$lang['ali_sys_name']         = 'Название';
$lang['ali_sys_tag']          = 'Обозначение';
$lang['ali_sys_members']      = 'Участники';
$lang['ali_sys_notFound']     = 'Такой Альянс не существует';
$lang['ali_sys_memberName']   = 'Имя';
$lang['ali_sys_points']       = 'Очки';
$lang['ali_sys_lastActive']   = 'Активность';
$lang['ali_sys_totalMembers'] = 'Всего';
$lang['ali_sys_clear']        = 'Сбросить';
$lang['ali_sys_main_page']    = 'Вернуться на главную страницу Альянса';
$lang['ali_sys_joined']       = 'Дата вступления';

$lang['ali_frm_write']        = 'Писать на форум';
$lang['ali_info_title']       = 'Информация об Альянсе';
$lang['ali_info_internal']    = 'Внутрення информация';
$lang['ali_info_leave']       = 'Покинуть Альянс';
$lang['ali_info_leave_success'] = 'Вы покинули Альянс [%s].<br />Теперь вы можете создать свой собственный Альянс или подать заявку на вступление в другой Альянс<br />';


$lang['Name']           = 'Название';
$lang['Tag']            = 'Обозначение';
$lang['Members']        = 'Участники';


$lang['Accept_cand']             = 'Принять';
$lang['alliance']             = 'Альянс';
$lang['alliances']            = 'Альянсы';
$lang['Alliance_information']       = 'Информация об Альянсе';
$lang['Alliance_logo']        = 'Логотип Альянса';
$lang['alliance_tag']         = 'Обозначение Альянса';
$lang['Allow_request']        = 'Принимать заявки';
$lang['allyance_name']        = 'Имя Альянса';
$lang['ally_admin']        = 'Управление Альянсом';
$lang['ally_been_maked']   = 'Альянс %s успешно создан';
$lang['ally_description']           = 'Описание Альянса';
$lang['ally_dissolve']     = 'Удаление Альянса';
$lang['Ally_info_1']             = 'Информация об Альянсе';
$lang['ally_maked']        = '%s создан';
$lang['Ally_nodescription']         = 'У Альянса нет описания';
$lang['ally_notexist']        = 'Альянс больше не существует';
$lang['Ally_not_exist']          = 'К сожалению нет никакой информации о этом Альянсе';
$lang['Ally_transfer']     = 'Передать Альянс';
$lang['All_players']          = 'Все игроки';
$lang['always_exist']         = '%s уже существует';
$lang['Aplication_acepted']      = 'Вы приняты';
$lang['Aplication_hello']           = 'Приветствую<br>Альянс :';
$lang['Aplication_rejected']        = 'Ваша заявка на вступление в альянс была отклонена.<br>Причина:<br>';
$lang['apply_cantbeadded']       = 'Запрос не удался, попробуйте ещё раз!';
$lang['apply_registered']     = 'Ваша заявка была отправлена.<br><br><a href=alliance.php>Назад</a>';
$lang['Back']           = 'Назад';
$lang['Canceld_req_text']  = 'Вы отменили заявку на вступление в [%s]';
$lang['Change']            = 'Изменить';
$lang['ch_allyname']       = 'Изменить имя Альянса';
$lang['ch_allytag']        = 'Изменить обозначение Альянса';
$lang['Circular_message']     = 'Сообщение Альянсу';
$lang['Circular_sended']      = 'Сообщение успешно отправлено';
$lang['Clear']             = 'Очистить';
$lang['Click_writerequest']      = 'Нажмите здесь чтобы написать заявку';
$lang['Continue']          = 'продолжить';
$lang['Delete_apply']         = 'Отклонить заявку';
$lang['Denied_access']        = 'Доступ запрещён!';
$lang['Destiny']        = 'Получатель';
$lang['Exit_of_this_alliance']      = 'Выйти из Альянса';
$lang['External_text']        = 'Внешний текст';
$lang['Founder']        = 'Создатель';
$lang['Founder_name']         = 'Звание основателя';
$lang['Function']          = 'Функция';
$lang['Go_out_welldone']      = 'Вы успешно покинули Альянс';
$lang['have_not_name']        = 'Введите имя Альянса';
$lang['have_not_tag']         = 'Введите обозначение Альянса';
$lang['Help']           = 'Помощь';
$lang['Inactive']          = 'Неактивный';
$lang['Inner_section']        = 'Внутренний текст';
$lang['Internal_text']        = 'Внутренний текст';
$lang['knowed_allys']     = 'Существующие Альянсы';
$lang['laws_config']          = 'Настройка прав доступа';
$lang['Main_Page']         = 'Домашняя страница';
$lang['make_alliance']        = 'Создание Альянса';
$lang['make_alliance_owner']  = 'Создать Альянс';
$lang['max']            = 'макс.';
$lang['member']            = 'Участник';
$lang['memberlist_view']      = 'Просмотр списка участников';
$lang['members']        = 'Участники';
$lang['members_admin']     = 'Управление участниками';
$lang['Members_list']         = 'Список участников';
$lang['members_who_recived_message'] = 'Следующие члены Альянса получили сообщение:';
$lang['Message']        = 'Сообщение';
$lang['Motive_optional']      = 'Причина (опционально)';
$lang['New_name']          = 'Новое обозначение';
$lang['New_tag']           = 'Новый Тэг';
$lang['not_allow_request']       = 'Отклонять заявки';
$lang['Novate']            = 'Новичок';
$lang['Number']            = '№';
$lang['Off']            = 'Off-line';
$lang['Ok']             = 'Ок';
$lang['On']             = 'On-line';
$lang['Online']            = 'Статус';
$lang['Options']        = 'Опции';
$lang['Position']          = 'Статус';
$lang['Public_text_of_alliance']    = 'Внешний текст';
$lang['Range']             = 'Звание';
$lang['Reject_cand']             = 'Отклонить';
$lang['Reload']            = 'Пример';
$lang['Repel']             = 'Repel';
$lang['requests_view']        = 'Просмотр заявок';
$lang['Request_answer']       = 'Запрос отклонен';
$lang['Request_date']      = 'Дата подачи заявки';
$lang['Request_text']         = 'Текст заявки';
$lang['s']           = '[N/A]';
$lang['Search']               = 'Поиск';
$lang['searchd_ally_avail']   = 'Найдены Альянсы:';
$lang['search_alliance']      = 'Поиск';
$lang['Send']           = 'Отправить';
$lang['Send_Apply']        = 'Принять заявку';
$lang['Send_circular_mail']      = 'Послать сообщение всему Альянсу';
$lang['Set_range']            = 'Изменение ранга';
$lang['Show_of_request_text']       = 'Текст заявки';
$lang['Texts']             = 'Редактирование текста';
$lang['Text_mail']         = 'Отправка сообщения всему Альянсу';
$lang['top10alliance']        = 'Топ 10 Альянсов';
$lang['transfer']             = 'Передача';
$lang['transfer_ally']           = 'Передача альянса';
$lang['transfer_to']          = 'Передать альянс игроку:';
$lang['Want_go_out']          = 'Вы действительно хотите покинуть Альянс ?';
$lang['write_apply']          = 'Подать заявку';
$lang['your_alliance']        = 'Ваш Альянс';
$lang['your_apply']        = 'Ваша заявка';

?>
