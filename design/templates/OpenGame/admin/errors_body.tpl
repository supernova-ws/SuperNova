<h2>{adm_er_ttle}</h2>
<table>
<tr>
<td class="c" colspan="6">{adm_er_list} [<a href="?deleteall=yes">{adm_er_clear}</a>]</td>
</tr>
<tr>
<th>{adm_er_idmsg}</th>
<th>{adm_er_play}</th>
<th>{adm_er_type}</th>
<th width="120px">{adm_er_time}</th>
<th width="16px">{adm_er_page}</th>
<th><img src="../design/images/r1.png"></th>
</tr>
{errors_list}
<!-- BEGIN error -->
    <tr>
      <th class=n><a href=errors.php?detail={error.LOG_ID}><u>{error.LOG_ID}</u></a></th>
      <th class=n>{error.LOG_USERNAME}</th>
      <th class=n>{error.LOG_TITLE}</th>
      <th class=n>{error.LOG_TIMESTAMP}</th>
      <th class=b>{error.LOG_PAGE}</th>
      <th class=n><a href="?delete={error.LOG_ID}"><img src="../design/images/r1.png"></a></th>
    </tr>
    <tr><td colspan="6" class=b>{error.LOG_TEXT}</td></tr>
<!-- END error -->
<tr><th class=b colspan=6>{errors_num}&nbsp;{adm_er_nbs}</th></tr>
</table>