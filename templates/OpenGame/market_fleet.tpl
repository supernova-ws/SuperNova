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

function eco_market_recalc()
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

{message}
<br />
<form action="" method="POST">
  <table>
    <tr><td class="c" colspan=6><div class="fl"><!-- IF MODE == 2 -->{L_eco_mrk_scraper}<!-- ELSE -->{L_eco_mrk_stockman}<!-- ENDIF --></div><div class="fr">{L_eco_mrk_service_cost} {rpg_cost} {L_eco_mrk_dark_matter_short}</div></td></tr>
    <tr align="center" rowspan=2><td class="c" rowspan=2>{L_sys_ships}</td><td class="c" colspan=3><!-- IF MODE == 2 -->{L_eco_mrk_scraper_price} {L_eco_mrk_scraper_perShip}<!-- ELSE -->{L_eco_mrk_stockman_price} {L_eco_mrk_stockman_perShip}<!-- ENDIF --></td><td class="c" rowspan=2><!-- IF MODE == 2 -->{L_eco_mrk_scraper_onOrbit}<!-- ELSE -->{L_eco_mrk_stockman_onStock}<!-- ENDIF --></td><td class="c" rowspan=2><!-- IF MODE == 2 -->{L_eco_mrk_scraper_to}<!-- ELSE -->{L_eco_mrk_stockman_buy}<!-- ENDIF --></td></tr>
    <tr align="center"><td class="c">{L_Metal}</td><td class="c">{L_Crystal}</td><td class="c">{L_Deuterium}</td></tr>
    <!-- BEGIN ships -->
      <tr>
        <th>{ships.NAME}</th>
        <th><span class="fr">{ships.METAL}</span></th>
        <th><span class="fr">{ships.CRYSTAL}</span></th>
        <th><span class="fr">{ships.DEUTERIUM}</span></th>
        <th><span class="fr">{ships.COUNT}</span></th>
        <th>
          <script type="text/javascript"><!--
            sn_ainput_make('ships[{ships.ID}]', 0, ships[{ships.ID}]['count'], 1);

            jQuery('#ships{ships.ID}slide').bind('slide slidechange', eco_market_recalc);
          --></script>
        </th>
      </tr>
    <!-- BEGINELSE ships -->
      <tr>
        <th class="c" colspan="6" align=center>
          <!-- IF MODE == 2 -->
            {L_eco_mrk_scraper_noShip}
          <!-- ELSE -->
            {L_eco_mrk_stockman_noShip}
          <!-- ENDIF -->
        </th>
      </tr>
    <!-- END ships -->

    <tr align=right>
      <td class="c" align=left>{L_eco_mrk_scraper_total}</td>
      <td class="c"><span id="total_metal">0</span></td>
      <td class="c"><span id="total_crystal">0</span></td>
      <td class="c"><span id="total_deuterium">0</span></td>
      <td class="c" colspan=2>
        <!-- IF MODE == 2 -->
          <input type="submit" name="scrape" value="{L_eco_mrk_scraper_to}">
        <!-- ELSE -->
          <input type="submit" name="stock" value="{L_eco_mrk_stockman_buy}">
        <!-- ENDIF -->
      </td>
    </td></tr>
  </table>
  <input type="hidden" name="mode" value="{MODE}">
</form>
