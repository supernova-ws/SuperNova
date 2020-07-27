<?php

/*
#############################################################################
#  Filename: infos.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Strategy Game
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#  Copyright © 2008 Aleksandar Spasojevic <spalekg@gmail.com>
#  Copyright © 2005 - 2008 KGsystem
#############################################################################
*/

/**
*
* @package language
* @system [English]
* @version 45d0
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


$a_lang_array = array(
  'wiki_title' => 'Novapedia',

  'wiki_char_nominal' => 'Nominal',
  'wiki_char_actual' => 'Actual',

  'wiki_ship_engine_header' => 'Engine characteristics',

  'wiki_ship_header' => 'Flying characteristics',
  'wiki_ship_speed' => 'Speed',
  'wiki_ship_consumption' => 'Deuterium consumption',
  'wiki_ship_capacity' => 'Cargo bay capacity',
  'wiki_ship_hint' => '<li>Actual data calculated with applying all bonuses - techs, mercs etc</li>',

  'wiki_combat_header' => 'Battle characteristics',
  'wiki_combat_attack' => 'Attack power, hits',
  'wiki_combat_shield' => 'Shield battery capacity, hits',
  'wiki_combat_armor' => 'Durability, hits',

  'wiki_combat_volley_header' => 'Volley',
  'wiki_combat_volley_to' => 'Will destroy',
  'wiki_combat_volley_from' => 'Will lost',

  'info' => array(
    STRUC_MINE_METAL => array(
      'description' => 'The main supplier of raw materials for the construction of load-bearing structures of buildings and ships. Metal is the most inexpensive raw material, but takes more than everything else. For production of metal requires less total energy. Than mines more deeper. on most planets metal is at great depths, deeper mines you can obtain more metals, production increases. At the same time, larger mines require more energy.',
      'description_short' => 'The main supplier of raw materials for the construction of load-bearing structures of buildings and ships.',
    ),

    STRUC_MINE_CRYSTAL => array(
      'description' => 'For the synthesis of crystals requires approximately twicw more enery than for extraction of metal, so it therefore appreciated more. Crystals-main part of any modern computers and a key componet of warp drive-engines. Therefore, it is required for all ships and almost all buildings. Improving synth increases the number of produced crystals.',
      'description_short' => 'The main supplier of raw materials for computer systems and warp-drives.',
    ),

    STRUC_MINE_DEUTERIUM => array(
      'description' => 'Deuterium is heavy hydrogen. Because of this, as in the mains, more large stockpiles are at the bottom of the sea. Improving synth also contributes to development of these deep deposits of deuterium. Deuterium is needed as fuel for ships, nearly all studies, see the galaxies, and use sensor phalanx',
      'description_short' => 'Extracts from the water on the planet, a small percentage of deuterium.',
    ),

    STRUC_MINE_SOLAR => array(
      'description' => 'To ensure energy mines and synthesizers are huge solar power plants. The more built stations,the greater the surface is covered with solar panels that transform light energy into electricity. Solar power plants are the foundation of the energy world.',
      'description_short' => 'Produces energy from sunlight. Energy is required for the majority of buildings.',
    ),

    STRUC_MINE_FUSION => array(
      'description_short' => 'Extracts energy from education Atom helium two heavy hydrogen atoms.',
      'description' => '',
    ),

    STRUC_FACTORY_ROBOT => array(
      'description' => 'Provides a simple labor, which can be used in the construction of a global infrastructure. Each level increases the speed of factory building.',
      'description_short' => 'Manufactures machines and mechanisms that are used in construction of a global infrastructure. Each level increases the speed of development of factory building.',
    ),

    STRUC_FACTORY_NANO => array(
      'description' => 'nanobots are the final evolution of robotic factories. The only equipment-nanobots to manipulate individual molecules and even atoms of matter. Since their invention made possible the production of virtually any material with predefined properties. Moreover, thanks to nanobots you can quickly produce finished parts of any forms and configurations. But invention nanobots revoke conventional plants. Although nanobots can produce any design, many things still energetically more favorable to &quot;old fashioned&quot;. But even with such restrictions each level nanobots shortens the time of construction of any buildings, defences, and ships by half.',
      'description_short' => 'Nanobots are specialized complexes to construct objects from individual molecules and atoms. Each level increases the speed of buildings, defences, and ships by twice.',
      'effect' => '',
    ),

    STRUC_FACTORY_HANGAR => array(
      'description' => 'Shipyard can produce all types of ships and defences. The faster you can build more complex and larger ships and defensive structures. By constructing factories nanites factory are simplified many chains that can dramatically improve the performance of the shipyard.',
      'description_short' => 'Shipyard produces spaceships, orbital structures and defences.',
    ),

    STRUC_STORE_METAL => array(
      'description' => 'Great location for extracted ores. The higher the level, the more metal you can store in it. If it is filled up, the extraction of metal ends.',
      'description_short' => 'Storage for metal.',
    ),

    STRUC_STORE_CRYSTAL => array(
      'description' => 'Great location for extracted ores. The higher the level, the more crystal you can store in it. If it is filled up, the extraction of crystal ends.',
      'description_short' => 'Storage for crystal.',
    ),

    STRUC_STORE_DEUTERIUM => array(
      'description' => 'Great location for extracted fuels. The higher the level, the more deuterium you can store in it. If it is filled up, the extraction of deuterium ends.',
      'description_short' => 'Storage for deuterium.',
    ),

    STRUC_LABORATORY => array(
      'description' => 'To study new technologies requires a working research station. Level of development research station is critical factor in how quickly could be developed new technologies. The higher the level of development research laboratory, the more can be researched new technologies. In order to complete as soon as the research work on the same planet, it sends all available scientists and then leave their planet. Once the technology is investigated, the scientists are returning to their home planet and carry with them knowledge about it. So new technologies can be applied on other planets.',
      'description_short' => 'Laboratory is for researching new technologies.',
    ),

    STRUC_TERRAFORMER => array(
      'description' => 'As more and more important building planets became the limited usable space. Traditional methods such as construction skyward and inside were insufficient. A small group of physicists and nanotehnikov found the solution-terraformer.<br><br>Spending large amounts of energy terraformer can convert vast areas and even the entire continents, making them suitable for construction. This structure is constantly produced special nanity, responsible for the constant quality of soil.',
      'description_short' => 'Terraformer can transform a huge territory, increasing the number of construction fields.',
    ),

    STRUC_ALLY_DEPOSIT => array(
      'description' => 'Alliance depot provides a way to ensure fuel friendly fleets that help with defense and are in orbit. The higher the level of development, the more deuterium can be sent to fleets in orbit.',
      'description_short' => 'Alliance depot provides a way to ensure fuel to friendly fleets that help with defense and are in orbit.',
    ),

    STRUC_LABORATORY_NANO => array(
      'description' => 'Reduce time to research stage doubled.',
      'description_short' => 'Nanobots equipped with the latest technology. Heavy duty crystaline computers and super-precise nanosbots accelerated by any study by twice.',
    ),

    STRUC_MOON_STATION => array(
      'description' => 'The moon has no atmosphere, therefore before the planned stay is required to build Lunar bases. It provides the necessary air, Gravitation and warmth. The higher the level of development of the lunar base, the more secure the biosphere area. Each level of the lunar base can build 3 sector, up to a maximum total square of the moon. It is 2 (diameter of the Moon/1000) ^ 2, each level lunar base itself occupies one field.',
      'description_short' => 'The moon has no atmosphere, therefore before the planned stay is required to build lunar base.',
    ),

    STRUC_MOON_PHALANX => array(
      'description' => 'High frequency sensors full browsing frequency spectrum by all the falangu radiations. Powerful computers combines tiny fluctuations in energy and thus gain information about the movements of ships at the distant planets. To view the Moon should be given the energy in the form of deuterium (per view 1000 * phalanx level). View is the transition from the Moon menu Galaxy and the title enemy planet located in range sensors (formula: (level phalanges) ^ 2-1).',
      'description_short' => 'High frequency sensors full browsing frequency spectrum by all the ship fuel radiation.',
    ),

    STRUC_MOON_GATE => array(
      'description' => 'Gate is a huge teleportery that can transmit between the fleets of all sizes without time-consuming. These teleportery do not require deuterium, but between two hops must undergo one hour, or gate recharge. Is also forwarding resources. The entire process requires very highly developed technology.',
      'description_short' => 'Gate is a huge teleportery that can transmit between the fleets of all sizes without time-consuming.',
    ),

    STRUC_SILO => array(
      'description' => 'Missile silo serve for storing missiles. With each level you can store four interplanetary or twelve interceptor missiles more. One interplanetary missiles require space triple the interceptor missile. May be any combination of different types of missiles.',
      'description_short' => 'Missile silo allows firing rockets and missile plus storage.',
    ),

    TECH_SPY => array(
      'description_short' => 'Using this technology produces data on other planets.',
      'description' => '',
    ),

    TECH_COMPUTER => array(
      'description' => 'Computer technology is designed to increase availability of computer power. As a result of the planet are more productive and efficient computer systems, increasing processing power and speed of computing processes. With the increasing power of computers you can command the entire of fleets. Each level of development of computer technology makes it possible to command + 1 fleet. The more sent fleets, the more you can do  and thus capture more raw materials. Naturally, this technology is useful and traders, as it enables them to simultaneously send larger merchant fleets. For this reason, you should constantly develop computer technology throughout the entire game.',
      'description_short' => 'With the increasing power of computers you can command the entire many fleets. Each level of computer technology increases the maximum number of fleets by 1.',
    ),

    TECH_WEAPON => array(
      'description' => 'Weapon technology focuses on the further development of weapons systems. Particular importance is attached to implementing existing systems more energy and more precisely this energy channel. The weapons systems become more efficient, and weapons causes more devastation. Each level increases the power of weapons technology weapons military units by 10%. Weapons technology is important in competitive content of parts. Why should constantly develop throughout the game.',
      'description_short' => 'Weapon technology makes weapons systems more efficient. Each level increases power weapons military units at 10% of the basic.',
    ),

    TECH_SHIELD => array(
      'description' => 'Development of this technology allows you to increase the supply of energy shields and shielding, which in turn increases their resilience and ability to absorb or reflect energy attacks of the enemy. Thanks to this with each passing level effectiveness of ship\'s shields and stationary shield generators increases by 10% of the rated power.',
      'description_short' => 'This technology examines more new features greater energy shields that make them more efficient. Thanks to this with each passing level effectiveness of shields is increased by 10%.',
    ),

    TECH_ARMOR => array(
      'description' => 'Special alloys improve armor spacecraft. Once found very resistant alloy, special beams are changing the molecular structure of a spacecraft, and brings it to a known alloy. The sustainability of armor can increase with each level at 10%.',
      'description_short' => 'Special alloys improve armor spacecraft. With each level strength armor increased by 10 % of the base value.',
    ),

    TECH_ENERGY => array(
      'description' => 'Energy technology is a further development of transmission systems and energy storage that are required for many new technologies.',
      'description_short' => 'Study of energy technology improves impact outcome of thermonuclear electrostations.',
    ),

    TECH_HYPERSPACE => array(
      'description' => 'By Plexus 4th and 5th dimension has become possible to explore new, more economical and efficient engine.',
      'description_short' => 'By Plexus 4th and 5th dimension has become possible to explore new, more economical and efficient engine.',
    ),

    TECH_ENGINE_CHEMICAL => array(
      'description' => 'Jet engine is based on the principle of effectiveness. Fabric, hot to elevated temperatures, thrown in the opposite direction and gives faster ship. The effectiveness of these engines is small enough, but they are quite reliable, cheap production and services. Furthermore they take up much less space on the ship than the other engines, so they are still quite often can be found on small ships. Because the rocket engines are the foundation of any flight into space, should examine them as soon as possible. Further development of these engines makes the following ships with each level at 10% faster: small transports (until a researched pulse engine the 5th level), large transports, light fighters, recyclers and spy probes.',
      'description_short' => 'Further development of these engines makes some ships faster, but each level increases the speed of only 10%.',
    ),

    TECH_ENGINE_ION => array(
      'description' => 'Impulse engine is based on the principle of effectiveness, and warming up of matter are the nuclear reaction. You can also inject additional mass. Further development of these engines makes the following ships with each level to 20% faster: small transportation, bombers (until a researched hyperspace engine 8th level), cruisers, heavy fighter and colonizers. Each level increases the reach of interplanetary missiles.',
      'description_short' => 'Impulse engine is based on the principle of effectiveness. Further development of these engines makes some ships faster, but with each level increases the speed of only 20%.',
    ),

    TECH_ENGINE_HYPER => array(
      'description' => 'By spatio-temporal curvature in the immediate environment of spacecraft space shrinks, the faster the overcome long distances. The higher developed Hyperspace drive, the higher the compression space, which makes the following ships with each level of 30% faster: Hypertransport, Cruiser, Bomber (after research of 8th level), Battleship, Destroyer, Death Star and Supernova-class cruiser.',
      'description_short' => 'By spatio-temporal curvature in the immediate environment of spacecraft space shrinks, the faster the overcome long distances. The higher the developed hyperspace drive, the higher the compression space, with each level of the speed of ships rises up 30%.',
    ),

    TECH_LASER => array(
      'description' => 'Laser (light amplification using induced emission of radiation) produces rich energy beam of coherent light. These devices are used in all areas of optical computers before heavy lasers that freely cut armor spacecraft. Laser technology is an important element for the study of further weapons technology.',
      'description_short' => 'Thanks to focus light rays that occurs when an object causes him injury.',
    ),

    TECH_ION => array(
      'description' => 'Truly deadly beam of accelerated ions. In contact with an object they cause immense damage.',
      'description_short' => 'Truly deadly beam of accelerated ions. In contact with an object they cause immense damage.',
    ),

    TECH_PLASMA => array(
      'description' => 'Further development of the ion technology which accelerates the ions, and energetic plasma. She has had a devastating effect in contact with an object.',
      'description_short' => '',
    ),

    TECH_RESEARCH => array(
      'description' => 'This network enables communication scientists working in research laboratories from different planets. Each new level allows you to attach to the network for additional laboratory (primarily attached laboratory senior levels). Of all United in a network of laboratories in each study involved only those that are sufficient to conduct the study level. Speed study FY08 levels involved in it laboratories.',
      'description_short' => 'This network enables communication scientists working in research laboratories from different planets. Each new level allows you to attach to the network for additional laboratory.',
    ),

    TECH_EXPEDITION => array(
      'description' => 'Expedition technology encompasses various scanning technology and makes it possible to equip the ships of different classes of research module. It contains a database, a small mobile laboratory, as well as various biokletki and vessels for samples. For the safety of the ship when investigating hazardous objects research module is equipped with an autonomous energy and generator of energy field, which in extreme cases can surround a powerful energy field research module.',
      'description_short' => 'Now you can equip ships providing research module processing the collected data in long flights.',
    ),

    TECH_COLONIZATION => array(
      'description' => 'Ruler with many colonies, has more advantages over others.',
      'description_short' => 'This technology is very important that you could build your Empire with many colonies.',
    ),

    TECH_ASTROTECH => array(
      'description' => 'Астрокартография позволяет увеличить максимальное количество колоний и экспедиций, а так же максимальную длительность экспедиции',
      'description_short' => 'Астрокартография позволяет увеличить максимальное количество колоний и экспедиций, а так же максимальную длительность экспедиции',
    ),

    TECH_GRAVITON => array(
      'description' => 'Graviton is a particle that has neither mass nor charge and determines the force of attraction. By launching the concentrated charge gravitonov can create artificial gravitational field that, like a black hole, dragging a ton, so you can destroy ships or even the moon. To produce a sufficient quantity of gravitonov requires huge amounts of energy.',
      'description_short' => 'Graviton is a particle that has neither mass nor charge and determines the force of attraction. By launching the concentrated charge gravitonov can create artificial gravitational field that, like a black hole, dragging a ton, so you can destroy ships or even the Moon.',
    ),

    SHIP_CARGO_SMALL => array(
      'description' => 'Transports have approximately the same size as the fighter aircraft, but they don\'t have powerful engines and onboard weapons to save space. Small transport accommodates 5,000 units of stuff. Because small firepower small transports are often accompanied by other ships when pulse engine researched up to 5th grade, small transport improves the speed and it is equipped with a base that type engine.',
      'description_short' => 'Small transport is highly maneuverable craft, which can easily transport the raw material to other planets. After research impulse drive 5th level ships are being refurbished.',
    ),

    SHIP_CARGO_BIG => array(
      'description' => 'Aboard this ship there is only weak armament and no serious technology ... For this reason, they should never be run unattended. Thanks to its advanced Jet engine large transportation serves as a rapid interplanetary dostavsika resources, as it accompanies the fleets to attack the enemy planet to grab as many resources as possible.',
      'description_short' => 'Further development of small transports a ships with greater capacity and more developed an engine that can travel faster than light travel, until small transports not installed impulse engines 5th level.',
    ),

    SHIP_CARGO_SUPER => array(
      'description' => 'The last word in technology transfer. Supertransport-giant transport ship equipped with impulse engines. Its speed is low and the fuel consumption is very high, but it completely pays off an extraordinary sitting capacity. Supertransport is equipped with a laser turrent, but has a powerful shield.',
      'description_short' => 'A giant self-propelled barge equipped with impulse engines. Equipped with a powerful shield, but of weapons has only laser turrent.',
    ),

    SHIP_CARGO_HYPER => array(
      'description' => 'If Supercargo was &quot;last word in transportation technology&quot; then Hypertransport would be a final point. &quot;Giant&quot; is too weak word to describe this ship. Sized as small moon this transport able to transfer enormeous amount of resources. Only hyperdrives can move fully loaded ship. They allow Hypertransport to slide through Universe with acceptable speed. But price is very high - cost of one ship is about ten times cost of Destructor. Moreover - fuel consumption of this transport will made almost any Emperor cry like a baby. Thou Hypertransport is a ship for a large and powerfull Empires which need to transfer several millions tons of resources at once',
      'description_short' => 'Cargo ship sized as small moon. Equiped with hyperdrives and able to transfer million ton of resources at once',
    ),

    SHIP_SMALL_FIGHTER_LIGHT => array(
      'description' => 'Lightweight fighter is a highly maneuverable craft, which can be found on almost every planet. Cost is not too big, but the shielding power and capacity are very small.',
      'description_short' => 'Lightweight fighter is a highly maneuverable craft, which can be found on almost every planet. Cost is not too big, but the shielding power and capacity are very small.',
    ),

    SHIP_SMALL_FIGHTER_HEAVY => array(
      'description' => 'In further development of the lightweight fighter scientists came to a point where it became clear that an ordinary engine lacks power. In order to optimally remove ship, was first used pulse engine. Though it has cost, but it also opened new possibilities. Thanks to this engine has more power for weapons and shields, moreover, for this kind of fighter aircraft were also used valuable materials. This has led to improved structural integrity and stronger firepower, melee weapons, it represents a greater threat than its predecessor. After these changes Beaufighter represents a new era of technology, shipbuilding basis technology.',
      'description_short' => 'Further development of the lightweight fighter, it is better to secure and has a stronger attack.',
    ),

    SHIP_MEDIUM_DESTROYER => array(
      'description' => 'With the development of heavy lasers and ion guns, more and more marginalized by heavy fighters. Despite the many improvements in firepower and armor cannot be so modified to efficiently counter these defensive guns. It was therefore decided to build a new class of ships, which would have more firepower and armor. So were the destroyers. Destroyers protected almost three times stronger than heavy fighters and more than twice the firepower. They are very quick. There is no better weapons against high defense. Nearly a century cruisers unlimited dominated in the universe. With the advent of Gauss guns and plasma guns their rule came to an end. However, today they are used against groups of fighters.',
      'description_short' => 'Destroyers protected almost three times stronger than heavy fighters and firepower they surpass heavy fighters almost doubled. They are very fast.',
    ),

    SHIP_LARGE_CRUISER => array(
      'description' => 'Cruisers tend to form the backbone of the fleet. Their heavy guns, high speed and large cargo make them serious opponents.',
      'description_short' => 'Cruisers tend to form the backbone of the fleet. Their heavy guns, high speed and large cargo make them serious opponents.',
    ),

    SHIP_COLONIZER => array(
      'description' => 'This well protected boat serves conquer new planets that need expanding Empire. It is used in the new colonies as the supplier of raw material and use it to take apart-its useful material for the development of the new world. Maximum number of colonies per Empire depends on Universe Settings.',
      'description_short' => 'This ship can absorb planet.',
    ),

    SHIP_RECYCLER => array(
      'description' => 'Space battles were all lost. Destroyed thousands of ships and encountered the wreckage seemed forever lost. Normal transports couldn\'t come close to them without being badly corrupted small fragments. With the new opening in shield technology it has become possible to effectively address this problem, a new class of ship, similar large transport-processor. With its help you can reuse the seemingly lost resources. Due to the new boards are small fragments no longer represented danger. Unfortunately, these devices require space, so its cargo tonnage is limited to 20 000.',
      'description_short' => 'Using the raw material is extracted from the wreckage of tire.',
    ),

    SHIP_SPY => array(
      'description' => 'Spy probes are small mobile that the ships which deliver with distances of fleets and planets. Their high-performance engine allows them to travel long distances for a few seconds. Once it lands on the orbit of a planet, they are there for some time to build the data. At this time the enemy is relatively easy to detect them and attack. To save space not found no armor or shields, no guns, making the sounders in case light objectives.',
      'description_short' => 'Spy probes are small mobile that the ships which deliver with distances of fleets and planets.',
    ),

    SHIP_LARGE_BOMBER => array(
      'description' => 'Bomber was developed specifically to destroy the planetary protection. Using a laser sight it just resets plasma bombs on the surface of the planet and thus causes enormous damage to the defensive structures when hyperspace engine researched up to 8th grade, the bomber is a basic speed and it is equipped with this type of engine.',
      'description_short' => 'Bomber was developed specifically to destroy the planetary protection.',
    ),

    SHIP_SATTELITE_SOLAR => array(
      'description' => 'Solar satellites are launched into orbit of the planet. They collect solar energy and transmit it to the ground station. Efficiency solar satellites depends on the strength of solar radiation. In principle, the extraction of energy orbits more approximate to the Sun is higher than on the planets, remote from the Sun. Due to its price/quality ratio solar satellites solve energy problems of many worlds. But attention: solar satellites can be destroyed in battle.',
      'description_short' => 'Solar satellites is a simple platform of solar cells, which are located on high orbit. They collect sunlight and expose it using laser ground station.',
    ),

    SHIP_LARGE_DESTRUCTOR => array(
      'description' => 'Destructor - King of warships. The ion, plasma and gauss turrets can thanks to its advanced sensor to a 99% even high-speed mobile that the fighters. Since destroyers are very large, their agility is very limited, and in battle they resemble rather a battle station than combat ship. Deuterium consumption are also appreciated as their fighting power.',
      'description_short' => 'Destructor - King among military ships.',
    ),

    SHIP_HUGE_DEATH_STAR => array(
      'description' => 'Death star is equipped with a giant graviton gun that could destroy all types of ships and even the moon. To produce enough energy, celebrity deaths almost entirely consists of generators. Only huge stellar Empire have enough resources and workers for the construction of this huge ship.',
      'description_short' => 'Death star is equipped with a giant graviton gun that can destroy ships and even the Moon. ',
    ),

    SHIP_LARGE_BATTLESHIP => array(
      'description' => 'This high-tech ship brings death intruder fleets. Its advanced laser guns keep heavy enemy ships at a distance and can destroy several units one in one gulp. Due to its small size and incredibly powerful weapons, lenght linear Cruiser is very small, but at the expense of hyperspatial engine as little fuel consumption.',
      'description_short' => 'Starcruiser specializes in capturing enemy fleets.',
    ),

    SHIP_HUGE_SUPERNOVA => array(
      'description' => 'You are granted a ship from the emperor for your cruelty skills.',
      'description_short' => 'The flagship of the fleet of the Empire. The huge build cost will be compensated with terrible firepower and advanced protection. One ship of this class is able to defeat an average fleet by itself.',
    ),

    UNIT_DEF_TURRET_MISSILE => array(
      'description' => 'Launcher is a simple and inexpensive means of defence. Because this is the development of conventional ballistic artillery shells, it does not require further upgrading. The small cost of its production justifies its use against smaller fleets, but over time it loses value. Later it is used only for withdrawal of enemy shots. Defensive deactivates itself once they severely damaged. Recoverability of the fortifications after a battle with up to 70%.',
      'description_short' => 'Launcher is a simple and inexpensive means of defence.',
    ),

    UNIT_DEF_TURRET_LASER_SMALL => array(
      'description_short' => 'Using the concentrated attack targets photons can be achieved much great destruction than conventional ballistic weapons.',
      'description' => '',
    ),

    UNIT_DEF_TURRET_LASER_BIG => array(
      'description' => 'Heavy laser represents a further development of the laser light. Structure has been reinforced and improved with new materials. Wrapper could do much more resistant. At the same time has been improved and energy system and the target computer, so heavy laser can concentrate much more on target. Defensive deactivates itself once they severely damaged. Recoverability of the fortifications after a battle with up to 70%.',
      'description_short' => 'Heavy laser represents a further development of laser light.',
    ),

    UNIT_DEF_TURRET_GAUSS => array(
      'description_short' => 'Coilgun accelerates mnogotonnye charges with gigantic energy costs.',
      'description' => '',
    ),

    UNIT_DEF_TURRET_ION => array(
      'description_short' => 'Ion Cannon directs to the purpose of wave ion, which destabilizes the shields and damages the electronics.',
      'description' => '',
    ),

    UNIT_DEF_TURRET_PLASMA => array(
      'description' => 'Laser technology was brought to perfection, Ionic technology has reached the final stage and was thought to be virtually impossible, even qualitatively Cannon system to achieve even greater efficiency. But everything was to change when the idea to combine both systems. Using the technology of nuclear fusion, laser heat the substance (usually a deuterium) up to ultrahigh temperatures reaching millions of degrees. Ionic technology provides enrichment plasma electric charge, its stabilization and acceleration. Once electric charge sufficiently warmed, ionized and is under pressure, it produce using accelerators in the direction of the goal. Glowing bluish color plasma Bowl looks awesome, the only question is, how long they will enjoy the command ship-purpose if after a few seconds, the armor he explodes into pieces and Electronics will burn ... Plasma turret is generally the most frightening weapons, and this technology represents a trade-off. Defensive deactivates itself once they severely damaged. Recoverability of the fortifications after a battle with up to 70%.',
      'description_short' => 'The last word in Planetary Defense technologies created a symbiosis of laser and Ion tech.',
    ),

    UNIT_DEF_SHIELD_SMALL => array(
      'description' => 'Long before the shield generators become sufficiently small to be used on ships, there is a huge generators on the surface of planets. They obvolakivali an entire planet force field that can absorb the shock of the attack. Small fleets are constantly attacking on these billboards dome. Due to the growing technological development of these boards can be even greater. Later, you can build stronger large shield dome. On every planet you can build only one small shield dome.',
      'description_short' => 'Small shield protects the planet and absorbs shock attack.',
    ),

    UNIT_DEF_SHIELD_BIG => array(
      'description' => 'Further development of the small shield dome. It can deter even stronger attack to planet, consuming significantly more energy.',
      'description_short' => 'Further development of the small shield dome. It can deter even stronger attack to planet, consuming significantly more energy.',
    ),

    UNIT_DEF_SHIELD_PLANET => array(
      'description' => 'The best protection for your planets',
      'description_short' => 'The best protection for your planets',
    ),

    UNIT_DEF_MISSILE_INTERCEPTOR => array(
      'description' => 'Rocket interceptors destroy attacking interplanetary missiles. An interceptor missile destroys one interplanetary missile.',
      'description_short' => 'Rocket interceptors destroy attacking interplanetary missiles',
    ),

    UNIT_DEF_MISSILE_INTERPLANET => array(
      'description_short' => 'Interplanetary missiles destroy enemy defenses',
      'description' => '',
    ),

    MRC_TECHNOLOGIST => array(
      'description' => 'Technologist is a recognized expert in astromineralogy and crystalography. With his team of Metalurgists and chemists, he supported the interplanetary Government when developing new resources and optimize their refinery.',
      'description_short' => 'Technologist is a recognized expert in astromineralogy and crystalography. With his team of Metalurgists and chemists, he supported the interplanetary Government when developing new resources and optimize their refinery.',
      'effect' => 'per level to metal, crystal and deuterium production, to energy production on solar and fusion stations each level',
    ),

    MRC_ENGINEER => array(
      'description' => 'Engineer is expert in structures and ship building',
      'description_short' => 'Engineer is expert in structures and ship building',
      'effect' => 'per level to construction speed for buildings and ships<br />+1 slot per level to planet structures and hangar queries',
    ),

    MRC_FORTIFIER => array(
      'description' => 'Fortifier - Army Engineer. His in-depth knowledge of defensive systems allow you to shorten planet defense building time',
      'description_short' => 'Fortifier - Army Engineer. His in-depth knowledge of defensive systems allow you to shorten planet defense building time',
      'effect' => 'per level to construction speed of missiles and defense structures<br />+10% per level to attack, armor and shields when defending planet<br />+1 slot per level to defence structures and missile queries',
    ),

    MRC_STOCKMAN => array(
      'description' => 'Cargo-master is a highly skilled specialist in storage. His genius allows you to get the most out of storage resources to increase their effective capacity beyond the builders.',
      'description_short' => 'Cargo-master is a highly skilled specialist in storage. His genius allows you to get the most out of storage resources to increase their effective capacity beyond the builders.',
      'effect' => 'size of warehouses for each level',
    ),

    MRC_SPY => array(
      'description' => 'Spy-master person Empire. He had hundreds of thousands of individuals and a million ideas for mask works, defensive networks and fleets. Everyone who saw his real face, is now dead.',
      'description_short' => 'Spy-master person Empire. He had hundreds of thousands of individuals and a million ideas for mask works, defensive networks and fleets. Everyone who saw his real face, is now dead.',
      'effect' => 'level of spying for each level',
    ),

    MRC_ACADEMIC => array(
      'description' => 'Academicians are actors Guild Technocrats. Their mind and scholars degree allow them Excel in their acts even constructors. They specialize in the field of technological progress.',
      'description_short' => 'Academicians are actors Guild Technocrats. Their mind and scholars degree allow them Excel in their acts even constructors. They specialize in the field of technological progress.',
      'effect' => 'per level to technology research speed',
    ),

//    MRC_DESTRUCTOR => array(
//      'description' => 'Destroyer, a ruthless army officer. He suggests how the Empire\'s planets brutal methods should be. The same Destroyer has developed technology and manufactures Deathstars.',
//      'effect' => 'Allows to build Deathstars in the shipyard',
//    ),

    MRC_ADMIRAL => array(
      'description' => 'Admiral is tried by war veteran and a brilliant strategist. Even in the hottest fights he doesn\'t lose a review and maintains contact with commanders fleets. The wise ruler can rely on him in battle and thereby use to battle more ships.',
      'description_short' => 'Admiral is tried by war veteran and a brilliant strategist. Even in the hottest fights he doesn\'t lose a review and maintains contact with commanders fleets. The wise ruler can rely on him in battle and thereby use to battle more ships.',
      'effect' => 'armor, shields and attack ships for each level',
    ),

    MRC_COORDINATOR => array(
      'description' => 'The Coordinator is an expert in managing fleets. His knowledge can make the most of the fleet management system.',
      'description_short' => 'The Coordinator is an expert in managing fleets. His knowledge can make the most of the fleet management system.',
      'effect' => 'additional fleet for each level',
    ),

    MRC_NAVIGATOR => array(
      'description' => 'Navigator-genius in calculating the trajectories of fleets. His knowledge of laws warp drive-space device jump-drive and technologies all existing types of engines can speed flying ships.',
      'description_short' => 'Navigator-genius in calculating the trajectories of fleets. His knowledge of laws warp drive-space device jump-drive and technologies all existing types of engines can speed flying ships.',
      'effect' => 'speed of ships for each level',
    ),

//    MRC_ASSASIN => array(
//      'description' => 'Assasin is a trusted killer. But it\'s not his only quality. Assassin has developed the new cruiser class Supernova. He is the only person who can manage this ship.',
//      'effect' => 'Allows to build Supernova Cruisers in the shipyard',
//    ),

    MRC_EMPEROR => array(
      'description' => 'Emperor - your personal assistant and Deputy. The accuracy of its reports and punctuality in everything-his best qualities, capable of total control over the Empire.',
      'description_short' => 'Emperor - your personal assistant and Deputy. The accuracy of its reports and punctuality in everything-his best qualities, capable of total control over the Empire.',
      'effect' => 'Allows you to change the characteristics of the Emperor',
    ),

    ART_LHC => array(
      'description' => 'LHC creates waves of gravitons that forces debris to concentrate in one place<br /><span class=warning>WARNING! Using of LHC is not a guarantee to creation of new moon!</span>',
      'effect' => 'Allows another chance to create moon<br />1% per 1.000.000 of debris but not more then 30%',
    ),

    ART_HOOK_SMALL => array(
      'description' => 'Принцип действия этого Артефакта до конца не изучен что, впрочем, не мешает его использовать. Малый Крюк телепортирует на устойчивую орбиту планеты небольшой астероид. Таким образом у планеты получается луна минимального диаметра',
      'effect' => 'Creates minimal size Moon',
    ),

    ART_HOOK_MEDIUM => array(
      'description' => 'Принцип действия этого Артефакта до конца не изучен что, впрочем, не мешает его использовать. Средний Крюк телепортирует на устойчивую орбиту планеты астероид, создавая таким образом луну<br /><span class=warning>ВНИМАНИЕ! Размер луны СЛУЧАЕН!</span>',
      'effect' => 'Creates random size Moon',
    ),

    ART_HOOK_LARGE => array(
      'description' => 'Принцип действия этого Артефакта до конца не изучен что, впрочем, не мешает его использовать. Большой Крюк телепортирует на устойчивую орбиту планеты огромный астероид. Таким образом у планеты получается луна максимального диаметра',
      'effect' => 'Creates maximum size Moon',
    ),

    ART_RCD_SMALL => array(
      'description' => 'Small Rapid Colony Deployer (RCD) is a set of ready constructions and programs that allows deploy on planet basic colony in no time<br />
        If there are buildings on planet they will be upgraded or left intact - if their level higher then RCD\'s programming. RCD can be fully deployed on planet even when there is lack of free sectors. RCD can not be deployed on moon.<br />
        Basic colony includes Metal Mine, Crystal Mine and Deuterium Synthetizer of level 10, Solar Plant of level 14 and Robotics Factory level 4',
      'effect' => 'Instantly deploys basic colony on planet',
    ),

    ART_RCD_MEDIUM => array(
      'description' => 'Medium Rapid Colony Deployer (RCD) is a set of ready constructions and programs that allows deploy on planet advanced colony in no time<br />
        If there are buildings on planet they will be upgraded or left intact - if their level higher then RCD\'s programming. RCD can be fully deployed on planet even when there is lack of free sectors. RCD can not be deployed on moon.<br />
        Advanced colony includes Metal Mine, Crystal Mine and Deuterium Synthetizer of level 15, Solar Plant of level 20 and Robotics Factory level 8',
      'effect' => 'Instantly deploys advanced colony on planet',
    ),

    ART_RCD_LARGE => array(
      'description' => 'Large Rapid Colony Deployer (RCD) is a set of ready constructions and programs that allows deploy on planet improved colony in no time<br />
        If there are buildings on planet they will be upgraded or left intact - if their level higher then RCD\'s programming. RCD can be fully deployed on planet even when there is lack of free sectors. RCD can not be deployed on moon.<br />
        Improved colony includes Metal Mine, Crystal Mine and Deuterium Synthetizer of level 20, Solar Plant of level 25, Robotics Factory level 10 and Nanite Factory level 1',
      'effect' => 'Instantly deploys improved colony on planet',
    ),

    ART_HEURISTIC_CHIP => array(
      'description' => 'Эвристический чип - уникальный преинсталлированный набор программ, записанных на кристаллический носитель. Подключаясь к исследовательской сети, алгоритмы чипа способны проанализировать текущее состояние исследования и выдать новые эффективные эвристики, таким образом значительно сокращая время исследования. Однажды активированный чип невозможно перенастроить на другое исследование. К сожалению, как и с любым другим кристаллическим чипом, декомпиляция &quot;зашитой&quot; программы принципиально невозможна, равно как и копирование сборщиками.',
      'effect' => 'Уменьшает время текущего исследования в два раза (если до конца исследования осталось больше часа) или моментально заканчивает его (если до конца исследования осталось меньше 1 часа, но больше 1 минуты)', //. Если времени исследования осталось менее часа - остаток не переходит на следующий слот в очереди',
    ),

    ART_NANO_BUILDER => array(
      'description' => 'Как известно, сборщики обычно не используются в строительстве крупных объектов типа зданий. Экономически целесообразней возводить строения методом традиционной &quot;блочной сборки&quot;, когда отдельные стандартизированные детали производятся на роботизированных фабриках. Однако специализированные наносборщики оказываются эффективнее традиционных методов. Эти крошечные роботы собраны в преконфигурированные пакеты, каждый из которых обладает своим собственным роевым суб-ИИ. Анализируя текущее состояние возводимого здания, наностроители безошибочно находят узкие места и вычисляют наиболее эффективные пути ускорения строительства. Пакет является одноразовым и после использования больше непригоден к работе. Вдобавок инициированный пакет уже невозможно перенастроить на интеграцию с другой стройкой. Хотя сборщики и способны воспроизвести отдельно взяты наностроитель, но без управляющего кристалла такая реплика является не более чем масштабной моделью...',
      'effect' => 'Уменьшает время постройки/разрушения текущего здания на данной планете в два раза (если до конца процесса осталось больше часа) или моментально заканчивает его (если до конца процесса меньше 1 часа, но больше 1 минуты)', // . Если времени строительства осталось менее часа - остаток не переходит на следующий слот в очереди',
    ),


    UNIT_PLAN_STRUC_MINE_FUSION  => array(
      'description' => 'Allows to build on planets sturcture &quot;Thermonuclear plant&quot;',
      'effect' => 'Allows to build on planets sturcture &quot;Thermonuclear plant&quot;',
    ),

    UNIT_PLAN_SHIP_CARGO_SUPER  => array(
      'description' => 'Allows to build on planet\'s hangars &quot;Super Cargo&quot; ships',
      'effect' => 'Allows to build on planet\'s hangars &quot;Super Cargo&quot; ships',
    ),

    UNIT_PLAN_SHIP_CARGO_HYPER  => array(
      'description' => 'Allows to build on planet\'s hangars &quot;Hypercargo&quot; ships',
      'effect' => 'Allows to build on planet\'s hangars &quot;Hypercargo&quot; ships',
    ),

    UNIT_PLAN_SHIP_DEATH_STAR  => array(
      'description' => 'Allows to build on planet\'s hangars &quot;Death Star&quot; ships',
      'effect' => 'Allows to build on planet\'s hangars &quot;Death Star&quot; ships',
    ),

    UNIT_PLAN_SHIP_SUPERNOVA  => array(
      'description' => 'Allows to build on planet\'s hangars &quot;Supernova&quot;-class cruisers',
      'effect' => 'Allows to build on planet\'s hangars &quot;Supernova&quot;-class cruisers',
    ),

    UNIT_PLAN_DEF_SHIELD_PLANET  => array(
      'description' => 'Allows to build on planets defense system &quot;Planet protector&quot;',
      'effect' => 'Allows to build on planets defense system &quot;Planet protector&quot;',
    ),


    RES_METAL => array(
      'description' => 'Metametallic iron-normed energy-neutraul compound (shortly &quot;metal&quot;) is a basic raw material from which nanobots produces all materials and details used in construction and research. Metal comes in ignots. Each ignot volume is 127 litres and weights 1 metric tonn including protective case. &quot;Iron-normed&quot; means that standard pack of nanobot will produce from 1 ignot 1 tonn of pure iron. &quot;Energy-neutral&quot; means that nanobots will use exact amount of energy which can be extracted from ignot. &quot;Metametallic compaund&quot; means that ignot can include simple and complex chemical substances. Composition of metal ignot can differ from planet to planet and from mine to mine - but their physical characteristics remain the same. Usually metal is slightly radioactive',
      'effect' => '',
    ),

    RES_CRYSTAL => array(
      'description' => 'Crystal is a complex termoplastic polymer which demonstrates Superlight Conduction Effect. SCE - increasing photon speed in a crystall above 300000 km/s. All modern computers uses crystall as material for processing and memory units. Residuals (&quot;anomal assemblies&quot; - i.e. polymers that has same formula but didn\'t demonstrates SCE) used in solar panels whose efficiency is about 100%. Specially selected crystal is a main part of jump-drive - device which allow faster-then-light travels',
      'effect' => '',
    ),

    RES_DEUTERIUM => array(
      'description' => 'Deuterium, also called heavy hydrogen, is one of two stable isotopes of hydrogen. The nucleus of deuterium, called a deuteron, contains one proton and one neutron, whereas the far more common hydrogen isotope, protium, has no neutron in the nucleus. Deuterium used as fuel for termonuclear reactors and all types of ship engines. It is stored in liquefied form in a standard thermally insulated containers which also is a fuel cells for reactors and engines. Ship cargo bays with automatic feeders also serves as &quot;fuel tank&quot; for any ship',
      'effect' => '',
    ),

    RES_ENERGY => array(
      'description' => 'Electric energy - unified type of energy that used everywhere. On planets it\'s usually produced on solar stations and solar sattelites. On space ships and some colonies too far from sun it\'s produced on termonuclear reactors and plants - respectivly',
      'effect' => '',
    ),

    RES_DARK_MATTER => array(
      'description' => '<span class="dark_matter">Dark Matter</span> is a matter that neither emits nor scatters light or other electromagnetic radiation, and so cannot be directly detected via optical or radio astronomy. From it we can get an incredible amount of energy. Because of this, and also because of the complexities associated with its production of <span class="dark_matter">Dark Matter</span> is highly appreciated.',
      'effect' => '',
    ),

    UNIT_PLANET_DENSITY => array(
      'description' => 'Средняя планетарная плотность (далее - просто "плотность") характеризует химический состав геосферы планеты. В частности, она очень точно предсказывает соотношения добываемых полезных веществ.<br /><br />
      В общем случае геосфера планеты делится на атмосферу (газовая оболочка), гидросферу (жидкая оболочка), литосферу (твердая оболочка из относительно легких веществ и элементов), мантию (промежуточная оболочка между литосферой и ядром) и ядро (самые тяжелые вещества и элементы, находящиеся в центре планеты под высоким давлением).<br /><br />
      Основное влияние на плотность оказывает ядро. Именно оно содержит основную массу планеты, поскольку плотность химических соединений убывает по мере подъема от центра планеты к её поверхности.<br /><br />
      Таким образом именно тип ядра полностью определяет виды и уровень добычи полезных ископаемых. Комбинации типов планетарных ядер с разными температурными режимами и размерами дают всё многообразие известных планет.<br /><br />
      Для примера рассмотрим планету с ледяным ядром. Оно состоит из водяного льда с небольшой примесью других веществ, мантия - из твердого метана, а литосфера - из кристаллического водорода.<br />
      В подавляющем большинстве такие планеты встречаются на периферии звездных систем. И выглядят они под стать своему названию: гигантские глыбы из смеси льда.<br />
      Впрочем, иногда такая планета может оказаться и на низкой орбите вокруг звезды. Обычно это происходит после столкновения звездных систем. Так же звезда может захватить своим тяготением какую-нибудь блуждающую планету.<br />
      Близость к звезде преображает планету. На ней образуется быстроулетучивающаяся атмосфера из легких газов (водород и гелий), а иногда даже и гидросфера из жидкого водорода и метана. При этом ядро планеты по-прежнему остается ледяным.<br />
      Существование такой планеты чрезвычайно коротко во вселенских масштабах, но учитывая те же масштабы, обнаружить такую планету вполне возможно.
      <br />
      <br />

      Тип ядра можно сменить за ТМ на странице <a href="overview.php?mode=manage" class="link ok">"Управление планетой"</a> (пункт меню "Планета", кнопка "Управление" на странице).
      Стоимость смены типа ядра зависит от количества секторов самой планеты без учета секторов от Терраформеров, но учитывая сектора, купленные за ТМ. Стоимость нового типа ядра не зависит от текущего.
      Добыча ресурсов в пределах одного класса приблизительно равны в пересчете на потраченную ТМ (как определяется добыча - см. ниже после таблицы).
      Ядра более редкого класса стоят дороже, но и дают заметно больше ресурсов (см. выше).<br /><br />

      Всего существует 10 типов ядер, рассортированных на четыре класса:
      <table>
        <tr class="c_c">
          <th rowspan="2">Тип ядра</th>
          <th colspan="2">Плотность, кг/м&sup3;</th>
          <th colspan="3">Добыча</th>
          <th rowspan="2">Класс</th>
        </tr>
        <tr class="c_c">
          <th>Минимальная</th>
          <th>Максимальная</th>
          <th>Металл</th>
          <th>Кристалл</th>
          <th>Дейтерий</th>
        </tr>

        <tr class="c_c">
          <th>Водородный лёд</th>
          <td>250</td>
          <td>750</td>
          <td class="error">Очень плохая</td>
          <td class="warning">Плохая</td>
          <td class="positive">Отличная</td>
          <td class="error">Раритет</td>
        </tr>

        <tr class="c_c">
          <th>Метановый лёд</th>
          <td>750</td>
          <td>1250</td>
          <td class="warning">Плохая</td>
          <td class="notice">Неплохая</td>
          <td class="positive">Очень хорошая</td>
          <td class="warning">Редкий</td>
        </tr>

        <tr class="c_c">
          <th>Водный лёд</th>
          <td>1250</td>
          <td>2000</td>
          <td class="notice">Неплохая</td>
          <td class="notice">Неплохая</td>
          <td class="positive">Хорошая</td>
          <td class="notice">Продвинутый</td>
        </tr>

        <tr class="c_c">
          <th>Кристалл</th>
          <td>2000</td>
          <td>2500</td>
          <td class="error">Очень плохая</td>
          <td class="positive">Отличная</td>
          <td class="warning">Плохая</td>
          <td class="error">Раритет</td>
        </tr>

        <tr class="c_c">
          <th>Силикат</th>
          <td>2500</td>
          <td>3500</td>
          <td class="warning">Плохая</td>
          <td class="positive">Очень хорошая</td>
          <td class="notice">Неплохая</td>
          <td class="warning">Редкий</td>
        </tr>

        <tr class="c_c">
          <th>Камень</th>
          <td>3500</td>
          <td>4750</td>
          <td class="notice">Неплохая</td>
          <td class="positive">Хорошая</td>
          <td class="notice">Неплохая</td>
          <td class="notice">Продвинутый</td>
        </tr>

        <tr class="c_c">
          <th>Стандарт</th>
          <td>4750</td>
          <td>5750</td>
          <td>Стандартная</td>
          <td>Стандартная</td>
          <td>Стандартная</td>
          <td>Базовый</td>
        </tr>

        <tr class="c_c">
          <th>Руда</th>
          <td>5750</td>
          <td>7000</td>
          <td class="positive">Хорошая</td>
          <td class="notice">Неплохая</td>
          <td class="notice">Неплохая</td>
          <td class="notice">Продвинутый</td>
        </tr>
        <tr class="c_c">
          <th>Оливин</th>
          <td>7000</td>
          <td>8250</td>
          <td class="positive">Очень хорошая</td>
          <td class="notice">Неплохая</td>
          <td class="warning">Плохая</td>
          <td class="warning">Редкий</td>
        </tr>
        <tr class="c_c">
          <th>Металл</th>
          <td>8250</td>
          <td>9500</td>
          <td class="positive">Отличная</td>
          <td class="error">Очень плохая</td>
          <td class="error">Очень плохая</td>
          <td class="error">Раритет</td>
        </tr>
      </table><br />

      Класс ядра, который можно найти в экспедиции, ограничен эффективным уровнем Астрокартографии (т.е. уровень с учётом всех бонусов: от родного мира, Премиум-аккаунта, внутриигровых акций итд).
      Это сделано для облегчения игры для новичков и уравнивания стартовых условий
      Например, если первая же найденная колония имеет тип ядра "Водородный лёд" - новый игрок получает сильное пенальти на развитие планеты (планеты "Раритетного" класса очень сложно застраивать сам-один и для нормальной застройки требуется транспорт других типов ресурсов с остальных планет).
      <ul>
        <li>Если эффективный уровень Астрокартографии меньше 6 - можно найти планеты с ядрами классов "Стандарт" и "Продвинутый";</li>
        <li>6-10 - можно так же найти планеты с ядрами "Редкого" класса;</li>
        <li>Более 11 - можно так же найти планеты с ядрами "Раритетного" класса.</li>
      </ul>
      <br />

      Добыча считается в процентах от добычи указанного типа ресурсов на стандартном типе ядра в пересчете на металл приведенные к стоимости шахты:
      <ul>
        <li><span class="error">Очень плохая</span> - менее 40%</li>
        <li><span class="warning">Плохая</span> - не меньше 40%, но менее 80% </li>
        <li><span class="notice">Неплохая</span> - не меньше 80%, меньше 100% </li>
        <li>Неплохая - 100% базовой добычи</li>
        <li><span class="ok">Хорошая</span> - больше 100%, но меньше 300%</li>
        <li><span class="ok">Очень хорошая</span> - больше 300%, но меньше 400%</li>
        <li><span class="ok">Отличная</span> - больше 400%</li>
      </ul><br/>
      Класс типа ядра определяется в процентах от добычи всех ресурсов на стандартном типе ядра в пересчете на металл вычисленные для шахт эквивалентной стоимости предельно-достижимого уровня:
      <ul>
        <li>"Базовый" класс ядер - 100% от базовой добычи. Включает только ядра типа "Стандарт" - самый распространенный тип ядра планеты (примерно треть от всех планет)</li>
        <li>
          <span class="notice">Продвинутый</span> - более 100%, но менее 150% от базовой добычи.
            Включает в себя ядра типа "Руда", "Камень" и "Водный лёд".
            Самый распространенный класс ядер - примерно половина планет содержат ядра этого класса;
        </li>
        <li><span class="warning">Редкий</span> - не меньше 150%, но менее 250% от базовой добычи. Включает в себя ядра типа "Оливин", "Силикат" и "Метановый лёд". Как не сложно понять из названия класса - встречается редко;</li>
        <li><span class="error">Раритет</span> - не меньше 250% от базовой добычи. Включает в себя ядра типа "Металл", "Кристалл" и "Водородный лёд". Встречается ОЧЕНЬ редко - менее 5% планет содержат ядра этого класса.</li>
      </ul><br />
',
    ),

    UNIT_CAN_NOT_BE_BUILD => array(
      'description'       => 'Этот юнит не может быть построен игроком в настоящее время. Нет, его нельзя купить. Возможно, его можно было купить/построить раньше. И нет, не спрашивайте можно ли будет его построить когда-либо или получить как-нибудь. Сие неизвестно есть<br/>Хотя... может быть его можно получить другими способами? Спросите у других игроков... У АД-ии спрашивать бесполезно',
      'description_short' => 'В настоящее время этот юнит невозможно построить или купить',
      'effect'            => 'Этот юнит невозможно построить или купить',
    ),
  )
);
