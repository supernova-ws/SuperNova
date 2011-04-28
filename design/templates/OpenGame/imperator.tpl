<script type="text/javascript"> 
  jQuery.noConflict(); 
</script> 

<br>
<table width=519>
  <!-- News Frame -->
  <!-- IF GAME_NEWS_OVERVIEW -->
    <tr><td colspan=4 class="c">{L_news_title}</td></tr>
    <!-- BEGIN news -->
      <tr>
        <th width="90">
          <!-- IF news.IS_NEW -->
            <font color=red>{L_news_new}</font><br>
          <!-- ENDIF -->
          <font color=cyan>{news.TIME}</font>
        </th>
        <th valign=top colspan=3>
          <div align=justify>{news.ANNOUNCE}<!-- IF news.DETAIL_URL --> <a href="{news.DETAIL_URL}"><u><font color="green">{L_news_more}</font></u></a><!-- ENDIF --></div>
        </th>
      </tr>
    <!-- BEGINELSE news -->
      <tr><th colspan="4" class="c">{L_news_none}</th></tr>
    <!-- END news -->
  <!-- ENDIF -->

  <!-- IF NEW_MESSAGES -->
    <tr><th colspan=4><a href=messages.php>
      {L_ov_you_have}
      <!-- IF NEW_MESSAGES == 1 -->
        {L_ov_new_message}
      <!-- ELSE -->
        {NEW_MESSAGES} {L_ov_new_messages}
      <!-- ENDIF -->
    </a></th></tr>
  <!-- ENDIF -->

  <tr>
    <td colspan=4 class="c">
      <span class="fl">{L_imp_imperator} {user_username} {L_sys_from} {REGISTRATION_DATE}</span>
      <span class="fr"><strong>{L_ov_rank}: <a href="stat.php?start={user_rank}"><span class="ov_user_rank">{user_rank} ({RANK_DIFF})</span> из <span class="ov_user_total">{USERS_TOTAL}</span></strong></a></strong></span>
    </td>
  </tr>
  <tr>
    <th rowspan=2>{L_ov_raids}</th>
    <th colspan="1" align="center">{L_sys_level}: {raid_lvl}</th>
    <th colspan="2" align="center">{L_ov_experience}: {raid_xp}/{raid_lvl_up}</th>
  </tr>
  <tr>
    <th>{L_NumberOfRaids}: {raids}</th>
    <th>{L_RaidsWin}: {raidswin}</th>
    <th>{L_RaidsLoose}: {raidsloose}</th>
  </tr>
  <tr>
    <th>{L_Economica}</th>
    <th colspan="1" align="center">{L_sys_level}: {builder_lvl}</th>
    <th colspan="2" align="center">{L_ov_experience}: {builder_xp}/{builder_lvl_up}</th>
  </tr>
  <tr align="center" class="c">
    <th rowspan=2>{L_ov_points}</th>
    <th>{L_user_points_1}: {user_points}</th>
    <th>{L_user_points_3}: {user_defs_points}</th>
    <th>{L_user_points_4}: {user_fleet}</th>
  </tr>
  <tr>
    <th>{L_user_points_2}: {player_points_tech}</th>
    <th colspan="2">{L_user_points_all}: {total_points}</th>
  </tr>
</table>

<!-- IF BANNER_URL -->
  <table width="519" align="center">
    <tr><td class="c">{L_aff_banner}</td></tr>
    <tr>
      <th>
        <img src="{BANNER_URL}"><br><br>
        {L_aff_banner_bb}<br>
        <input type="text" value="[img]{BANNER_URL}[/img]" size="55">
      </th>
    </tr>
  </table>
<!-- ENDIF -->

<!-- IF USERBAR_URL -->
  <table width="519" align="center">
    <tr><td class="c">{L_aff_userbar}</td></tr>
    <tr>
      <th>
        <img src="{USERBAR_URL}"><br><br>
        {L_aff_userbar_bb}<br>
        <input type="text" value="[img]{USERBAR_URL}[/img]" size="55">
      </th>
    </tr>
  </table>
<!-- ENDIF -->
