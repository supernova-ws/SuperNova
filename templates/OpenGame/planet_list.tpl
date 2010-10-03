<!-- IF $OVERVIEW -->
  <!-- DEFINE $CELL_CLASS = 'class="tr"' -->
<!-- ENDIF -->

<!-- BEGIN planet -->
  <!-- IF $OVERVIEW -->
   <tr>
  <!-- ENDIF -->
  <th valign="top" {$CELL_CLASS}><center>
  <!-- IF planet.ID -->
    <!-- IF $OVERVIEW -->
      <!-- IF planet.MOON_ID == PLANET_ID -->{L_sys_moon} {planet.MOON_NAME}<!-- ELSE -->{planet.NAME}<!-- ENDIF --> [{planet.GALAXY}:{planet.SYSTEM}:{planet.PLANET}]<br>
    <!-- ENDIF -->
    
    <!-- IF planet.ID == PLANET_ID -->
      <!-- DEFINE $PLANET_IMG_SIZE = 100 -->
    <!-- ELSE -->
      <!-- DEFINE $PLANET_IMG_SIZE = 75 -->
    <!-- ENDIF -->

    <div style="position: relative; height: {$PLANET_IMG_SIZE}px; width: {$PLANET_IMG_SIZE}px;">
      <span style="position: absolute; left: 0px; top: 0px; width: 100%; height: 100%">
        <!-- IF planet.IMAGE -->
        <a href="overview.php?cp={planet.ID}&re=0"><img style="border-style: none;" src="{dpath}planeten/{planet.IMAGE}.jpg" height="100%" width="100%"></a>
        <!-- ELSE -->
        &nbsp;
        <!-- ENDIF -->
      </span>

      <!-- IF planet.BUILDING -->
        <span title="{planet.BUILDING_TIP}" style="position: absolute; top: 0px; left: 0px; width: 25%; height: 25%" class="icon_alpha">
          <a href="buildings.php?cp={planet.ID}&re=0"><img src="images/build_building.png" height="100%" width="100%"></a>
        </span>
      <!-- ENDIF -->
      
      <!-- IF planet.TECH -->
        <span title="{planet.TECH}" style="position: absolute; top: 25%; left: 0px; width: 25%; height: 25%" class="icon_alpha">
          <a href="buildings.php?mode=research&cp={planet.ID}&re=0"><img src="images/build_tech.png" height="100%" width="100%"></a>
        </span>
      <!-- ENDIF -->

      <!-- IF planet.HANGAR -->
        <span title="{planet.HANGAR}" style="position: absolute; top: 50%; left: 0px; width: 25%; height: 25%;" class="icon_alpha">
          <a href="buildings.php?mode=fleet&cp={planet.ID}&re=0"><img src="images/build_hangar.png" height="100%" width="100%"></a>
        </span>
      <!-- ENDIF -->

      <!-- IF planet.FLEET_OWN -->
        <span style="position: absolute; top: 0px; left: 25%; width: 25%; height: 25%;" class="icon_alpha">
          <a href="fleet.php?cp={planet.ID}&re=0"><img src="images/icon_fleet_own.png" height="100%" width="100%"></a>
        </span>
      <!-- ENDIF -->

      <!-- IF planet.FLEET_ENEMY -->
        <span style="position: absolute; top: 26%; left: 26%; width: 48%; height: 48%;">
          <img src="images/icon_warning.png" height="100%" width="100%" style="border-style: none; cursor: pointer;" onclick="javascript:window.location = '?cp={planet.ID}&re=0'">
        </span>
      <!-- ENDIF -->

      <span style="position: absolute; left: 0; width: 100%; top: 96%; height: 4%; overflow: hidden;">
        <div class="fl" style="position: relative; left: 0px; height: 100%; width: {planet.FILL}%; background-color: <!-- IF planet.FILL >= 100 -->red<!-- ELSEIF planet.FILL > 80 -->yellow<!-- ELSE -->green<!-- ENDIF -->;"></div>
      </span>

      <!-- IF planet.MOON_ID -->
        <div style="position: absolute; top: 0; left: 68%; width: 32%; height: 32%;">
          <a href="?cp={planet.MOON_ID}&re=0" title="{planet.MOON_NAME} [{planet.GALAXY}:{planet.SYSTEM}:{planet.PLANET}]"><img style="border-style: none;" src="{dpath}planeten/small/s_{planet.MOON_IMG}.jpg" height="100%" width="100%"></a>
          
          <!-- IF planet.MOON_ENEMY != 0 -->
            <span style="position: absolute; top: 26%; left: 26%; width: 48%; height: 48%;">
              <img src="images/icon_warning.png" height="100%" width="100%" style="border-style: none; cursor: pointer;" onclick="javascript:window.location = '?cp={planet.MOON_ID}&re=0'">
            </span>
          <!-- ENDIF -->

          <span style="position: absolute; left: 0; width: 100%; top: 90%; height: 10%; overflow: hidden;">
            <div class="fl" style="position: relative; left: 0px; height: 100%; width: {planet.MOON_FILL}%; background-color: <!-- IF planet.MOON_FILL >= 100 -->red<!-- ELSEIF planet.MOON_FILL > 80 -->yellow<!-- ELSE -->green<!-- ENDIF -->;"></div>
          </span>
        </div>
      <!-- ENDIF -->
    </div>
    <!-- IF planet.BUILDING -->{planet.BUILDING}<span id="ov_building{planet.ID}"></span><br><font color="darkgrey"><span id="ov_building{planet.ID}_timer"></span></font><!-- ELSE -->{L_sys_no_task}<!-- ENDIF -->
    <!-- IF $OVERVIEW -->
    <br><br>
    <!-- ENDIF -->
  <!-- ENDIF -->
  </center></th>
  <!-- IF $OVERVIEW -->
    </tr>
  <!-- ENDIF -->
<!-- END planet -->
