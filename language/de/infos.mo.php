<?php

/*
#############################################################################
#  Filename: infos.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massen-Mehrspieler-Online-Browser-Weltraum-Strategiespiel
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [German]
* @version 46a158
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$a_lang_array = array(
  'wiki_title' => 'NovaPedia',

  'wiki_char_nominal' => 'Nominal',
  'wiki_char_actual' => 'Aktuell',

  'wiki_ship_engine_header' => 'Triebwerksmerkmale',

  'wiki_ship_header' => 'Transportmerkmale',
  'wiki_ship_speed' => 'Geschwindigkeit',
  'wiki_ship_consumption' => 'Deuteriumverbrauch',
  'wiki_ship_capacity' => 'Laderaumkapazität',
  'wiki_ship_hint' => '<li>Aktuelle Geschwindigkeit und Verbrauch werden unter Berücksichtigung aller Boni angezeigt - Technologien, Söldner usw.</li>',

  'wiki_combat_header' => 'Kampfmerkmale',
  'wiki_combat_attack' => 'Schusskraft, Treffer',
  'wiki_combat_shield' => 'Schildkapazität, Treffer',
  'wiki_combat_armor' => 'Strukturelle Integrität, Treffer',

  'wiki_combat_volley_header' => 'Schnellfeuer',
  'wiki_combat_volley_to' => 'Trifft Einheiten',
  'wiki_combat_volley_from' => 'Verliert Einheiten',
  'info' => array(
    STRUC_MINE_METAL => array(
      'description' => 'Der Hauptlieferant von Rohstoffen für den Bau von Gebäuden und Schiffen. Metall ist der billigste Rohstoff, wird aber in größeren Mengen benötigt als alles andere. Die Metallproduktion benötigt am wenigsten Energie. Je größer die Minen sind, desto tiefer reichen sie. Auf den meisten Planeten befindet sich Metall in großen Tiefen, in diesen tieferen Minen kann mehr Metall abgebaut werden, die Produktion steigt. Gleichzeitig benötigen größere Minen mehr Energie.',
      'description_short' => 'Hauptlieferant von Rohstoffen für den Bau von Gebäuden und Schiffen.',
    ),

    STRUC_MINE_CRYSTAL => array(
      'description' => 'Für die Kristallsynthese wird etwa doppelt so viel Energie benötigt wie für die Gewinnung der gleichen Menge Metall, daher ist es entsprechend wertvoller. Kristalle sind ein Hauptbestandteil moderner Computer und ein Schlüsselkomponente von Warp-Antrieben. Daher werden sie für alle Schiffe und fast alle Gebäude benötigt. Die Verbesserung des Synthesizers erhöht die Menge der produzierten Kristalle.',
      'description_short' => 'Hauptlieferant von Rohstoffen für Computersysteme und Warp-Antriebe.',
    ),

    STRUC_MINE_DEUTERIUM => array(
      'description' => 'Deuterium ist schwerer Wasserstoff. Wie bei den Minen befinden sich größere Vorkommen am Meeresboden. Die Verbesserung des Synthesizers fördert auch die Erschließung dieser tiefen Deuteriumvorkommen. Deuterium wird als Treibstoff für Schiffe, für fast alle Forschungen, zur Galaxieansicht und für die Verwendung der Sensorphalanx benötigt.',
      'description_short' => 'Extrahiert einen geringen Anteil Deuterium aus dem Planetenwasser.',
    ),

    STRUC_MINE_SOLAR => array(
      'description' => 'Für die Energieversorgung der Minen und Synthesizer sind riesige Solarkraftwerke erforderlich. Je mehr Kraftwerke gebaut werden, desto mehr Fläche ist mit Solarzellen bedeckt, die Lichtenergie in Elektrizität umwandeln. Solarkraftwerke sind die Grundlage der Energieversorgung eines Planeten.',
      'description_short' => 'Erzeugt Energie aus Sonnenlicht. Energie wird für den Betrieb der meisten Gebäude benötigt.',
    ),

    STRUC_MINE_FUSION => array(
      'description_short' => 'Erzeugt Energie durch die Fusion von zwei schweren Wasserstoffatomen zu einem Heliumatom',
      'description' => 'In Fusionskraftwerken verschmelzen unter enormem Druck und hoher Temperatur zwei schwere Wasserstoffatome zu einem Heliumatom. Dabei wird Energie in Form von Strahlung freigesetzt (41,32*10^-13 J pro Fusion, also 172 MW*h pro Gramm Wasserstoff). Je größer der Fusionsreaktor ist, desto komplexer werden die Fusionsprozesse, und der Reaktor produziert mehr Energie.<br><br>Produktionsformel:<br>30 * [Fusionskraftwerk-Level] * (1,05 + [Energietechnik-Level] * 0,01) ^ [Fusionskraftwerk-Level]<br>Die Energieproduktion kann auch durch die Entwicklung der Energietechnik erhöht werden.',
    ),

    STRUC_FACTORY_ROBOT => array(
      'description' => 'Stellt einfache Arbeitskräfte bereit, die beim Bau der planetaren Infrastruktur eingesetzt werden können. Jede Stufe der Fabrik erhöht die Baugeschwindigkeit von Gebäuden.',
      'description_short' => 'Produziert Maschinen und Mechanismen, die beim Bau der planetaren Infrastruktur eingesetzt werden. Jede Stufe der Fabrik erhöht die Baugeschwindigkeit von Gebäuden.',
    ),

    STRUC_FACTORY_NANO => array(
      'description' => 'Nanofabriken sind die finale Evolutionsstufe der robotergesteuerten Fabriken. Ihre einzige Ausstattung sind Nano-Assembler, die die Manipulation einzelner Moleküle und sogar Atome ermöglichen. Mit ihrer Erfindung wurde die Produktion nahezu aller Materialien mit vordefinierten Eigenschaften möglich. Darüber hinaus können Nano-Assembler sofort fertige Teile jeder Form und Konfiguration produzieren. Dennoch haben Nano-Assembler die herkömmlichen Fabriken nicht überflüssig gemacht. Obwohl eine Nanofabrik jede Konstruktion produzieren kann, ist es energetisch oft günstiger, Dinge "altmodisch" herzustellen. Aber selbst mit diesen Einschränkungen halbiert jede Stufe der Nanofabrik die Bauzeit aller Gebäude, Verteidigungsanlagen und Schiffe.',
      'description_short' => 'Die einzige Ausstattung dieser Fabrik sind Nano-Assembler - spezialisierte Komplexe, die Objekte aus einzelnen Molekülen und Atomen konstruieren können.',
      'effect' => 'Jede Stufe der Nanofabrik verdoppelt die Baugeschwindigkeit aller Konstruktionen.',
    ),

    STRUC_FACTORY_HANGAR => array(
      'description' => 'In der Werft werden alle Arten von Schiffen und Verteidigungsanlagen produziert. Je größer sie ist, desto schneller können komplexere und größere Schiffe und Verteidigungsanlagen gebaut werden. Durch den Bau von Nanofabriken werden viele technologische Ketten vereinfacht, was die Produktivität der Werft erheblich steigert.',
      'description_short' => 'Werften produzieren Raumschiffe, orbitale Strukturen und Verteidigungsanlagen.',
    ),

    STRUC_STORE_METAL => array(
      'description' => 'Lager für abgebautes Erz. Je größer es ist, desto mehr Erz kann gelagert werden. Wenn das Lager voll ist, wird die Metallproduktion eingestellt. Das Lager wird AUSSCHLIESSLICH für die Produktion verwendet - die Gesamtmenge an Metall auf dem Planeten (z.B. durch Transport von einer anderen Planeten) kann die maximale Lagerkapazität überschreiten.',
      'description_short' => 'Lager für abgebautes Erz vor der weiteren Verarbeitung.',
    ),

    STRUC_STORE_CRYSTAL => array(
      'description' => 'In diesem Lager werden Halbfabrikate für die Kristallsynthese gelagert. Je größer es ist, desto mehr Material kann gelagert werden. Wenn das Lager voll ist, wird die Kristallsynthese eingestellt. Das Lager wird AUSSCHLIESSLICH für die Produktion verwendet - die Gesamtmenge an Kristall auf dem Planeten (z.B. durch Transport von einer anderen Planeten) kann die maximale Lagerkapazität überschreiten.',
      'description_short' => 'Lager für die Lagerung von Kristall-Halbfabrikaten vor der weiteren Verarbeitung.',
    ),

    STRUC_STORE_DEUTERIUM => array(
      'description' => 'Spezielle Tanks für die Lagerung von schwerem Wasser. Sie befinden sich normalerweise in der Nähe von Raumhäfen. Je größer sie sind, desto mehr schweres Wasser kann gelagert werden. Wenn sie voll sind, wird die Deuteriumproduktion eingestellt. Die Tanks werden AUSSCHLIESSLICH für die Produktion verwendet - die Gesamtmenge an Deuterium auf dem Planeten (z.B. durch Transport von einer anderen Planeten) kann ihre maximale Kapazität überschreiten.',
      'description_short' => 'Tanks für die Lagerung von schwerem Wasser vor der Deuteriumextraktion.',
    ),

    STRUC_LABORATORY => array(
      'description' => 'Für die Erforschung neuer Technologien ist eine Forschungsstation erforderlich. Der Entwicklungsstand der Forschungsstation ist entscheidend dafür, wie schnell neue Technologien erforscht werden können. Je höher der Entwicklungsstand der Forschungslabor ist, desto mehr neue Technologien können erforscht werden. Um die Forschungsarbeiten auf einem Planeten so schnell wie möglich abzuschließen, werden alle verfügbaren Wissenschaftler dorthin geschickt und verlassen somit ihre Heimatplaneten. Sobald eine Technologie erforscht ist, kehren die Wissenschaftler auf ihre Heimatplaneten zurück und bringen das Wissen darüber mit. So können neue Technologien auch auf anderen Planeten angewendet werden.',
      'description_short' => 'Im Labor werden neue Technologien erforscht.',
    ),

    STRUC_TERRAFORMER => array(
      'description' => 'Mit zunehmender Bebauung der Planeten wurde die Frage der begrenzten nutzbaren Flächen immer wichtiger. Traditionelle Methoden wie das Bauen in die Höhe oder Tiefe erwiesen sich als unzureichend. Eine kleine Gruppe von Physikern und Nanotechnikern fand eine Lösung - den Terraformer.<br><br>Durch den Einsatz enormer Energiemengen kann der Terraformer große Gebiete oder sogar ganze Kontinente umwandeln und für die Bebauung nutzbar machen. In dieser Struktur werden kontinuierlich spezielle Naniten produziert, die für die ständige Bodenqualität verantwortlich sind.',
      'description_short' => 'Der Terraformer kann große Gebiete umwandeln und die Anzahl der verfügbaren Bausektoren erhöhen.',
    ),

    STRUC_ALLY_DEPOSIT => array(
      'description' => 'Das Allianzlager bietet die Möglichkeit, befreundete Flotten, die bei der Verteidigung helfen und im Orbit sind, mit Treibstoff zu versorgen. Je höher der Entwicklungsstand, desto mehr Deuterium kann an die Flotten im Orbit gesendet werden.',
      'description_short' => 'Das Allianzlager bietet die Möglichkeit, befreundete Flotten, die bei der Verteidigung helfen und im Orbit sind, mit Treibstoff zu versorgen.',
    ),

    STRUC_LABORATORY_NANO => array(
      'description' => 'Halbiert die Zeit der Forschungsphase.',
      'description_short' => 'Nanolabore sind mit modernster Technologie ausgestattet. Hochleistungs-Kristallcomputer und ultrapräzise Nano-Assembler ermöglichen es, jede Forschung zu beschleunigen.',
    ),

    STRUC_MOON_STATION => array(
      'description' => 'Der Mond hat keine Atmosphäre, daher muss vor der Besiedlung eine Mondbasis errichtet werden. Sie stellt die notwendige Luft, Schwerkraft und Wärme bereit. Je höher der Entwicklungsstand der Mondbasis ist, desto größer ist die mit einer Biosphäre versorgte Fläche. Jede Stufe der Mondbasis kann 3 Sektoren bebauen, maximal bis zur gesamten Mondfläche. Die Mondfläche beträgt 2 (Monddurchmesser/1000)^2, wobei jede Stufe der Mondbasis selbst ein Feld belegt.',
      'description_short' => 'Der Mond hat keine Atmosphäre, daher muss vor der Besiedlung eine Mondbasis errichtet werden.',
    ),

    STRUC_MOON_PHALANX => array(
      'description' => 'Hochfrequente Sensoren scannen das gesamte Spektrum der auf die Phalanx auftreffenden Strahlung. Leistungsstarke Computer kombinieren die kleinsten Energiefluktuationen und erhalten so Informationen über Schiffsbewegungen auf entfernten Planeten. Für die Ansicht muss dem Mond Energie in Form von Deuterium bereitgestellt werden (1000 * Phalanx-Level pro Ansicht). Die Ansicht erfolgt durch den Wechsel vom Mond zum Galaxiemenü und zum Namen des feindlichen Planeten, der sich im Sensorradius befindet (Formel: (Phalanx-Level)^2-1 Systeme).',
      'description_short' => 'Hochfrequente Sensoren scannen das gesamte Spektrum der auf die Phalanx auftreffenden Strahlung.',
    ),

    STRUC_MOON_GATE => array(
      'description' => 'Tore sind riesige Teleporter, die Flotten jeder Größe ohne Zeitaufwand zwischen sich versenden können. Diese Teleporter benötigen kein Deuterium, jedoch muss zwischen zwei Sprüngen eine Stunde vergehen, sonst überhitzen die Tore. Eine Ressourcenversendung ist ebenfalls nicht möglich. Der gesamte Prozess erfordert eine extrem hoch entwickelte Technologie.',
      'description_short' => 'Tore sind riesige Teleporter, die Flotten jeder Größe ohne Zeitaufwand zwischen sich versenden können.',
    ),

    STRUC_SILO => array(
      'description' => 'Raketensilos dienen der Lagerung von Raketen. Mit jeder Stufe können vier interplanetare oder zwölf Abfangraketen mehr gelagert werden. Eine interplanetare Rakete benötigt dreimal so viel Platz wie eine Abfangrakete. Jede Kombination verschiedener Raketentypen ist möglich.',
      'description_short' => 'Das Raketensilo ermöglicht den Start von Raketen und dient gleichzeitig als Raketenlager.',
    ),

    TECH_SPY => array(
      'description_short' => 'Mit dieser Technologie werden Daten über andere Planeten gesammelt.',
      'description' => 'Spionage dient der Erforschung neuer und effizienterer Sensoren. Je höher diese Technologie entwickelt ist, desto mehr Informationen hat der Spieler über die Ereignisse in seiner Umgebung. Der Unterschied im Spionagelevel zum Gegner spielt eine entscheidende Rolle - je höher die eigene Spionagetechnologie erforscht ist, desto mehr Informationen enthalten die Spionagedaten und desto geringer ist die Chance, entdeckt zu werden. Je mehr Sonden gesendet werden, desto mehr Details werden über den Gegner gesammelt, aber gleichzeitig steigt die Gefahr, entdeckt zu werden. Die Spionage verbessert auch die Ortung fremder Flotten. Dabei ist ebenfalls das eigene Spionagelevel entscheidend. Ab dem zweiten Entwicklungslevel wird bei einem Angriff neben der Angriffsmeldung auch die Gesamtzahl der angreifenden Schiffe angezeigt. Ab dem vierten Level werden die Arten der angreifenden Schiffe sowie ihre Gesamtzahl erkannt, und ab dem achten Level die genaue Anzahl jedes Schiffstyps. Für Angreifer ist diese Technologie sehr wichtig, da sie Informationen darüber liefert, ob das Opfer eine Flotte und/oder Verteidigung aufgestellt hat oder nicht. Daher sollte sie so früh wie möglich erforscht werden. Am besten direkt nach der Erforschung kleiner Transporter.',
    ),

    TECH_COMPUTER => array(
      'description' => 'Die Computertechnologie dient der Erweiterung der verfügbaren Rechenleistung. Dadurch werden auf dem Planeten produktivere und effizientere Computersysteme entwickelt, die Rechenleistung und die Geschwindigkeit der Berechnungen steigen. Mit zunehmender Computerleistung können gleichzeitig immer mehr Flotten befehligt werden. Jede Stufe der Computertechnologie ermöglicht das Kommando über +1 Flotte. Je mehr Flotten versendet werden, desto mehr Angriffe können durchgeführt und damit mehr Rohstoffe erbeutet werden. Natürlich ist diese Technologie auch für Händler nützlich, da sie ihnen ermöglicht, gleichzeitig mehr Handelsflotten zu versenden. Aus diesem Grund sollte die Computertechnologie während des gesamten Spiels kontinuierlich weiterentwickelt werden.',
      'description_short' => 'Mit zunehmender Computerleistung können immer mehr Flotten befehligt werden. Jede Stufe der Computertechnologie erhöht die maximale Anzahl der Flotten um eins.',
    ),

    TECH_WEAPON => array(
      'description' => 'Die Waffentechnologie beschäftigt sich vor allem mit der Weiterentwicklung der vorhandenen Waffensysteme. Dabei wird besonderer Wert darauf gelegt, die vorhandenen Systeme mit mehr Energie zu versorgen und diese Energie präziser zu lenken. Dadurch werden die Waffensysteme effektiver und die Waffen richten mehr Schaden an. Jede Stufe der Waffentechnologie erhöht die Feuerkraft der Truppen um 10%. Die Waffentechnologie ist wichtig für die Wettbewerbsfähigkeit der Truppen. Daher sollte sie während des gesamten Spiels kontinuierlich weiterentwickelt werden.',
      'description_short' => 'Die Waffentechnologie macht die Waffensysteme effektiver. Jede Stufe erhöht die Feuerkraft der Truppen um 10% des Basiswerts.',
    ),

    TECH_SHIELD => array(
      'description' => 'Die Entwicklung dieser Technologie ermöglicht eine erhöhte Energieversorgung der Schilde und Schutzschirme, was wiederum ihre Stabilität und ihre Fähigkeit erhöht, die Energie von Angriffen des Gegners zu absorbieren oder zu reflektieren. Dadurch steigt die Effektivität der Schiffe und stationären Kraftfelder mit jedem erforschten Level um 10% der Nennleistung.',
      'description_short' => 'Diese Technologie beschäftigt sich mit der Erforschung neuer Möglichkeiten zur besseren Energieversorgung von Schilden, was sie effektiver und stabiler macht. Dadurch steigt die Effektivität der Schilde mit jedem erforschten Level um 10%.',
    ),

    TECH_ARMOR => array(
      'description' => 'Spezielle Legierungen verbessern die Panzerung von Raumschiffen. Sobald eine sehr widerstandsfähige Legierung gefunden wurde, verändern spezielle Strahlen die molekulare Struktur des Raumschiffs und bringen sie in den Zustand der erforschten Legierung. So kann die Widerstandsfähigkeit der Panzerung mit jedem Level um 10% steigen.',
      'description_short' => 'Spezielle Legierungen verbessern die Panzerung von Raumschiffen. Mit jedem Level erhöht sich die Panzerungsstärke um 10% des Basiswerts.',
    ),

    TECH_ENERGY => array(
      'description' => 'Die Energietechnologie beschäftigt sich mit der Weiterentwicklung von Systemen zur Energieübertragung und -speicherung, die für viele neue Technologien benötigt werden.',
      'description_short' => 'Die Erforschung der Energietechnologie ermöglicht eine verbesserte Leistung von Fusionskraftwerken.',
    ),

    TECH_HYPERSPACE => array(
      'description' => 'Durch die Verflechtung der 4. und 5. Dimension wurde es möglich, einen neuen sparsameren und effizienteren Antrieb zu erforschen.',
      'description_short' => 'Durch die Verflechtung der 4. und 5. Dimension wurde es möglich, einen neuen sparsameren und effizienteren Antrieb zu erforschen.',
    ),

    TECH_ENGINE_CHEMICAL => array(
      'description' => 'Der chemische Raketenantrieb ist die einfachste Art von Antrieb. Dabei werden exotherme chemische Reaktionen von Treibstoff und Oxidator (zusammen als Treibstoff bezeichnet) genutzt, bei denen die Verbrennungsprodukte in der Brennkammer auf hohe Temperaturen erhitzt werden, sich ausdehnen, in einer Überschall-Düse beschleunigt werden und aus dem Antrieb strömen. Der Treibstoff eines chemischen Raketenantriebs ist sowohl die Quelle der thermischen Energie als auch des gasförmigen Arbeitsmediums, dessen innere Energie bei der Expansion in kinetische Energie des Strahls umgewandelt wird. Die Effizienz dieser Antriebe ist relativ gering, aber sie sind sehr zuverlässig, günstig in der Produktion und anspruchslos in der Wartung. Außerdem nehmen sie im Vergleich zu anderen Antrieben viel weniger Platz auf dem Schiff ein, daher findet man sie oft auf kleinen Schiffen. Da chemische Antriebe die Grundlage für jeden Raumflug sind, sollten sie so früh wie möglich erforscht werden. Jede Stufe der Entwicklung dieser Antriebe macht die folgenden Schiffe 10% schneller: Kleiner Transporter (bis zur Erforschung des Ionenantriebs Level 5), Großer Transporter, Recycler, Spionagesonde und Leichter Jäger.',
      'description_short' => 'Die weitere Entwicklung dieser Antriebe macht einige Schiffe schneller. Jedes Level erhöht die Geschwindigkeit des Schiffes um 10% des Basiswerts.',
    ),

    TECH_ENGINE_ION => array(
      'description' => 'Das Funktionsprinzip des Ionenantriebs besteht darin, Gas zu ionisieren und es mit einem elektrostatischen Feld zu beschleunigen. Aufgrund des hohen Verhältnisses von Ladung zu Masse können Ionen auf sehr hohe Geschwindigkeiten beschleunigt werden (bis zu 210 km/s im Vergleich zu 3-4,5 km/s bei chemischen Raketenantrieben). So kann im Ionenantrieb ein sehr hoher spezifischer Impuls erreicht werden. Dadurch kann der Verbrauch des ionisierten Gases im Vergleich zu chemischen Raketen deutlich reduziert werden, erfordert aber einen höheren Energieaufwand. Daher tragen alle Schiffe mit Ionenantrieb einen zusätzlichen Fusionsreaktor, der ausschließlich für den Antrieb arbeitet, und zeichnen sich durch einen erhöhten Deuteriumverbrauch aus. Am sinnvollsten ist der Einbau dieser Antriebe entweder in leichte Schiffe, die schnell beschleunigen müssen, oder in schwere Schiffe, deren Beschleunigung keine Rolle spielt. Daher umfasst die Liste der Schiffe mit diesem Antrieb den Kleinen Transporter (nach der Entwicklung des Antriebs Level 5), den Supertransporter, den Kolonisierer, den Schweren Jäger, den Zerstörer und den Bomber (bis zur Entwicklung der Hyperantriebstechnologie Level 8). Diese Antriebe werden auch in interplanetaren Raketen eingesetzt - in diesem Fall wird der Reaktor so eingestellt, dass die Treibstoffreste als zusätzlicher Sprengstoff dienen.',
      'description_short' => 'Der Ionenantrieb funktioniert nach dem Prinzip der Ionisierung von Gas und dessen Beschleunigung durch ein elektrostatisches Feld. Jede Stufe der Entwicklung dieser Antriebe macht die damit ausgerüsteten Schiffe um 20% der Basisschnelligkeit schneller.',
    ),

    TECH_ENGINE_HYPER => array(
      'description' => 'Durch die Raum-Zeit-Krümmung in der unmittelbaren Umgebung des Schiffes wird der Raum komprimiert, wodurch große Entfernungen schneller überwunden werden können. Je höher der Hyperantrieb entwickelt ist, desto stärker ist die Raumkompression, wodurch die folgenden Schiffe mit jedem Level um 30% schneller werden: Hypertransporter, Kreuzer, Bomber (nach der Entwicklung des Antriebs Level 8), Schlachtkreuzer, Zerstörer, Todesstern und der Kreuzer der "SuperNova"-Klasse.',
      'description_short' => 'Der Antrieb komprimiert die Raum-Zeit um das Schiff herum und beschleunigt es auf Überlichtgeschwindigkeit. Mit jedem Level erhöht sich die Geschwindigkeit der Schiffe um 30%.',
    ),

    TECH_LASER => array(
      'description' => 'Laser (Lichtverstärkung durch stimulierte Emission von Strahlung) erzeugen einen energiereichen Strahl kohärenten Lichts. Diese Geräte finden in allen möglichen Bereichen Anwendung, von optischen Computern bis hin zu schweren Lasern, die problemlos die Panzerung von Raumschiffen durchschneiden. Die Lasertechnologie ist ein wichtiges Element für die Erforschung weiterer Waffentechnologien.',
      'description_short' => 'Durch die Fokussierung von Licht entsteht ein Strahl, der beim Auftreffen auf ein Objekt Schaden verursacht.',
    ),

    TECH_ION => array(
      'description' => 'Ein wahrhaft tödlicher Strahl aus beschleunigten Ionen. Beim Auftreffen auf ein Objekt verursachen sie enormen Schaden.',
      'description_short' => 'Ein wahrhaft tödlicher Strahl aus beschleunigten Ionen. Beim Auftreffen auf ein Objekt verursachen sie enormen Schaden.',
    ),

    TECH_PLASMA => array(
      'description' => 'Eine Weiterentwicklung der Ionentechnologie, die nicht Ionen, sondern hochenergetisches Plasma beschleunigt. Es hat eine verheerende Wirkung beim Auftreffen auf ein Objekt.',
      'description_short' => 'Eine Weiterentwicklung der Ionentechnologie, die nicht Ionen, sondern hochenergetisches Plasma beschleunigt. Es hat eine verheerende Wirkung beim Auftreffen auf ein Objekt.',
    ),

    TECH_RESEARCH => array(
      'description' => 'Dieses Netzwerk ermöglicht die Kommunikation zwischen Wissenschaftlern, die in Forschungslabors auf verschiedenen Planeten arbeiten. Jede neue Stufe ermöglicht die Verbindung eines zusätzlichen Labors (zuerst werden die Labors mit den höchsten Stufen verbunden). Von allen vernetzten Labors nehmen nur diejenigen an einer Forschung teil, die das erforderliche Level für diese Forschung haben. Die Forschungsgeschwindigkeit entspricht der Summe der Level der beteiligten Labors.',
      'description_short' => 'Dieses Netzwerk ermöglicht die Kommunikation zwischen Wissenschaftlern, die in Forschungslabors auf verschiedenen Planeten arbeiten. Jede neue Stufe ermöglicht die Verbindung eines zusätzlichen Labors.',
    ),

    TECH_EXPEDITION => array(
      'description' => 'Die Expeditionstechnologie umfasst verschiedene Scan-Technologien und ermöglicht die Ausstattung von Schiffen verschiedener Klassen mit einem Forschungsmodul. Es enthält eine Datenbank, ein kleines mobiles Labor sowie verschiedene Biokammern und Probenbehälter. Für die Sicherheit des Schiffes bei der Erforschung gefährlicher Objekte ist das Forschungsmodul mit einer autonomen Energieversorgung und einem Kraftfeldgenerator ausgestattet, der in Extremsituationen das Modul mit einem starken Kraftfeld umgeben kann.',
      'description_short' => 'Schiffe können nun mit einem Forschungsmodul ausgestattet werden, das die Verarbeitung gesammelter Daten während langer Flüge ermöglicht.',
    ),

    TECH_COLONIZATION => array(
      'description' => 'Ein Herrscher mit vielen Kolonien im Universum hat mehr Vorteile gegenüber anderen.',
      'description_short' => 'Diese Technologie ist sehr wichtig, damit du ein Imperium mit vielen Kolonien gründen kannst.',
    ),

    TECH_ASTROTECH => array(
      'description' => 'Die Astrokartografie ermöglicht es, die maximale Anzahl an Kolonien und Expeditionen sowie die maximale Dauer einer Expedition zu erhöhen.',
      'description_short' => 'Die Astrokartografie ermöglicht es, die maximale Anzahl an Kolonien und Expeditionen sowie die maximale Dauer einer Expedition zu erhöhen.',
    ),

    TECH_GRAVITON => array(
      'description' => 'Ein Graviton ist ein Teilchen, das weder Masse noch Ladung besitzt und die Anziehungskraft bestimmt. Durch das Abfeuern eines konzentrierten Gravitonenstrahls kann ein künstliches Gravitationsfeld erzeugt werden, das ähnlich wie ein schwarzes Loch Masse anzieht und so Schiffe oder sogar Monde zerstören kann. Um genügend Gravitonen zu produzieren, werden enorme Energiemengen benötigt.',
      'description_short' => 'Durch das Abfeuern eines konzentrierten Gravitonenstrahls kann ein künstliches Gravitationsfeld erzeugt werden, das ähnlich wie ein schwarzes Loch Masse anzieht und so Schiffe oder sogar Monde zerstören kann.',
    ),

    SHIP_CARGO_SMALL => array(
      'description' => 'Transporter haben etwa die gleiche Größe wie Jäger, aber sie besitzen keine leistungsstarken Antriebe und Bordwaffen, um Platz zu sparen. Ein kleiner Transporter kann 5000 Einheiten Rohstoffe transportieren. Aufgrund der geringen Feuerkraft werden kleine Transporter oft von anderen Schiffen begleitet. Wenn der Ionenantrieb bis zur Stufe 5 erforscht ist, erhöht sich die Basisschnelligkeit des kleinen Transporters, und er wird mit diesem Antriebstyp ausgestattet.',
      'description_short' => 'Der kleine Transporter ist ein wendiges Schiff, das schnell Rohstoffe zu anderen Planeten transportieren kann. Nach der Erforschung des Ionenantriebs Level 5 werden die Schiffe umgerüstet.',
    ),

    SHIP_CARGO_BIG => array(
      'description' => 'An Bord dieses Schiffes gibt es nur schwache Bewaffnung und keine ernsthaften Technologien... Aus diesem Grund sollten sie niemals ohne Begleitung losgeschickt werden. Dank seines hoch entwickelten chemischen Antriebs dient der große Transporter als schneller interplanetarer Ressourcenlieferant und begleitet Flotten bei Angriffen auf feindliche Planeten, um so viele Rohstoffe wie möglich zu erbeuten.',
      'description_short' => 'Der große Transporter hat eine größere Kapazität als der kleine Transporter. Die Geschwindigkeit des großen Transporters ist ebenfalls höher, aber nur bis die kleinen Transporter mit Ionenantrieben Level 5 ausgestattet werden.',
    ),

    SHIP_CARGO_SUPER => array(
      'description' => 'Das letzte Wort in der Transporttechnologie. Der Supertransporter ist ein riesiges Transportschiff, das mit Ionenantrieben ausgestattet ist. Seine Geschwindigkeit ist gering und der Treibstoffverbrauch sehr hoch, aber dies wird durch seine außergewöhnliche Kapazität mehr als wettgemacht. Der Supertransporter ist nur mit Laser-Antimeteoriten-Türmen ausgestattet, hat aber einen starken Schild.',
      'description_short' => 'Ein riesiges selbstfahrendes Schiff, das mit Ionenantrieben ausgestattet ist. Es ist mit einem starken Schild ausgestattet, hat aber nur Laser-Antimeteoriten-Türme als Bewaffnung.',
    ),

    SHIP_CARGO_HYPER => array(
      'description' => 'Wenn der Supertransporter das letzte Wort in der Transporttechnologie ist - dann ist der Hypertransporter der Endpunkt. "Riesig" ist ein zu schwaches Wort, um dieses Schiff zu beschreiben. Mit der Größe eines kleinen Mondes kann dieser Transporter eine unglaubliche Menge an Ressourcen transportieren. Nur Hyperantriebe können ein voll beladenes Schiff bewegen. Sie ermöglichen es dem Hypertransporter, sich mit akzeptabler Geschwindigkeit durch das Universum zu bewegen. Aber der Preis ist hoch - die Kosten des Transporters nähern sich denen eines Dutzends Zerstörern. Und der Treibstoffverbrauch kann selbst einen Kaiser zum Weinen bringen. Daher ist der Hypertransporter ein Schiff für riesige und mächtige Imperien, die Dutzende oder Hunderte von Millionen Tonnen Ressourcen auf einmal transportieren müssen.',
      'description_short' => 'Ein Transportschiff von der Größe eines kleinen Mondes. Ausgestattet mit Hyperantrieben und in der Lage, eine Million Tonnen Ressourcen in einem einzigen Transport zu befördern.',
    ),

    SHIP_SMALL_FIGHTER_LIGHT => array(
      'description' => 'Der leichte Jäger ist ein wendiges Schiff, das auf fast jedem Planeten zu finden ist. Die Kosten dafür sind nicht besonders hoch, aber die Schildkraft und Kapazität sind sehr gering.',
      'description_short' => 'Der leichte Jäger ist ein wendiges Schiff, das auf fast jedem Planeten zu finden ist. Die Kosten dafür sind nicht besonders hoch, aber die Schildkraft und Kapazität sind sehr gering.',
    ),

    SHIP_SMALL_FIGHTER_HEAVY => array(
      'description' => 'Bei der weiteren Entwicklung des leichten Jägers kamen die Wissenschaftler zu dem Punkt, an dem klar wurde, dass ein herkömmlicher Antrieb nicht über die erforderliche Leistung verfügte. Um das Schiff optimal zu bewegen, wurde erstmals ein Ionenantrieb eingesetzt. Obwohl dies die Kosten erhöhte, eröffnete es auch neue Möglichkeiten. Durch den Einsatz dieses Antriebs blieb mehr Energie für Bewaffnung und Schilde übrig, außerdem wurden für diese Art von Jägern auch wertvolle Materialien verwendet. Dies führte zu einer verbesserten strukturellen Integrität und einer stärkeren Feuerkraft, wodurch er im Kampf eine größere Bedrohung darstellt als sein Vorgänger. Nach diesen Änderungen repräsentiert der schwere Jäger eine neue Ära der Schiffbautechnologie, die Grundlage der Kreuzerbautechnologie.',
      'description_short' => 'Eine Weiterentwicklung des leichten Jägers, er ist besser geschützt und hat eine höhere Angriffskraft.',
    ),

    SHIP_MEDIUM_DESTROYER => array(
      'description' => 'Mit der Entwicklung schwerer Laser und Ionenkanonen wurden schwere Jäger immer mehr verdrängt. Trotz zahlreicher Verbesserungen konnten Feuerkraft und Panzerung nicht so verändert werden, dass sie effektiv gegen diese Verteidigungswaffen bestehen konnten. Daher wurde beschlossen, eine neue Schiffsklasse zu bauen, die mehr Panzerung und Feuerkraft vereint. So entstanden die Zerstörer. Zerstörer sind fast dreimal stärker geschützt als schwere Jäger und verfügen über mehr als doppelt so viel Feuerkraft. Außerdem sind sie sehr schnell. Es gibt keine bessere Waffe gegen eine mittlere Verteidigung. Fast ein Jahrhundert lang herrschten Kreuzer uneingeschränkt im Universum. Mit dem Aufkommen von Gauß- und Plasmakanonen endete ihre Herrschaft. Dennoch werden sie auch heute noch gerne gegen Jägergruppen eingesetzt.',
      'description_short' => 'Zerstörer sind fast dreimal stärker geschützt als schwere Jäger und haben fast die doppelte Feuerkraft. Außerdem sind sie sehr schnell.',
    ),

    SHIP_LARGE_CRUISER => array(
      'description' => 'Kreuzer bilden in der Regel das Rückgrat der Flotte. Ihre schweren Geschütze, hohe Geschwindigkeit und große Ladekapazität machen sie zu ernsthaften Gegnern.',
      'description_short' => 'Kreuzer bilden in der Regel das Rückgrat der Flotte. Ihre schweren Geschütze, hohe Geschwindigkeit und große Ladekapazität machen sie zu ernsthaften Gegnern.',
    ),

    SHIP_COLONIZER => array(
      'description' => 'Dieses gut geschützte Schiff dient der Eroberung neuer Planeten, die ein wachsendes Imperium benötigt. Es wird in der neuen Kolonie als Rohstofflieferant verwendet - es wird auseinandergenommen und das gesamte nutzbare Material für die Erschließung der neuen Welt verwendet. Die maximale Anzahl der Kolonien hängt von den Universumseinstellungen ab, die über den Menüpunkt "Weltkonstanten" eingesehen werden können.',
      'description_short' => 'Mit diesem Schiff können unbewohnte Planeten besiedelt werden.',
    ),

    SHIP_RECYCLER => array(
      'description' => 'Raumkäufe nahmen immer größere Ausmaße an. Tausende von Schiffen wurden zerstört, und die dabei entstandenen Trümmer schienen für immer verloren zu sein. Normale Transporter konnten sich ihnen nicht nähern, ohne durch kleine Trümmerteile schwer beschädigt zu werden. Mit einer neuen Entdeckung im Bereich der Schildtechnologie wurde es möglich, dieses Problem effektiv zu lösen, und eine neue Schiffsklasse entstand, ähnlich dem großen Transporter - der Recycler. Mit ihm konnten die scheinbar verlorenen Ressourcen wieder genutzt werden. Dank der neuen Schilde stellten kleine Trümmerteile keine Gefahr mehr dar. Leider benötigen diese Geräte Platz, daher ist seine Ladekapazität auf 20.000 begrenzt.',
      'description_short' => 'Mit dem Recycler werden Rohstoffe aus Trümmern gewonnen.',
    ),

    SHIP_SPY => array(
      'description' => 'Spionagesonden sind kleine, wendige Schiffe, die aus großer Entfernung Daten über Flotten und Planeten liefern. Ihr hochleistungsfähiger Antrieb ermöglicht es ihnen, große Entfernungen in wenigen Sekunden zu überwinden. Einmal in der Umlaufbahn eines Planeten angekommen, sammeln sie einige Zeit lang Daten. Während dieser Zeit kann der Feind sie relativ leicht entdecken und angreifen. Um Platz zu sparen, wurden weder Panzerung, Schilde noch Waffen installiert, was die Sonden im Falle einer Entdeckung zu leichten Zielen macht.',
      'description_short' => 'Spionagesonden sind kleine, wendige Schiffe, die aus großer Entfernung Daten über Flotten und Planeten liefern.',
    ),

    SHIP_LARGE_BOMBER => array(
      'description' => 'Der Bomber wurde speziell entwickelt, um die planetare Verteidigung zu zerstören. Mit einem Laserzielgerät wirft er präzise Plasmabomben auf die Planetenoberfläche und richtet so enorme Schäden an den Verteidigungsanlagen an. Wenn der Hyperantrieb bis zur Stufe 8 erforscht ist, erhöht sich die Basisschnelligkeit des Bombers, und er wird mit diesem Antriebstyp ausgestattet.',
      'description_short' => 'Der Bomber wurde speziell entwickelt, um die planetare Verteidigung zu zerstören.',
    ),

    SHIP_SATTELITE_SOLAR => array(
      'description' => 'Solarsatelliten werden in die Umlaufbahn eines Planeten geschickt. Sie sammeln Sonnenenergie und übertragen sie an eine Bodenstation. Die Effizienz der Solarsatelliten hängt von der Stärke der Sonneneinstrahlung ab. Grundsätzlich ist die Energiegewinnung in Umlaufbahnen, die näher an der Sonne liegen, höher als auf Planeten, die weiter von der Sonne entfernt sind. Aufgrund ihres Preis-Leistungs-Verhältnisses lösen Solarsatelliten die Energieprobleme vieler Welten. Aber Achtung: Solarsatelliten können im Kampf zerstört werden.',
      'description_short' => 'Solarsatelliten sind einfache Plattformen aus Solarzellen, die sich in einer hohen Umlaufbahn befinden. Sie sammeln Sonnenlicht und übertragen es per Laser an eine Bodenstation.',
    ),

    SHIP_LARGE_DESTRUCTOR => array(
      'description' => 'Der Zerstörer ist der König unter den Kriegsschiffen. Seine mehrflügeligen Ionen-, Plasma- und Gaußkanonentürme können dank ihrer verbesserten Peilsensoren sogar schnelle, wendige Jäger mit einer Treffergenauigkeit von 99% treffen. Da Zerstörer sehr groß sind, ist ihre Manövrierfähigkeit sehr eingeschränkt, und im Kampf ähneln sie eher einer Kampfstation als einem Kriegsschiff. Ihr Deuteriumverbrauch ist ebenso hoch wie ihre Kampfkraft.',
      'description_short' => 'Der Zerstörer ist der König unter den Kriegsschiffen.',
    ),

    SHIP_HUGE_DEATH_STAR => array(
      'description' => 'Der Todesstern ist mit einer Gravitonenkanone ausgestattet, die alle Arten von Schiffen und sogar Monde zerstören kann. Um genügend Energie zu produzieren, besteht der Todesstern praktisch vollständig aus Generatoren. Nur riesige Sternenimperien haben genug Ressourcen und Arbeitskräfte, um dieses gewaltige Schiff zu bauen.',
      'description_short' => 'Der Todesstern ist mit einer riesigen Gravitonenkanone ausgestattet, die Schiffe und sogar Monde zerstören kann.',
    ),

    SHIP_LARGE_BATTLESHIP => array(
      'description' => 'Dieses Hochtechnologieschiff bringt Tod für angreifende Flotten. Seine verbesserten Lasergeschütze halten schwere feindliche Schiffe auf Distanz und können mehrere Einheiten mit einem Salvo vernichten. Aufgrund seiner geringen Größe und unglaublich starken Bewaffnung ist die Ladekapazität des Schlachtkreuzers sehr gering, aber dank des Hyperantriebs ist auch der Treibstoffverbrauch gering.',
      'description_short' => 'Der Schlachtkreuzer ist auf die Abwehr feindlicher Flotten spezialisiert.',
    ),

    SHIP_HUGE_SUPERNOVA => array(
      'description_short' => 'Der Kreuzer der "SuperNova"-Klasse ist das Flaggschiff der Raumflotte des Imperiums.',
      'description' => 'Der Kreuzer der "SuperNova"-Klasse ist das Flaggschiff der Raumflotte des Imperiums. Die enorme Baukosten werden durch die erschreckende Feuerkraft und fortschrittliche Schutzsysteme mehr als wettgemacht. Ein einziges Schiff dieser Klasse kann eine mittlere Flotte allein vernichten.',
    ),

    UNIT_DEF_TURRET_MISSILE => array(
      'description' => 'Die Raketenanlage ist ein einfaches und billiges Verteidigungsmittel. Da es sich um eine Weiterentwicklung herkömmlicher ballistischer Geschütze handelt, benötigt es keine weitere Modernisierung. Die geringen Produktionskosten rechtfertigen seinen Einsatz gegen kleinere Flotten, aber mit der Zeit verliert es an Bedeutung. Später wird es nur noch verwendet, um feindliche Schüsse abzulenken. Verteidigungsanlagen deaktivieren sich selbst, sobald sie stark beschädigt werden. Die Möglichkeit, Verteidigungsanlagen nach einem Kampf wiederherzustellen, beträgt bis zu 70%.',
      'description_short' => 'Die Raketenanlage ist ein einfaches und billiges Verteidigungsmittel.',
    ),

    UNIT_DEF_TURRET_LASER_SMALL => array(
      'description_short' => 'Durch den konzentrierten Beschuss eines Ziels mit Photonen können deutlich größere Zerstörungen erreicht werden als mit herkömmlicher ballistischer Bewaffnung.',
      'description' => 'Um die übermäßigen Erfolge in der Raumschifftechnologie auszugleichen, mussten Wissenschaftler eine Verteidigungsanlage schaffen, die mit größeren und besser bewaffneten Flotten fertig wird. Dies führte zur Entwicklung des leichten Lasers. Durch den konzentrierten Beschuss eines Ziels mit Photonen können deutlich größere Zerstörungen erreicht werden als mit herkömmlicher ballistischer Bewaffnung. Um der stärkeren Feuerkraft neuer Schiffstypen standzuhalten, ist er auch mit verbesserten Schilden ausgestattet. Um jedoch die Produktionskosten niedrig zu halten, wurde die Struktur nicht weiter verstärkt. Der leichte Laser bietet das beste Preis-Leistungs-Verhältnis und ist daher auch für weiter entwickelte Zivilisationen interessant. Verteidigungsanlagen deaktivieren sich selbst, sobald sie stark beschädigt werden. Die Möglichkeit, Verteidigungsanlagen nach einem Kampf wiederherzustellen, beträgt bis zu 70%.',
    ),

    UNIT_DEF_TURRET_LASER_BIG => array(
      'description' => 'Der schwere Laser ist eine Weiterentwicklung des leichten Lasers. Die Struktur wurde verstärkt und mit neuen Materialien verbessert. Die Hülle konnte deutlich widerstandsfähiger gemacht werden. Gleichzeitig wurden das Energiesystem und der Zielcomputer verbessert, sodass der schwere Laser deutlich mehr Energie auf das Ziel konzentrieren kann. Verteidigungsanlagen deaktivieren sich selbst, sobald sie stark beschädigt werden. Die Möglichkeit, Verteidigungsanlagen nach einem Kampf wiederherzustellen, beträgt bis zu 70%.',
      'description_short' => 'Der schwere Laser ist eine Weiterentwicklung des leichten Lasers.',
    ),

    UNIT_DEF_TURRET_GAUSS => array(
      'description_short' => 'Die Gaußkanone beschleunigt tonnenschwere Geschosse mit enormem Energieaufwand.',
      'description' => 'Lange Zeit galten Artilleriegeschütze angesichts der Entwicklung moderner Laser- und Ionentechnik und der ständig verbesserten Schutzsysteme als veraltet, bis neue Forschungen im Bereich der Energietechnik die Artillerie auf ein qualitativ neues Niveau hoben. Eigentlich waren die Prinzipien elektromagnetischer Massenbeschleuniger auf der Erde schon seit dem 20. Jahrhundert bekannt. Einer davon ist die Gaußkanone. Mit der Lösung der technischen Schwierigkeiten wurde ihr Einsatz als Waffe Realität. Tonnenschwere Geschosse werden durch ein Magnetfeld mit enormem Energieaufwand beschleunigt und haben eine so hohe Austrittsgeschwindigkeit, dass Staubpartikel in der Luft um das Geschoss herum verbrennen und die Schallwelle den Boden erschüttert. Selbst moderne Panzerungen und Schilde können der Durchschlagskraft der Gaußkanone nur schwer standhalten, und es kommt nicht selten vor, dass das Ziel einfach durchschossen wird. Verteidigungsanlagen deaktivieren sich selbst, sobald sie stark beschädigt werden. Die Möglichkeit, Verteidigungsanlagen nach einem Kampf wiederherzustellen, beträgt bis zu 70%.',
    ),

    UNIT_DEF_TURRET_ION => array(
      'description_short' => 'Die Ionenkanone richtet eine Ionenwelle auf das Ziel, die Schilde destabilisiert und Elektronik beschädigt.',
      'description' => 'Im 21. Jahrhundert gab es auf der Erde bereits das, was allgemein als EMP bekannt ist. EMP steht für elektromagnetischer Impuls, der die Fähigkeit besitzt, zusätzliche Spannungen in Schaltkreise zu induzieren und damit massive Störungen zu verursachen, die alle empfindlichen Geräte zerstören können. Damals basierten EMP-Waffen hauptsächlich auf Raketen und Bomben, auch in Kombination mit Nuklearwaffen. Inzwischen haben sich EMP-Waffen ständig weiterentwickelt, da man in ihnen ein großes Potenzial sah, Ziele nicht zu zerstören, sondern kampf- und manövrierunfähig zu machen und damit ihre Eroberung zu vereinfachen. Bisher ist die höchste Form der EMP-Waffen die Ionenkanone. Sie richtet eine Welle aus Ionen (elektrisch geladene Teilchen) auf das Ziel, die Schilde destabilisiert und Elektronik beschädigt, sofern sie nicht sehr gut geschützt ist, was manchmal einer vollständigen Zerstörung gleichkommt. Die kinetische Durchschlagskraft kann vernachlässigt werden. Die Ionentechnik wird nur auf Kreuzern eingesetzt, da der Energieverbrauch der Ionenkanonen enorm ist und im Kampf oft Ziele zerstört werden müssen, anstatt sie zu lähmen. Verteidigungsanlagen deaktivieren sich selbst, sobald sie stark beschädigt werden. Die Möglichkeit, Verteidigungsanlagen nach einem Kampf wiederherzustellen, beträgt bis zu 70%.',
    ),

    UNIT_DEF_TURRET_PLASMA => array(
      'description' => 'Die Lasertechnologie wurde zur Perfektion gebracht, die Ionentechnik erreichte ihr Endstadium und es wurde für praktisch unmöglich gehalten, selbst aus qualitativer Sicht der Waffensysteme, noch größere Effektivität zu erreichen. Aber alles sollte sich ändern, als die Idee aufkam, beide Systeme zu kombinieren. Mit Hilfe der Fusionsforschung werden Substanzen (normalerweise Deuterium) mit Lasern auf ultrahohe Temperaturen von Millionen Grad erhitzt. Die Ionentechnik sorgt für die Anreicherung des Plasmas mit elektrischer Ladung, seine Stabilisierung und Beschleunigung. Sobald die Ladung ausreichend erhitzt, ionisiert und unter Druck steht, wird sie mit Hilfe von Beschleunigern in Richtung des Ziels abgefeuert. Die bläulich leuchtende Plasmakugel sieht beeindruckend aus, nur fragt man sich, wie lange die Besatzung des Zielschiffs sie genießen wird, wenn die Panzerung innerhalb weniger Sekunden in Stücke gerissen wird und die Elektronik durchbrennt... Die Plasmakanone gilt allgemein als die schrecklichste Waffe, und diese Technik hat ihren Preis. Verteidigungsanlagen deaktivieren sich selbst, sobald sie stark beschädigt werden. Die Möglichkeit, Verteidigungsanlagen nach einem Kampf wiederherzustellen, beträgt bis zu 70%.',
      'description_short' => 'Das letzte Wort in planetaren Verteidigungstechnologien, geboren aus der Symbiose von Laser- und Ionentechnik.',
    ),

    UNIT_DEF_SHIELD_SMALL => array(
      'description' => 'Lange bevor Schildgeneratoren klein genug waren, um auf Schiffen eingesetzt zu werden, gab es bereits riesige Generatoren auf Planetenoberflächen. Sie umhüllten einen ganzen Planeten mit einem Kraftfeld, das Angriffsschläge absorbieren konnte. Kleine Angriffsflotten scheitern ständig an diesen Schildkuppeln. Dank des wachsenden technologischen Fortschritts können diese Schilde noch verstärkt werden. Später kann eine stärkere große Schildkuppel gebaut werden. Auf jedem Planeten kann nur eine kleine Schildkuppel gebaut werden.',
      'description_short' => 'Die kleine Schildkuppel schützt den Planeten und absorbiert Angriffsschläge.',
    ),

    UNIT_DEF_SHIELD_BIG => array(
      'description' => 'Eine Weiterentwicklung der kleinen Schildkuppel. Sie kann noch stärkere Angriffe auf den Planeten abwehren, indem sie deutlich mehr Energie absorbiert.',
      'description_short' => 'Eine Weiterentwicklung der kleinen Schildkuppel. Sie kann noch stärkere Angriffe auf den Planeten abwehren, indem sie deutlich mehr Energie absorbiert.',
    ),

    UNIT_DEF_SHIELD_PLANET => array(
      'description' => 'Der beste Schutz für deine Planeten',
      'description_short' => 'Der beste Schutz für deine Planeten',
    ),

    UNIT_DEF_MISSILE_INTERCEPTOR => array(
      'description' => 'Abfangraketen zerstören angreifende interplanetare Raketen. Eine Abfangrakete zerstört eine interplanetare Rakete.',
      'description_short' => 'Abfangraketen zerstören angreifende interplanetare Raketen.',
    ),

    UNIT_DEF_MISSILE_INTERPLANET => array(
      'description_short' => 'Interplanetare Raketen zerstören die feindliche Verteidigung.',
      'description' => 'Interplanetare Raketen zerstören die Verteidigung des Gegners. Durch interplanetare Raketen zerstörte Verteidigungsanlagen werden nicht wiederhergestellt.',
    ),

    MRC_TECHNOLOGIST => array(
      'description' => 'Der Technologe ist ein anerkannter Experte für die Optimierung der Gewinnung und Verarbeitung von Ressourcen. Mit seinem Team aus Metallurgen, Chemikern und Energietechnikern unterstützt er planetare Regierungen bei der Entwicklung neuer Ressourcenquellen und optimiert deren Aufbereitung.',
      'description_short' => 'Der Technologe ist ein anerkannter Experte für die Optimierung der Gewinnung und Verarbeitung von Ressourcen. Mit seinem Team aus Metallurgen, Chemikern und Energietechnikern unterstützt er planetare Regierungen bei der Entwicklung neuer Ressourcenquellen und optimiert deren Aufbereitung.',
      'effect' => 'zur Gewinnung von Metall, Kristallen und Deuterium, zur Energieerzeugung in Solar- und Fusionskraftwerken für jedes Level.',
    ),

    MRC_ENGINEER => array(
      'description' => 'Der Ingenieur ist der modernste Baumeister des Imperiums. Seine DNA wurde mutiert, was ihm einen außergewöhnlichen Verstand und übermenschliche Kraft verlieh. Ein Ingenieur kann allein eine ganze Stadt entwerfen und bauen.',
      'description_short' => 'Der Ingenieur ist der modernste Baumeister des Imperiums. Seine DNA wurde mutiert, was ihm einen außergewöhnlichen Verstand und übermenschliche Kraft verlieh. Ein Ingenieur kann allein eine ganze Stadt entwerfen und bauen.',
      'effect' => 'zur Bauzeit von Gebäuden und Schiffen für jedes Level<br />+1 Slot für Bau- und Schiffswarteschlangen für jedes Level',
    ),

    MRC_FORTIFIER => array(
      'description' => 'Der Festungsbauer ist ein militärischer Ingenieurspezialist. Sein tiefes Wissen über Verteidigungssysteme ermöglicht es, die Bauzeiten planetarer Verteidigungssysteme zu verkürzen.',
      'description_short' => 'Der Festungsbauer ist ein militärischer Ingenieurspezialist. Sein tiefes Wissen über Verteidigungssysteme ermöglicht es, die Bauzeiten planetarer Verteidigungssysteme zu verkürzen.',
      'effect' => 'zur Bauzeit von Verteidigungsanlagen und Raketen für jedes Level.<br />+10% für jedes Level zu Angriff, Panzerung und Schilden des Planetenbesitzers bei der Verteidigung<br />+1 Slot für die Warteschlange von Verteidigungsanlagen und Raketen für jedes Level',
    ),

    MRC_STOCKMAN => array(
      'description' => 'Der Lagerist ist ein hochqualifizierter Spezialist für die Lagerung. Sein Genie ermöglicht es, das Maximum aus den Ressourcenlagern herauszuholen und deren effektive Kapazität über die von den Bauherren vorgesehenen Grenzen hinaus zu erhöhen.',
      'description_short' => 'Der Lagerist ist ein hochqualifizierter Spezialist für die Lagerung. Sein Genie ermöglicht es, das Maximum aus den Ressourcenlagern herauszuholen und deren effektive Kapazität über die von den Bauherren vorgesehenen Grenzen hinaus zu erhöhen.',
      'effect' => 'zur Größe der Lager für jedes Level.',
    ),

    MRC_SPY => array(
      'description' => 'Der Spion ist die mysteriöseste Persönlichkeit des Imperiums. Er hat hunderte Gesichter, tausend Persönlichkeiten und eine Million Ideen, die sich auf die Tarnung von Bauwerken, Verteidigungsnetzen und Flotten beziehen. Alle, die sein wahres Gesicht gesehen haben, sind jetzt tot.',
      'description_short' => 'Der Spion ist die mysteriöseste Persönlichkeit des Imperiums. Er hat hunderte Gesichter, tausend Persönlichkeiten und eine Million Ideen, die sich auf die Tarnung von Bauwerken, Verteidigungsnetzen und Flotten beziehen. Alle, die sein wahres Gesicht gesehen haben, sind jetzt tot.',
      'effect' => 'zum Spionagelevel für jedes Level.',
    ),

    MRC_ACADEMIC => array(
      'description' => 'Akademiker sind Mitglieder der Gilde der Technokraten. Ihr Verstand und ihre akademischen Grade ermöglichen es ihnen, sogar die Konstrukteure in ihren Taten zu übertreffen. Sie sind auf technologischen Fortschritt spezialisiert.',
      'description_short' => 'Akademiker sind Mitglieder der Gilde der Technokraten. Ihr Verstand und ihre akademischen Grade ermöglichen es ihnen, sogar die Konstrukteure in ihren Taten zu übertreffen. Sie sind auf technologischen Fortschritt spezialisiert.',
      'effect' => 'zur Forschungsgeschwindigkeit für jedes Level.',
    ),

//    MRC_DESTRUCTOR => array(
//      'description' => 'Der Zerstörer ist ein gnadenloser Offizier. Er bringt mit brutalen Methoden Ordnung auf den Planeten des Imperiums. Ebenso hat der Zerstörer die Technologie zur Produktion von Todessternen entwickelt.',
//      'effect' => 'Ermöglicht den Bau von Todessternen in der Werft.',
//    ),

    MRC_ADMIRAL => array(
      'description' => 'Der Admiral ist ein kriegserprobter Veteran und genialer Stratege. Selbst in den heißesten Gefechten verliert er nicht den Überblick und hält Kontakt zu den Flottenkommandanten. Ein weiser Herrscher kann sich im Kampf voll und ganz auf ihn verlassen und so mehr Schiffe für den Kampf einsetzen.',
      'description_short' => 'Der Admiral ist ein kriegserprobter Veteran und genialer Stratege. Selbst in den heißesten Gefechten verliert er nicht den Überblick und hält Kontakt zu den Flottenkommandanten. Ein weiser Herrscher kann sich im Kampf voll und ganz auf ihn verlassen und so mehr Schiffe für den Kampf einsetzen.',
      'effect' => 'zu Panzerung, Schilden und Angriff der Schiffe für jedes Level.',
    ),

    MRC_COORDINATOR => array(
      'description' => 'Der Koordinator ist ein Experte in der Flottenverwaltung. Sein Wissen ermöglicht es, das Maximum aus dem Flottenkontrollsystem herauszuholen.',
      'description_short' => 'Der Koordinator ist ein Experte in der Flottenverwaltung. Sein Wissen ermöglicht es, das Maximum aus dem Flottenkontrollsystem herauszuholen.',
      'effect' => 'zusätzliche Flotte für jedes Level.',
    ),

    MRC_NAVIGATOR => array(
      'description' => 'Der Navigator ist ein Genie in der Berechnung von Flugbahnen. Seine Kenntnisse der Warp-Raum-Gesetze, der Funktionsweise des Jump-Antriebs und der Technologien aller existierenden Antriebstypen ermöglichen es, die Geschwindigkeit der Schiffe zu erhöhen.',
      'description_short' => 'Der Navigator ist ein Genie in der Berechnung von Flugbahnen. Seine Kenntnisse der Warp-Raum-Gesetze, der Funktionsweise des Jump-Antriebs und der Technologien aller existierenden Antriebstypen ermöglichen es, die Geschwindigkeit der Schiffe zu erhöhen.',
      'effect' => 'zur Geschwindigkeit der Schiffe für jedes Level.',
    ),

    MRC_EMPEROR => array(
      'description' => 'Der Kaiser ist Ihr persönlicher Assistent und Stellvertreter. Die Genauigkeit seiner Berichte und seine penible Pünktlichkeit in allem sind seine besten Eigenschaften, die eine totale Kontrolle über das Imperium ermöglichen.',
      'description_short' => 'Der Kaiser ist Ihr persönlicher Assistent und Stellvertreter. Die Genauigkeit seiner Berichte und seine penible Pünktlichkeit in allem sind seine besten Eigenschaften, die eine totale Kontrolle über das Imperium ermöglichen.',
      'effect' => 'Ermöglicht die Änderung der Eigenschaften des Kaisers.',
    ),

    ART_LHC => array(
      'description' => 'Der LHC erzeugt einen Gravitonenstrahl, der auf die Stelle mit der höchsten Trümmerkonzentration im Orbit gerichtet ist, wodurch die Trümmer sich gegenseitig anziehen.<br /><span class=warning>ACHTUNG! Die Verwendung des LHC garantiert nicht die Entstehung eines Mondes!</span>',
      'effect' => 'Ermöglicht einen erneuten Versuch, einen Mond zu erschaffen<br />1% pro Million Trümmer, aber maximal 30%',
    ),

    ART_HOOK_SMALL => array(
      'description' => 'Das Funktionsprinzip dieses Artefakts ist nicht vollständig verstanden, was seine Verwendung jedoch nicht behindert. Der kleine Haken teleportiert einen kleinen Asteroiden auf eine stabile Umlaufbahn des Planeten. Dadurch erhält der Planet einen Mond mit minimalem Durchmesser.',
      'effect' => 'Erzeugt einen Mond mit minimaler Größe um den Planeten.',
    ),

    ART_HOOK_MEDIUM => array(
      'description' => 'Das Funktionsprinzip dieses Artefakts ist nicht vollständig verstanden, was seine Verwendung jedoch nicht behindert. Der mittlere Haken teleportiert einen Asteroiden auf eine stabile Umlaufbahn des Planeten und erzeugt so einen Mond.<br /><span class=warning>ACHTUNG! Die Größe des Mondes ist ZUFÄLLIG!</span>',
      'effect' => 'Erzeugt einen Mond mit zufälliger Größe um den Planeten.',
    ),

    ART_HOOK_LARGE => array(
      'description' => 'Das Funktionsprinzip dieses Artefakts ist nicht vollständig verstanden, was seine Verwendung jedoch nicht behindert. Der große Haken teleportiert einen riesigen Asteroiden auf eine stabile Umlaufbahn des Planeten. Dadurch erhält der Planet einen Mond mit maximalem Durchmesser.',
      'effect' => 'Erzeugt einen Mond mit maximaler Größe um den Planeten.',
    ),

    ART_RCD_SMALL => array(
      'description' => 'Der kleine Autonome Kolonisationskomplex (kurz AKK) ist ein Satz fertiger Konstruktionen und Programme, die es ermöglichen, auf einem neu entdeckten Planeten sofort eine Basis-Kolonie zu errichten.<br />Wenn auf dem Planeten bereits Gebäude vorhanden sind, die Teil des AKK sind, werden sie entweder verbessert oder bleiben unverändert. Der AKK kann auch auf einem Planeten mit unzureichenden freien Sektoren vollständig eingesetzt werden. Der AKK kann nicht auf einem Mond eingesetzt werden.<br />Die Kolonie umfasst eine Metallmine, einen Kristallsynthesizer und einen Deuteriumsynthesizer der Stufe 10, ein Solarkraftwerk der Stufe 14 und eine Roboterfabrik der Stufe 4.',
      'effect' => 'Ermöglicht die sofortige Errichtung einer Basis-Kolonie auf einem Planeten.',
    ),

    ART_RCD_MEDIUM => array(
      'description' => 'Der mittlere Autonome Kolonisationskomplex (kurz AKK) ist ein Satz fertiger Konstruktionen und Programme, die es ermöglichen, auf einem neu entdeckten Planeten sofort eine Kolonie mittleren Niveaus zu errichten.<br />Wenn auf dem Planeten bereits Gebäude vorhanden sind, die Teil des AKK sind, werden sie entweder verbessert oder bleiben unverändert. Der AKK kann auch auf einem Planeten mit unzureichenden freien Sektoren vollständig eingesetzt werden. Der AKK kann nicht auf einem Mond eingesetzt werden.<br />Die Kolonie umfasst eine Metallmine, einen Kristallsynthesizer und einen Deuteriumsynthesizer der Stufe 15, ein Solarkraftwerk der Stufe 20 und eine Roboterfabrik der Stufe 8.',
      'effect' => 'Ermöglicht die sofortige Errichtung einer Kolonie mittleren Niveaus auf einem Planeten.',
    ),

    ART_RCD_LARGE => array(
      'description' => 'Der große Autonome Kolonisationskomplex (kurz AKK) ist ein Satz fertiger Konstruktionen und Programme, die es ermöglichen, auf einem neu entdeckten Planeten sofort eine fortgeschrittene Kolonie zu errichten.<br />Wenn auf dem Planeten bereits Gebäude vorhanden sind, die Teil des AKK sind, werden sie entweder verbessert oder bleiben unverändert. Der AKK kann auch auf einem Planeten mit unzureichenden freien Sektoren vollständig eingesetzt werden. Der AKK kann nicht auf einem Mond eingesetzt werden.<br />Die Kolonie umfasst eine Metallmine, einen Kristallsynthesizer und einen Deuteriumsynthesizer der Stufe 20, ein Solarkraftwerk der Stufe 25, eine Roboterfabrik der Stufe 10 und eine Nanofabrik der Stufe 1.',
      'effect' => 'Ermöglicht die sofortige Errichtung einer fortgeschrittenen Kolonie auf einem Planeten.',
    ),

    ART_HEURISTIC_CHIP => array(
      'description' => 'Der heuristische Chip ist ein einzigartiger, vorinstallierter Satz von Programmen, die auf einem kristallinen Speichermedium gespeichert sind. Durch die Verbindung mit dem Forschungsnetzwerk können die Algorithmen des Chips den aktuellen Forschungsstand analysieren und neue effektive Heuristiken liefern, wodurch die Forschungszeit erheblich verkürzt wird. Einmal aktiviert, kann der Chip nicht für eine andere Forschung neu konfiguriert werden. Leider ist, wie bei jedem anderen kristallinen Chip, die Dekompilierung der "eingebetteten" Programm grundsätzlich unmöglich, ebenso wie die Kopie durch Assembler.',
      'effect' => 'Verringert die Zeit der aktuellen Forschung um die Hälfte (wenn mehr als eine Stunde bis zum Ende der Forschung verbleibt) oder beendet sie sofort (wenn weniger als 1 Stunde, aber mehr als 1 Minute bis zum Ende der Forschung verbleibt).',
    ),

    ART_NANO_BUILDER => array(
      'description' => 'Wie bekannt ist, werden Assembler normalerweise nicht im Bau großer Objekte wie Gebäude eingesetzt. Es ist wirtschaftlich sinnvoller, Gebäude mit der traditionellen "Blockbauweise" zu errichten, bei der einzelne standardisierte Teile in robotergesteuerten Fabriken produziert werden. Spezialisierte Nano-Assembler erweisen sich jedoch als effizienter als traditionelle Methoden. Diese winzigen Roboter sind in vorkonfigurierten Paketen zusammengefasst, die jeweils ihre eigene Schwarm-Sub-KI besitzen. Durch die Analyse des aktuellen Zustands des im Bau befindlichen Gebäudes finden die Nano-Bauer fehlerfrei Engpässe und berechnen die effektivsten Wege zur Beschleunigung des Baus. Das Paket ist Einweg und nach Gebrauch nicht mehr verwendbar. Zudem kann ein initiiertes Paket nicht mehr für die Integration mit einer anderen Baustelle neu konfiguriert werden. Obwohl Assembler in der Lage sind, einen einzelnen Nano-Bauer zu reproduzieren, ist eine solche Replik ohne den Steuerkristall nicht mehr als ein maßstabsgetreues Modell...',
      'effect' => 'Verringert die Bau-/Abbauzeit des aktuellen Gebäudes auf diesem Planeten um die Hälfte (wenn mehr als eine Stunde bis zum Ende des Prozesses verbleibt) oder beendet ihn sofort (wenn weniger als 1 Stunde, aber mehr als 1 Minute bis zum Ende des Prozesses verbleibt).',
    ),

    ART_DENSITY_CHANGER  => array(
      'description' => '',
      'effect' => '',
    ),

    UNIT_PLAN_STRUC_MINE_FUSION  => array(
      'description' => 'Bauplan für das Gebäude "Fusionskraftwerk"',
      'effect' => 'Ermöglicht den Bau des Gebäudes "Fusionskraftwerk" auf Planeten.',
    ),

    UNIT_PLAN_SHIP_CARGO_SUPER  => array(
      'description' => 'Bauplan für das Schiff "Supertransporter"',
      'effect' => 'Ermöglicht den Bau des Schiffes "Supertransporter" in Werften.',
    ),

    UNIT_PLAN_SHIP_CARGO_HYPER  => array(
      'description' => 'Bauplan für das Schiff "Hypertransporter"',
      'effect' => 'Ermöglicht den Bau des Schiffes "Hypertransporter" in Werften.',
    ),

    UNIT_PLAN_SHIP_DEATH_STAR  => array(
      'description' => 'Bauplan für das Schiff "Todesstern"',
      'effect' => 'Ermöglicht den Bau des Schiffes "Todesstern" in Werften.',
    ),

    UNIT_PLAN_SHIP_SUPERNOVA  => array(
      'description' => 'Bauplan für den Kreuzer der "SuperNova"-Klasse',
      'effect' => 'Ermöglicht den Bau des Kreuzers der "SuperNova"-Klasse in Werften.',
    ),

    UNIT_PLAN_DEF_SHIELD_PLANET  => array(
      'description' => 'Bauplan für die Verteidigungsanlage "Planetare Verteidigung"',
      'effect' => 'Ermöglicht den Bau der Verteidigungsanlage "Planetare Verteidigung" auf einem Planeten.',
    ),

    RES_METAL => array(
      'description' => 'Metametallische Eisen-normierte energieneutrale Verbindung (oder einfach "Metall") ist der Grundrohstoff, aus dem Nanobots alle notwendigen Materialien und/oder Konstruktionen herstellen, die im Bau von Gebäuden, Schiffen, Verteidigungsanlagen usw. verwendet werden. Geliefert wird es in Form von schwach radioaktiven Barren mit einem Volumen von 127 Litern und einem Gewicht von 1 Tonne, einschließlich der Schutzverpackung. "Eisen-normiert" bedeutet, dass ein Standard-Satz von Nanobots aus einem Barren eine Tonne Eisen produzieren kann. "Energieneutral" bedeutet, dass dabei genau so viel Energie verbraucht wird, wie im Barren selbst enthalten ist. Und schließlich bedeutet "metametallische Verbindung", dass, obwohl der Hauptteil des Barrens aus verschiedenen Metallen besteht, auch andere Substanzen - sowohl komplexe als auch einfache - in seiner Zusammensetzung enthalten sein können. Die Zusammensetzung der Verbindung kann von Planet zu Planet und sogar von Mine zu Mine variieren, im Grenzfall reines Eisen sein, aber Größe, Form und Gewicht der Barren bleiben immer gleich.',
      'effect' => '',
    ),

    RES_CRYSTAL => array(
      'description' => 'Kristall - ein komplexer thermoplastischer Polymer, der den Effekt der Überlichtgeschwindigkeitsleitung (EÜL) demonstriert. EÜL - eine Erhöhung der Photonengeschwindigkeit im Kristallkörper über 300000 km/s. Alle modernen Rechengeräte verwenden Kristalle als Grundbaumaterial. Produktionsabfälle ("anomale Assemblies" - d.h. Polymere, die in der Formel Kristallen entsprechen, aber keinen EÜL demonstrieren) werden in Solarzellen verwendet, deren Wirkungsgrad sich 100% nähert. Darüber hinaus werden speziell ausgewählte Kristalle in Jump-Antrieben verwendet - Geräten, die interplanetare und interstellare Reisen mit Überlichtgeschwindigkeit ermöglichen.',
      'effect' => '',
    ),

    RES_DEUTERIUM => array(
      'description' => 'Deuterium, schwerer Wasserstoff - ein stabiles Isotop von Wasserstoff mit einer Atommasse von 2. Der Kern (Deuteron) besteht aus einem Proton und einem Neutron. Deuterium ist der Treibstoff für Fusionsreaktoren und alle Arten von Antrieben. Es wird in flüssiger Form in standardmäßigen thermoisolierten Behältern gelagert, die auch als Treibstoffblöcke für Antriebsanlagen und Fusionsreaktoren dienen. Daher sind die Laderäume, die mit einem automatischen Zuführ- und Stapelsystem ausgestattet sind, auch der "Treibstofftank" jedes Raumschiffs.',
      'effect' => '',
    ),

    RES_ENERGY => array(
      'description' => 'Elektrische Energie - die einzige universelle Energieart, die überall verwendet wird. Auf Planeten wird sie normalerweise von Solarkraftwerken und Solarsatelliten erzeugt. Auf besonders weit von der Sonne entfernten kalten Planeten und Raumschiffen wird elektrische Energie von Deuterium-Fusionsreaktoren erzeugt.',
      'effect' => '',
    ),

    RES_DARK_MATTER => array(
      'description' => '<span class="dark_matter">Dunkle Materie</span> (abgekürzt <span class="dark_matter">DM</span>) - eine mit Standardmethoden nicht nachweisbare nicht-baryonische Materie, auf die 23% der Masse des Universums entfallen. Aus ihr kann eine unglaubliche Menge an Energie gewonnen werden. Aufgrund dessen und der mit ihrer Gewinnung verbundenen Schwierigkeiten wird <span class="dark_matter">Dunkle Materie</span> sehr hoch geschätzt.',
      'effect' => '',
    ),

    RES_METAMATTER => array(
      'description' => 'Metamaterie - das ist so ein neues Zeug, für das eine Beschreibung geschrieben werden muss.',
      'effect' => '',
    ),


    UNIT_CAN_NOT_BE_BUILD => array(
      'description'       => 'Diese Einheit kann derzeit nicht vom Spieler gebaut werden. Nein, man kann sie nicht kaufen. Vielleicht konnte man sie früher kaufen/bauen. Und nein, frag nicht, ob man sie jemals bauen oder irgendwie bekommen kann. Das ist unbekannt.<br/>Obwohl... vielleicht kann man sie auf andere Weise bekommen? Frag andere Spieler... Die AD-Administration zu fragen ist nutzlos.',
      'description_short' => 'Diese Einheit kann derzeit nicht gebaut oder gekauft werden.',
      'effect'            => 'Diese Einheit kann nicht gebaut oder gekauft werden.',
    ),
  )
);