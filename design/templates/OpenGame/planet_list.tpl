<!-- IF $OVERVIEW -->
  <!-- DEFINE $CELL_CLASS = 'class="tr"' -->
<!-- ENDIF -->

<!-- IF $OVERVIEW && .planet > 5 -->
  <!-- DEFINE $TWO_COLUMN = true -->
<!-- ENDIF -->

<!-- BEGIN planet -->
  <!-- IF ($TWO_COLUMN && planet.S_ROW_COUNT is even) || ( $OVERVIEW && ! $TWO_COLUMN) -->
  <!-- IF $OVERVIEW -->
  <!-- ENDIF -->
   <tr>
  <!-- ENDIF -->
  <th valign="top" {$CELL_CLASS} width="{$CELL_PLANET}"><center>
  <!-- IF planet.ID -->
    <!-- IF $OVERVIEW -->
      <!-- IF planet.MOON_ID == PLANET_ID -->{L_sys_moon} {planet.MOON_NAME}<!-- ELSE -->{planet.NAME}<!-- ENDIF --><br />[{planet.GALAXY}:{planet.SYSTEM}:{planet.PLANET}]<br>
    <!-- ENDIF -->
    
    <!-- IF planet.ID == PLANET_ID || (planet.MOON_ID == PLANET_ID && $OVERVIEW) -->
      <!-- DEFINE $PLANET_IMG_SIZE = 100 -->
      <!-- DEFINE $RESOURCE_BAR = 4 -->
      <!-- DEFINE $METAL_BAR = 100 -->
      <!-- DEFINE $CRYSTAL_BAR = 103 -->
      <!-- DEFINE $DEUTERIUM_BAR = 106 -->
      <!-- DEFINE $OVERALL_BAR = 109 -->
      <!-- DEFINE $FRAME_WIDTH = 112 -->
    <!-- ELSE -->
      <!-- DEFINE $PLANET_IMG_SIZE = 75 -->
      <!-- DEFINE $RESOURCE_BAR = 4 -->
      <!-- DEFINE $METAL_BAR = 75 -->
      <!-- DEFINE $CRYSTAL_BAR = 79 -->
      <!-- DEFINE $DEUTERIUM_BAR = 83 -->
      <!-- DEFINE $OVERALL_BAR = 87 -->
      <!-- DEFINE $FRAME_WIDTH = 91 -->
    <!-- ENDIF -->

    <!-- IF planet.MOON_ID == PLANET_ID -->
      <!-- DEFINE $MOON_IMG_SIZE = '66%' -->
    <!-- ELSE -->
      <!-- DEFINE $MOON_IMG_SIZE = '32%' -->
    <!-- ENDIF -->

    <div style="position: relative; height: {$DEUTERIUM_BAR}px; width: {$FRAME_WIDTH}px; border: 0px solid blue;">
      <div style="position: absolute; height: {$PLANET_IMG_SIZE}px; width: {$PLANET_IMG_SIZE}px; border: 0px solid yellow; left: 0px;">
        <span style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%">
          <!-- IF planet.IMAGE -->
          <a href="overview.php?cp={planet.ID}&re=0"><img style="border-style: none;" src="{dpath}planeten/{planet.IMAGE}.jpg" height="100%" width="100%"></a>
          <!-- ELSE -->
          &nbsp;
          <!-- ENDIF -->
        </span>

        <!-- IF planet.BUILDING -->
          <span title="{planet.BUILDING_TIP}" style="position: absolute; top: 0px; left: 0px; width: 25%; height: 25%" class="icon_alpha">
            <a href="buildings.php?cp={planet.ID}&re=0"><img src="{D_SN_ROOT_VIRTUAL}design/images/build_building.png" height="100%" width="100%"></a>
          </span>
        <!-- ENDIF -->
        
        <!-- IF planet.TECH -->
          <span title="{planet.TECH}" style="position: absolute; top: 25%; left: 0px; width: 25%; height: 25%" class="icon_alpha">
            <a href="buildings.php?mode=research&cp={planet.ID}&re=0"><img src="{D_SN_ROOT_VIRTUAL}design/images/build_tech.png" height="100%" width="100%"></a>
          </span>
        <!-- ENDIF -->

        <!-- IF planet.HANGAR -->
          <span title="{planet.HANGAR}" style="position: absolute; top: 50%; left: 0px; width: 25%; height: 25%;" class="icon_alpha">
            <a href="buildings.php?mode=fleet&cp={planet.ID}&re=0"><img src="{D_SN_ROOT_VIRTUAL}design/images/build_hangar.png" height="100%" width="100%"></a>
          </span>
        <!-- ENDIF -->

        <span style="position: absolute; bottom: 0; right: 0; cursor: pointer;" title="{L_flt_gather_all}" class="icon_alpha" onclick="document.location='fleet.php?fleet_page=5&cp={planet.ID}&re=0'">
          <div class="icons icon-gather"></div>
        </span>

        <!-- IF planet.FLEET_OWN -->
          <span style="position: absolute; top: 0px; left: 25%; width: 25%; height: 25%; cursor: pointer;" class="icon_alpha" onmouseover="fleet_dialog_show(this, '{planet.PLANET_FLEET_ID}')"  onclick="document.location='fleet.php?cp={planet.ID}&re=0'">
            <img src="{D_SN_ROOT_VIRTUAL}design/images/icon_fleet_own.png" height="100%" width="100%">
          </span>
        <!-- ENDIF -->

        <!-- IF planet.FLEET_ENEMY -->
          <span style="position: absolute; top: 26%; left: 26%; width: 48%; height: 48%;">
            <img src="{D_SN_ROOT_VIRTUAL}design/images/icon_warning.png" height="100%" width="100%" style="border-style: none; cursor: pointer;" onclick="javascript:window.location = '?cp={planet.ID}&re=0'">
          </span>
        <!-- ENDIF -->

        <!-- IF planet.MOON_ID -->
          <div style="position: absolute; top: 0; right: 0; width: {$MOON_IMG_SIZE}; height: {$MOON_IMG_SIZE};">
            <a href="overview.php?cp={planet.MOON_ID}&re=0" title="{planet.MOON_NAME} [{planet.GALAXY}:{planet.SYSTEM}:{planet.PLANET}]"><img style="border-style: none;" src="{dpath}planeten/small/s_{planet.MOON_IMG}.jpg" height="100%" width="100%"></a>
            
            <span style="position: absolute; bottom: 0; right: 0; cursor: pointer;" title="{L_flt_gather_all}" class="icon_alpha" onclick="document.location='fleet.php?fleet_page=5&cp={planet.MOON_ID}&re=0'">
              <div class="icons icon-gather"></div>
            </span>

            <span style="position: absolute; left: 0; width: 100%; top: 90%; height: 10%; overflow: hidden;">
              <div class="fl" style="position: relative; left: 0px; height: 100%; width: {planet.MOON_FILL}%; background-color: <!-- IF planet.MOON_FILL >= 100 -->red<!-- ELSEIF planet.MOON_FILL > 80 -->yellow<!-- ELSE -->green<!-- ENDIF -->;"></div>
            </span>

            <!-- IF planet.MOON_ENEMY != 0 -->
              <span style="position: absolute; top: 15%; left: 15%; width: 70%; height: 70%;">
                <img src="{D_SN_ROOT_VIRTUAL}design/images/icon_warning.png" height="100%" width="100%" style="border-style: none; cursor: pointer;" onclick="javascript:window.location = '?cp={planet.MOON_ID}&re=0'">
              </span>
            <!-- ENDIF -->
          </div>
        <!-- ENDIF -->
      </div>

      <!-- IF planet.METAL_PERCENT < 50 --> 
        <!-- DEFINE $FIELD_COLOR = 'negative_bg' -->
      <!-- ELSEIF planet.METAL_PERCENT < 80 --> 
        <!-- DEFINE $FIELD_COLOR = 'warning_bg' -->
      <!-- ELSEIF planet.METAL_PERCENT < 100 --> 
        <!-- DEFINE $FIELD_COLOR = 'neutral_bg' -->
      <!-- ELSE -->
        <!-- DEFINE $FIELD_COLOR = 'positive_bg' -->
      <!-- ENDIF -->
      <div class="{$FIELD_COLOR}" style="position: absolute; top: 0px; left: {$METAL_BAR}px; height: {$PLANET_IMG_SIZE}px; width: {$RESOURCE_BAR}px; overflow: hidden;">
        <div class="metal_bg" style="position: absolute; bottom: 0px; left: 0px; width: 100%; height: {planet.METAL_PERCENT}%"></div>
      </div>

      <!-- IF planet.CRYSTAL_PERCENT < 50 --> 
        <!-- DEFINE $FIELD_COLOR = 'negative_bg' -->
      <!-- ELSEIF planet.CRYSTAL_PERCENT < 80 --> 
        <!-- DEFINE $FIELD_COLOR = 'warning_bg' -->
      <!-- ELSEIF planet.CRYSTAL_PERCENT < 100 --> 
        <!-- DEFINE $FIELD_COLOR = 'neutral_bg' -->
      <!-- ELSE -->
        <!-- DEFINE $FIELD_COLOR = 'positive_bg' -->
      <!-- ENDIF -->
      <div class="{$FIELD_COLOR}" style="position: absolute; top: 0px; left: {$CRYSTAL_BAR}px; height: {$PLANET_IMG_SIZE}px; width: {$RESOURCE_BAR}px; overflow: hidden;">
        <div class="crystal_bg" style="position: absolute; bottom: 0px; left: 0px; width: 100%; height: {planet.CRYSTAL_PERCENT}%"></div>
      </div>

      <!-- IF planet.DEUTERIUM_PERCENT < 50 --> 
        <!-- DEFINE $FIELD_COLOR = 'negative_bg' -->
      <!-- ELSEIF planet.DEUTERIUM_PERCENT < 80 --> 
        <!-- DEFINE $FIELD_COLOR = 'warning_bg' -->
      <!-- ELSEIF planet.DEUTERIUM_PERCENT < 100 --> 
        <!-- DEFINE $FIELD_COLOR = 'neutral_bg' -->
      <!-- ELSE -->
        <!-- DEFINE $FIELD_COLOR = 'positive_bg' -->
      <!-- ENDIF -->
      <div class="{$FIELD_COLOR}" style="position: absolute; top: 0px; left: {$DEUTERIUM_BAR}px; height: {$PLANET_IMG_SIZE}px; width: {$RESOURCE_BAR}px; overflow: hidden;">
        <div class="deuterium_bg" style="position: absolute; bottom: 0px; left: 0px; width: 100%; height: {planet.DEUTERIUM_PERCENT}%"></div>
      </div>
      <!-- IF planet.FILL < 50 --> 
        <!-- DEFINE $FIELD_COLOR = 'positive_bg' -->
      <!-- ELSEIF planet.FILL < 80 --> 
        <!-- DEFINE $FIELD_COLOR = 'neutral_bg' -->
      <!-- ELSEIF planet.FILL < 100 --> 
        <!-- DEFINE $FIELD_COLOR = 'warning_bg' -->
      <!-- ELSE -->
        <!-- DEFINE $FIELD_COLOR = 'negative_bg' -->
      <!-- ENDIF -->
      <div style="width: {$PLANET_IMG_SIZE}px; height: 9px; overflow: hidden; top: {$PLANET_IMG_SIZE}px; left: 0px; position: absolute;">
        <div style="position: absolute; left: 0px; top: 0px; height: 100%; width: {planet.FILL}%;" class="{$FIELD_COLOR}"></div>
        <div style="position: absolute; left: 0px; top: -1px; height: 100%; width: 100%; font-size: 9px; font-weight: bold;">{planet.FIELDS_CUR}&nbsp;/&nbsp;{planet.FIELDS_MAX}</div>
      </div>
    </div>

    
    <!-- IF planet.BUILDING -->{planet.BUILDING}<div style="width: 75; white-space: normal;" id="ov_building{planet.ID}"></div>
      <div id="ov_building{planet.ID}_timer" style="color: darkgrey;"></div><!-- ELSE -->{L_sys_no_task}<!-- ENDIF -->
    <!-- IF $OVERVIEW -->
    <br><br>
    <!-- ENDIF -->
  <!-- ENDIF -->
  </center></th>
  <!-- IF ($TWO_COLUMN && planet.S_ROW_COUNT is odd) || ( $OVERVIEW && ! $TWO_COLUMN) -->
  <!-- IF $OVERVIEW -->
  <!-- ENDIF -->
    </tr>
  <!-- ENDIF -->
<!-- END planet -->
