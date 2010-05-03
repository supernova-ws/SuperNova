<br>
<center>
<h2>OgameTr - Ayarlar</h2>

<form action="?mode=change" method="post">
 <table width="519">

     <tbody><tr><td class="c" colspan="2">OgameTr - Ayarlar</td></tr>
<tr>
      <th>Oyun Ýsmi<br><small>Oyunda Geçen Ýsim.</small></th>
   <th><input name="game_name" size="20" value="{game_name}" type="text"></th>
    </tr>
  <tr>
  <th>Oyun Yapýmcý Adý<br><small></small></th>
   <th><input name="copyright" size="40" maxlength="254" value="{copyright}" type="text"></th>
  </tr>

   <tr><th colspan="2"></th></tr>
  

	<!-- Planet Settings -->
  
  <tr>
  <td class="c" colspan="2">Gezegen Ayarlarý</td>
  </tr>
  <tr>
   <th>Gezegen Alaný</th>
   <th><input name="initial_fields" maxlength="80" size="10" value="{initial_fields}" type="text"> pól
   </th>
  </tr>
  <tr>
   <th>Kaç X ile Üreteceði</th>
   <th>x<input name="resource_multiplier" maxlength="80" size="10" value="{resource_multiplier}" type="text">
   </th>
  </tr>
  <tr>
   <th>Baþlangýç Metal</th>
   <th><input name="metal_basic_income" maxlength="80" size="10" value="{metal_basic_income}" type="text"> Baþlasýn
  </tr>
  <tr>
   <th>Baþlangýç Kristal</th>
   <th><input name="crystal_basic_income" maxlength="80" size="10" value="{crystal_basic_income}" type="text"> Baþlasýn
   </th>
  </tr>
  <tr>
   <th>Baþlangýç Deuterium</th>
   <th><input name="deuterium_basic_income" maxlength="80" size="10" value="{deuterium_basic_income}" type="text"> Baþlasýn
   </th>
  </tr>
  <tr>
   <th>Baþlangýç Enerji</th>
   <th><input name="energy_basic_income" maxlength="80" size="10" value="{energy_basic_income}" type="text"> 
   </th>
  </tr>

	<!-- Miscelaneos Settings -->

    <tr>
     <td class="c" colspan="2">Hata Monitörü</td>
	</tr>

  <tr>
     <th>Hata Monitörünü Göster</a></th>
   <th>
    <input name="debug"{debug} type="checkbox" />
   </th>
  </tr>

  <tr>
   <th colspan="2"><input value="Uygula" type="submit"></th>
  </tr>


   
 </tbody></table>

 
</form>

</center>
