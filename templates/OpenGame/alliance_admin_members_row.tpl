<tr>
  <th>{i}</th>
  <th>{username}</th>
  <th><a href="messages.php?mode=write&id={id}"><img src="{dpath}img/m.gif" border=0 alt="{Write_message}"></a></th>
  <th>{ally_range}</th>
  <th>{points}</th>
  <th><a href="galaxy.php?mode=0&galaxy={galaxy}&system={system}">[{galaxy}:{system}:{planet}]</a></th>
  <th>{ally_register_time}</th>
  <th>{onlinetime}</th>
  <th><span {functions}>
    <a href="alliance.php?mode=admin&edit=members&kick={id}" alt="{ali_kickUser}" title="{ali_kickUser}"
      onclick="javascript:return confirm('{Kick_sure}');"><img src="{dpath}pic/abort.gif" border=0 ></a>
    &nbsp;
    <a href="alliance.php?mode=admin&edit=members&rank={id}" alt="{Set_range}" title="{Set_range}"><img src="{dpath}pic/key.gif" border=0></a>
  </span></script></th>
</tr>
