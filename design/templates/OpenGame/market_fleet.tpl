<script type="text/javascript"><!--
var ships = Array();
<!-- BEGIN ships -->
ships[{ships.ID}] = 
{
  id: {ships.ID}, 
  metal: {ships.METAL}, 
  crystal: {ships.CRYSTAL}, 
  deuterium: {ships.DEUTERIUM}, 
  count: {ships.COUNT}, 
  element: null
};
<!-- END ships -->
//--></script>

<script type="text/javascript"><!--
function eco_mrk_ship_recalc()
{
  var t = Array(0,0,0);

  for(i in ships)
  {
    ship_count = jQuery('#ships' + i + 'slide').slider("value");
    if( ship_count )
    {
      t[0] += ships[i]['metal'] * ship_count;
      t[1] += ships[i]['crystal'] * ship_count;
      t[2] += ships[i]['deuterium'] * ship_count;
    }
  }

  document.getElementById('total_metal').innerHTML = t[0];
  document.getElementById('total_crystal').innerHTML = t[1];
  document.getElementById('total_deuterium').innerHTML = t[2];
}
//--></script>

<h2>{L_eco_mrk_title}:&nbsp;<!-- IF MODE == 2 -->{L_eco_mrk_scraper}<!-- ELSE -->{L_eco_mrk_stockman}<!-- ENDIF --></h2>
{message}
<form action="" method="POST">
  <input type="hidden" name="mode" value="{MODE}">

  <table>
    <tr rowspan=2>
      <th class="c_l" rowspan=2>{L_sys_ships}</th>
      <th class="c_c" colspan=3>
        <!-- IF MODE == 2 -->
          {L_eco_mrk_scraper_price}&nbsp;{L_eco_mrk_scraper_perShip}
        <!-- ELSE -->
          {L_eco_mrk_stockman_price}&nbsp;{L_eco_mrk_stockman_perShip}
        <!-- ENDIF -->
      </hd>
      <th class="c_c" rowspan=2>
        <!-- IF MODE == 2 -->
          {L_eco_mrk_scraper_onOrbit}
        <!-- ELSE -->
          {L_eco_mrk_stockman_onStock}
        <!-- ENDIF -->
      </th>
      <th class="c_c" rowspan=2>
        <!-- IF MODE == 2 -->
          {L_eco_mrk_scraper_to}
        <!-- ELSE -->
          {L_eco_mrk_stockman_buy}
        <!-- ENDIF -->
      </th>
    </tr>
    <tr>
      <th class="c_c">{L_Metal}</th>
      <th class="c_c">{L_Crystal}</th>
      <th class="c_c">{L_Deuterium}</th>
    </tr>
    <!-- BEGIN ships -->
      <tr>
        <td class="c_l">{ships.NAME}</td>
        <td class="c_r">{ships.METAL}</td>
        <td class="c_r">{ships.CRYSTAL}</td>
        <td class="c_r">{ships.DEUTERIUM}</td>
        <td class="c_r">{ships.COUNT}</td>
        <td class="c_c">
          <script type="text/javascript"><!--
            sn_ainput_make('ships[{ships.ID}]', {max: ships[{ships.ID}]['count'], value: '{ships.AMOUNT}'});

            jQuery('#ships{ships.ID}slide').bind('slide slidechange', eco_mrk_ship_recalc);
          --></script>
        </td>
      </tr>
    <!-- BEGINELSE ships -->
      <tr>
        <td class="c_c" colspan="6">
          <!-- IF MODE == 2 -->
            {L_eco_mrk_scraper_noShip}
          <!-- ELSE -->
            {L_eco_mrk_stockman_noShip}
          <!-- ENDIF -->
        </td>
      </tr>
    <!-- END ships -->

    <!-- IF .ships -->
    <tr>
      <th class="c_l">{L_eco_mrk_scraper_total}</th>
      <th class="c_r"><span id="total_metal">0</span></th>
      <th class="c_r"><span id="total_crystal">0</span></th>
      <th class="c_r"><span id="total_deuterium">0</span></th>
      <th class="c_c" colspan=2>
        <div class="fr">
          <!-- IF MODE == 2 -->
            <input type="submit" name="scrape" value="{L_eco_mrk_scraper_to}">
          <!-- ELSE -->
            <input type="submit" name="stock" value="{L_eco_mrk_stockman_buy}">
          <!-- ENDIF -->
        </div>
        <div class="fl">
          {L_eco_mrk_service_cost}&nbsp;{rpg_cost}&nbsp;{L_sys_dark_matter_sh}
        </div>
      </th>
    </tr>
    <!-- ENDIF -->

  </table>
</form>
