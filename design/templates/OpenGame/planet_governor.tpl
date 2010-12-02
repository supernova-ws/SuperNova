{L_sys_governor}<br>
<a href="overview.php?mode=manage">
<!-- IF GOVERNOR_ID -->
<div class="unit_preview" style="" id="unit{production.ID}" unit_id="{production.ID}">
  <span style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%">
    <img style="border-style: none;" src="{dpath}gebaeude/{GOVERNOR_ID}.jpg" height="100%" width="100%">
  </span>

  <span style="position: absolute; top: 18px; left: 0px; width: 100%; height: 5ex; font-size: 100%;" class="icon_alpha">
    {GOVERNOR_NAME}
  </span>

  <span style="position: absolute; bottom: 0px; right: 0px;" class="icon_alpha" onclick="document.location='infos.php?gid={GOVERNOR_ID}'">
    <div class="icons icon-info"></div>
  </span>
  
  <!-- IF GOVERNOR_CAN_BUILD -->
    <span style="position: absolute; top: 0px; right: 0px;" class="icon_alpha" onclick="document.location='?cmd=insert&building={production.ID}'">
      <div class="icons icon-plus"></div>
    </span>
  <!-- ENDIF -->

  <!-- IF GOVERNOR_LEVEL && 0 -->
    <span style="position: absolute; top: 0px; left: 0px;" class="icon_alpha" onclick="document.location='?cmd=destroy&building={production.ID}'">
      <div class="icons icon-minus" title="{L_bld_destroy}: {L_sys_metal} {production.DESTROY_METAL}; {L_sys_crystal} {production.DESTROY_CRYSTAL}; {L_sys_deuterium} {production.DESTROY_DEUTERIUM}; {L_sys_time} {production.DESTROY_TIME}"></div>
    </span>
  <!-- ENDIF -->

  <span style="position: absolute; top: 0; left: 20%; width: 60%; height: 16px; text-align: center; font-size: 120%;" class="icon_alpha">
    <!-- IF GOVERNOR_LEVEL -->
      {GOVERNOR_LEVEL}/{GOVERNOR_LEVEL_MAX}
    <!-- ENDIF -->
  </span>

  <!-- IF production.METAL_REST > 99999999999 || production.CRYSTAL_REST > 9999999999 || production.DEUTERIUM_REST_NUM > 9999999999 -->
    <!-- DEFINE $FONT_SIZE = '80%' -->
  <!-- ELSE -->
    <!-- DEFINE $FONT_SIZE = '100%' -->
  <!-- ENDIF -->

  <span style="position: absolute; top: 45px; left: 0px; width: 100%; font-size: {$FONT_SIZE}; text-align: left;" class="icon_alpha"> <!--  onclick="eco_struc_select_unit({production.ID})" onmouseout="eco_struc_unborder_unit({production.ID})" onmouseover="eco_struc_show_unit_info({production.ID})"> -->
    <!-- IF production.METAL --><div><div style="left: 0px; position: absolute;">{L_sys_metal}</div><div style="right: 0px; position:absolute;">{production.METAL_REST}</div></div><br><!-- ENDIF -->
  </span>

  <span style="position: absolute; bottom: 2px; right: 18px; width: 84%; font-size: 100%; text-align: left;" class="icon_alpha"> <!--  onclick="eco_struc_select_unit({production.ID})" onmouseout="eco_struc_unborder_unit({production.ID})" onmouseover="eco_struc_show_unit_info({production.ID})"> -->
    <div class="fr">{production.TIME}</div>
  </span>
</div>

<!-- ELSE -->
  <span>{L_ov_no_governor}</span>
<!-- ENDIF -->
</a>