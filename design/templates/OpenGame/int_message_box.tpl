<!-- IF .messages -->
<table width="519" border="0" cellpadding="0" cellspacing="1">
  <!-- IF MESSAGE_BOX_TITLE --><tr><th class="c_l">{MESSAGE_BOX_TITLE}</th></tr><!-- ENDIF -->
  <tr>
    <td class="c_l">
      <div class="hint">
        <ul>
        <!-- BEGIN messages -->
          <li<!-- IF messages.CLASS --> class="{messages.CLASS}"<!-- ENDIF -->>{messages.TEXT}</li>
        <!-- END messages -->
        </ul>
      </div>
    </td>
  </tr>
</table>
<br>
<!-- ENDIF -->