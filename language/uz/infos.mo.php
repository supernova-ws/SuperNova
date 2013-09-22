<?php

/*
#############################################################################
#  Filename: infos.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Startegy Game
#
#  Copyright В© 2009 Gorlum for Project "SuperNova.WS"
#  Copyright В© 2009 MSW
#############################################################################
*/

/**
*
* @package language
* @system [Uzbek]
* @version 37a10.3
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();

$lang = array_merge($lang, array(
  'wiki_title' => 'Novapediya',
  'wiki_requrements' => 'Qurulish va tadqiqotlar uchun talablar',

  'wiki_char_nominal' => 'Aniqlik',
  'wiki_char_actual' => 'Aktuallik',

  'wiki_ship_engine_header' => 'Dvigetellarning xususiyati',

  'wiki_ship_header' => 'Transportlarning xususiyati',
  'wiki_ship_speed' => 'Tezlik',
  'wiki_ship_consumption' => 'Deyteriya sarfi',
  'wiki_ship_capacity' => 'Yuk sig`imi',
  'wiki_ship_hint' => '<li>Aktual tezlik va barcha bonuslar tehnologiyalarni bajarish va boshqalardan keladi.</li>',

  'wiki_combat_header' => 'Xarbiy xususiyati',
  'wiki_combat_attack' => 'Zarba o`qining kuchi',
  'wiki_combat_shield' => 'Qalqon batareyasining sig`imi,zarbasi',
  'wiki_combat_armor' => 'Hitlar va Strukturniy nishonlar',

  'wiki_combat_volley_header' => 'Kuchaytirilgan olov',
  'wiki_combat_volley_to' => 'Urushda yengish donasi',
  'wiki_combat_volley_from' => 'Jangda yoq`otish donasi',

//  'wiki_combat_volley_text' => 'Bir kuch bilan yengish,
));

if(!is_array($lang['info']))
{
  $lang['info'] = array();
}

$lang['info'] = (array(
    STRUC_MINE_METAL => array(
      'description' => 'Bino va kemalarni qurishlishi uchun asosiy xom-ashyo yetkazib beruvchi. Metall-eng arzon hom-ashyo,lekin boshqa resurslardan ko\`p talab qilinadiganlardan biri.Metall ishlab chiqarish uchun oz energiya talab qilinadi.Qanchalik shaxtaning ko`p bo`lishi uni chuqurlashuviga olib keladi.Ko`pchilik sayyoralarda metall katta chuqurliklarda joylashadi. Ushbu chuqur konlarda ko`pgina metallarni ishlab chiqara olasiz.Demak katta chuqir konlar katta energiyani talab qiladi.',
      'description_short' => 'Bino va kemalarni qurishlishi uchun asosiy xom-ashyo yetkazib beruvchi.',
    ),

    STRUC_MINE_CRYSTAL => array(
      'description' => 'Kristallarni sintezlash uchun metallga nisbatan ikki marotaba ko`p energiya talab qiladi, shuning uchun u ozgina qimmatroq. Kristall-hohlagan bir zamonaviy kompyuter texnologiyasining va varp-dvigateli kalit qismining   asosi hisoblanadi. Shuning uchun hamma kemalar va deyarli hamma binolarga kristall talab qilinadi.Sintizatorni mukammmallashtirish kristall ishlab chiqarish sonini oshiradi.',
      'description_short' => 'Kompyuter texnologiyalari va varp-uzatish qurulmalari uchun asosiy xom-ashyo yetkazib beruvchi.',
    ),

    STRUC_MINE_DEUTERIUM => array(
      'description' => 'Yoqilgi - bu og`ir vadorod.Uning katta zaxirasi dengiz ostida joylashadi.Sintizatorni oshirish chuqur deyteriya zaxiralarining rivojlanishiga hissa qo`shadi.Deyteriya kemalarga yoqilgi sifatida, deyarli barcha tadqiqotlar uchun,galaktikani ko`rish uchun zarur hisoblanadi.',
      'description_short' => 'Sayyoradigi deyteriyaning uncha katta bo`lmagan qismini suvdan parchalaydi.',
    ),

    STRUC_MINE_SOLAR => array(
      'description' => 'Energiya shaxtalari va sentezatorlarini ta`minlash uchun katta miqdordagi quyosh elektrostansiyalari ta`lab qilinadi.Qanchalik stansiyalarni qurulishi,shunchalik yer satxini quyosh batareyalari bilan qamrab oladi va u yorug`lik energiyasini elektr energiyasiga almashtirib beradi.Quyosh elektrostansiyasi sayyoraning asosiy elektr ta`minlovchisi hisoblanadi.',
      'description_short' => 'Quyosh nuridan energiya ishlab chiqaradi.Ko`pchilik qurulishlarning faoliyati,ishlash uchun energiya talab qilinadi.',
    ),

    STRUC_MINE_FUSION => array(
      'description_short' => 'Ikki og`ir vadorod atomi bilan geliy atomi xosilasi jarayonidan energiyani ajratadi.',
      'description' => 'Termoyaderniy elektrostansiyalarda -2 og`ir vodorod atomining  yuqori haroratida va termoyadro sentezining katta bosimi ostida bitta- geliy atomiga birlashtiriladi.Bu jarayonda geliyning atomlari shakllanadi,Qanchalik termoyader reaktorining ko`p bo`lishi , shunchalik sentezlash jarayoning murakkablashuviga va rektorning ko`p energiya ishlab chiqarishiga olib keladi.',
    ),

    STRUC_FACTORY_ROBOT => array(
      'description' => 'Planeta infratuzilmasini qurishda oddiy ishchi kuchini ishlab chiqarish. Har darajada qurish vaqti kopayib boradi.',
      'description_short' => 'Planeta infratuzilmasini qurishda mashina va mehanizmlarni ishlab chiqarish. Korhonalarni qurishning har darajasida qurish vaqti kopayib boradi.',
    ),

    STRUC_FACTORY_NANO => array(
      'description' => 'Nanofabrika bu zavodlar qurilishidagi asosiy vazifani bajaruvchi fabrikalardan biridir. Uning birgina vazifasi nanoyiguvchilardir. Har qoshimcha nanofabrika qurganingizda boshqa zavodlar, himoyalanuvchi qurollarni va kemalarni qurilishi vaqti ikki barobar kamayadi va siz zavodlarni, himoyalanuvchi qurollarni va kemalarni tez qurish imkoniga ega bolasiz.',
      'description_short' => 'Fabrikaning asosiy negizi nanoyiguvchilardir. Ular atom va malekulalarni organib chiqadi va ulardan oqilona foydalanihshadi.',
      'effect' => 'Fabrikaning har darajaga kotarilishi boshqa fabrikalar qurilishini ikki barobarga qisqartiradi.',
    ),

    STRUC_FACTORY_HANGAR => array(
      'description' => 'Verf fabrikasini qurish bilan barcha turdagi kemalarni va himoyalanuvchi qurollarni qurishingiz mumkin. Uni qancha kop qursangiz katta kemalar va himoyalanuvchi qurollar chiqishi ham shuncha tezlashadi. Verf fabrikasini qurganiz sayin boshqa tehnologiyalar chiqishi ham osonlashadi.',
      'description_short' => 'Verf kosmik kemalar opbita strukturasi va himoyalanuvchi qurollar ishlab chiqaradi',
    ),

    STRUC_STORE_METAL => array(
      'description' => 'Rudalarni kattagina ombori. Uning darajasi qancha katta bolsa shuncha kop metal joylashtirsa boladi. Agar u tolib qolsa metal qazib olish tohtaydi.',
      'description_short' => 'Bu omborlar qayta ishlanmagan Metalni keyin ishlatilish vaqtigacha saqlab beradi.',
    ),

    STRUC_STORE_CRYSTAL => array(
      'description' => 'Bu omborda qayta ishlanmagan kristal saqlanadi. Uning darajasi qancha katta bolsa shuncha kop kristal joylashtirsa boladi. Agar u tolib qolsa kristal ishlab chiqarish tohtaydi.',
      'description_short' => 'Kristallar keyin ishlatishga tayyorlash uchun saqlanadi.',
    ),

    STRUC_STORE_DEUTERIUM => array(
      'description' => 'Bu omborda qayta ishlanmagan yoqilgilar saqlanadi. Odatda u kosmik kemalar uchadigan port yaqinida boladi. Uning darajasi qancha katta bolsa shuncha kop yoqilgi joylashtirsa boladi. Agar u tolib qolsa yoqilgi ishlab chiqarish tohtaydi.',
      'description_short' => 'Yoqilgini keyin ishlatishga tayyorlash uchun saqlanadi.',
    ),

    STRUC_LABORATORY => array(
      'description' => 'Yangi tehnologiyalarni ishga tushirish uchun keyingi darajaga kotarish kerak. Keyingi darajaga kotarishni ochish uchun esa ushbu fabrikani qurish kerak yani laboratoriyani. Laboratoriyani qancha kop qurganiz sayin keyingi tehnologiyalar ochish vaqti kamayib boradi. Yangi tehnologiyalarni boshqa sayyoralarda ham qollasa boladi.',
      'description_short' => 'Bu laboratoriyada yangi tehnologiyalar kashf qilinadi.',
    ),

    STRUC_TERRAFORMER => array(
      'description' => 'Sayyoraga fabrikalar qurganiz sayin undagi joy kamayib boradi. Joy tugagach siz fabrikalar qura olmaysiz. Fizik olimlarning kichik guruhi va nanotehniklar birlashib planetada joy kattalashtirishning yolini topishdi - Terraformer.<br><br>Katta moqdorda energinani ishlatgan holda terraformer yangi joylar yaratadi. Buning uchun nanotehnologlar javob berishadi.',
      'description_short' => 'Terraformer bu sayyorada qoshimcha joy yaratish imkonini beradi.',
    ),

    STRUC_ALLY_DEPOSIT => array(
      'description' => 'Ittifoqdosh ombori bu sizga yordamga kelgan ittifoqdoshingiz kosmik kemalari uchun yoqilgilar saqlanadigan ombordir. Uning darajasi qancha yuqori bolsa shuncha kop ittifoqdoshingiz kosmikkemalarini qabul qila oladi.',
      'description_short' => 'Ittifoqdosh ombori bu sizga yordamga kelgan ittifoqdoshingiz kosmik kemalari uchun yoqilgilar saqlanadigan ombordir.',
    ),

    STRUC_LABORATORY_NANO => array(
      'description' => 'Nanolaboratoriya - Yangi tehnologiyalar ochish vaqtini ikki barobarga qisqartiradi.',
      'description_short' => 'Nanolaboratoriya - tehnika olamining eng songgi yutiqlaridan biri. Ichki kichlilik evaziga Kristal komputerlar va aniq ishlovchi nanoyiguvchilar istalgan tehnologiyani ochish vaqtini ikki barobarga qisqartira oladi.',
    ),

    STRUC_MOON_STATION => array(
      'description' => 'OY - atmosferaga kira olmaydi shuning uchun uning joyi kam boladi. Siz oyni qura olmaysiz. U qachondir sizga raqibingiz juda katta kosmik kemalar bilan hujum qilganda ozi paydo boladi. Oyni joyini kopaytirish uchun Oy bazasini qurish kerak boladi. Oy bazasi havo, tortishish kuchi va issiqlik ishlab chiqarib beradi. Oy bazanini qancha kop qursangiz oydagi rivojlanish shuncha jadallashadi. Har bir Oy bazasini qurganingizda 3 ta yangi joyga ega bolasiz. Uning diametri 2 (oy diametri/1000)^2, ni tashkil qiladi.',
      'description_short' => 'OY - atmosferaga kira olmaydi shuning uchun uning joyi kam boladi.',
    ),

    STRUC_MOON_PHALANX => array(
      'description' => 'Kuchli chastotali koruvchi yaqinlashib kelayotgan va ketayotgan barcha kemalarni kuzatish imkonini beradi. Uni faqat oyga qursa boladi. Yoritgichlarni nima foydasi bor: ular orqali yaqinlashib kelayotgan dushman kemalari soni va vaqtini aniqlasa boladi. Bu ish oz energiya ishlatuvchi kuchli komputerlar yordamida amalga oshiriladi. Bu yoritgichlarni ishlashi uchun energiya kerak boladi. Oyda esa energiya ishlab chiqarib bolmaydi. Shuning uchun energiyani yoqilgidan oladi. Birinchi darajadagi Yoritgichlar ishlashi uchun oyda 1000 yoqilgi bolishi shart. Har darajada yoqilgi ikki barobar oshib boradi. Kelayotgan kemalarni kuzatish sayyorani kuzatish tugmasini bosib korish mumkin',
      'description_short' => 'Kuchli chastotali koruvchi yaqinlashib kelayotgan va ketayotgan barcha kemalarni kuzatish imkonini beradi.',
    ),

    STRUC_MOON_GATE => array(
      'description' => 'Eshik - bu boshqa sayyoraga tez otkazuvchi tuynukdir. Uni yordamida siz anchagina vaqtni tejasingiz mukin boladi. Bu teleportatsiya kemalar uchun yoqilgi talab qilmaydi, faqatgina bir sayyoradan ikkinchi sayyoraga teleport qilish uchun bir soat vaqt ketadi. Teleportatsiya orqali Resurslarni tashishni ham iloji yoq. Barcha ishlar kuchli tehnologiyalar asosida yuritiladi.',
      'description_short' => 'Eshik - bu boshqa sayyoraga tez otkazuvchi tuynukdir.',
    ),

    STRUC_SILO => array(
      'description' => 'Paketalar shahtasi - bu raketalar saqlovchi ombordir. Har bir daraja to\'rt sayyoralararo raketalar yoki on ikki raketa ushlovchi ishlab chiqarish imkonini beradi. Bir sayyoralararo raketa saqlash uch oddiy raketa ushlovchi joyi bilan tengdir. Sayyoralararo reketa bilan siz dushmanga uzoqdan turib hujum qila olasiz. Raketa ushlovchi bilan dushmaningiz sizga sayyoralararo raketa uchirgandaular kelayotgan raketalarni urib tushurishadi. Yani sayyoralar aro raketa hujum uchun, raketa ushlovchi esa himoya uchun kerak',
      'description_short' => 'Р Raketalar shahtasi - yangi raketalar ishlab chiqarish va ularni saqlovchi ombor vazifasini otaydi.',
    ),

    TECH_SPY => array(
      'description_short' => 'Bu tehnologiya orqali siz boshqa planeta haqida malumot olishingiz mumkin boladi.',
      'description' => 'Josuslik ishlarini amalga oshiruvchi ushbu qurilma orqali siz yangi imkoniyatlarga ega bolasiz. Yani siz hujum qilmoqchi bolgan sayyora haqida har qanday malumotni qolga kiritishingiz mumkin va shu orinda aksincha sizning sayyorangizda josuslikqilishganda ular sayyorangiz haqida malumot olib ketishini oldini oladi yani ularni kemalarini urib tushiradi. Bu tehnologiyani qancha kop chiqarsangiz bu tehnologiyadan shuncha keng foydalana olasiz. Josuslik urushda katta rol oynaydi va dushman haqida malumot yetkazib turadi. Josuslik harakatini kuchaytirish uchun josuslik tehnologiyasini darajasini kopaytirish kerak boladi. Bu tehnologiyani sayyorada qurishni boshlashigiz bilan darajasini kotarib qoyishni maslahat beramiz.',
    ),

    TECH_COMPUTER => array(
      'description' => 'Komputer tehnologiyasi- sayyoradagi komputer ishlarini kuchaytirib beradi. Bu tehnologiya yordamida siz katta miqdordagi qoshinga birdaniga buyruq berish imkoniga ega bolasiz. Uning har darakasi +1 miqdordagi flotni oz ichiga oladi. Qancha kop kemani birdaniga boshqarsangiz mehnat samaradorligi shuncha oshadi.',
      'description_short' => 'Sayyoradagi komputerlarni kuchaytiradi va katta miqdordagi kemalar toplamiga birdaniga buyruq berish imkoniga ega bolasiz.Har keyingi daraja kemalar kattaligini oz ichiga oladi.',
    ),

    TECH_WEAPON => array(
      'description' => 'Qurol tehnologiyasi - yangi qurollar ishlab chiqarishdagi yutuqlarni, qurollarni kuchaytirishni oz ichiga oladi. Bu tehnologiyaning har darajasi sayyorangizdagi qurollarni 10% ga oshiradi. Bu tehnologiya asosan hujumda kerak boladi. Oyin urushni asos qilib olgani uchun bu tehnologiya muhim ahamiyatga egadir.',
      'description_short' => 'Qurol tehnologiyasi - yangi qurollar ishlab chiqarishdagi yutuqlarni, qurollarni kuchaytirishni oz ichiga oladi. Bu tehnologiyaning har darajasi sayyorangizdagi qurollarni 10% ga oshiradi.',
    ),

    TECH_SHIELD => array(
      'description' => 'Mana bu texnologiyaning rivojlanishi qalqonlar quvvatini ta`minlashni va ekranning himoyasini oshiradi , shu bois o`z navbatida turg`unlik qobiliyatini ko`taradi va yutish qobiliyati yoki dushman energiya hujumini aks ettiradi. Shu tufayli har bir o`rganilayotgan darajasi bilan kema qalqonlari samaradorligi va tok ishlab chiqaruvchi mashinalar energiya maydonini belgilangan quvvatdan 10% ga ko`tariladi.',
      'description_short' => 'Ushbu texnologiya qalqonlarning katta energota`minotning judaham yangi imkoniyatlari bilan shug`ullanadi, shu uchun ularni samarali va turg`un qiladi. Har bir o`rganilgan daraja evaziga qalqonlarning samaradorligi 10% ga oshadi.',
    ),

    TECH_ARMOR => array(
      'description' => 'Maxsus qotishmalar fazoviy kemalarni zirhini yaxshilaydi. Judaham turg`un qotishma topilishi bilanoq, maxsus nurlar fazoviy kemani molekulyar tarkibi o`zgartirayapti va uni o`rganilgan qotishma holatiga qadar olib boradi. Shunday qilib, zirhlarning chidamliligi har bir darajada 10%ga kuchayadi.',
      'description_short' => 'Maxsus qotishmalar fazoviy kemalarni zirhini yaxshilaydi. Har bir darajada zirhlarning mustahkamligi asosiysidan 10% ga ko`tariladi.',
    ),

    TECH_ENERGY => array(
      'description' => 'Energiya tehnologiyasi energiyani kuchaytirish va yangi tehnologiyalarni ochishda yordam beradi.',
      'description_short' => 'Bu tehnologiyani darajasini oshirish bilan Termoyaderniy elektrostansiya beradigan energiya miqdorini kopaytirsa boladi.',
    ),

    TECH_HYPERSPACE => array(
      'description' => 'Yolni kesib otishning 4-5 ozgarishi divigatelni tejas va bu dvigatel ishlaganda effektivniy ishlash imkoniga ega boladi. ',
      'description_short' => 'Yolni kesib otishning 4-5 ozgarishi divigatelni tejas va bu dvigatel ishlaganda effektivniy ishlash imkoniga ega boladi.',
    ),

    TECH_ENGINE_CHEMICAL => array(
      'description' => 'Kimyoviy raketa dvigateli - dvigatellarning eng sodda korinishi. Bu dvigatel issiqlik va okislitel orqali ekzotermicheskiy kimyoviy reaksiya hosil qiladi. Natijada kuchli harorat hosil boladi va bu kemalar tezligini oshiradi. Lekin bu dvigatelning ishlash hususiyati unchalik yuqori emasfaqatgina kemalar unga muhtoj bolishi mumkin. Darajasini oshirish arzonga tushishligi sabab qurgan yahshi. Uning har darajasi kemalar tezligini 10% ga tezlashtiradi. Bu dvigateldan foydalana oladigan kemalar: Kuchik transport (bu dvigatelni 5 darajaga kotarmagan bolsangiz ham), Katta transport, Qayta ishlovchi kemala, Josuslar zondi va Yengil Istribitel',
      'description_short' => 'Bu dvigatelni kuchaytirish bir qancha kemalarni tez harakat qilishiga yordam beradi. Dvigatelning har darajasi kemalar tezligini 10% ga tezlashtiradi.',
    ),

    TECH_ENGINE_ION => array(
      'description' => 'Ionoviy Dvigatel - ion gazlarini siqib qayta ishlaydi. Shu sabab kuchli massoviy razryadni hosil qilib ota kuchli tezlik hosib qoladi (himicheskiy dvigatelda 210 km/s - ionoviy dvigatel 3вЂ”4,5 km/s ga tengdir). Shuning uchun ionoviy dvigatelni tezroq ochib darajasini kotarish lozim. Lekin bu dvigatel kop energiya sarflaydi. Shuning uchun bu dvigateldan foydalanuvchi kemalar bortiga qoshimcha energiya ishlab chiqaruvchi termoyaderniy reaktor qoyilgan. Ham energiyangizni ham yoqilgingizni tejaydi. Bu dvigateldan foydalana oladigan kemalar: Super transport, Klonlashtiruvchi kema, Ogir Istribitel, Esmines va Bambardirovshik (Faqat giper tehnologiyani 8 darajaga olib chiqqanizdan song). Hamda bu dvigatel sayyoralar aro reketalarga ham ornatilgan.',
      'description_short' => 'Ionoviy Dvigatel - ion gazlarini siqib qayta ishlaydi. Bu dvigatelning har darajasi kemalar tezligini 20% ga tezlashtirib beradi.',
    ),

    TECH_ENGINE_HYPER => array(
      'description' => 'Bu tehnologiya sabab kemalar uzoq sayyoralarga ucha oladi. Gipertransportlar har darajasiga 30% tezroq yetadi. Bu dvigateldan foydalana oladigan kemalar: Gipertransport, Kreyser, Bambardirovshik (8-darajadan song), Lineyniy kreyser, Yoq qiluvchi( Unichtojitel ), Yulduzlik olim va kreyserlar turidan "Sverhnoviy"',
      'description_short' => 'Bu tehnologiya sabab kemalar uzoq sayyoralarga ucha oladi. Gipertransportlar har darajasiga 30% tezroq yetadi.',
    ),

    TECH_LASER => array(
      'description' => 'Lazerlar himoya uchun ihtiro qilingan ajoyib tehnologiyadir. Bu lazerlar hech qaysi tehnologiya bilan bogliq emas, holis ravishda siz tomon kelayotgan kemalar qalqonini yoq qiladi. Bu tehnologiyani yangi darajaga otkazish orqali siz lazerlarni kuchaytiribgina qolmay yangi tehnologiyalarni ham ochishingiz mumkin.',
      'description_short' => 'Nurni boshqara olish tehnologiyasi orqali kelayotgan kemalarga bemalol ota oladi.',
    ),

    TECH_ION => array(
      'description' => 'Ionoviy tehnologiya orqali siz ionoviy dvigatellarni tezlashtira olasiz va ionoviy qurol ishlab chiqara olash imkoniga ega bolasiz.',
      'description_short' => 'Ionoviy tehnologiya orqali siz ionoviy dvigatellarni tezlashtira olasiz va ionoviy qurol ishlab chiqara olash imkoniga ega bolasiz.',
    ),

    TECH_PLASMA => array(
      'description' => 'Ionoviy tehnologiyani yangi avlodi Plazmenniy tehnologiyadir. U plazmenniy qurollarni qurish va sayyorani kuchli himoya qilish mumkin.',
      'description_short' => 'Ionoviy tehnologiyani yangi avlodi Plazmenniy tehnologiyadir. U plazmenniy qurollarni qurish va sayyorani kuchli himoya qilish mumkin.',
    ),

    TECH_RESEARCH => array(
      'description' => 'Bu halqa sizga boshqa sayyoralarda tadqiqot otkazish imkonini beradi. Uning har darajasi laboratoriyadan yangi organuvchilarni qosha oladi.',
      'description_short' => 'Bu halqa sizga boshqa sayyoralarda tadqiqot otkazish imkonini beradi. Uning har darajasi laboratoriyadan yangi organuvchilarni qosha oladi.',
    ),

    TECH_EXPEDITION => array(
      'description' => 'Ekspeditsiya tehnologiyasi - boshqa tehnologiyalarni boshqarish va boshqa sayyoralarga izlanishlar olib borishga yordam beradi. Uning kichikkina laboratoriyasi, biblatekasi,  mavjud. Kemalar havfsizligini taminlash uchun uning darajasini kotaring. Shunda biror havf sezgan kemalar atrofida kuchli energiya oqimi hosil qiladi va vaziyyatdan sog omon chiqib ketishadi',
      'description_short' => 'Ekspeditsiya tehnologiyasi - boshqa tehnologiyalarni boshqarish va boshqa sayyoralarga izlanishlar olib borishga yordam beradi..',
    ),

    TECH_COLONIZATION => array(
      'description' => 'Vlastitel, koinotda koplab koloniyalar yaratish imkonini beradi.',
      'description_short' => 'Bu tehnologiya ota muhim bolib koinot tez rivojlanishga yordam beradi.',
    ),

    TECH_GRAVITON => array(
      'description' => 'Graviton - bu qism bolib, kuchni kopaytirishda yordam beradi. Bu kuchni graviton yollar orqali oladi. Kerakli miqdorda graviton yigish uchun katta miqdordagi energiya kerak boladi.',
      'description_short' => 'Graviton - bu qism bolib, kuchni kopaytirishda yordam beradi. Bu kuchni graviton yollar orqali oladi. Kerakli miqdorda graviton yigish uchun katta miqdordagi energiya kerak boladi.',
    ),

    SHIP_CARGO_SMALL => array(
      'description' => 'Transportlar tahminan shunday hajmda boladi, lekin ular joyni tejash maqsadida kuchli dvigatel va kuchli qurollar bilan tanimlanmagan boladi. Kichik transport 5000 homashyo yuradi. Bu kema kopincha boshqa katta kemalarni  kuzatib boradi. Agar ionoviy dvigatel 5 darajadan oshsa uning tezligi oshadi.',
      'description_short' => 'Kichik transport - bu qurol olib yurmaydigan homashyo ni tez boshqa sayyoraga tashuvchi kemadir. Agar ionoviy dvigatel 5 darajadan oshsa uning tezligi oshadi.',
    ),

    SHIP_CARGO_BIG => array(
      'description' => 'Katta transport - bu kema ham joy tejash maqsadida qurol olib yurmaydi... Shuning uchun bu kemani kuzatuvchi kemalarsiz boshqa sayyoraga jonatib bolmaydi. Kimyoviy dvigatel tufayli bu kema sayyoralar aro tez harakatlanadi va hom ashyoni vaqtida yetkazib olib boradi, hamda bu kema hujum qilishda koproq hom ashyo olib kelish maqsadida qurolli kemalar bilan birga borishi ham mumkin.',
      'description_short' => 'Katta transport bu kichik transport qiluvchi vazifani bajaradi farqi bu kemaga koproq hom ashyo sigadi. Tezligi ham yuqori.',
    ),

    SHIP_CARGO_SUPER => array(
      'description' => 'Transport olamining ohirgi sozlaridan biri bu super transportdir. Supertransport - katta transportlardan bolib, ionoviy dvigatel bilan harakatlanadi. Uning tezligi uncha yuqori emasР°, yoqilgi sarfi esa yuqoridir. Uning boshqa kemalardan afzalligi qalqoni ota mustahkamligidir.',
      'description_short' => 'Transport olamining ohirgi sozlaridan biri bu super transportdir. Supertransport - katta transportlardan bolib, ionoviy dvigatel bilan harakatlanadi..',
    ),

    SHIP_CARGO_HYPER => array(
      'description' => 'Supertransport transportlar olamining ohirgi sozlaridan biri bolsa Gipertransport bu marradir. "Bahaybat(gigantskiy)" - bu kema uchun oddiy sozdir. Bu kemaning kattaligi ortacha oy bilan teng bolib, juda katta miqdordagi hom ashyoni boshqa sayyoraga tezda olib ota oladi. U faqat gidrodvigatel bilan yuradi. Lekin uning narhi yuqori birgina gipertransport uchun onlab unik puli ketadi. Yoqilgi sarfi esa har bir imperatrni yiglatmasdan qolmaydi. Shuning uchun bu kema kuchli rivojlangan va millionlab homashyoni bshqa sayyoraga tashish zarur bolgan imperiyalar uchundir',
      'description_short' => 'Bu kemaning kattaligi ortacha oy bilan teng bolib, juda katta miqdordagi hom ashyoni boshqa sayyoraga tezda olib ota oladi.',
    ),

    SHIP_FIGHTER_LIGHT => array(
      'description' => 'Yengil istribitel - bu har bir sayyorada topish mumkin bolgan kichik qurollangan kemadir. Uning sarfi uncha yuqori emas. Qalqon kuchliligi mosligi ham past darajada. ',
      'description_short' => 'Yengil istribitel - bu har bir sayyorada topish mumkin bolgan kichik qurollangan kemadir. Uning sarfi uncha yuqori emas. Qalqon kuchliligi mosligi ham past darajada.',
    ),

    SHIP_FIGHTER_HEAVY => array(
      'description' => 'Yengil istribitelning keyingi avlodi. Urganuvchilar bu tehnologiyani yaratishda obyektiv dvigatel oddiy dvigateldan yahshiroq degan qarorga kelishdi. Bu kema ionoviy dvigatel joylashtirilgan ilk kemadir. Shu orinda narhi ham kotarilgan. Uni qurishda qimmat materiallardan foydalanishgan. Shuning uchun Ogir istribitel tehnologiyalar olamida yangilikni ilk kiritgan kema hisoblanadi.',
      'description_short' => 'Yengis istribitelga qaraganda yahshi himoya va qurolga ega.',
    ),

    SHIP_DESTROYER => array(
      'description' => 'Nurli qurol yaratilgach kemalar olamida ham ozgarish boldi. ogir hurli qurol va ionoviy pushka bilan taminlangan kemalar ishlab chiqarila boshlandi. Otli qurollarga qarshi tura oladigan Qalqonlar bilan taminlandi. Esmineslar shunday yaratildi. U ogir istribiteldan kora har taraflama kuchliroq edi. Shu qatorda tezroq ham edi. Ular koinot hukmdoriga aylandi. Qachonki Gaus va Plazma himoya quroli yaratilgach ularning davri otdi. Hozirda ular faqatgina istribitellar ustidan galaba qozonihslari mumkin holos.',
      'description_short' => 'Esmineslar 2 kuchli qurollanganlar safiga kiradi. Ogir istribiteldan kuchliroq va qalqonlari utga chidamli qilib yaratilgandir. Ular juda tezdir',
    ),

    SHIP_CRUISER => array(
      'description' => 'Kreyserlar flotning qoshimcha kemalaridir. Ular ogir qurollar, kuchli tezlik va katta yuk tashish uchun moljallangan joy bilan taminlandi.',
      'description_short' => 'Kreyserlar flotning qoshimcha kemalaridir. Ular ogir qurollar, kuchli tezlik va katta yuk tashish uchun moljallangan joy bilan taminlandi.',
    ),

    SHIP_COLONIZER => array(
      'description' => 'Bu kuchli himoya bilan taminlangan kema koinotda yangi sayyora ochishda hizmat qiladi. Bu esa oz ornida imperiyangiz kengayishi va rivojlanishni kuchaytiradi. U homashyo tashuvchi qoshimcha kemalar bilan birgalikda jonatiladi. Klonlashtirish chegarasini  menyudagi \'Dunyo konstanti\' dan bilib olishingiz mumkin',
      'description_short' => 'Bu kemaning sharofati bilan isz koinotda kiritilmagan sayyorani barpo etishingiz mumkin.',
    ),

    SHIP_RECYCLER => array(
      'description' => 'oyindagi urushlari butun koinot boylab otadi. Jangda minglab kemalar yoq qilinadi. Oqibatda orbitada ulkan siniqlar toplami hosib boladi. Oddiy kemalar ularni oldiga yaqinlasholmaydi. Siniqlar toplamini yigish uchun "Qalqon tehnologiyasi" yordamida alohida kema "Qayta ishlovchi kemalar" oylab topildi. Bu kema yordamida zararni qoplashingiz mumkin boladi. Uning yuk tashish hajmi 20 000 etib tayinlangan va qurol briktirilmagan.',
      'description_short' => 'Qayta ishlovchi kemalar orqali siz siniqlar toplamini yigishingiz mumkin.',
    ),

    SHIP_SPY => array(
      'description' => 'Josuslik zondi - Bu kichik kozga tashlanmaydigan kema bolib, dushman sayyorasini organish bilan shugullanadi. Ular ota kuchli dvigatel bilan taminlangan. Ular bir necha soniyada dushman hududiga kira oladi. Malumotlarni olish chogida ularni dushman sezib qolsa darhol yoq qoladi. Kema na joy, na qurol va na qalqon hech narsa bilan taminlanmagan.',
      'description_short' => 'Josuslik zondi - Bu kichik kozga tashlanmaydigan kema bolib, dushman sayyorasini organish bilan shugullanadi.',
    ),

    SHIP_BOMBER => array(
      'description' => 'Bambardirovshik sayyora himoyasini yoq qilish uchun mahsus ishlab chiqilgan. Hurli moljal yordamida bu kema plazmali bombalarni aniq joyiga tashlay oladi. Giper dvigatelni 8 darajaga kotarib uning tezligini oshirish mumkin.',
      'description_short' => 'Bambardirovshik sayyora himoyasini yoq qilish uchun mahsus ishlab chiqilgan.',
    ),

    SHIP_SATTELITE_SOLAR => array(
      'description' => 'Quyosh sputniklari - Sayyora orbitasida suzib yuruvchi kemalardir. Ular quyoshdan energiya ishlab sayyoraga yuborishadi. Natijada energiya tahchilligiga barham beriladi. Demak sayyora quyoshga qancha yaqin bolsa quyosh sputniklari shuncha kop energiya ishlab chiqradi.Sayyora quyoshga qanchalik yaqinligini uning haroratidan bilsangiz boladi. Uning narhi ham ancha arzon, lekin shu qatorda sizga hujum vaqtida ular yoq bolib ketadi.',
      'description_short' => 'Quyosh sputniklari - Sayyora orbitasida suzib yuruvchi kemalardir. Ular quyoshdan energiya ishlab sayyoraga yuborishadi. Natijada energiya tahchilligiga barham beriladi.',
    ),

    SHIP_DESTRUCTOR => array(
      'description' => 'Yoq qiluvchilar (Unichtojitel-UNIK) - harbiy kemalar orasida qirol hisoblanadi. Unga ionoviy, plazmenniy va gaus pushkalari joylashtirilgan. Shuning uchun u hujum qilganda 99% yutish imkoni mavjud. Shuning uchun yoq qiluvchi kemalar buyukdir. Shu qatorda kamchiliklari yuk yuklash joyi kam qilingan. Yoqilgi sarfi qurol kopligi bois kop ishlatadi.',
      'description_short' => 'Yoq qiluvchilar (Unichtojitel-UNIK) - harbiy kemalar orasida qirol hisoblanadi.',
    ),

    SHIP_DEATH_STAR => array(
      'description' => 'Yulduzli Olim (Zvezda Smerti) istalgan turdagi kemalarni yoq qila oluvchi gravitatsion pushkalar bilan taminlangan. Hattoki OY ni ham. Ularni ishlatish uchun katta miqdorda energiya kerak boladi. Kemaga alohida energiya ishlab chiqaruvchi tehnologiyalar ornatilgan.',
      'description_short' => 'Yulduzli Olim (Zvezda Smerti) istalgan turdagi kemalarni yoq qila oluvchi gravitatsion pushkalar bilan taminlangan. Hattoki OY ni ham.',
    ),

    SHIP_BATTLESHIP => array(
      'description' => 'Lineyniy kreyserlar kuchli tehnologiya bilan yaratilgan kemadir. Ular kuchli nurli qurol bilan taminlangan. U hajmi kichik bolishiga qaramasdan kuchli hujum qila oladi. Uning yuk honasi juda kichik, lekin gipertranstva oqibatida yoqilgini juda kam sarf etadi.',
      'description_short' => 'Lineyner kreyserlar dushman flotini yoq qilish uchun mahsus ishlangan..',
    ),

    SHIP_SUPERNOVA => array(
      'description' => 'Sizning ohirgi ochgan kemangiz budir va u zorlarning zoridir!',
      'description_short' => 'Kreyserlar avlodidan &quot;Sverhnoviy&quot; - nafaqat Imperiyadagi balki ushbu oyindagi nag kuchli va ng qimmat kemadir. Unga qarshi hech bir kema bas kela olmaydi. U ota kuchli himoya va ota kuchli qurol bilan taminlangan. Birgina shunday kema ortacha flotni yoq qila oladi.',
    ),

    UNIT_DEF_TURRET_MISSILE => array(
      'description' => 'Paketa ornatilmasi - Oddiy va arzon himoya quroli. Uni kuchaytirishning iloji ham yoq. U ota kam sarf qiladi, shuning uchun u kichik flotlargagina bas kladi. Jangdagi maglubiyatdan song tiklanish darajasi 70% ni tahkil qiladi.',
      'description_short' => 'Paketa ornatilmasi - Oddiy va arzon himoya quroli.',
    ),

    UNIT_DEF_TURRET_LASER_SMALL => array(
      'description_short' => 'Yengil Nurli himoyalanuvchi qurol yordamida sayyoraga yaqinlashgan kuchik flotni bemalol yoq qila olasiz.',
      'description' => 'Nurli himoyalanuvchi qurol yordamida sayyoraga yaqinlashgan kuchik flotni bemalol yoq qila olasiz. Uning narhi ham arzon. Jangdagi maglubiyatdan song tiklanish darajasi 70% ni tahkil qiladi.',
    ),

    UNIT_DEF_TURRET_LASER_BIG => array(
      'description' => 'Ogir Nurli qurol yengil qurolning ajdodi bolib uning mukammallashtirilgan qismidir. U yangi materiallardan tashkil topgan bolib zirhi ham kuchaytirilgan. Shuning uchun bu himoya quroli kop energiya sarflaydi. Jangdagi maglubiyatdan song tiklanish darajasi 70% ni tahkil qiladi.',
      'description_short' => 'Ogir Nurli qurol yengil qurolning ajdodi bolib uning mukammallashtirilgan qismidir.',
    ),

    UNIT_DEF_TURRET_GAUSS => array(
      'description_short' => 'Gaus pushkasi ota kuchli energiya sarf qilib kuchli qurol bilan taminlangan.',
      'description' => 'Ancha yillardan beri nurli quroldan foydalanishgan jangchilar endi yangi davrga otishdi, organuvchilar yangi qurol kashf etishdi. U juda tez va aniq otadi. Ovozidan bolsa yer titraydi. Hattoki zamonaviy zirh va qalqan bu quroldan barbod bolishi mumkin. Jangdagi maglubiyatdan song tiklanish darajasi 70% ni tahkil qiladi.',
    ),

    UNIT_DEF_TURRET_ION => array(
      'description_short' => 'Ionoviy himoyalanuvchi qurol Ionoviy tolqin hosi qilib dushman flotini karaht holga olib keladi.',
      'description' => 'Ionoviy himoyalanuvchi qurol Ionoviy tolqin hosi qilib dushman flotini karaht holga olib keladi. Bu quroldan foydalanganingizda dushman floti elektronikasiga hamda qalqoniga yahshigina zarba bera olasiz. Uni koproq oyga quring. Jangdagi maglubiyatdan song tiklanish darajasi 70% ni tahkil qiladi.',
    ),

    UNIT_DEF_TURRET_PLASMA => array(
      'description' => 'Nurli qurol tehnologiyasining songgi avlodi bu Plazma qurolidir. U ota kuchli bolib uniklar bilan tenglasha oladi. Jangdagi maglubiyatdan song tiklanish darajasi 70% ni tahkil qiladi.',
      'description_short' => 'Sayyora himoyasi uchun qollaniladigan qurollarning ohirgisi Plazmadir.',
    ),

    UNIT_DEF_SHIELD_SMALL => array(
      'description' => 'Kichik Qalqon sayyorani dushman hujumidan himoyalaydi.',
      'description_short' => 'Kichik Qalqon sayyorani dushman hujumidan himoyalaydi.',
    ),

    UNIT_DEF_SHIELD_BIG => array(
      'description' => 'Kichik qalqonning keyingi avlodi Katta qalqon yaratildi. U hujumdan himoya uchun yanayam kuchaytirildi. Shunga yarasha energiya sarflaydi',
      'description_short' => 'Kichik qalqonning keyingi avlodi Katta qalqon yaratildi. U hujumdan himoya uchun yanayam kuchaytirildi. Shunga yarasha energiya sarflaydi.',
    ),

    UNIT_DEF_SHIELD_PLANET => array(
      'description' => 'Barcha sayyoralar uchun eng zor himoya',
      'description_short' => 'Barcha sayyoralar uchun eng zor himoya',
    ),

    UNIT_DEF_MISSILE_INTERCEPTOR => array(
      'description' => 'Raketa tutuvchilar - sayyoralararo raketalardan sayyorangizni himoya qiluvchi quroldir. Birgina raketa ushlovchi bitta sayyoralararo reketani tohtatib qoladi.',
      'description_short' => 'Raketa tutuvchilar - sayyoralararo raketalardan sayyorangizni himoya qiluvchi quroldir.',
    ),

    UNIT_DEF_MISSILE_INTERPLANET => array(
      'description_short' => 'Sayyoralararo reketalar flot jonatmasdan dushman sayyorasi himoyasiga zarba bera olish imkoniga ega bolishadi',
      'description' => 'Sayyoralararo raketalar dushman himoyasini yoq qiladi. Agar biror himoyalanuvchi qurolni sayyoralararo raketa bilan ursangiz u qayta tiklanmaydi.',
    ),

    MRC_TECHNOLOGIST => array(
      'description' => 'Tehnolog - resurslarni qayta ishlash va uni yigishni tezlashtirish borasida ustadir. U ozining ozining kamandasi bilan metallurgiya, kimyo va energetikaga tasir otkaza oladi. Tafsiyamiz birinchi orinda tehnologdan koproq yollang. Shundagina tez rivojlana olasiz',
      'effect' => 'Metal, kristal qazib olish, deyteriya ishlab chiqarish,elektrni qayta ishlash borasidagi usta bu Tehnolog.',
    ),

    MRC_ENGINEER => array(
      'description' => 'Injiner - Imperiyaning zamonaviy quruvchisi. Uning DNK si ozgarib mutatsiya holatiga tushgan va unda ajoyib qobiliyat paydo bolgan. Birgina injinet butun boshli shaharni barpo eta oladi.',
      'effect' => 'Zavodlar va kemalarni qurish uning har darajasiga <br />+1 joy qoshilib boradi',
    ),

    MRC_FORTIFIER => array(
      'description' => 'Fortifikator - Harbiy injiner, oz ishining ustasi. Uning yordamida himoyalanuvchi qurollarni qurish vaqtini qisqartirish mumkin',
      'effect' => 'Himoyalanuvchi qurollar va raketalarning qurish tezligi har darajaga vaqtdan kamayib boradi va <br />+10% qalqon,zirh va hujum kuchioshib boradi va har darajaga <br />+1 himoyalanuvchi qurollar va raketalar qurish uchun joy qoshiladi',
    ),

    MRC_STOCKMAN => array(
      'description' => 'Kargo-usta Omborlarni sozlash boyicha kuchli mutahassisdir. Uning oqilona rejalari oqibatida resurslar saqlanuvchi omborlar kengayadi, ularning sigimi ham oshadi.',
      'effect' => 'Har darajasiga ombor kattaligi barobarida qoshilib boradi.',
    ),

    MRC_SPY => array(
      'description' => 'Josus - Imperiyaning sirli odami. Uning minglab yuzi, minglab ismi va minglab goyalari bor. U istagan joyiga berkina oladi va sayyora haqida malumotni toliq yetkazib beradi. Boldi agar uning yuzini kimdir korsa u oldi hisob.',
      'effect' => 'Har darajasi kuchayib boradi.',
    ),

    MRC_ACADEMIC => array(
      'description' => 'Akademiklar - Tehnokratlar Gildiyasi qatnashchilaridir. Ularning aql-idrok ombori sayyora rivojiga juda katta yordam beradi. Ular tehnologiya olamining ustalari.',
      'effect' => 'Har darajasiga tehnologiyalar darajasini oshirish vaqti ozayib boradi',
    ),

//    MRC_DESTRUCTOR => array(
//      'description' => 'Buzgunchi - rahmsiz zobit. U sayyoradagi tartibni qattiq tartibda boshqaradi.',
//      'effect' => 'Yulduzli olim kemasini ochishda yordam beradi',
//    ),

    MRC_ADMIRAL => array(
      'description' => 'Admiral - u harbiy veteran va ota aqlli strategdir. Ota kuchli jangda ham u ozini yoqotib qoymaydi va flotlarga buyruq bera oladi. Dono boshqaruvchi jangda unga toliq topshirsa ham boladi.',
      'effect' => 'Har darajasiga qalqon, zirh va hujum oshib boradi.',
    ),

    MRC_COORDINATOR => array(
      'description' => 'Koordinator - Flotni boshqarish borasida usta. Uning asosiy maqsadi flotni uzoqroq yashatish.',
      'effect' => 'Har darajasiga qoshimcha flot.',
    ),

    MRC_NAVIGATOR => array(
      'description' => 'Navigator - Flot uchish yonalishini aniq hisoblovchi daho. U orqali flot tezligini oshirsa ham boladi.',
      'effect' => 'Har darajasiga kemalar tezligi oshib boradi.',
    ),

//    MRC_ASSASIN => array(
//      'description' => 'Assasin - Imperatorning ishonchli odami, qotil. Uning qirralari faqatgini shu emas. oldirish bilan bir qatorda kreyserlar avlodidan "Sverhnoviy"ni chiqishida yordam beradi. Bu kemaning boshqaruv komputeri Assasinning DNK si bilan sozlangan. Shuning uchun bu kemani boshqara oladigan yagona odam u.',
//      'effect' => 'Kreyserlar avlodidan "Sverhnoviy" ni qurish imkoni yuzaga keladi',
//    ),

    MRC_EMPEROR => array(
      'description' => 'Imperator - Sizning shahsiy yordamchingiz va orinbosaringiz. Siz yoqligingizda Imperiyani nazorat qiladi.',
      'effect' => 'Imperiya hususiyatini ozgartira oladi',
    ),

    ART_LHC => array(
      'description' => 'BAK Tortishish kuchini olga suradi va organib chiqadi, katta miqdordagi siniqlar toplamidan oyni barpo etishga kirishadi. <br /><span class="warning">Diqqat! BAK ni ishlatish tufayli aniq oy paydo boladi deya olmaymiz!</span>',
      'effect' => 'Har million siniqlar toplamidan Oyni yarata oladi',
    ),

    ART_RCD_SMALL => array(
      'description' => 'Kichik Avtonom Klonlashtirilgan Kompleks (Qisqacha - AKK) tayyor konstruksiya va programma bilan ishlaydi. <br />Agar sayyorada biror vazifa berilgan bolsa, AKK ga kiradi.Akk yangi sektorlar yarata oladi, lekin uni oyga qaratib bolmaydi<br />Koloniya ozida 10 darajadagi Rudnik, Kristal, Deyteriya, 14 darajadagi Quyosh energiyasi va 4 darajadagi Robotlar fabrikasini istagan sayyoraga tashiy oladi.',
      'effect' => 'Flotlarga Sayyoragagi zavodlarni birdanigaboshqa sayyoraga tashish imkonini beradi.',
    ),

    ART_RCD_MEDIUM => array(
      'description' => 'Orta Avtonom Klonlashtirilgan Kompleks (Qisqacha - AKK) tayyor konstruksiya va programma bilan ishlaydi. <br />Agar sayyorada biror vazifa berilgan bolsa, AKK ga kiradi.Akk yangi sektorlar yarata oladi, lekin uni oyga qaratib bolmaydi<br />Koloniya ozida 15 darajadagi Rudnik, Kristal, Deyteriya, 20 darajadagi Quyosh energiyasi va 8 darajadagi Robotlar fabrikasini istagan sayyoraga tashiy oladi.',
      'effect' => 'Flotlarga Sayyoragagi zavodlarni birdanigaboshqa sayyoraga tashish imkonini beradi.',
    ),

    ART_RCD_LARGE => array(
      'description' => 'Katta Avtonom Klonlashtirilgan Kompleks (Qisqacha - AKK) tayyor konstruksiya va programma bilan ishlaydi. <br />Agar sayyorada biror vazifa berilgan bolsa, AKK ga kiradi.Akk yangi sektorlar yarata oladi, lekin uni oyga qaratib bolmaydi<br />Koloniya ozida 20 darajadagi Rudnik, Kristal, Deyteriya, 25 darajadagi Quyosh energiyasi, 10 darajadagi Robotlar fabrikasi va 1 darajadagi Nano fabrikani istagan sayyoraga tashiy oladi.',
      'effect' => 'Flotlarga Sayyoragagi zavodlarni birdanigaboshqa sayyoraga tashish imkonini beradi.',
    ),

    ART_HEURISTIC_CHIP => array(
      'description' => 'Эвристический чип - уникальный преинсталлированный набор программ, записанных на кристаллический носитель. Подключаясь к исследовательской сети, алгоритмы чипа способны проанализировать текущее состояние исследования и выдать новые эффективные эвристики, таким образом сокращая время исследования. Однажды активированный чип невозможно перенастроить на другое исследование. К сожалению, как и с любым другим кристаллическим чипом, декомпиляция "зашитой" программы принципиально невозможна, равно как и копирование сборщиками.',
      'effect' => 'Уменьшает длительность активного исследования на 1 час. Если времени исследования осталось менее часа - остаток не переходит на следующий слот в очереди',
    ),

    ART_NANO_BUILDER => array(
      'description' => 'Как известно, сборщики обычно не используются в строительстве крупных объектов типа зданий. Экономически целесообразней возводить строения методом традиционной "блочной сборки", когда отдельные стандартизированные детали производятся на роботизированных фабриках. Однако специализированные наносборщики оказываются эффективнее традиционных методов. Эти крошечные роботы собраны в преконфигурированные пакеты, каждый из которых обладает своим собственным роевым суб-ИИ. Анализируя текущее состояние возводимого здания, наностроители безошибочно находят узкие места и вычисляют наиболее эффективные пути ускорения строительства. Пакет является одноразовым и после использования больше непригоден к работе. Вдобавок инициированный пакет уже невозможно перенастроить на интеграцию с другой стройкой. Хотя сборщики и способны воспроизвести отдельно взяты наностроитель, но без управляющего кристалла такая реплика является не более чем масштабной моделью...',
      'effect' => 'Уменьшает длительность постройки здания на текущей планете на 1 час. Если времени строительства осталось менее часа - остаток не переходит на следующий слот в очереди',
    ),


    UNIT_PLAN_STRUC_MINE_FUSION  => array(
      'description' => '"Termoyadro Elektrostansiya" chizmasi" ',
      'effect' => 'Bu chizmani olsangiz "Termoyadro Elektrostansiya" qurish imkoniga ega bolasiz',
    ),

    UNIT_PLAN_SHIP_CARGO_SUPER  => array(
      'description' => '"Supertransport" kemasining chizmasi ',
      'effect' => 'Bu chizmani olsangiz "Supertransport" kemasini qurish imkoniga ega bolasiz',
    ),

    UNIT_PLAN_SHIP_CARGO_HYPER  => array(
      'description' => '"Gipertransport" kemasining chizmasi ',
      'effect' => 'Bu chizmani olsangiz "Gipertransport" kemasini qurish imkoniga ega bolasiz',
    ),

    UNIT_PLAN_SHIP_DEATH_STAR  => array(
      'description' => '"O`lim yulduzi" kemasining chizmasi ',
      'effect' => 'Bu chizmani olsangiz "Olim Yulduzi" ( ZS ) kemasini qurish imkoniga ega bolasiz',
    ),

    UNIT_PLAN_SHIP_SUPERNOVA  => array(
      'description' => 'Kreyserlar avlodidan "Sverhnoviy" chizmasi',
      'effect' => 'Bu chizmani olsangiz kreyserlar avlodidan "Sverhnoviy" ( SN ) kemasini qurish imkoniga ega bolasiz',
    ),

    UNIT_PLAN_DEF_SHIELD_PLANET  => array(
      'description' => '"Sayyora himoyasi" chizmasi ',
      'effect' => 'Bu chizmani olsangiz "Sayyora himoyasi" qurolini qurish imkoniga ega bolasiz',
    ),


    RES_METAL => array(
      'description' => 'Meral qazib olsih bilan sayyorani keng kolamda rivojlantirish mumkin. Uni qayta ishlan zavodlar, kemalar, himoyalanuvchi qurollar va boshqa narsalar ishlab chiqarishingiz mumkin.',
      'effect' => '',
    ),

    RES_CRYSTAL => array(
      'description' => 'Kristal - Qiyin termoplastik polimer bolib, bu oyinda muhim unsurlardan hisoblanadi. ESP - kristal qatlamini 300000 km/s gacha ozgartiradi va u yordamida binolar shakllanadi kemalar quriladi. Ular yordamida tezlik kuchaytirish imkoni vujudga keladi.',
      'effect' => '',
    ),

    RES_DEUTERIUM => array(
      'description' => 'Yoqilgi(Deyteriya), ogir vadarod bolib atom massasi bilan toyinilgan. U yordamida kemalar parvoz qiladi va zavodlar qurish imkoni vujudga keladi',
      'effect' => '',
    ),

    RES_ENERGY => array(
      'description' => 'Elektr energiya - Energiyaning umumiy korinishi bilib, Sayyora ish harakatini taminladi. Energiyani oshirish uchun Sayyoraga quyosh sputniklarini yoki elektrostansiyalar yoki termoyaderniy stansiya qurishingiz kerak boladi. U kemalar parvozi uchun ham juda zarur unsurdir.',
      'effect' => '',
    ),

    RES_DARK_MATTER => array(
      'description' => 'TM  - bu oyindagi asosiy unsurlardan biri bolib u har narsani boshqaradi va jangda golib kelishingizga yordam beradi.',
      'effect' => '',
    ),

)) + $lang['info'];

?>
