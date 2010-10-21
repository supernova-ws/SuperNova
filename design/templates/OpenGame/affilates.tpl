<h1>{L_aff_title}</h1>
<table width="519">
  <tr><td class="c">{L_aff_title}</td></tr>
  <tr><th>
    {L_aff_text1} {C_rpg_bonus_divisor} {L_aff_text2}
  </th></tr>
  <tr><th>
    <h2>{L_aff_link}</h2>
    {L_aff_link_bb}<br>
    <input name="htmlbbcode" type="text" id="htmlbbcode" value="[url={serverURL}/reg.php?id_ref={user_id}]{serverURL}[/url]" size="80"><br><br>

    {L_aff_link_html}<br>
    <input name="htmllink" type="text" id="htmllink" value="<a href={serverURL}/reg.php?id_ref={user_id}>{serverURL}</a>" size="80">
  </th></tr>
  <tr><th>
    <h2>{L_aff_banner}</h2>
    <img src="{bannerURL}"><br><br>

    {L_aff_banner_bb}<br>
    <input name="bannerbbcode" type="text" id="bannerbbcode" value="[url={serverURL}/reg.php?id_ref={user_id}][img]{bannerURL}[/img][/url]" size="80"><br><br>

    {L_aff_banner_html}<br>
    <input name="bannerlink" type="text" id="bannerlink" value="<a href={serverURL}/reg.php?id_ref={user_id}><img src={bannerURL}></a>" size="80">
  </th></tr>
  <tr><th>
    <h2>{L_aff_userbar}</h2>
    <img src="{userbarURL}"><br><br>

    {L_aff_userbar_bb}<br>
    <input name="userbarbbcode" type="text" id="userbarbbcode" value="[url={serverURL}/reg.php?id_ref={user_id}][img]{userbarURL}[/img][/url]" size="80"><br><br>

    {L_aff_userbar_html}<br>
    <input name="userbarlink" type="text" id="userbarlink" value="<a href={serverURL}/reg.php?id_ref={user_id}><img src={userbarURL}></a>" size="80">
  </th></tr>
</table>

<h2>{L_aff_list}</h2>
<table>
 <tr>
   <td class="c" >{L_sys_player}</td>
   <td class="c" >{L_sys_register_date}</td>
   <td class="c" >{L_aff_gained}</td>
   <td class="c" >{L_aff_your_bonus}</td>
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
 <tr><th colspan=4>{L_aff_none}</th></tr>
 <!-- ENDIF -->
</table>
