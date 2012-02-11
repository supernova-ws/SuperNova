<h2>{adm_er_ttle}</h2>

<table>
  <tr>
    <th class="c_l" width="60px">{adm_er_idmsg}</th>
    <th class="c_l" width="120px">{adm_er_time}</th>
    <th class="c_l" width="250px">{adm_er_type}</th>
    <th class="c_l">{adm_er_play}</th>
    <th class="c_l">{adm_er_page}</th>
    <th class="c_c" width="16px"><img src="design/images/r1.png"></th>
  </tr>

  <!-- BEGIN error -->
    <tr>
      <td class="c_l"><a href="admin/errors.php?detail={error.LOG_ID}"><u>{error.LOG_ID}</u></a></td>
      <td class="c_l">{error.LOG_TIMESTAMP}</td>
      <td class="c_l">[{error.LOG_CODE}] {error.LOG_TITLE}</td>
      <td class="c_l">{L_adm_er_idmsg} {error.LOG_SENDER}<!-- IF error.LOG_USERNAME -->&nbsp;[{error.LOG_USERNAME}]<!-- ENDIF --></td>
      <td class="c_l">{error.LOG_PAGE}</td>
      <td class="c_c"><a href="admin/errors.php?delete={error.LOG_ID}"><img src="design/images/r1.png"></a></td>
    </tr>

    <tr><td colspan="6" class="c_l error_message">{error.LOG_TEXT}</td></tr>
  <!-- END error -->

  <tr>
    <th class="c_l" colspan=6>
      {adm_er_nbs}&nbsp;{errors_num}/{LOG_SIZE}
      <span class="fr">[<a href="admin/errors.php?deleteall=yes">{adm_er_clear}</a>]</span>
    </th>
  </tr>
</table>
