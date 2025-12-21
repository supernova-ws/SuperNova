<?php

require_once __DIR__ .'/../includes/debug.tools.php';

$override = file_get_contents(getcwd() . '/global_override.css');

$files = glob(getcwd() . '/skins/EpicBlueSnowy/gebaeude/*.png');


///* Unit image in list on Build page - single small image */
//div.unit_preview[id="unit1"][unit_id="1"] > img.unit_preview_image,
///* Selected unit on build page - single small image*/
//#unit_info #unit_info_image_wrapper > img#unit_info_image[unit_id="1"],
///* Novapedia Image - small or big */
//table.novapedia img[unit_id="1"]
//  /*table.novapedia .unit_image_large[unit_id="1"] {*/
//  /*table.novapedia .unit_image_small[unit_id="1"],*/
//{
//content: url("/skins/EpicBlueSnowy/gebaeude/0001.png");
//}

$template = <<<TEMPLATE

/* Unit image in list on Build page - single small image */
div.unit_preview[unit_id="%1\$d"] > img.unit_preview_image,
/* Selected unit on build page - single small image*/
#unit_info #unit_info_image_wrapper > img#unit_info_image[unit_id="%1\$d"],
/* Novapedia Image - small  */
table.novapedia img.unit_image_small[unit_id="%1\$d"]
{
content: url("/skins/EpicBlueSnowy/gebaeude/%1\$s.png");
}
/* Novapedia Image - large image */
table.novapedia img.unit_image_large[unit_id="%1\$d"] {
content: url("/skins/EpicBlueSnowy/gebaeude/%1\$s_large.png");
}
/* Tech page - small image */
div.tech_image[unit_id="%1\$d"] > img {
content: url("/skins/EpicBlueSnowy/gebaeude/%1\$s.png");
}
TEMPLATE;

$governors = <<<TEMPLATE

/* Unit image in list on Build page - single small image */
div.unit_preview[unit_id="%1\$d"] img
{
content: url("/skins/EpicBlueSnowy/gebaeude/%1\$s.png");
}
TEMPLATE;

$ships = <<<TEMPLATE

/* Unit image on fleet on orbit - small image */
div.ship_miniature_container[unit_id="%1\$d"]
{
background-image: url("/skins/EpicBlueSnowy/gebaeude/%1\$s.png") !important;
height: 120px !important;
width: 120px !important;
}
/* Ship image when sending fleet */
div.unit_miniatures_wrapper[unit_id="%1\$d"] img { content: url("/skins/EpicBlueSnowy/gebaeude/%1\$s.png"); }
TEMPLATE;

$css = [];
foreach ($files as $filepath) {
  $filepath = realpath($filepath);

  $filename = basename($filepath);

  $strUnitId = explode('.', $filename)[0];

  if(!is_numeric($strUnitId)) {continue;}

  $css[] = sprintf($template, $strUnitId);
  // Governors
  if($strUnitId >= 600 && $strUnitId < 700) {
    $css[] = sprintf($governors, $strUnitId);
  }
  // ships
  if($strUnitId >= 200 && $strUnitId < 400) {
    $css[] = sprintf($ships, $strUnitId);
  }
}

file_put_contents(getcwd() . '/design/css/global_override.css', $override . implode("\n", $css));