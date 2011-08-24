<h2>{L_adm_lng_title}</h2>
<h2 class="warning">{L_adm_lng_warning}</h2>
<form method="post">
<!-- IF .domain -->
    {L_adm_lng_domain} <select name="domain">
      <!-- BEGIN domain -->
        <option value="{domain.NAME}">{domain.NAME}</option>
      <!-- END domain -->
    </select>
    <input type="submit">
<!-- ELSEIF .language -->
  <input type="hidden" name="domain" value="{DOMAIN}">
  <table>
   <tr>
     <th class="c_l">{L_adm_lng_string_name}</th>
     <!-- BEGIN language -->
       <th class="c_l">{language.LANG_NAME_NATIVE}</td>
     <!-- END language -->
   </tr>
  <!-- BEGIN string -->
    <tr>
      <td class="c_l">{string.NAME}</td>
      <!-- BEGIN locale -->
        <td><input type="text" size=30 name="lang_new{string.NAME}{locale.LANG}" value="{locale.VALUE}"></td>
      <!-- END local -->
    </tr>
  <!-- END string -->
  </table>
  <input type="submit">
<!-- ENDIF -->
</form>
