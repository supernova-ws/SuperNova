<?php
/** Created by Gorlum 10.05.2025 23:27 */

namespace Fleet;

class Constants {
  // Mission outcome types
  /** @var int Something bad happens */
  const OUTCOME_TYPE_BAD = -1;
  /** @var int Nothing really happens */
  const OUTCOME_TYPE_NEUTRAL = 0;
  /** @var int Something good happens */
  const OUTCOME_TYPE_GOOD = 1;

  // Global mission outcomes
  /** @var int Outcome was not calculated yet */
  const OUTCOME_NOT_CALCULATED = -1;
  /** @var int Nothing happens during mission */
  const OUTCOME_NONE = 0;

  // Expedition outcomes
  /** @var int Some units were lost */
  const EXPEDITION_OUTCOME_LOST_FLEET = 1;
  /** @var int Found some units */
  const EXPEDITION_OUTCOME_FOUND_FLEET = 2;
  /** @var int Found some resources */
  const EXPEDITION_OUTCOME_FOUND_RESOURCES = 3;
  /** @var int Found Dark Matter */
  const EXPEDITION_OUTCOME_FOUND_DM = 4;
//  /** @var int Found Artifact */
//  const EXPEDITION_OUTCOME_FOUND_ARTIFACT = 5;
  /** @var int Fleet lost */
  const EXPEDITION_OUTCOME_LOST_FLEET_ALL = 6;

  /** @var int Mission expedition base chance that nothing will be found in 1 hour. Decreased with each hour spent in expedition */
  const OUTCOME_EXPEDITION_NOTHING_DEFAULT_CHANCE = 200;

  /** @var string Hook name to bind explore addons */
  const HOOK_MISSION_EXPLORE_ADDON = 'flt_mission_explore_addon';

  // Key names in arrays
  const K_OUTCOME = 'outcome';
  const K_OUTCOME_TYPE = 'outcome_type';
  const K_OUTCOME_SECONDARY = 'secondary';

}
