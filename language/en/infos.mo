<?php
/*
#############################################################################
#  Filename: infos.mo
#  Create date: Saturday, March 29, 2008	 21:58:24
#  Project: prethOgame
#  Description: RPG web based game
#
#  Copyright © 2009 - 2010 Gorlum for http://supernova.ws
#  Copyright © 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright © 2005 - 2008 KGsystem
#############################################################################
*/
if (!defined('INSIDE')) 
{
	die('Hack attempt!');
}

global $config;

$lang = array_merge($lang, array(
  // ----------------------------------------------------------------------------------------------------------
  // Interface !
  'info_title_param'  => 'Parameter',
  'info_title_base'   => 'Base',
  'info_title_actual' => 'Current',

  'nfo_page_title'  => 'Information',
  'nfo_title_head'  => 'Information',
  'nfo_name'        => 'Name',
  'nfo_destroy'     => 'Destruction',
  'nfo_level'       => 'level',
  'nfo_range'       => 'Distance sensors',
  'nfo_used_energy' => 'Power consumption',
  'nfo_used_deuter' => 'Deuterium consumption',
  'nfo_prod_energy' => 'Energy production',
  'nfo_difference'  => 'Difference',
  'nfo_prod_p_hour' => 'Production per hour',
  'nfo_needed'      => 'Requirements',
  'nfo_dest_durati' => 'Time to kill',

  'nfo_struct_pt'   => 'Structural (life)',
  'nfo_shielf_pt'   => 'Shield',
  'nfo_attack_pt'   => 'Attack power',
  'nfo_rf_again'    => 'One attack kill',
  'nfo_rf_from'     => 'one attack kill',
  'nfo_capacity'    => 'Capacity',
  'nfo_units'       => 'units',
  'nfo_base_speed'  => 'Base speed',
  'nfo_consumption' => 'Fuel consumption (Deuterium)',

  'info' => array(
    1 => array(
      'description' => 'The main supplier of raw materials for the construction of load-bearing structures of buildings and ships. Metal is the most inexpensive raw material, but takes more than everything else. For production of metal requires less total energy. Than mines more deeper. on most planets metal is at great depths, deeper mines you can obtain more metals, production increases. At the same time, larger mines require more energy.',
      'description_short' => 'The main supplier of raw materials for the construction of load-bearing structures of buildings and ships.'
    ),

    2 => array(
      'description' => 'For the synthesis of crystals requires approximately twicw more enery than for extraction of metal, so it therefore appreciated more. Crystals-main part of any modern computers and a key componet of warp drive-engines. Therefore, it is required for all ships and almost all buildings. Improving synth increases the number of produced crystals.',
      'description_short' => 'The main supplier of raw materials for computer systems and warp-drives.'
    ),

    3 => array(
      'description' => 'Deuterium is heavy hydrogen. Because of this, as in the mains, more large stockpiles are at the bottom of the sea. Improving synth also contributes to development of these deep deposits of deuterium. Deuterium is needed as fuel for ships, nearly all studies, see the galaxies, and use sensor phalanx',
      'description_short' => 'Extracts from the water on the planet, a small percentage of deuterium.'
    ),

    4 => array(
      'description' => 'To ensure energy mines and synthesizers are huge solar power plants. The more built stations,the greater the surface is covered with solar panels that transform light energy into electricity. Solar power plants are the foundation of the energy world.',
      'description_short' => 'Produces energy from sunlight. Energy is required for the majority of buildings.'
    ),

    12 => array(
      'description' => 'On thermalnuclear power plants using fusion under enormous pressure and high temperature 2 heavy hydrogen atoms are combined into one helium atom. However, when a helium nucleus, produces energy in 41,32*10^-13 J in the form of radiation (thus 1 g of hydrogen combustion produces 172 mwat of Power). The larger thermalnuclear reactor, the more complex synthesis processes, the reactor produces more energy.<br><br>Production: formula<bróðîâåíü>30 * [TE] * (1,05 + [energy technology] * 0,01) ^ [TE]<br>Extraction of energy also can be increased at the expense of the development of energy technology.',
      'description_short' => 'Extracts energy from education Atom helium two heavy hydrogen atoms.'
    ),

    14 => array(
      'description' => 'Provides a simple labor, which can be used in the construction of a global infrastructure. Each level increases the speed of factory building.',
      'description_short' => 'Manufactures machines and mechanisms that are used in construction of a global infrastructure. Each level increases the speed of development of factory building.'
    ),

    15 => array(
      'description' => 'nanobots are the final evolution of robotic factories. The only equipment-nanobots to manipulate individual molecules and even atoms of matter. Since their invention made possible the production of virtually any material with predefined properties. Moreover, thanks to nanobots you can quickly produce finished parts of any forms and configurations. But invention nanobots revoke conventional plants. Although nanobots can produce any design, many things still energetically more favorable to &quot;old fashioned&quot;. But even with such restrictions each level nanobots shortens the time of construction of any buildings, defences, and ships by half.',
      'description_short' => 'Nanobots are specialized complexes to construct objects from individual molecules and atoms. Each level increases the speed of buildings, defences, and ships by twice.'
    ),

    21 => array(
      'description' => 'Shipyard can produce all types of ships and defences. The faster you can build more complex and larger ships and defensive structures. By constructing factories nanites factory are simplified many chains that can dramatically improve the performance of the shipyard.',
      'description_short' => 'Shipyard produces spaceships, orbital structures and defences.'
    ),

    22 => array(
      'description' => 'Great location for extracted ores. The higher the level, the more metal you can store in it. If it is filled up, the extraction of metal ends.',
      'description_short' => 'Storage for metal.'
    ),

    23 => array(
      'description' => 'Great location for extracted ores. The higher the level, the more crystal you can store in it. If it is filled up, the extraction of crystal ends.',
      'description_short' => 'Storage for crystal.'
    ),

    24 => array(
      'description' => 'Great location for extracted fuels. The higher the level, the more deuterium you can store in it. If it is filled up, the extraction of deuterium ends.',
      'description_short' => 'Storage for deuterium.'
    ),

    31 => array(
      'description' => 'To study new technologies requires a working research station. Level of development research station is critical factor in how quickly could be developed new technologies. The higher the level of development research laboratory, the more can be researched new technologies. In order to complete as soon as the research work on the same planet, it sends all available scientists and then leave their planet. Once the technology is investigated, the scientists are returning to their home planet and carry with them knowledge about it. So new technologies can be applied on other planets.',
      'description_short' => 'Laboratory is for researching new technologies.'
    ),

    33 => array(
      'description' => 'As more and more important building planets became the limited usable space. Traditional methods such as construction skyward and inside were insufficient. A small group of physicists and nanotehnikov found the solution-terraformer.<br><br>Spending large amounts of energy terraformer can convert vast areas and even the entire continents, making them suitable for construction. This structure is constantly produced special nanity, responsible for the constant quality of soil.',
      'description_short' => 'Terraformer can transform a huge territory, increasing the number of construction fields.'
    ),

    34 => array(
      'description' => 'Alliance depot provides a way to ensure fuel friendly fleets that help with defense and are in orbit. The higher the level of development, the more deuterium can be sent to fleets in orbit.',
      'description_short' => 'Alliance depot provides a way to ensure fuel to friendly fleets that help with defense and are in orbit.'
    ),

    35 => array(
      'description' => 'Reduce time to research stage doubled.',
      'description_short' => 'Nanobots equipped with the latest technology. Heavy duty crystalline computers and super-precise nanosbots accelerated by any study by twice.'
    ),

    41 => array(
      'description' => 'The moon has no atmosphere, therefore before the planned stay is required to build Lunar bases. It provides the necessary air, Gravitation and warmth. The higher the level of development of the lunar base, the more secure the biosphere area. Each level of the lunar base can build 3 sector, up to a maximum total square of the moon. It is 2 (diameter of the Moon/1000) ^ 2, each level lunar base itself occupies one field.',
      'description_short' => 'The moon has no atmosphere, therefore before the planned stay is required to build lunar base.'
    ),

    42 => array(
      'description' => 'High frequency sensors full browsing frequency spectrum by all the falangu radiations. Powerful computers combines tiny fluctuations in energy and thus gain information about the movements of ships at the distant planets. To view the Moon should be given the energy in the form of deuterium (5 000 per view). View is the transition from the Moon menu Galaxy and the title enemy planet located in range sensors (formula: (level phalanges) ^ 2-1).',
      'description_short' => 'High frequency sensors full browsing frequency spectrum by all the ship fuel radiation.'
    ),

    43 => array(
      'description' => 'Gate is a huge teleportery that can transmit between the fleets of all sizes without time-consuming. These teleportery do not require deuterium, but between two hops must undergo one hour, or gate recharge. Is also forwarding resources. The entire process requires very highly developed technology.',
      'description_short' => 'Gate is a huge teleportery that can transmit between the fleets of all sizes without time-consuming.'
    ),

    44 => array(
      'description' => 'Missile silo serve for storing missiles. With each level you can store five interplanetary or ten interceptor missiles anymore. One interplanetary missiles require space twice the interceptor missile. May be any combination of different types of missiles.',
      'description_short' => 'Missile silo allows firing rockets and missile plus storage.'
    ),

    TECH_SPY => array(
      'description' => 'Espionage is designed to explore new and better sensors. The higher is this technology, the more information the player has in his neighborhood. Difference in levels of espionage an opponent plays a crucial role to play-the more we investigate own spyware technology, the more information can be found in the intelligence and the less chance of being detected. The more sent probes, the more details about the enemy, is collected but is in danger of being discovered. The espionage improves locating foreign fleets. Also important is the level of development of own spying. Starting with the second level of development when a denial you also reports about the attack and also shows the total number of attacking ships. The fourth level detected by type of attacking ships, as well as their total number, but from the eighth to the exact number of each type of ships. For nalëtcikov this technology is very important because it provides information about whether the victim has invoiced fleet and/or protection or not, so you should investigate it as soon as possible. Best-after studies of small transports.',
      'description_short' => 'Using this technology produces data on other planets.'
    ),

    TECH_COMPUTER => array(
      'description' => 'Computer technology is designed to increase availability of computer power. As a result of the planet are more productive and efficient computer systems, increasing processing power and speed of computing processes. With the increasing power of computers you can command the entire of fleets. Each level of development of computer technology makes it possible to command + 1 fleet. The more sent fleets, the more you can do  and thus capture more raw materials. Naturally, this technology is useful and traders, as it enables them to simultaneously send larger merchant fleets. For this reason, you should constantly develop computer technology throughout the entire game.',
      'description_short' => 'With the increasing power of computers you can command the entire many fleets. Each level of computer technology increases the maximum number of fleets by 1.'
    ),

    TECH_WEAPON => array(
      'description' => 'Weapon technology focuses on the further development of weapons systems. Particular importance is attached to implementing existing systems more energy and more precisely this energy channel. The weapons systems become more efficient, and weapons causes more devastation. Each level increases the power of weapons technology weapons military units by 10%. Weapons technology is important in competitive content of parts. Why should constantly develop throughout the game.',
      'description_short' => 'Weapon technology makes weapons systems more efficient. Each level increases power weapons military units at 10% of the basic.'
    ),

    TECH_SHIELD => array(
      'description' => 'Development of this technology allows you to increase the supply of energy shields and shielding, which in turn increases their resilience and ability to absorb or reflect energy attacks of the enemy. Thanks to this with each passing level effectiveness of ship\'s shields and stationary shield generators increases by 10% of the rated power.',
      'description_short' => 'This technology examines more new features greater energy shields that make them more efficient. Thanks to this with each passing level effectiveness of shields is increased by 10%.'
    ),

    TECH_ARMOR => array(
      'description' => 'Special alloys improve armor spacecraft. Once found very resistant alloy, special beams are changing the molecular structure of a spacecraft, and brings it to a known alloy. The sustainability of armor can increase with each level at 10%.',
      'description_short' => 'Special alloys improve armor spacecraft. With each level strength armor increased by 10 % of the base value.'
    ),

    TECH_ENERGY => array(
      'description' => 'Energy technology is a further development of transmission systems and energy storage that are required for many new technologies.',
      'description_short' => 'Study of energy technology improves impact energy and power.'
    ),

    TECH_HYPERSPACE => array(
      'description' => 'By Plexus 4th and 5th dimension has become possible to explore new, more economical and efficient engine.',
      'description_short' => 'By Plexus 4th and 5th dimension has become possible to explore new, more economical and efficient engine.'
    ),

    TECH_ENGINE_CHEMICAL => array(
      'description' => 'Jet engine is based on the principle of effectiveness. Fabric, hot to elevated temperatures, thrown in the opposite direction and gives faster ship. The effectiveness of these engines is small enough, but they are quite reliable, cheap production and services. Furthermore they take up much less space on the ship than the other engines, so they are still quite often can be found on small ships. Because the rocket engines are the foundation of any flight into space, should examine them as soon as possible. Further development of these engines makes the following ships with each level at 10% faster: small transports (until a researched pulse engine the 5th level), large transports, light fighters, processors and spy probes.',
      'description_short' => 'Further development of these engines makes some ships faster, but each level increases the speed of only 10%.'
    ),

    TECH_ENIGNE_ION => array(
      'description' => 'Impulse engine is based on the principle of effectiveness, and warming up of matter are the nuclear reaction. You can also inject additional mass. Further development of these engines makes the following ships with each level to 20% faster: small transportation, bombers (until a researched hyperspace engine 8th level), cruisers, heavy fighter and colonizers. Each level increases the reach of interplanetary missiles.',
      'description_short' => 'Impulse engine is based on the principle of effectiveness. Further development of these engines makes some ships faster, but with each level increases the speed of only 20%.'
    ),

    TECH_ENGINE_HYPER => array(
      'description' => "By spatio-temporal curvature in the immediate environment of spacecraft space shrinks, the faster the overcome long distances. The higher developed Hyperspace drive, the higher the compression space, which makes the following ships with each level of 30% faster: {$lang['tech'][SHIP_CRUISER]}, {$lang['tech'][SHIP_BOMBER]}, {$lang['tech'][SHIP_DESTRUCTOR]}, {$lang['tech'][SHIP_DEATH_STAR]}, {$lang['tech'][SHIP_BATTLESHIP]} and {$lang['tech'][SHIP_SUPERNOVA]}.",
      'description_short' => 'By spatio-temporal curvature in the immediate environment of spacecraft space shrinks, the faster the overcome long distances. The higher the developed hyperspace drive, the higher the compression space, with each level of the speed of ships rises up 30%.'
    ),

    TECH_LASER => array(
      'description' => 'Laser (light amplification using induced emission of radiation) produces rich energy beam of coherent light. These devices are used in all areas of optical computers before heavy lasers that freely cut armor spacecraft. Laser technology is an important element for the study of further weapons technology.',
      'description_short' => 'Thanks to focus light rays that occurs when an object causes him injury.'
    ),

    TECH_ION => array(
      'description' => 'Truly deadly beam of accelerated ions. In contact with an object they cause immense damage.',
      'description_short' => 'Truly deadly beam of accelerated ions. In contact with an object they cause immense damage.'
    ),

    TECH_PLASMA => array(
      'description' => 'Further development of the ion technology which accelerates the ions, and energetic plasma. She has had a devastating effect in contact with an object.',
      'description_short' => 'Further development of the ion technology which accelerates the ions, and not vysokoènergeticeskuû plasma. She has had a devastating effect in contact with an object.'
    ),

    TECH_RESEARCH => array(
      'description' => 'This network enables communication scientists working in research laboratories from different planets. Each new level allows you to attach to the network for additional laboratory (primarily attached laboratory senior levels). Of all United in a network of laboratories in each study involved only those that are sufficient to conduct the study level. Speed study FY08 levels involved in it laboratories.',
      'description_short' => 'This network enables communication scientists working in research laboratories from different planets. Each new level allows you to attach to the network for additional laboratory.'
    ),

    TECH_EXPEDITION => array(
      'description' => 'Expedition technology encompasses various scanning technology and makes it possible to equip the ships of different classes of research module. It contains a database, a small mobile laboratory, as well as various biokletki and vessels for samples. For the safety of the ship when investigating hazardous objects research module is equipped with an autonomous energy and generator of energy field, which in extreme cases can surround a powerful energy field research module.',
      'description_short' => 'Now you can equip ships providing research module processing the collected data in long flights.'
    ),

    TECH_COLONIZATION => array(
      'description' => 'Ruler with many colonies, has more advantages over others.',
      'description_short' => 'This technology is very important that you could build your Empire with many colonies.'
    ),

    TECH_GRAVITON => array(
      'description' => 'Graviton is a particle that has neither mass nor charge and determines the force of attraction. By launching the concentrated charge gravitonov can create artificial gravitational field that, like a black hole, dragging a ton, so you can destroy ships or even the moon. To produce a sufficient quantity of gravitonov requires huge amounts of energy.',
      'description_short' => 'Graviton is a particle that has neither mass nor charge and determines the force of attraction. By launching the concentrated charge gravitonov can create artificial gravitational field that, like a black hole, dragging a ton, so you can destroy ships or even the Moon.'
    ),

    SHIP_CARGO_SMALL => array(
      'description' => 'Transports have approximately the same size as the fighter aircraft, but they don\'t have powerful engines and onboard weapons to save space. Small transport accommodates 5,000 units of stuff. Because small firepower small transports are often accompanied by other ships when pulse engine researched up to 5th grade, small transport improves the speed and it is equipped with a base that type engine.',
      'description_short' => 'Small transport is highly maneuverable craft, which can easily transport the raw material to other planets. After research impulse drive 5th level ships are being refurbished.'
    ),

    SHIP_CARGO_BIG => array(
      'description' => 'Aboard this ship there is only weak armament and no serious technology ... For this reason, they should never be run unattended. Thanks to its advanced Jet engine large transportation serves as a rapid interplanetary dostavsika resources, as it accompanies the fleets to attack the enemy planet to grab as many resources as possible.',
      'description_short' => 'Further development of small transports a ships with greater capacity and more developed an engine that can travel faster than light travel, until small transports not installed impulse engines 5th level.'
    ),

    SHIP_CARGO_SUPER => array(
      'description' => 'The last word in technology transfer. Supertransport-giant transport ship equipped with impulse engines. Its speed is low and the fuel consumption is very high, but it completely pays off an extraordinary sitting capacity. Supertransport is equipped with a laser turrent, but has a powerful shield.',
      'description_short' => 'A giant self-propelled barge equipped with impulse engines. Equipped with a powerful shield, but of weapons has only laser turrent.'
    ),

    SHIP_FIGHTER_LIGHT => array(
      'description' => 'Lightweight fighter is highly maneuverable craft, which can be found on almost every planet. Cost is not too big, but the shielding power and capacity of very small.',
      'description_short' => 'Lightweight fighter is highly maneuverable craft, which can be found on almost every planet. Cost is not too big, but the shielding power and capacity of very small.'
    ),

    SHIP_FIGHTER_HEAVY => array(
      'description' => 'In further development of the lightweight fighter scientists came to a point where it became clear that an ordinary engine lacks power. In order to optimally remove ship, was first used pulse engine. Though it has cost, but it also opened new possibilities. Thanks to this engine has more power for weapons and shields, moreover, for this kind of fighter aircraft were also used valuable materials. This has led to improved structural integrity and stronger firepower, melee weapons, it represents a greater threat than its predecessor. After these changes Beaufighter represents a new era of technology, shipbuilding basis technology.',
      'description_short' => 'Further development of the lightweight fighter, it is better to secure and has a stronger attack.'
    ),

    SHIP_DESTROYER => array(
      'description' => 'With the development of heavy lasers and ion guns, more and more marginalized by heavy fighters. Despite the many improvements in firepower and armor cannot be so modified to efficiently counter these defensive guns. It was therefore decided to build a new class of ships, which would have more firepower and armor. So were the destroyers. Destroyers protected almost three times stronger than heavy fighters and more than twice the firepower. They are very quick. There is no better weapons against high defense. Nearly a century cruisers unlimited dominated in the universe. With the advent of Gauss guns and plasma guns their rule came to an end. However, today they are used against groups of fighters.',
      'description_short' => 'Destroyers protected almost three times stronger than heavy fighters and firepower they surpass heavy fighters almost doubled. They are very fast.'
    ),

    SHIP_CRUISER => array(
      'description' => 'Cruisers tend to form the backbone of the fleet. Their heavy guns, high speed and large cargo make them serious opponents.',
      'description_short' => 'Cruisers tend to form the backbone of the fleet. Their heavy guns, high speed and large cargo make them serious opponents.'
    ),

    SHIP_COLONIZER => array(
      'description' => "This well protected boat serves conquer new planets that need expanding Empire. It is used in the new colonies as the supplier of raw material and use it to take apart-its useful material for the development of the new world. Each Empire may have a maximum of {$config->player_max_colonies} colonies (excluding home planet).",
      'description_short' => 'This ship can absorb planet.'
    ),

    SHIP_RECYCLER => array(
      'description' => 'Space battles were all lost. Destroyed thousands of ships and encountered the wreckage seemed forever lost. Normal transports couldn\'t come close to them without being badly corrupted small fragments. With the new opening in shield technology it has become possible to effectively address this problem, a new class of ship, similar large transport-processor. With its help you can reuse the seemingly lost resources. Due to the new boards are small fragments no longer represented danger. Unfortunately, these devices require space, so its cargo tonnage is limited to 20 000.',
      'description_short' => 'Using the raw material is extracted from the wreckage of tire.'
    ),

    SHIP_SPY => array(
      'description' => 'Spy probes are small mobile that the ships which deliver with distances of fleets and planets. Their high-performance engine allows them to travel long distances for a few seconds. Once it lands on the orbit of a planet, they are there for some time to build the data. At this time the enemy is relatively easy to detect them and attack. To save space not found no armor or shields, no guns, making the sounders in case light objectives.',
      'description_short' => 'Spy probes are small mobile that the ships which deliver with distances of fleets and planets.'
    ),

    SHIP_BOMBER => array(
      'description' => 'Bomber was developed specifically to destroy the planetary protection. Using a laser sight it just resets plasma bombs on the surface of the planet and thus causes enormous damage to the defensive structures when hyperspace engine researched up to 8th grade, the bomber is a basic speed and it is equipped with this type of engine.',
      'description_short' => 'Bomber was developed specifically to destroy the planetary protection.'
    ),

    SHIP_SATTELITE_SOLAR => array(
      'description' => 'Solar satellites are launched into orbit of the planet. They collect solar energy and transmit it to the ground station. Efficiency solar satellites depends on the strength of solar radiation. In principle, the extraction of energy orbits more approximate to the Sun is higher than on the planets, remote from the Sun. Due to its price/quality ratio solar satellites solve energy problems of many worlds. But attention: solar satellites can be destroyed in battle.',
      'description_short' => 'Solar satellites is a simple platform of solar cells, which are located on high orbit. They collect sunlight and expose it using laser ground station.'
    ),

    SHIP_DESTRUCTOR => array(
      'description' => 'Destructor - King of warships. The ion, plasma and gauss turrets can thanks to its advanced sensor to a 99% even high-speed mobile that the fighters. Since destroyers are very large, their agility is very limited, and in battle they resemble rather a battle station than combat ship. Deuterium consumption are also appreciated as their fighting power.',
      'description_short' => 'Destructor - King among military ships.'
    ),

    SHIP_DEATH_STAR => array(
      'description' => 'Death star is equipped with a giant graviton gun that could destroy all types of ships and even the moon. To produce enough energy, celebrity deaths almost entirely consists of generators. Only huge stellar Empire have enough resources and workers for the construction of this huge ship.',
      'description_short' => 'Death star is equipped with a giant graviton gun that can destroy ships and even the Moon. '
    ),

    SHIP_BATTLESHIP => array(
      'description' => 'This high-tech ship brings death intruder fleets. Its advanced laser guns keep heavy enemy ships at a distance and can destroy several units one in one gulp. Due to its small size and incredibly powerful weapons, lenght linear Cruiser is very small, but at the expense of hyperspatial engine as little fuel consumption.',
      'description_short' => 'Starcruiser specializes in capturing enemy fleets.'
    ),

    SHIP_SUPERNOVA => array(
      'description' => 'You are granted a ship from the emperor for your cruelty skills.',
      'description_short' => "{$lang['tech'][SHIP_SUPERNOVA]} - the flagship of the fleet of the Empire. The huge cost of erection with compensated terrible firepower and advanced protection. One ship of this class is able to defeat the average fleet alone."
    ),

    401 => array(
      'description' => 'Launcher is a simple and inexpensive means of defence. Because this is the development of conventional ballistic artillery shells, it does not require further upgrading. The small cost of its production justifies its use against smaller fleets, but over time it loses value. Later it is used only for withdrawal of enemy shots. Defensive deactivates itself once they severely damaged. Recoverability of the fortifications after a battle with up to 70%.',
      'description_short' => 'Launcher is a simple and inexpensive means of defence.'
    ),

    402 => array(
      'description' => 'To offset excessive gains in technology spacecraft, scientists had to create a defensive construction, spravlâûseesâ with larger and better armed fleets. This has led to light laser. Using the concentrated attack targets photons can achieve far greater devastation than conventional ballistic weapons. To confront stronger firepower of new types of ships, he also has improved. However, the cost of production remained low, not escalated further structure. Light laser has the best price/quality ratio, so it also interesting and for more advanced civilizations. Defensive deactivates itself once they severely damaged. Recoverability of the fortifications after a battle with up to 70%.',
      'description_short' => 'Using the concentrated attack targets photons can be achieved much great destruction than conventional ballistic weapons.'
    ),

    403 => array(
      'description' => 'Heavy laser represents a further development of the laser light. Structure has been reinforced and improved with new materials. Wrapper could do much more resistant. At the same time has been improved and energy system and the target computer, so heavy laser can concentrate much more on target. Defensive deactivates itself once they severely damaged. Recoverability of the fortifications after a battle with up to 70%.',
      'description_short' => 'Heavy laser represents a further development of laser light.'
    ),

    404 => array(
      'description' => 'Long regarded as obsolete artillery against the background of the development of modern laser and ion technology and continuously improving protection until the new research in the field of energy technology prevented raise artillery. In fact, principles of electromagnetic electron masses were known on Earth since the 20th century. One of them-Gauss cannon. With the decision of the technical difficulties to use it as a weapon. Mnogotonnye shells are accelerated magnetic field when the enormous cost of energy and have the output speed of the particles of dust in the air around shells are burned, as well audible wave rocks the Earth. Even the modern armor and shields have difficulty resisting punchy force Gauss guns, and often it happens that objective just prostrelivaetsâ through and through. Defensive deactivates itself once they severely damaged. Recoverability of the fortifications after a battle with up to 70%.',
      'description_short' => 'Coilgun accelerates mnogotonnye charges with gigantic energy costs.'
    ),

    405 => array(
      'description' => 'In the 21st century on Earth already existed that commonly known as AMY. AMY means electromagnetic pulse, which has the ability to induce additional voltage in pattern and thereby causing massive interference that could destroy all sensitive devices. Then AMY-shells were largely based missiles and bombs, also in combination with nuclear weapons. Meanwhile, EMP Cannon constantly evolved since they saw great potential not to destroy the target, and make them incapable of battle and manëvrennosti and, thus, simplify capturing them. So far, the highest form of EMP shells provided with ion gun. It sends for the purpose of a wave of ions (electrically charged particles), which destabilizes the shields and damages the electronics, only if it is not very well protected, sometimes like a complete destruction. Kinetic punchy force can be neglected. Ionic technology is only used on krejserah, as well as energy consumption is enormous, and ion cannons in combat often destroy rather than paralyze target. Defensive deactivates itself once they severely damaged. Recoverability of the fortifications after a battle with up to 70%.',
      'description_short' => 'Ion Cannon directs to the purpose of wave ion, which destabilizes the shields and damages the electronics.'
    ),

    406 => array(
      'description' => 'Laser technology was brought to perfection, Ionic technology has reached the final stage and was thought to be virtually impossible, even qualitatively Cannon system to achieve even greater efficiency. But everything was to change when the idea to combine both systems. Using the technology of nuclear fusion, laser heat the substance (usually a deuterium) up to ultrahigh temperatures reaching millions of degrees. Ionic technology provides enrichment plasma electric charge, its stabilization and acceleration. Once electric charge sufficiently warmed, ionized and is under pressure, it produce using accelerators in the direction of the goal. Glowing bluish color plasma Bowl looks awesome, the only question is, how long they will enjoy the command ship-purpose if after a few seconds, the armor he explodes into pieces and Electronics will burn ... Plasma turret is generally the most frightening weapons, and this technology represents a trade-off. Defensive deactivates itself once they severely damaged. Recoverability of the fortifications after a battle with up to 70%.',
      'description_short' => 'The last word in Planetary Defense technologies created a symbiosis of laser and Ion tech.'
    ),

    407 => array(
      'description' => 'Long before the shield generators become sufficiently small to be used on ships, there is a huge generators on the surface of planets. They obvolakivali an entire planet force field that can absorb the shock of the attack. Small fleets are constantly attacking on these billboards dome. Due to the growing technological development of these boards can be even greater. Later, you can build stronger large shield dome. On every planet you can build only one small shield dome.',
      'description_short' => 'Small shield protects the planet and absorbs shock attack.'
    ),

    408 => array(
      'description' => 'Further development of the small shield dome. It can deter even stronger attack to planet, consuming significantly more energy.',
      'description_short' => 'Further development of the small shield dome. It can deter even stronger attack to planet, consuming significantly more energy.'
    ),

    409 => array(
      'description' => 'The best protection for your planets',
      'description_short' => 'The best protection for your planets'
    ),

    502 => array(
      'description' => 'Rocket interceptors destroy attacking interplanetary missiles. An interceptor missile destroys one interplanetary missile.',
      'description_short' => 'Rocket interceptors destroy attacking interplanetary missiles'
    ),

    503 => array(
      'description' => 'Interplanetary missiles to destroy the enemy\'s protection. Deleted mežplanetnymi missile defensive structures no longer restored.',
      'description_short' => 'Interplanetary missiles destroy enemy defenses'
    ),


    MRC_TECHNOLOGIST => array(
      'description' => 'Technologist is a recognized expert in astromineralogy and crystallography. With his team of Metallurgists and chemists, he supported the interplanetary Government when developing new resources and optimize their refinery.',
      'effect' => 'to metal, crystal and deuterium production, to energy production on solar and fusion stations each level.<br>At 5th level allows to build fusion station'
    ),

    MRC_ENGINEER => array(
      'description' => 'Engineer-the most up-to-date Builder of the Empire. His DNA was subjected to mutations, which provided him with an extraordinary mind and sverhceloveceskuû force. Architect alone can design and build a city.',
      'effect' => 'to construction time of buildings and ships each level'
    ),

    MRC_FORTIFIER => array(
      'description' => 'Fortifier - Army Engineer. His in-depth knowledge of defensive systems allow you to shorten planet defense building time',
      'effect' => 'to construction time of missiles and defense structures<br />
        +10% to atack, armor and shields when defending planet<br />
        At 3rd level allows building of Planetary Defense'
    ),


    MRC_STOCKMAN => array(
      'description' => 'Cargo-master is a highly skilled specialist in storage. His genius allows you to get the most out of storage resources to increase their effective capacity beyond the builders.',
      'effect' => 'size of warehouses for each level'
    ),

    MRC_SPY => array(
      'description' => 'Spy-master person Empire. He had hundreds of thousands of individuals and a million ideas for mask works, defensive networks and fleets. Everyone who saw his real face, is now dead.',
      'effect' => 'level of spying for each level'
    ),

    MRC_ACADEMIC => array(
      'description' => 'Academicians are actors Guild Technocrats. Their mind and scholars degree allow them Excel in their acts even constructors. They specialize in the field of technological progress.',
      'effect' => 'by the time of studies for each level'
    ),

    MRC_DESTRUCTOR => array(
      'description' => 'Devastator - a ruthless army officer. He suggests how the planets Empire brutal methods. The same Destructor has developed technology manufactures Stars Death.',
      'effect' => "Allows to build {$lang['tech'][SHIP_DESTRUCTOR]} in the shipyard"
    ),


    MRC_ADMIRAL => array(
      'description' => 'Admiral is tried by war veteran and a brilliant strategist. Even in the hottest fights he doesn\'t lose a review and maintains contact with commanders fleets. The wise ruler can rely on him in battle and thereby use to battle more ships.',
      'effect' => 'armor, shields and attack ships for each level'
    ),

    MRC_COORDINATOR => array(
      'description' => 'The Coordinator is an expert in managing fleets. His knowledge can make the most of the fleet management system.',
      'effect' => 'additional fleet for each level'
    ),

    MRC_NAVIGATOR => array(
      'description' => 'Navigator-genius in calculating the trajectories of fleets. His knowledge of laws warp drive-space device jump-drive and technologies all existing types of engines can speed flying ships.',
      'effect' => 'speed of ships for each level'
    ),

    MRC_ASSASIN => array(
      'description' => "Assasin-trusted killer Emperor. But it's not only its quality. However, Disruptor, Assasin has developed {$lang['tech'][SHIP_SUPERNOVA]}. Central computer ships configured to DNA Assasina. Therefore, it is the only person who can manage this ship",
      'effect' => "Allows to build {$lang['tech'][SHIP_SUPERNOVA]} in the shipyard"
    ),

    MRC_EMPEROR => array(
      'description' => 'Emperor - your personal assistant and Deputy. The accuracy of its reports and punctuality in everything-his best qualities, capable of total control over the Empire.',
      'effect' => 'Allows you to change the characteristics of the Emperor'
    )
  )
));

?>
