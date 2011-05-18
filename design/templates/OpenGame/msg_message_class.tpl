<h2>{L_msg_page_header}</h2>
<table width="569">
  <tr class="c_c">
    <th>{L_head_type}</th>
    <th>{L_head_count}</th>
    <th>{L_head_total}</th>
  </tr>

  <!-- BEGIN message_class -->
    <tr class="{message_class.STYLE}">
      <td class="{message_class.STYLE}"><a href="messages.php?mode=show&message_class={message_class.ID}"><span class="{message_class.STYLE}">{message_class.TEXT}</span></a></td>
      <td class="{message_class.STYLE}">{message_class.UNREAD}</span></td>
      <td class="{message_class.STYLE}">{message_class.TOTAL}</span></td>
    </tr>
  <!-- END message_class -->
  <tr>
    <th class="c_c" colspan="3"><a href="messages.php?mode=write">{L_msg_compose}</a></th>
  </tr>
</table>
