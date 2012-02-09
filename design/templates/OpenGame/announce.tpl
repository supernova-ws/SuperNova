<!-- IF AUTHLEVEL >= 3 -->
  <br>
  <table width="519">
    <form action="announce.php" method="post" name="fForm">
      <tr><td class="c" colspan=2><!-- IF MODE == 'edit' -->{L_news_edit}<!-- ELSEIF MODE == 'copy' -->{L_news_copy}<!-- ELSE -->{L_news_add}<!-- ENDIF --></td></tr>
      <tr>
        <td>{L_news_date}</td>
        <td>
          <input class="fl" name="dtDateTime" size="19" maxlength="19" value="{tsTimeStamp}">
          <span class="fr"><!-- IF MODE == 'edit' -->{L_news_mode_edit}<!-- ELSEIF MODE == 'copy' -->{L_news_mode_copy}<!-- ELSE -->{L_news_new}<!-- ENDIF --></span>
          <input type="hidden" name="id" value="{ID}">
          <input type="hidden" name="mode" value="{MODE}">
        </td>
      </tr>

      <tr>
        <td valign="top">{L_news_announce}</td>
        <td><textarea name="text" cols=40 rows=5 width="80%">{strAnnounce}</textarea></td>
      </tr>
      <tr>
        <td>{L_news_detail_url}</td>
        <td><input name="detail_url" size=62 maxlength="250" value="{DETAIL_URL}"></td>
      </tr>
      <tr>
        <td colspan="2" align="center">
          <span class="fl"><input type="checkbox" id="news_mass_mail" name="news_mass_mail" value="1" <!-- IF MODE != 'edit' -->checked<!-- ENDIF -->>&nbsp;<label for="news_mass_mail">{L_news_mass_mail}</label></span>
          <input class="fr" type=submit value="<!-- IF MODE == 'edit' -->{L_news_edit}<!-- ELSEIF MODE == 'copy' -->{L_news_copy}<!-- ELSE -->{L_news_add}<!-- ENDIF -->">
        </td>
      </tr>
    </form>
  </table>
<!-- ENDIF -->
<br>
<table width="519">
  <tr><td class="c" colspan="<!-- IF AUTHLEVEL >= 3 -->5<!-- ELSE -->2<!-- ENDIF -->"><div class="fl">{L_news_title}</div><div class="fr">{L_news_total} {NEWS_COUNT}</div></td></tr>
  <tr>
    <th>{L_news_date}</th>
    <th width="100%">{L_news_announce}</th>
    <!-- IF AUTHLEVEL >= 3 -->
      <th><img src="design/images/icon_edit.png"></th>
      <th><img src="design/images/icon_copy.gif"></th>
      <th><img src="design/images/r1.png"></th>
    <!-- ENDIF -->
  </tr>
  <!-- INCLUDE news_list.tpl -->
</table>
