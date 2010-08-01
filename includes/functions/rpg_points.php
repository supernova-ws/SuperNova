<?php

function rpg_pointsAdd($userID, $darkMatter, $comment = false){
  global $debug, $config;

  if(!($darkMatter && $userID)) return false;

  doquery("UPDATE {{users}} SET rpg_points = rpg_points + '$darkMatter' WHERE `id` = {$userID}");
  $rowsAffected = mysql_affected_rows();
  if($rowsAffected){
    $debug->warning("Player ID {$userID} Dark Matter was adjusted with {$darkMatter}. Reason: {$comment}", 'Dark Matter Change', 102);

    if($darkMatter>0){
      $oldReferral = doquery("SELECT * FROM {{referrals}} WHERE `id` = {$userID}", '', true);
      if($oldReferral['id']){
        doquery("UPDATE {{referrals}} SET dark_matter = dark_matter + '$darkMatter' WHERE `id` = {$userID}");
        $newReferral = doquery("SELECT * FROM {{referrals}} WHERE `id` = {$userID}", '', true);

        $partnerBonus = floor($newReferral['dark_matter']/$config->rpg_bonus_divisor) - floor($oldReferral['dark_matter']/$config->rpg_bonus_divisor);
        if($partnerBonus > 0)
          rpg_pointsAdd($newReferral['id_partner'], $partnerBonus, "Incoming From Referral ID{$userID}");
      }
    }
  }else{
    $debug->warning("Error adjusting Dark Matter for player ID {$userID} with {$darkMatter}. Reason: {$comment}", 'Dark Matter Change', 402);
  }
  return $rowsAffected;
}
?>