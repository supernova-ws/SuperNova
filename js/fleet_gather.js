var colonies = [];
var reCalcGatheringStarted = false;

function reCalcGathering() {
  var colonyValue, maxColonyValue, freeCapacity, colony, resourceID, addedValue;

  if (reCalcGatheringStarted) {
    return;
  }
  reCalcGatheringStarted = true;

  var resourceGrid = [0, 0, 0];
  for (colony in colonies) {
    colonyValue = 0;
    maxColonyValue = 0;
    freeCapacity = colonies[colony][3];

    for (resourceID in resourceGrid) {
      if (!resourceGrid.hasOwnProperty(resourceID)) {
        continue;
      }

      if (jQuery('#ga_' + colony + '_' + resourceID + '').is(':checked')) {
        addedValue = Math.min(freeCapacity, colonies[colony][resourceID]);
        freeCapacity -= addedValue;

        resourceGrid[resourceID] += addedValue;
        colonyValue += addedValue;
        maxColonyValue += colonies[colony][resourceID];
      }
    }

    jQuery('#ga_' + colony + '_a')
      .removeClass("negative zero positive")
      .addClass(colonyValue < maxColonyValue ? (colonies[colony][3] ? 'negative' : 'zero') : 'positive')
      .text(sn_format_number(colonyValue));
    // jQuery('#ga_' + colony + '_a').html(
    //   '<span class="' + (colonyValue < maxColonyValue ? (colonies[colony][3] ? 'negative' : 'zero') : 'positive') + '">'
    //   + sn_format_number(colonyValue)
    //   + '</span>'
    // );
  }

  jQuery('#ga_a_0').html(sn_format_number(resourceGrid[0]));
  jQuery('#ga_a_1').html(sn_format_number(resourceGrid[1]));
  jQuery('#ga_a_2').html(sn_format_number(resourceGrid[2]));
  jQuery('#ga_a_a').html(sn_format_number(resourceGrid[0] + resourceGrid[1] + resourceGrid[2]));

  reCalcGatheringStarted = false;
}

jQuery(document).ready(function(){
  reCalcGathering();
});
