<h1>{L_adm_er_ttle}</h1>
<table>
<tr>
<td class="c" colspan="2">{L_adm_er_idmsg} {log_id}</td>
</tr>
<tr><th>{L_adm_er_time}</th><td><b>{log_timestamp}</b> UNIX_TIME {log_time}</td></tr>
<tr><th>{L_adm_er_play}</th><td><b>ID {log_sender}</b> {log_username}</td></tr>
<tr><th>{L_adm_er_page}</th><td>{log_page}</td></tr>
<tr><th>{L_adm_er_type}</th><td><b>{log_code}</b> {log_title}</td></tr>
<tr><th>{L_adm_er_text}</th><td><div style="width: 800px">{log_text}</div></td></tr>
<tr><th>{L_adm_er_bktr}</th><td><div style="width: 800px">
<!-- BEGIN vars -->
{vars.VAR_VALUE}
<!-- END vars -->
</div></td></tr>
</table>
