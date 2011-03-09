<?php
$lang['Version']     = 'Version';
$lang['Description'] = 'D&eacute;scription';
$lang['changelog']   = array(
//'<font color="lime">0.8a</font>' => '',

'0.8' => 'Infos (Chlorel)
- FIX: Skin sur nouvel installeur
- DIV: Travaux esthetique sur l\'ensemble des fichiers
- FIX: Oublie de modification d\'appel sur quelques functions nouvellement modifiees',

'0.7m' => 'Correction de bugs (Chlorel)
- ADD: Interface d\'activation de protection des plan&egrave;tes
- FIX: Les lunes vont a nouveau au bon joueur et pas a "un" joueur quand elles sont crees depuis l\'administration
- FIX: Overview Evenements de flottes (les personnelles pour le moment) utilisent a present le css (default.css)
- MOD: Adaption de diverses fonctions a l\'utilisation du css
- FIX: Chat interne (divers ajustements) (e-Zobar)',

'0.7k' => 'Correction de bugs (Chlorel)
- FIX: Retour de flotte en transport
- ADD: Protection des planetes d\'administration
- MOD: Liste des joueurs dans la section admin liens sur les ent&ecirc;tes pour tri
- MOD: Page g&eacute;n&eacute;rale section admin avec liens sur les ent&ecirc;tes pour tri
- FIX: Lors de l\'utilisation d\'un skin autre que celui d\'XNova, il s\'applique aussi en section admin
- FIX: Ajout du lune dans le panneau d\'administration (e-Zobar)
- ADD: Mode transf&egrave;re dans l\'installateur (e-Zobar)',

'0.7j' => 'Correction de bugs (Chlorel)
- FIX: On peut a nouveau retirer une construction de la queue de fabrication
- FIX: On peut a nouveau envoyer une flotte en transport entre deux planetes
- FIX: La liste des raccourcis dans la selection de la cible fonctionne a nouveau
- FIX: On ne peu plus detruire un batiment que l\'on ne possede pas
- ADD: Tout beau tout nouveau installeur (e-Zobar)
- FIX: Rarcellage de hieroglyphes (e-Zobar)',

'0.7i' => 'Correction de bugs (Chlorel)
- Suppression cheat +1
- Ajustement des dur&eacute;e de vols / consommation des flottes entre le code PHP et le code JAVA
- Tri des colonies par le joueur dans options
- Preparation du multiskin dans options
- Divers amenagements dans le code pour les Administrateurs (Liste de messages, Liste de Joueurs)
- Travaux sur le skin (e-Zobar)
- Travaux sur l\'installeur (e-Zobar)',

'0.7h' => 'Correction de bugs (Chlorel)
- Interface Officier refaite
- Ajout blocage des "refresh meta"
- Ajustement de divers Bugs
- Correction de divers textes (flousedid)
- Correction de defauts visuels (e-Zobar)',

'0.7g' => 'Correction diverses (Chlorel)
- Modification de l\'ordre du traitement de la liste de construction de batiments
- Mise en conformit&eacute; du code pour une seule commande "echo"
- Quelques modules de r&eacute;&eacute;crits
- Correction bug de d&eacute;doublement de flotte
- Mise &agrave; jour dynamique de la taille des silos, production des mines et de l\'&eacute;nergie
- Divers adaptations dans la section admin (e-Zobar)
- Modification lourde du style XNova (e-Zobar)',

'0.7f' => 'Informations et porte de saut: (Chlorel)
- Nouvelle page d\'information completement repens&eacute;e
- Nouvelle interface porte de saut int&eacute,gr&eacute;e a la page d\'information
- Nouvelle gestion de l\'affichage des rapid fire dans la page d\'information
- Multitude de correction faites par e-Zobar',

'0.7e' => 'Partout et nulle part : (Chlorel)
- Nouvelle page registration (mise au standard)
- Nouvelle page records (mise en conformit&eacute; avec le site)
- Modif kernel (y en a pas mal mais pas possible de toutes les expliquer l&agrave; et de toutes maniere pas
  grand monde ne serait capable de les comprendre',

'0.7d' => 'Partie admin : (e-Zobar)
- menage dans pas mal de modules
- alignement du menu au style de fonctionnement du site
- traduction complete de ce qui n\'etait pas encore en francais',

'0.7c' => 'Statistiques : (Chlorel)
- Suppression des appels base de donn&eacute;es de l\ancien systeme de Statistiques
- Bug Impossibilit&eacute; de fabriquer des defenses ou des elements de flotte n\'utilisant pas de metal
- Bug Comme certains petits rigolos s\'amusent a lancer des quantit&eacute;es enormes de vaisseau dans
  une meme ligne de la queue de construction vaisseau, nous en sommes arriv&eacute;s a limiter le nombre
  d\'element fabriquable par ligne donc maximum 1000 vaisseaux ou defenses a la fois !!
- Bug erreur lors de la selection planete par la combo
- Mise a jour de l\'installeur',

'0.7b' => 'Statistiques : (Chlorel)
- Reecriture de la page de Statistique (appell&eacute;e par l\'utilisateur)
- Les stat alliance s\'affichent !
- Ecriture du generateur admin des stats
- Separation des stats de l\'enregistrement utilisateur (les stats on leur propre base de donn&eacute;es)',

'0.7a' => 'Divers : (Chlorel)
- Bug Technologies (la duree de recherche apparait a nouveau quand on revient dans le laboratoire
- Bug Missiles (mis a plat de la port&eacute;e des missiles interplanetaires, et mise en place de la limite de fabrication par rapport a la taille du silo)
- Bug Port&eacute;e des phalange corrig&eacute; (on ne peut plus phalanger toute la galaxie)
- Bug Correction de la conssomation de deuterium quand on passe par le menu galaxie',

'0.7' => 'Building :
- Reecriture de la page
- Modularisation
- Correction bugs de statistiques
- Debugage de la liste de construction batiments
- Diverses retouches (Chlorel)
- Divers debug (au fil de l\'eau) (e-Zobar)
- Ajout de fonction sur la vue principale (Tom1991)',

'0.6b' => 'Divers :
- Correction & Ajouts de fonctions pour les officiers (Tom1991)
- Menage dans les scripts java inclus (Chlorel)
- Correction divers bug (Chlorel)
- Mise en place version 0.5 de la liste de construction batiments (Chlorel)',

'0.6a' => 'Graphisme :
- Ajout Skin XNova (e-Zobar)
- Correction d\'effets nefastes (e-Zobar)
- Ajout de bugs involotaires (Chlorel)',

'0.6' => 'Galaxy (suite): (by Chlorel)
- Modification et reecriture de flottenajax.php
- Modification des routine javascript et ajax pour permettre les modification dynamiques de la galaxie
- Corrections bug dans certains liens des popups
- Definition nouveau protocole d\'appel, dorenavant meme sur une lune, la galaxie s\'affiche a partir de la bonne position
- Correction des appels de recyclage
- Ajout module "Officier" (by Tom1991)',

'0.5' => 'Galaxy: (by Chlorel)
- Decoupage ancien module
- Modification systeme de generation des popup dans la vue de la galaxie
- Modularisation de la generation de page',

'0.4' => 'Overview: (by Chlorel)
- Mise en forme ancien module
- Gestion de l\'affichage des flotte personnelle 100%
- Modification affichage des lunes quand presentes
- Correction bug renommer les lunes (pour qu\'elles soient effectivement renomm&eacute;es)',

'0.3' => 'Gestion de flottes: (by Chlorel)
- Modification / modularisation / documentation de la boucle de gestion des vols 100%
- Modification Mission d\'espionnage 100%
- Modification Mission de Colonisation 100%
- Modification Mission Transport 100%
- Modification Mission Stationnement 100%
- Modification Mission Recyclage 100%',

'0.2' => 'Corrections
- Ajouts de la version 0.5 des Exploration (by Tom1991)
- Modification de la boucle de controle des flottes 10% (by Chlorel)',

'0.1' => 'Merge des version flotte:
- Mise en place de la strat&eacute;gie de developpement
- Mise en place de nouvelles pages de gestion de flotte',

'0.0' => 'Version de depart:
- Base du repack a Tom1991',
);

?>