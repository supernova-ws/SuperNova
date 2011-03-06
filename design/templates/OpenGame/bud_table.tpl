<br />
<table>
  <tr><td class=c colspan=6>{title}</td></tr>
  <tr>
    <th>{sys_player}</th>
    <th>{sys_alliance}</th>
    <th>{sys_coordinates}</th>
    <th>{column4title}</th>
    <th><img src="{dpath}img/m.gif" alt="{sys_writeMessage}" title="{sys_writeMessage}">&nbsp;<span {isShowAccept}><img src="design/images/r4.png" alt="{ali_req_accept}" title="{ali_req_accept}" border="0">&nbsp;</span><img src="design/images/r1.png" alt="{sys_delete}" title="{sys_delete}" border="0"></th>
  </tr>
  {rows}
  <tr {isShowPlaceholder}><th colspan=6>{empty}</th></tr>
</table>

<!-- INCLUDE page_hint.tpl -->
