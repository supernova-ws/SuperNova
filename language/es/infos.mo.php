<?php

/*
#############################################################################
#  Filename: infos.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Juego de estrategia espacial masivo multijugador en línea
#
#  Copyright © 2009-2018 Gorlum for Project "SuperNova.WS"
#############################################################################
*/

/**
*
* @package language
* @system [Spanish]
* @version 46d0
*
*/

/**
* NO CAMBIAR
*/

if (!defined('INSIDE')) die();

$a_lang_array = array(
  'wiki_title' => 'NovaPedia',

  'wiki_char_nominal' => 'Nominales',
  'wiki_char_actual' => 'Reales',

  'wiki_ship_engine_header' => 'Características de los motores',

  'wiki_ship_header' => 'Características de transporte',
  'wiki_ship_speed' => 'Velocidad',
  'wiki_ship_consumption' => 'Consumo de deuterio',
  'wiki_ship_capacity' => 'Capacidad de carga',
  'wiki_ship_hint' => '<li>La velocidad y consumo reales se muestran considerando todos los bonos: tecnologías, mercenarios, etc.</li>',

  'wiki_combat_header' => 'Características de combate',
  'wiki_combat_attack' => 'Potencia de disparo, puntos',
  'wiki_combat_shield' => 'Capacidad de escudos, puntos',
  'wiki_combat_armor' => 'Integridad estructural, puntos',

  'wiki_combat_volley_header' => 'Fuego de salva',
  'wiki_combat_volley_to' => 'Unidades impactadas',
  'wiki_combat_volley_from' => 'Unidades perdidas',
  'info' => array(
    STRUC_MINE_METAL => array(
      'description' => 'Principal proveedor de materias primas para la construcción de estructuras portantes de edificios y naves. El metal es la materia prima más barata, pero se requiere en mayores cantidades que otros recursos. Su producción consume menos energía. A mayor nivel de las minas, más profundas son. En la mayoría de los planetas, el metal se encuentra a grandes profundidades, lo que permite extraer más en minas más profundas. Sin embargo, minas más grandes requieren más energía.',
      'description_short' => 'Principal proveedor de materias primas para estructuras portantes de edificios y naves.',
    ),

    STRUC_MINE_CRYSTAL => array(
      'description' => 'La síntesis de cristales requiere aproximadamente el doble de energía que la extracción de una cantidad equivalente de metal, por lo que es más valioso. Los cristales son componentes esenciales en computadoras modernas y motores de curvatura. Se necesitan para casi todas las naves y edificios. Mejorar el sintetizador aumenta la producción de cristales.',
      'description_short' => 'Principal proveedor de materias primas para sistemas informáticos y motores de curvatura.',
    ),

    STRUC_MINE_DEUTERIUM => array(
      'description' => 'El deuterio es hidrógeno pesado. Al igual que en las minas, los depósitos más grandes se encuentran en las profundidades del mar. Mejorar el sintetizador permite explotar estos depósitos profundos. El deuterio se usa como combustible para naves, en la mayoría de las investigaciones, para escanear galaxias y en la falange sensorial.',
      'description_short' => 'Extrae una pequeña fracción de deuterio del agua del planeta.',
    ),

    STRUC_MINE_SOLAR => array(
      'description' => 'Para alimentar minas y sintetizadores, se necesitan enormes plantas de energía solar. A mayor número de plantas, más superficie se cubre con paneles solares que convierten la luz en electricidad. Las plantas solares son la base del suministro energético del planeta.',
      'description_short' => 'Genera energía a partir de la luz solar. La energía es necesaria para la mayoría de los edificios.',
    ),

    STRUC_MINE_FUSION => array(
      'description_short' => 'Genera energía fusionando dos átomos de hidrógeno pesado para formar helio',
      'description' => 'Las plantas de fusión nuclear combinan dos átomos de hidrógeno pesado bajo alta presión y temperatura para formar helio, liberando energía (41,32*10^-13 J por reacción). A mayor nivel de la planta, más complejos son los procesos de fusión, generando más energía.<br><br>Fórmula de producción:<br>30 * [nivel PF] * (1,05 + [nivel de tecnología energética] * 0,01) ^ [nivel PF]<br>La producción también puede aumentarse mejorando la tecnología energética.',
    ),

    STRUC_FACTORY_ROBOT => array(
      'description' => 'Proporciona mano de obra básica para la construcción de infraestructura planetaria. Cada nivel de la fábrica aumenta la velocidad de construcción de edificios.',
      'description_short' => 'Fabrica máquinas y mecanismos para la construcción de infraestructura planetaria. Cada nivel aumenta la velocidad de construcción.',
    ),

    STRUC_FACTORY_NANO => array(
      'description' => 'Las nanofábricas representan la evolución final de las fábricas robóticas. Su único equipo son los nanoensambladores, que manipulan moléculas y átomos individuales, permitiendo crear materiales con propiedades personalizadas. Aunque pueden producir cualquier estructura, muchos objetos siguen siendo más económicos de fabricar de manera tradicional. Cada nivel de nanofábrica reduce a la mitad el tiempo de construcción de edificios, defensas y naves.',
      'description_short' => 'Equipada con nanoensambladores especializados que construyen objetos a nivel molecular.',
      'effect' => 'Cada nivel de nanofábrica duplica la velocidad de construcción.',
    ),

    STRUC_FACTORY_HANGAR => array(
      'description' => 'En los astilleros se producen todo tipo de naves y defensas. A mayor nivel, más rápida es la construcción de naves complejas. La integración de fábricas de nanitos simplifica las cadenas de producción, mejorando drásticamente la productividad.',
      'description_short' => 'Produce naves espaciales, estructuras orbitales y defensas.',
    ),

    STRUC_STORE_METAL => array(
      'description' => 'Almacén para el mineral extraído. Cuanto más grande es, más mineral puede almacenar. Si el almacén está lleno, la extracción de metal se detiene. El almacén se usa EXCLUSIVAMENTE para la producción: la cantidad total de metal en el planeta (por ejemplo, al transportar recursos desde otro planeta) puede exceder la capacidad máxima del almacén.',
      'description_short' => 'Almacén para el mineral antes de su posterior procesamiento.',
    ),

    STRUC_STORE_CRYSTAL => array(
      'description' => 'Este almacén guarda materiales semielaborados para la síntesis de cristales. Cuanto más grande es, más material puede almacenar. Si el almacén está lleno, la síntesis de cristales se detiene. El almacén se usa EXCLUSIVAMENTE para la producción: la cantidad total de cristal en el planeta puede exceder la capacidad máxima del almacén.',
      'description_short' => 'Almacén para materiales semielaborados de cristal antes de su procesamiento final.',
    ),

    STRUC_STORE_DEUTERIUM => array(
      'description' => 'Tanques especiales para almacenar agua pesada, generalmente ubicados cerca de los puertos espaciales. Cuanto más grandes son, más agua pesada pueden contener. Si están llenos, la extracción de deuterio se detiene. Los tanques se usan EXCLUSIVAMENTE para la producción: la cantidad total de deuterio en el planeta puede exceder su capacidad máxima.',
      'description_short' => 'Tanques para almacenar agua pesada antes de la extracción de deuterio.',
    ),

    STRUC_LABORATORY => array(
      'description' => 'Para investigar nuevas tecnologías, se necesita una estación de investigación. El nivel del laboratorio determina la velocidad de desarrollo tecnológico. Cuanto más avanzado es el laboratorio, más tecnologías pueden investigarse. Todos los científicos disponibles se concentran en un planeta durante la investigación y luego difunden los conocimientos adquiridos.',
      'description_short' => 'Aquí se investigan nuevas tecnologías.',
    ),

    STRUC_TERRAFORMER => array(
      'description' => 'A medida que los planetas se urbanizan, el espacio útil se vuelve escaso. El terraformador soluciona esto transformando territorios enteros en áreas habitables, consumiendo enormes cantidades de energía. Genera nanitos especializados que optimizan constantemente el suelo.',
      'description_short' => 'Transforma territorios inútiles en zonas edificables.',
    ),

    STRUC_ALLY_DEPOSIT => array(
      'description' => 'El almacén de la alianza suministra combustible a flotas aliadas que ayudan en la defensa orbital. A mayor nivel, más deuterio puede enviarse a las flotas.',
      'description_short' => 'Proporciona combustible a flotas aliadas en órbita.',
    ),

    STRUC_LABORATORY_NANO => array(
      'description' => 'Reduce a la mitad el tiempo de investigación.',
      'description_short' => 'Equipado con computadoras cuánticas y nanoensambladores de última generación que aceleran cualquier investigación.',
    ),

    STRUC_MOON_STATION => array(
      'description' => 'La Luna carece de atmósfera, por lo que requiere una base lunar para su colonización. Cada nivel aumenta el área habitable (3 sectores por nivel, hasta cubrir toda la Luna). La base ocupa 1 sector.',
      'description_short' => 'Proporciona atmósfera, gravedad y calor en la Luna.',
    ),

    STRUC_MOON_PHALANX => array(
      'description' => 'Sensores de alta frecuencia que escanean todo el espectro electromagnético. Detectan movimientos de flotas en planetas distantes (alcance: (nivel de falange)^2 -1 sistemas). Consume deuterio (1000*nivel por escaneo).',
      'description_short' => 'Sensores avanzados para rastrear flotas enemigas.',
    ),

    STRUC_MOON_GATE => array(
      'description' => 'Portal de teletransportación masiva que mueve flotas enteras sin demoras. Requiere 1 hora entre saltos para evitar sobrecalentamiento. No transporta recursos.',
      'description_short' => 'Teletransporta flotas entre lunas instantáneamente.',
    ),

    STRUC_SILO => array(
      'description' => 'Silos para misiles. Cada nivel permite almacenar +4 misiles interplanetarios o +12 interceptores (1 misil interplanetario = 3 espacios de interceptor).',
      'description_short' => 'Almacena y lanza misiles defensivos/ofensivos.',
    ),

    TECH_SPY => array(
      'description_short' => 'Esta tecnología obtiene datos de otros planetas.',
      'description' => 'Mejora los sensores de espionaje. A mayor nivel, más información se obtiene sobre objetivos. La diferencia de niveles con el enemigo es crucial: ventaja en espionaje revela más datos y reduce la probabilidad de ser detectado. También mejora el rastreo de flotas enemigas.',
    ),

        TECH_COMPUTER => array(
      'description' => 'Amplía la capacidad computacional para gestionar más flotas simultáneamente. Cada nivel permite controlar +1 flota adicional. Esencial para comerciantes y atacantes.',
      'description_short' => 'Cada nivel permite comandar +1 flota simultánea.',
    ),

    TECH_WEAPON => array(
      'description' => 'Mejora los sistemas de armamento. Cada nivel aumenta un 10% el daño de las unidades militares.',
      'description_short' => '+10% de potencia de fuego por nivel.',
    ),

    TECH_SHIELD => array(
      'description' => 'Optimiza generadores de escudos. Cada nivel mejora un 10% la absorción de daño.',
      'description_short' => '+10% de eficiencia en escudos por nivel.',
    ),

    TECH_ARMOR => array(
      'description' => 'Desarrolla aleaciones avanzadas para blindaje. Cada nivel aumenta un 10% la resistencia estructural.',
      'description_short' => '+10% de blindaje por nivel.',
    ),

    TECH_ENERGY => array(
      'description' => 'Investiga sistemas de transmisión y almacenamiento energético para otras tecnologías.',
      'description_short' => 'Mejora la producción en plantas de fusión.',
    ),

    TECH_HYPERSPACE => array(
      'description' => 'Permite desarrollar motores hiperespaciales más eficientes.',
      'description_short' => 'Base para motores de hiperpropulsión.',
    ),

    // Sección de motores
    TECH_ENGINE_CHEMICAL => array(
      'description' => 'Motores básicos que usan reacciones químicas. Económicos pero poco eficientes. Cada nivel aumenta un 10% la velocidad de: Transporte Pequeño (hasta nivel 5 de motores iónicos), Transporte Grande, Reciclador, Sonda Espía y Cazador Ligero.',
      'description_short' => '+10% de velocidad en naves básicas por nivel.',
    ),

    TECH_ENGINE_ION => array(
      'description' => 'Motores que aceleran iones con campos electromagnéticos. Consumen más deuterio pero son más rápidos. Cada nivel otorga +20% de velocidad a: Transporte Pequeño (nivel 5+), Supertransporte, Colonizador, Cazador Pesado, Destructor y Bombardero (hasta nivel 8 de hiperpropulsión).',
      'description_short' => '+20% de velocidad en naves intermedias por nivel.',
    ),

    TECH_ENGINE_HYPER => array(
      'description' => 'Comprime el espacio-tiempo alrededor de la nave para viajes FTL. Cada nivel da +30% de velocidad a: Hipertransporte, Crucero, Bombardero (nivel 8+), Acorazado, Estrella de la Muerte y Crucero "Supernova".',
      'description_short' => '+30% de velocidad en naves avanzadas por nivel.',
    ),

    // Tecnologías de armas
    TECH_LASER => array(
      'description' => 'Desarrolla láseres coherentes para cortar blindajes. Requisito para otras tecnologías armamentísticas.',
      'description_short' => 'Armas de energía focalizada.',
    ),

    TECH_ION => array(
      'description' => 'Haces de iones acelerados que dañan sistemas electrónicos y escudos.',
      'description_short' => 'Armas EMP avanzadas.',
    ),

    TECH_PLASMA => array(
      'description' => 'Evolución de la tecnología iónica que dispara plasma supercaliente. Devastador contra estructuras.',
      'description_short' => 'El armamento más destructivo.',
    ),

    // Sección de naves
    SHIP_CARGO_SMALL => array(
      'description' => 'Nave ágil con capacidad para 5,000 unidades. Poca defensa. Al alcanzar Motores Iónicos nivel 5, se actualiza su sistema de propulsión.',
      'description_short' => 'Transportador rápido para recursos.',
    ),

    SHIP_CARGO_BIG => array(
      'description' => 'Versión ampliada del transporte pequeño. Ideal para saqueos pero vulnerable sin escolta.',
      'description_short' => 'Mayor capacidad que el transporte pequeño.',
    ),

    SHIP_CARGO_SUPER => array(
      'description' => 'Gigante con motores iónicos. Lento y costoso pero con escudos potentes y capacidad masiva.',
      'description_short' => 'Barcaza de carga pesada con escudos.',
    ),

    SHIP_CARGO_HYPER => array(
      'description' => 'Colosal nave del tamaño de una luna pequeña. Solo sus motores hiperespaciales pueden moverla cuando está cargada. Consume cantidades astronómicas de deuterio.',
      'description_short' => 'Transporte masivo para imperios avanzados.',
    ),

    SHIP_SMALL_FIGHTER_LIGHT => array(
      'description' => 'Nave rápida y económica con escudos y capacidad de carga limitados. Presente en casi todos los planetas.',
      'description_short' => 'Unidad básica de combate.',
    ),

    SHIP_SMALL_FIGHTER_HEAVY => array(
      'description' => 'Evolución del cazador ligero con motores iónicos, mejor blindaje y mayor poder de fuego. Precursor de la tecnología de cruceros.',
      'description_short' => 'Versión mejorada del cazador ligero.',
    ),

    SHIP_MEDIUM_DESTROYER => array(
      'description' => 'Especializado en destruir defensas medianas. 3 veces más resistente que un cazador pesado y el doble de daño.',
      'description_short' => 'Dominó el campo de batalla durante siglos.',
    ),

    SHIP_LARGE_CRUISER => array(
      'description' => 'Columna vertebral de las flotas. Combina potencia de fuego, velocidad y capacidad de carga.',
      'description_short' => 'Nave versátil para múltiples roles.',
    ),

    SHIP_COLONIZER => array(
      'description' => 'Nave fortificada que se desmonta al colonizar nuevos planetas. El límite de colonias depende de la configuración del universo.',
      'description_short' => 'Para establecer nuevas colonias.',
    ),

    SHIP_RECYCLER => array(
      'description' => 'Equipado con escudos especiales para recolectar escombros espaciales. Capacidad limitada a 20,000 unidades.',
      'description_short' => 'Recolecta recursos de campos de escombros.',
    ),

    SHIP_SPY => array(
      'description' => 'Pequeña y veloz, pero sin armadura ni escudos. Fácil de destruir si es detectada.',
      'description_short' => 'Obtiene información de planetas enemigos.',
    ),

    SHIP_LARGE_BOMBER => array(
      'description' => 'Diseñado específicamente para destruir defensas planetarias con bombas de plasma. Mejora sus motores al investigar Hiperpropulsión nivel 8.',
      'description_short' => 'Asedia estructuras defensivas.',
    ),

    SHIP_SATTELITE_SOLAR => array(
      'description' => 'Plataformas orbitales que transmiten energía solar a la superficie. Vulnerables en combate.',
      'description_short' => 'Generadores de energía en órbita.',
    ),

    SHIP_LARGE_DESTRUCTOR => array(
      'description' => 'El rey de las naves militares. Sus torretas multiflango tienen 99% de precisión. Movilidad limitada y alto consumo de deuterio.',
      'description_short' => 'La máxima expresión de poderío militar.',
    ),

    SHIP_HUGE_DEATH_STAR => array(
      'description' => 'Armada con un cañón de gravitones capaz de destruir lunas. Requiere recursos astronómicos para su construcción.',
      'description_short' => 'Arma definitiva de destrucción masiva.',
    ),

    SHIP_LARGE_BATTLESHIP => array(
      'description' => 'Crucero tecnológicamente avanzado con armas láser de largo alcance. Bajo consumo de combustible.',
      'description_short' => 'Especialista en interceptar flotas.',
    ),

    SHIP_HUGE_SUPERNOVA => array(
      'description_short' => 'El buque insignia de la flota imperial.',
      'description' => 'El Crucero "Supernova" justifica su enorme costo con un poder de fuego devastador y defensas avanzadas. Puede destruir flotas enteras solo.',
    ),

    // Defensas
    UNIT_DEF_TURRET_MISSILE => array(
      'description' => 'Defensa básica y económica. Efectiva contra flotas pequeñas. Se autorepara hasta 70% después de batallas.',
      'description_short' => 'Torreta de misiles balísticos.',
    ),

    UNIT_DEF_TURRET_LASER_SMALL => array(
      'description_short' => 'Láseres focales que causan más daño que armas balísticas.',
      'description' => 'Solución contra naves mejor blindadas. Mejor relación costo-beneficio que las torretas de misiles. Autoreparación hasta 70%.',
    ),

    UNIT_DEF_TURRET_LASER_BIG => array(
      'description' => 'Versión mejorada del láser pequeño con materiales avanzados y sistemas de focalización. Autoreparación hasta 70%.',
      'description_short' => 'Láser pesado de alta energía.',
    ),

    UNIT_DEF_TURRET_GAUSS => array(
      'description_short' => 'Acelera proyectiles de varias toneladas con electromagnetismo.',
      'description' => 'Tecnología antigua readaptada. Los proyectiles pueden atravesar blindajes modernos. Autoreparación hasta 70%.',
    ),

    UNIT_DEF_TURRET_ION => array(
      'description_short' => 'Desestabiliza escudos y daña sistemas electrónicos.',
      'description' => 'Evolución de las armas EMP. Inútil contra blancos sin electrónica. Alto consumo energético. Autoreparación hasta 70%.',
    ),

    UNIT_DEF_TURRET_PLASMA => array(
      'description' => 'Fusión de tecnología láser e iónica. Dispara bolas de plasma a millones de grados. El arma defensiva más poderosa. Autoreparación hasta 70%.',
      'description_short' => 'Sintetiza lo mejor de ambas tecnologías.',
    ),

    // Escudos planetarios
    UNIT_DEF_SHIELD_SMALL => array(
      'description' => 'Genera un campo de fuerza alrededor del planeta. Solo se puede construir un escudo pequeño por planeta.',
      'description_short' => 'Protección básica contra ataques.',
    ),

    UNIT_DEF_SHIELD_BIG => array(
      'description' => 'Escudo mejorado que absorbe más energía que la versión pequeña.',
      'description_short' => 'Versión reforzada del escudo pequeño.',
    ),

    UNIT_DEF_SHIELD_PLANET => array(
      'description' => 'La mejor protección disponible para tus planetas.',
      'description_short' => 'Defensa planetaria definitiva.',
    ),

    // Misiles
    UNIT_DEF_MISSILE_INTERCEPTOR => array(
      'description' => 'Destruye misiles interplanetarios enemigos. 1 interceptor = 1 misil enemigo.',
      'description_short' => 'Defensa antimisiles.',
    ),

    UNIT_DEF_MISSILE_INTERPLANET => array(
      'description_short' => 'Destruye estructuras defensivas enemigas.',
      'description' => 'Aniquila defensas planetarias. Las estructuras destruidas no se autoreparan.',
    ),

    // Mercenarios
    MRC_TECHNOLOGIST => array(
      'description' => 'Experto en optimización de recursos. Trabaja con metalúrgicos, químicos y energéticos para mejorar la producción.',
      'description_short' => 'Mejora la eficiencia de producción.',
      'effect' => 'Bonus a minería, síntesis y generación de energía por nivel.',
    ),

    MRC_ENGINEER => array(
      'description' => 'Constructor genéticamente mejorado con fuerza sobrehumana y mente brillante. Puede edificar ciudades enteras solo.',
      'description_short' => 'Maestro de la construcción.',
      'effect' => 'Acelera construcción de edificios/naves + slots adicionales por nivel.',
    ),


    MRC_FORTIFIER => array(
      'description' => 'Ingeniero militar especializado en sistemas defensivos. Acelera la construcción de estructuras de defensa planetaria.',
      'description_short' => 'Experto en fortificaciones planetarias.',
      'effect' => 'Aumenta velocidad de construcción de defensas +10% ataque/defensa por nivel. +1 slot de cola defensiva por nivel.',
    ),

    MRC_STOCKMAN => array(
      'description' => 'Especialista en almacenamiento que optimiza la capacidad de los depósitos más allá de sus límites teóricos.',
      'description_short' => 'Maestro de la gestión de almacenes.',
      'effect' => 'Aumenta capacidad de almacenamiento por nivel.',
    ),

    MRC_SPY => array(
      'description' => 'Operativo encubierto con identidades múltiples. Experto en camuflar instalaciones y flotas.',
      'description_short' => 'Espía maestro del engaño.',
      'effect' => 'Mejora nivel de espionaje por nivel.',
    ),

    MRC_ACADEMIC => array(
      'description' => 'Miembro del Gremio Tecnocrático. Supera incluso a los Constructores en avances científicos.',
      'description_short' => 'Investigador de élite.',
      'effect' => 'Acelera velocidad de investigación por nivel.',
    ),

    MRC_ADMIRAL => array(
      'description' => 'Veterano de guerra y estratega brillante. Coordina flotas complejas con eficiencia letal.',
      'description_short' => 'Comandante supremo de flotas.',
      'effect' => 'Mejora armadura, escudos y ataque de naves por nivel.',
    ),

    MRC_COORDINATOR => array(
      'description' => 'Experto en sistemas de control de flotas. Maximiza la eficiencia operativa.',
      'description_short' => 'Optimizador de operaciones navales.',
      'effect' => '+1 flota adicional por nivel.',
    ),

    MRC_NAVIGATOR => array(
      'description' => 'Genio de la astro-navegación. Domina las leyes del espacio warp y todos los sistemas de propulsión.',
      'description_short' => 'Navegante de precisión absoluta.',
      'effect' => 'Aumenta velocidad de las naves por nivel.',
    ),

    MRC_EMPEROR => array(
      'description' => 'Tu asistente personal. Su precisión y meticulosidad permiten un control total sobre el imperio.',
      'description_short' => 'Máxima autoridad ejecutiva.',
      'effect' => 'Permite modificar las características del Emperador.',
    ),

    // Artefactos
    ART_LHC => array(
      'description' => 'El Colisionador de Hadrones genera un flujo de radiación de gravitones que atrae escombros orbitales.<br /><span class=warning>¡ATENCIÓN! No garantiza la creación de lunas.</span>',
      'effect' => 'Permite reintentar creación de luna<br />1% por millón de escombros (máx. 30%)',
    ),

    ART_HOOK_SMALL => array(
      'description' => 'Teleporta un asteroide pequeño a órbita estable, creando una luna de tamaño mínimo.',
      'effect' => 'Crea luna de tamaño mínimo',
    ),

    ART_HOOK_MEDIUM => array(
      'description' => 'Teleporta un asteroide mediano a órbita estable.<br /><span class=warning>¡El tamaño final es aleatorio!</span>',
      'effect' => 'Crea luna de tamaño aleatorio',
    ),

    ART_HOOK_LARGE => array(
      'description' => 'Teleporta un asteroide masivo a órbita estable, creando una luna de tamaño máximo.',
      'effect' => 'Crea luna de tamaño máximo',
    ),

    ART_RCD_SMALL => array(
      'description' => 'Kit de colonización autónoma básico. Incluye: Mina de Metal 10, Sintetizador de Cristal 10, Sintetizador de Deuterio 10, Planta Solar 14 y Fábrica de Robots 4.',
      'effect' => 'Despliega colonia básica instantáneamente',
    ),

    ART_RCD_MEDIUM => array(
      'description' => 'Kit de colonización intermedio. Incluye: Mina de Metal 15, Sintetizador de Cristal 15, Sintetizador de Deuterio 15, Planta Solar 20 y Fábrica de Robots 8.',
      'effect' => 'Despliega colonia intermedia instantáneamente',
    ),

    ART_RCD_LARGE => array(
      'description' => 'Kit de colonización avanzado. Incluye: Mina de Metal 20, Sintetizador de Cristal 20, Sintetizador de Deuterio 20, Planta Solar 25, Fábrica de Robots 10 y Nanofábrica 1.',
      'effect' => 'Despliega colonia avanzada instantáneamente',
    ),

    ART_HEURISTIC_CHIP => array(
      'description' => 'Cristal programable con algoritmos de investigación. No puede reutilizarse ni copiarse.',
      'effect' => 'Reduce tiempo de investigación actual a la mitad (>1h) o completa instantáneamente (<1h)',
    ),

    ART_NANO_BUILDER => array(
      'description' => 'Enjambre de nano-robots de construcción única. Optimizan procesos de edificación en tiempo real.',
      'effect' => 'Reduce tiempo de construcción actual a la mitad (>1h) o completa instantáneamente (<1h)',
    ),

    // Planos
    UNIT_PLAN_STRUC_MINE_FUSION => array(
      'description' => 'Planos para construir Plantas de Fusión Nuclear.',
      'effect' => 'Permite construir Plantas de Fusión',
    ),

    UNIT_PLAN_SHIP_CARGO_SUPER => array(
      'description' => 'Planos del Supertransporte.',
      'effect' => 'Permite construir Supertransportes',
    ),

    UNIT_PLAN_SHIP_CARGO_HYPER => array(
      'description' => 'Planos del Hipertransporte.',
      'effect' => 'Permite construir Hipertransportes',
    ),

    UNIT_PLAN_SHIP_DEATH_STAR => array(
      'description' => 'Planos de la Estrella de la Muerte.',
      'effect' => 'Permite construir Estrellas de la Muerte',
    ),

    UNIT_PLAN_SHIP_SUPERNOVA => array(
      'description' => 'Planos del Crucero "Supernova".',
      'effect' => 'Permite construir Cruceros "Supernova"',
    ),

    UNIT_PLAN_DEF_SHIELD_PLANET => array(
      'description' => 'Planos del Escudo Planetario Definitivo.',
      'effect' => 'Permite construir Escudos Planetarios',
    ),

    // Recursos
    RES_METAL => array(
      'description' => 'Compuesto hierro-normalizado energéticamente neutro. Material base para construcción. Se presenta en lingotes de 127L (1 tonelada).',
      'effect' => '',
    ),

    RES_CRYSTAL => array(
      'description' => 'Polímero termoplástico con Efecto de Conductividad Superlumínica (ECS). Esencial para computación y motores warp.',
      'effect' => '',
    ),

    RES_DEUTERIUM => array(
      'description' => 'Isótopo estable de hidrógeno (protón+neutrón). Combustible para reactores de fusión y motores. Almacenado en contenedores criogénicos.',
      'effect' => '',
    ),

    RES_ENERGY => array(
      'description' => 'Energía universal generada por plantas solares o reactores de deuterio.',
      'effect' => '',
    ),

    RES_DARK_MATTER => array(
      'description' => '<span class="dark_matter">Materia Oscura</span> - 23% de la masa universal. Fuente energética extremadamente valiosa.',
      'effect' => '',
    ),

    RES_METAMATTER => array(
      'description' => 'Nuevo material con propiedades extraordinarias (descripción pendiente).',
      'effect' => '',
    ),    

    UNIT_PLANET_DENSITY => array(
      'description' => 'La densidad planetaria media (simplemente "densidad") caracteriza la composición química de la geosfera del planeta. En particular, predice con precisión las proporciones de recursos extraíbles.<br /><br />
      
      La geosfera planetaria se divide en:
      <ul>
        <li>Atmósfera (envoltura gaseosa)</li>
        <li>Hidrosfera (envoltura líquida)</li>
        <li>Litosfera (envoltura sólida de sustancias/elementos relativamente ligeros)</li>
        <li>Manto (capa intermedia entre litosfera y núcleo)</li>
        <li>Núcleo (elementos más pesados en el centro bajo alta presión)</li>
      </ul>

      El núcleo determina principalmente la densidad, conteniendo la mayor masa planetaria. La densidad de los compuestos químicos disminuye desde el centro hacia la superficie.<br /><br />

      Por ejemplo, un planeta con núcleo helado:
      <ul>
        <li>Núcleo: Hielo de agua con trazas de otros elementos</li>
        <li>Manto: Metano sólido</li>
        <li>Litosfera: Hidrógeno cristalino</li>
      </ul>
      
      Estos planetas suelen encontrarse en la periferia de los sistemas estelares. En raras ocasiones pueden orbitar cerca de una estrella, desarrollando una atmósfera temporal de hidrógeno/helio.<br /><br />

      Puedes cambiar el tipo de núcleo por Materia Oscura en <a href="overview.php?mode=manage" class="link ok">"Administrar planeta"</a>. El coste depende del número de sectores planetarios (excluyendo los de Terraformadores pero incluyendo los comprados con MO).<br /><br />

      Tipos de núcleos disponibles:
      <table>
        <tr class="c_c">
          <th rowspan="2">Tipo de núcleo</th>
          <th colspan="2">Densidad (kg/m³)</th>
          <th colspan="3">Producción</th>
          <th rowspan="2">Clase</th>
        </tr>
        <tr class="c_c">
          <th>Mínima</th>
          <th>Máxima</th>
          <th>Metal</th>
          <th>Cristal</th>
          <th>Deuterio</th>
        </tr>

        <tr class="c_c">
          <th>Hielo de hidrógeno</th>
          <td>250</td>
          <td>750</td>
          <td class="error">Muy pobre</td>
          <td class="warning">Pobre</td>
          <td class="positive">Excelente</td>
          <td class="error">Excepcional</td>
        </tr>

        <tr class="c_c">
          <th>Hielo de metano</th>
          <td>750</td>
          <td>1250</td>
          <td class="warning">Pobre</td>
          <td class="notice">Aceptable</td>
          <td class="positive">Muy buena</td>
          <td class="warning">Raro</td>
        </tr>

        <tr class="c_c">
          <th>Hielo acuoso</th>
          <td>1250</td>
          <td>2000</td>
          <td class="notice">Aceptable</td>
          <td class="notice">Aceptable</td>
          <td class="positive">Buena</td>
          <td class="notice">Avanzado</td>
        </tr>

        <tr class="c_c">
          <th>Cristalino</th>
          <td>2000</td>
          <td>2500</td>
          <td class="error">Muy pobre</td>
          <td class="positive">Excelente</td>
          <td class="warning">Pobre</td>
          <td class="error">Excepcional</td>
        </tr>

        <tr class="c_c">
          <th>Silíceo</th>
          <td>2500</td>
          <td>3500</td>
          <td class="warning">Pobre</td>
          <td class="positive">Muy buena</td>
          <td class="notice">Aceptable</td>
          <td class="warning">Raro</td>
        </tr>

        <tr class="c_c">
          <th>Rocoso</th>
          <td>3500</td>
          <td>4750</td>
          <td class="notice">Aceptable</td>
          <td class="positive">Buena</td>
          <td class="notice">Aceptable</td>
          <td class="notice">Avanzado</td>
        </tr>

        <tr class="c_c">
          <th>Estándar</th>
          <td>4750</td>
          <td>5750</td>
          <td>Estándar</td>
          <td>Estándar</td>
          <td>Estándar</td>
          <td>Básico</td>
        </tr>

        <tr class="c_c">
          <th>Mineral</th>
          <td>5750</td>
          <td>7000</td>
          <td class="positive">Buena</td>
          <td class="notice">Aceptable</td>
          <td class="notice">Aceptable</td>
          <td class="notice">Avanzado</td>
        </tr>

        <tr class="c_c">
          <th>Olivino</th>
          <td>7000</td>
          <td>8250</td>
          <td class="positive">Muy buena</td>
          <td class="notice">Aceptable</td>
          <td class="warning">Pobre</td>
          <td class="warning">Raro</td>
        </tr>

        <tr class="c_c">
          <th>Metálico</th>
          <td>8250</td>
          <td>9500</td>
          <td class="positive">Excelente</td>
          <td class="error">Muy pobre</td>
          <td class="error">Muy pobre</td>
          <td class="error">Excepcional</td>
        </tr>
      </table><br />

      La clase de núcleo que puedes encontrar en expediciones depende de tu nivel efectivo de Astrocartografía (incluyendo bonificaciones). Esto ayuda a equilibrar el juego para nuevos jugadores.<br /><br />

      Niveles de Astrocartografía:
      <ul>
        <li>Nivel &lt;6: Planetas con núcleos "Básico" y "Avanzado"</li>
        <li>Nivel 6-10: También núcleos "Raros"</li>
        <li>Nivel ≥11: También núcleos "Excepcionales"</li>
      </ul><br />

      Eficiencia de producción (en % respecto al núcleo estándar):
      <ul>
        <li><span class="error">Muy pobre</span>: &lt;40%</li>
        <li><span class="warning">Pobre</span>: 40-80%</li>
        <li><span class="notice">Aceptable</span>: 80-100%</li>
        <li>Estándar: 100%</li>
        <li><span class="ok">Buena</span>: 100-300%</li>
        <li><span class="ok">Muy buena</span>: 300-400%</li>
        <li><span class="ok">Excelente</span>: &gt;400%</li>
      </ul><br />


',
/*
      '<ul>
        <li>
          La clase básica incluye un tipo de núcleo. Los planetas con núcleos de esta clase son <span class="ok">muy comunes</span> - casi un tercio de los planetas habitables tienen este tipo de núcleo
          <ul>
            <li>
              Densidad del núcleo "Estándar": <span class="zero">más de 4750 pero menos de 5750</span> kg/m³
            </li>
            <li>
              En composición química, estos planetas son muy similares a la Tierra
            </li>
            <li>
              Recibieron este nombre porque los minerales están distribuidos de manera estándar - producción normal de metales, producción normal de cristales y producción normal de deuterio.
            </li>
          </ul>
          . Todos los planetas iniciales tienen núcleos estándar.
          (densidad )
          <br />
          Debido a la abundancia de hielo de agua-metano y grandes cantidades de hidrógeno en varios estados, los planetas helados tienen <span class="ok">una producción de deuterio muy alta</span>.
          Hay un inconveniente - debido a la pequeña cantidad de materia más densa, tienen <span class="error">una producción de cristales muy baja</span> y <span class="error">una producción de metal muy baja</span>. Los planetas helados son <span class="error">muy raros</span>.
        </li>
        <li>
          <span class="ok">Planetas silicatos</span> (densidad <span class="zero">más de 2000 pero menos de 3250</span> kg/m³) son <span class="warning">poco comunes</span>. En ellos
          <span class="error">la producción de metales es muy baja</span>, <span class="ok">la producción de cristales es muy alta</span> y aunque un poco más baja de lo normal, todavía tienen <span class="zero">buena producción de deuterio</span>.
        </li>
        <li>
          <span class="ok">Planetas rocosos</span> (densidad <span class="zero">más de 3250 pero menos de 4500</span> kg/m³) son <span class="zero">comunes</span>. En ellos
          hay una producción ligeramente reducida pero <span class="zero">buena de metales</span>, <span class="ok">alta producción de cristales</span> y <span class="warning">producción reducida de deuterio</span>.
        </li>
        <li>
          <span class="ok">Planetas estándar</span> (densidad <span class="zero">más de 4500 pero menos de 5750</span> kg/m³) son <span class="ok">muy comunes</span>.

        </li>
        <li>
          <span class="ok">Planetas ferrosos</span> (densidad <span class="zero">más de 5750 pero menos de 7000</span> kg/m³) son <span class="zero">comunes</span>.
          En ellos hay <span class="ok">una producción muy buena de metales</span>, <span class="warning">producción reducida de cristales</span> y <span class="zero">producción reducida de deuterio</span>.
        </li>
        <li>
          <span class="ok">Planetas metálicos</span> (densidad <span class="zero">más de 7000 pero menos de 8250</span> kg/m³) son <span class="warning">poco comunes</span>.
          En ellos hay <span class="ok">producción excelente de metales</span>, <span class="warning">baja producción de cristales</span> y <span class="zero">baja producción de deuterio</span>.
        </li>
        <li>
          <span class="ok">Planetas de metales pesados</span> (densidad <span class="zero">más de 8250</span> kg/m³) son <span class="error">muy raros</span>.
          En ellos hay <span class="ok">producción magnífica de metales</span>, <span class="error">producción de cristales muy baja</span> y <span class="error">producción de deuterio muy baja</span>.
        </li>
      </ul>

      <ul>
        <li>
          <span class="ok">Planetas helados</span> (densidad <span class="zero">menos de 2000</span> kg/m³).
          Debido a la abundancia de hielo de agua-metano y grandes cantidades de hidrógeno en varios estados, tienen <span class="ok">producción de deuterio muy alta</span>.
          El inconveniente es que por la poca cantidad de materia más densa, tienen <span class="error">producción de cristales muy baja</span> y <span class="error">producción de metal muy baja</span>. Son <span class="error">muy raros</span>.
        </li>
        <li>
          <span class="ok">Planetas silicatos</span> (densidad <span class="zero">más de 2000 pero menos de 3250</span> kg/m³) son <span class="warning">poco comunes</span>. En ellos
          <span class="error">la producción de metales es muy baja</span>, <span class="ok">la producción de cristales es muy alta</span> y aunque un poco más baja de lo normal, todavía tienen <span class="zero">buena producción de deuterio</span>.
        </li>
        <li>
          <span class="ok">Planetas rocosos</span> (densidad <span class="zero">más de 3250 pero menos de 4500</span> kg/m³) son <span class="zero">comunes</span>. En ellos
          hay una producción ligeramente reducida pero <span class="zero">buena de metales</span>, <span class="ok">alta producción de cristales</span> y <span class="warning">producción reducida de deuterio</span>.
        </li>
        <li>
          <span class="ok">Planetas estándar</span> (densidad <span class="zero">más de 4500 pero menos de 5750</span> kg/m³) son <span class="ok">muy comunes</span>. En composición química son muy similares a la Tierra.
          Los minerales están distribuidos de manera estándar - <span class="zero">buena producción de metales</span>, <span class="zero">buena producción de cristales</span> y <span class="zero">buena producción de deuterio</span>.
        </li>
        <li>
          <span class="ok">Planetas ferrosos</span> (densidad <span class="zero">más de 5750 pero menos de 7000</span> kg/m³) son <span class="zero">comunes</span>.
          En ellos hay <span class="ok">producción muy buena de metales</span>, <span class="warning">producción reducida de cristales</span> y <span class="zero">producción reducida de deuterio</span>.
        </li>
        <li>
          <span class="ok">Planetas metálicos</span> (densidad <span class="zero">más de 7000 pero menos de 8250</span> kg/m³) son <span class="warning">poco comunes</span>.
          En ellos hay <span class="ok">producción excelente de metales</span>, <span class="warning">baja producción de cristales</span> y <span class="zero">baja producción de deuterio</span>.
        </li>
        <li>
          <span class="ok">Planetas de metales pesados</span> (densidad <span class="zero">más de 8250</span> kg/m³) son <span class="error">muy raros</span>.
          En ellos hay <span class="ok">producción magnífica de metales</span>, <span class="error">producción de cristales muy baja</span> y <span class="error">producción de deuterio muy baja</span>.
        </li>
      </ul>',
*/
    ),

    UNIT_CAN_NOT_BE_BUILD => array(
      'description'       => 'Esta unidad no puede ser construida por jugadores actualmente. No, no se puede comprar. Quizás pudo ser obtenida/construida anteriormente. Y no, no preguntes si podrá ser construida en el futuro u obtenida de alguna manera. Eso se desconoce<br/>Aunque... tal vez pueda obtenerse por otros métodos? Pregunta a otros jugadores... Preguntar a los administradores es inútil',
      'description_short' => 'Actualmente esta unidad no puede ser construida o comprada',
      'effect'            => 'Esta unidad no puede ser construida o comprada',
    ),
  )
);
