<script src="scripts/cntchar.js" type="text/javascript"></script>
<br />
<table width=519>
	<tr>
	  <td class=c colspan=2>{ally_admin}</td>
	</tr>
	<tr>
	  <th colspan=2><a href="?mode=admin&edit=rights">{Law_settings}</a></th>
	</tr>
	<tr>
	  <th colspan=2><a href="?mode=admin&edit=members">{members_admin}</a></th>
	</tr>
	<tr>
	  <th colspan=2><a href="?mode=admin&edit=tag">{ch_allytag}</a></th>
	</tr>
	<!--<img src="{dpath}pic/appwiz.gif" border=0 alt="">-->
	<tr>
	  <th colspan=2><a href="?mode=admin&edit=name">{ch_allyname}</a></th>
	</tr>
	<!--<img src="{dpath}pic/appwiz.gif" border=0 alt="">-->
</table>
<br />
<form action="" method="POST">
<input type="hidden" name="t" value="{t}">
<table width=519>
	<tr>
	  <td class="c" colspan=3>{Texts}</td>
	</tr>
	<tr>
	  <th><a href="?mode=admin&edit=ally&t=1">{External_text}</a></th>
	  <th><a href="?mode=admin&edit=ally&t=2">{Internal_text}</a></th>
      <th><a href="?mode=admin&edit=ally&t=3">{Request_text}</a></th>
	</tr>
	<tr>
	  <td class=c colspan=3>{Show_of_request_text} (<span id="cntChars">0</span> / 5000 {characters})</td>
	</tr>
	<tr>
	  <th colspan=3><textarea name="text" cols=70 rows=15 onkeyup="javascript:cntchar(5000)">{text}</textarea>
{request_type}
	</th>
	</tr>
	<tr>
	  <th colspan=3>
	  <input type="hidden" name=t value={t}><input type="reset" value="{Reset}"> 
	  <input type="submit" value="{Save}">
	  </th>
	</tr>
</table>
</form>

<br />

<form action="" method="POST">
<table width=519>
	<tr>
	  <td class=c colspan=2>{Options}</td>
	</tr>
	<tr>
	  <th>{Main_Page}</th>
	  <th><input type=text name="web" value="{ally_web}" size="70"></th>
	</tr>
	<tr>
	  <th>{Alliance_logo}</th>
	  <th><input type=text name="image" value="{ally_image}" size="70"></th>
	</tr>
	<tr>
	  <th>{Requests}</th>
	  <th>
	  <select name="request_notallow"><option value=1{ally_request_notallow_0}>{not_allow_request}</option>
	  <option value=0{ally_request_notallow_1}>{Allow_request}</option></select>
	  </th>
	</tr>
	<tr>
	  <th>{Founder_name}</th>
	  <th><input type="text" name="owner_range" value="{ally_owner_range}" size=30></th>
	</tr>
	<tr>
	  <th colspan=2><input type="submit" name="options" value="{Save}"></th>
	</tr>
</table>
</form>

{ally_dissolve}
<br />
{Ally_transfer}

