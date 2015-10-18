function uni_set_ships(ship_data)
{
  ship_data = ship_data.split(",");
  for(ship_data_id in ship_data)
  {
    single_ship_data = ship_data[ship_data_id].split('=');
    jQuery('#' + single_ship_data[0]).html(single_ship_data[1]);
    if(single_ship_data[0] == 'missile')
    {
      jQuery('#SendMI').val(single_ship_data[1]);
      jQuery('#missile2').html(single_ship_data[1]);
    }
  }
}

function addToTable(strInfo, CmdCode)
{
  var e = document.getElementById('fleetstatusrow');
  if(!e)
  {
    return;
  }
  e.style.display = '';

  var e2 = document.getElementById('fleetstatustable');
  if(e2.rows.length > 2)
  {
    e2.deleteRow(2);
  }

  var td1 = document.createElement("td");
  td1.appendChild(document.createTextNode(strInfo));

  var row = e2.insertRow(0);
  row.className = CmdCode ? "error" : "success";
  row.appendChild(td1);

  jQuery('#ov_recycle').hide();
}

function doit(missionId, planet, planettype, shipcount) {
  var uni_send_fleet_params = {
    "mission": missionId,
    "galaxy": uni_galaxy,
    "system": uni_system,
    "planet": planet,
    "planet_type": planettype,
    "fleet": Array(),
  };

  if(missionId == MT_MISSILE) {
    jQuery.extend(uni_send_fleet_params,{
      "missiles": document.uni_missile_form.SendMI.value,
      "structures": document.uni_missile_form.Target.value,
    });
  }

  jQuery.post("flotenajax.php?action=send", uni_send_fleet_params, function(data) {
    if(data.indexOf('|') === -1) {
      addToTable(data, 1);
      popup_show($('#message_template').html().replace(/\[MESSAGE\]/g, data).replace(/\[CLASS\]/g, 'error_bg'));
      return;
    }
    retVals   = data.split("|");
    strInfo   = retVals[0];
    addToTable(retVals[0], 0);
    uni_set_ships(retVals[1]);
    popup_show($('#message_template').html().replace(/\[MESSAGE\]/g, retVals[0]).replace(/\[CLASS\]/g, 'ok_bg'));
  });
}
