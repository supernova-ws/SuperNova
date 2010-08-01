<table width=519>
 <tr><td class="c" colspan=4>{L_sys_affilate_list}</td></tr>
 <tr>
   <th>Игрок</th>
   <th>Дата регистрации</th>
   <th>Заработано ТМ игроком</th>
   <th>Ваш бонус</th>
 </tr>
 <!-- IF .affilates -->
 <!-- BEGIN affilates -->
 <tr>
   <th>{affilates.USERNAME}</th>
   <th>{affilates.REGISTERED}</th>
   <th>{affilates.DARK_MATTER}</th>
   <th>{affilates.GAINED}</th>
 </tr>
 <!-- END affilates -->
 <tr>
   <td colspan=3 class="c">{L_sys_total}</td>
   <td class="c" align="center">{GAINED}</td>
 </tr>
 <!-- ELSE -->
 <tr><th colspan=4>{L_sys_affilates_none}</th></tr>
 <!-- ENDIF -->
</table>
<table width="519">
  <tr><td class="c">{L_sys_affilates_title}</td></tr>
  <tr><th>
    {L_sys_affilates_text1}{rpg_bonus_divisor}{L_sys_affilates_text2}
  </th></tr>
  <tr><th>
    <h2>{sys_link_name}</h2>
    {sys_link_bb}<br>
    <input name="htmlbbcode" type="text" id="htmlbbcode" value="[url={serverURL}/reg.php?id_ref={user_id}]{serverURL}[/url]" size="90"><br><br>

    {sys_link_html}<br>
    <input name="htmllink" type="text" id="htmllink" value="<a href={serverURL}/reg.php?id_ref={user_id}>{serverURL}</a>" size="90"><br><br>
  </th></tr>
  <tr><th>
    <h2>{sys_banner_name}</h2>
    <img src="{bannerURL}"><br><br>

    {sys_banner_bb}<br>
    <input name="bannerbbcode" type="text" id="bannerbbcode" value="[url={serverURL}/reg.php?id_ref={user_id}][img]{bannerURL}[/img][/url]" size="90"><br><br>

    {sys_banner_html}<br>
    <input name="bannerlink" type="text" id="bannerlink" value="<a href={serverURL}/reg.php?id_ref={user_id}><img src={bannerURL}></a>" size="90"><br><br>
  </th></tr>
  <tr><th>
    <h2>{sys_userbar_name}</h2>
    <img src="{userbarURL}"><br><br>

    {sys_userbar_bb}<br>
    <input name="userbarbbcode" type="text" id="userbarbbcode" value="[url={serverURL}/reg.php?id_ref={user_id}][img]{userbarURL}[/img][/url]" size="90"><br><br>

    {sys_userbar_html}<br>
    <input name="userbarlink" type="text" id="userbarlink" value="<a href={serverURL}/reg.php?id_ref={user_id}><img src={userbarURL}></a>" size="90">
  </th></tr>
</table>
