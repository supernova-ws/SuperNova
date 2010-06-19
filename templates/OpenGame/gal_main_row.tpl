<tr>
<th width=30 style="white-space: nowrap">
  <div style="line-height: 1em; height: 1em">
    <a href="fleet.php?galaxy={galaxy}&system={system}&planet={planetN}&planettype=0&target_mission=7">{planetN}</a>
  </div>
</th>
{rowPlanet}
{rowPlanetName}
{rowMoon}
{rowDebris}


<th width=150 align=center><div style="line-height: 1em; height: 1em">
  {rowUser}
</div></th>

<th width=80><div style="line-height: 1em; height: 1em">
  <a {isShowAlly} style="cursor: pointer;" onmouseover='javascript:showAlly({ally_id});' onmouseout='return nd();'><span class="{ally_class}">{ally_tag}</span></a>
</div></th>
{rowActions}
</tr>
