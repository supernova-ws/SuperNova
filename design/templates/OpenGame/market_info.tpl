<h2>{L_eco_mrk_info}</h2>

{message}

<table width="519px">
  <tr>
    <td class="c_l" colspan="2">{L_eco_mrk_info_description} <i>&quot;{L_eco_mrk_info_description_2} {C_rpg_cost_info} {L_sys_dark_matter_sh}&quot;</i></td>
  </tr>

  <tr>
    <th class="c_l">{L_eco_mrk_info_player}</th>
  </tr>
  <tr>
    <td class="c_l">
      <form method="post">
        <input type="hidden" name="action" value="{D_MARKET_INFO_PLAYER}">
        {L_eco_mrk_info_player_description}<br />
        {L_sys_player_name} <input type="text" name="user_info_name"> <input type="submit" value="{L_eco_mrk_info_buy}"><br />
        {L_sys_hint_player_name}
      </form>
    </td>
  </tr>

<!--
  <tr>
    <td class="c_l"><a href="market.php?mode={D_MARKET_RESOURCES}">{L_eco_mrk_trader}</a></th>
    <td class="c_r">{C_rpg_cost_trader}&nbsp;{L_sys_dark_matter_sh}</td>
  </tr>
  <tr>
    <td class="c_l"><a href="market.php?mode={D_MARKET_SCRAPPER}">{L_eco_mrk_scraper}</a></td>
    <td class="c_r">{C_rpg_cost_scraper}&nbsp;{L_sys_dark_matter_sh}</td>
  </tr>
  <tr>
    <td class="c_l"><a href="market.php?mode={D_MARKET_STOCKMAN}">{L_eco_mrk_stockman}</a></td>
    <td class="c_r">{C_rpg_cost_stockman}&nbsp;{L_sys_dark_matter_sh}</td>
  </tr>
  <tr>
    <td class="c_l"><a href="market.php?mode={D_MARKET_INFO}">{L_eco_mrk_info}</a></td>
    <td class="c_r">{C_rpg_cost_stockman}&nbsp;{L_sys_dark_matter_sh}</td>
  </tr>

  <tr><th><a href="market.php?mode={D_MARKET_EXCHANGE}">{L_eco_mrk_exchange}</a></th><th>{C_rpg_cost_exchange}&nbsp;{L_sys_dark_matter_sh}</th></tr>
  <tr><th><a href="market.php?mode={D_MARKET_BANKER}">{L_eco_mrk_banker}</a></th><th>{C_rpg_cost_banker}&nbsp;{L_sys_dark_matter_sh}</th></tr>
  <tr><th><a href="market.php?mode={D_MARKET_PAWNSHOP}">{L_eco_mrk_pawnshop}</a></th><th>{C_rpg_cost_pawnshop}&nbsp;{L_sys_dark_matter_sh}</th></tr>
-->
</table>
