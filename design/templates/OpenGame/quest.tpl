<h2>{L_qst_quests}</h2>
<!-- IF IN_ADMIN && ! USER_ID -->
  <table width="519">
    <form action="admin/adm_quest.php" method="post" name="fForm">
      <input type="hidden" name="mode" value="{mode}">
      <input type="hidden" name="id" value="{QUEST_ID}">

      <tr><td class="c" colspan=2><!-- IF mode == 'edit' -->{L_qst_edit}<!-- ELSEIF mode == 'copy' -->{L_qst_copy}<!-- ELSE -->{L_qst_add}<!-- ENDIF --></td></tr>
      <tr>
        <td>{L_qst_name}</td>
        <td>
          <input class="fl" name="QUEST_NAME" size="50" maxlength="255" value="{QUEST_NAME}">
          <span class="fr"><!-- IF mode == 'edit' -->{L_qst_mode_edit}<!-- ELSEIF mode == 'copy' -->{L_qst_mode_copy}<!-- ELSE -->{L_qst_mode_add}<!-- ENDIF --></span>
        </td>
      </tr>

      <tr>
        <td valign="top">{L_qst_description}</td>
        <td><textarea name="QUEST_DESCRIPTION" cols=40 rows=5 width="80%">{QUEST_DESCRIPTION}</textarea></td>
      </tr>
      <tr class="c_l">
        <td>{L_qst_conditions}</td>
        <td class="c_l">
          <input name="QUEST_UNIT_AMOUNT" size="15" maxlength="15" value="{QUEST_UNIT_AMOUNT}"> x 
          <select name="QUEST_UNIT_ID">
            <!-- BEGIN allowed_unit -->
              <option value="{allowed_unit.ID}"<!-- IF allowed_unit.ID == QUEST_UNIT_ID --> selected<!-- ENDIF -->>{allowed_unit.NAME}</option>
            <!-- END allowed_unit -->
          </select>
        </td>
      </tr>
      <tr class="c_l">
        <td>{L_qst_rewards}</td>
        <td>
          <input name="QUEST_REWARDS_AMOUNT" size="15" maxlength="15" value="{QUEST_REWARDS_AMOUNT}"> x 
          <select name="QUEST_REWARDS_ID">
            <!-- BEGIN allowed_reward -->
              <option value="{allowed_reward.ID}"<!-- IF allowed_reward.ID == QUEST_REWARDS_ID --> selected<!-- ENDIF -->>{allowed_reward.NAME}</option>
            <!-- END allowed_reward -->
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="c_c">
          <input type="submit" name="confirm" value="<!-- IF mode == 'edit' -->{L_qst_edit}<!-- ELSEIF mode == 'copy' -->{L_qst_copy}<!-- ELSE -->{L_qst_add}<!-- ENDIF -->">
        </td>
      </tr>
    </form>
  </table>
  <br>
<!-- ENDIF -->

<!-- IF USER_ID -->
  <!-- DEFINE $COLSPAN = 4 -->
<!-- ELSE -->
  <!-- DEFINE $COLSPAN = 6 -->
<!-- ENDIF -->

<table width="800">
  <tr><td class="c" colspan="{$COLSPAN}">
    <div class="fl">{L_qst_quests}</div>
    <div class="fr">{L_qst_total}:&nbsp;{TOTAL}</div>
  </td></tr>
<!--
  <tr>
    <th class="c_c">{L_qst_name}</th>
    <th class="c_c" width="100%">{L_qst_description}</th>
    <th class="c_c">{L_qst_conditions}</th>
    <th class="c_c">{L_qst_rewards}</th>
    <!-- IF USER_ID -->
      <th class="c_c">{L_qst_status}</th>
    <!-- ENDIF -->
    <!-- IF IN_ADMIN && ! USER_ID -->
      <th class="c_c"><img src="design/images/icon_edit.png"></th>
      <th class="c_c"><img src="design/images/icon_copy.gif"></th>
      <th class="c_c"><img src="design/images/r1.png"></th>
    <!-- ENDIF -->
  </tr>
-->
  <!-- BEGIN quest -->
    <tr>
      <th class="c_c">{quest.QUEST_NAME}</th>
      <th class="c_c" width="240">{quest.QUEST_UNIT_AMOUNT}&nbsp;x&nbsp;{quest.QUEST_UNIT_NAME}</th>
      <th class="c_c" width="120">{quest.QUEST_REWARDS_AMOUNT}&nbsp;{quest.QUEST_REWARDS_NAME}</th>
      <!-- IF USER_ID -->
        <th class="c_c <!-- IF quest.QUEST_STATUS == 2 -->success<!-- ELSE -->notice<!-- ENDIF -->" width="80">{quest.QUEST_STATUS_NAME}</th>
      <!-- ENDIF -->
      <!-- IF IN_ADMIN && ! USER_ID -->
        <th class="c_c" width="22"><a href="admin/adm_quest.php?mode=edit&id={quest.QUEST_ID}"><img src="design/images/icon_edit.png"></a></th>
        <th class="c_c" width="22"><a href="admin/adm_quest.php?mode=copy&id={quest.QUEST_ID}"><img src="design/images/icon_copy.gif"></a></th>
        <th class="c_c" width="22"><a href="admin/adm_quest.php?mode=del&id={quest.QUEST_ID}"><img src="design/images/r1.png"></a></th>
      <!-- ENDIF -->
    </tr>
    <tr>
      <td align=justify colspan="{$COLSPAN}">{quest.QUEST_DESCRIPTION}</td>
    </tr>
  <!-- END quest -->
</table>
