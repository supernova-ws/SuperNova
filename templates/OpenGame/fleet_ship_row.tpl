<tr height="20">
  <th><div style="float: left"><a title="{fl_fleetspeed}{ShipSpeed}"</a>{ShipName}</div></th>
  <th>{ShipNumPrint}</th>
  <th style="{DisplayControls}"><span><div style="float: left"><a href="javascript:maxShip('ship{ShipID}'); shortInfo();">{fl_selmax}</a></span>&nbsp;</div></th>
  <th style="{DisplayControls}"><span><input name="ship{ShipID}" size="10" value="0" onfocus="javascript:if(this.value == '0') this.value='';" onblur="javascript:if(this.value == '') this.value='0';" alt="{ShipName}{ShipNum}" onChange="shortInfo()" onKeyUp="shortInfo()" /></span>&nbsp;
  </th>
</tr>
