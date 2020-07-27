# Project "SuperNova.WS"
## Disclaimer
WARNING! The project is in alpha stage!  Currently,  he  is  not  designed  for
production-use! Code is provided "as-is". You use at your own risk! The  author
is not liable for material, moral, karmic, spiritual,  and  any  other  damages
caused you to use, non-, the very existence of this code or any other way.

WARNING! Although Supernova is a oGame clone, it is not like offical  game!  Ie
many aspects of the game differ from the official oGame as well as from the RR.
Slider changed, that would fit my understanding of an  interesting  game.  Take
With this in mind when deciding - to install this engine yourself or not.

WARNING! Status of "Supernova" - the alpha  version.  Practically,  this  means
that the next update can completely change a certain aspect of the game.

Approximate engine development plan contained in the file /docs/changelog.todo

Code licensed under the GNU  GENERAL  PUBLIC  LICENSE  Version  2,  June  1991.
License itself is in the file docs/license.txt distribution.


### Advance notice of the need for training
These instructions assume the ability to  customize  and  use  third-party  Web
hosting,  familiarity  with  MySQL  and  PHP,  access  to   the   tools   MySQL
administration and hosting. If you do not have the  experience  of  independent
set of sites - this distribution you will not want

WARNING! I can not test all possible combinations and versions of MySQL & PHP &
XCache & Web Servers! This means that for  some  combinations  and  environment
settings engine can not work.  It  is  this  requires  skills  setting  up  and
configuring web servers.


## Requirements
- MySQL 5.x - STRICT_TRANS_TABLES mode must be disabled
- PHP >= 5.5
- Web-server
- XCache> = 1.2.h - optional, but very, very desirable. Without XCache not
                  will work some chips and increase significantly the load
                  to MySQL.


SuperNova being developed on Windows with WAMP Server 3.0.8. WAMP Configuration:
-  MySQL 5.7.14
-  Apache 2.4.23
-  PHP 5.6.25 + xCache 1.3.2
Live servers works under CentOS + lighttpd + xCache

On localhost SuperNova ALWAYS works with display_errors=1


## Location Engine
The engine can run from anywhere on the site, including subdirectories.  Ie  it
may be installed at the following addresses
*  http://domain.com
*  http://my.very.deep.domain.on.crappy.hoster.org
*  http://hosting.com/ogame/
and so on.


## Permissions of the Web server
In general, the engine can run from the web server with access rights "only  to
reading. "However, for the correct operation of individual  subsystems  account
Web server must be allowed to write in separate files/folders. Below list  of
subsystems and associated files/folders
* Cache subsystem template - directory /cache. Without these templates  every
    time will be rendered anew
* Avatar subsystem - directory /images/avatar
* For the subsystem warning about attempts to hack  the  web  server  account
    should be allowed to write to the disk file/badqrys.txt.  Without  this
    "Bad" requests will not be saved


## Custom modifications
If you use your own skin, template, or localization, does not change
BUILT-IN NOW! Subsequent changes in the repository can overwrite your
add! Make a copy of a skin/template/location under a different name and have
with a copy to have fun. Change the skin/template/default location can be at
configuration page server.


## Installation, configuration database
WARNING! Mode STRICT_TRANS_TABLES (variable sql_mode) must be disabled!

WARNING! All tables are stored in CH InnoDB! By default, MySQL does not
Configured for normal operation with the bases InnoDB! Default settings will suffice MySQL
to work up to 50 users online. If you are planning more
simultaneous players, then you need to configure MySQL to suit your
server.

As an example, cite your MySQL server configuration (2GB memory, server
allocated solely to heart failure and supportive forum)
```
innodb_additional_mem_pool_size 20971520
innodb_buffer_pool_size 536870912
innodb_flush_log_at_trx_commit 0
innodb_flush_method O_DIRECT
innodb_log_buffer_size 8388608
```
Especially pay attention to the variable innodb_flush_log_at_trx_commit!

It is also to facilitate disaster recovery, it is recommended to keep each
InnoDB table in a separate file. To do this, add a configuration file
Directive
```
innodb_file_per_table
```

## Installing, configuring a Web server
SN is designed for use keshera op-codes xCache. Although the engine can
work without it, this mode is not regular. No keshera op-codes
Some features of the engine will be blocked, as well as increase the load
to MySQL.

Like any kesher op-codes, xCache require special configuration Web server.
How to configure a web server to work with xCache can be found on the Internet
(Www.google.com) and on the home page xCache (http://xcache.lighttpd.net/)


## The installation, base
 1. Create a database "supernova" UTF-8
 2. Create a MySQL user "supernova_user" and give it all right to
    base "supernova"
 3. Load the database file/docs/supernova.sql
 4. Copy the file/docs/config.php.sample the root of the supernova and
    rename it to config.php
 5. In the file/config.php on line
    "Pass" => "MYSQL_PASSWORD",// ??Password to access the MySQL server.
    MYSQL_PASSWORD to replace the password database
 6. In the file/config.php on line
    "Secretword" => "SUPERNOVA",// ??keyword to create Cookies
    SUPERNOVA replace any sequence of characters
 7. Load distribution of the supernova in the root directory of the Web server
 8. Turn game on in DB: in table `sn_config` change value of variable
    `game_disable` to 0
 9. Go into the game with admin rights. The default admin account:
    Username: 'admin' (without the quotes)
    Password: 'admin' (without the quotes)
10. MUST change the administrator password on the page/options.php
11. Set up the game on the page/admin/settings.php
12. Supernova is ready to launch!
13. If something went wrong - see below section "Troubleshooting install/update"

WARNING! Scrap with team members (auth_level> 0) do not fall out!  It's  not  a
bug, it's made specifically to enhance the security  of  the  server!  For  the
game, and Administration need to  use  different  accounts!  Also  server  team
members do  not  participate  in  Records  calculation  (but  do  in  statistic
calculation)


## Installation, an advanced version
Settings for database access are in the file /config.php
Game Settings  are  in  the  table  <db_prefix>  config.  Appointment  Settings
intuitive names of variables

Just some game settings are stored in file/includes/constants.php

All changes - at your own risk

WARNING! If you have changed the table prefix ('sn_' by default), then the same
need   to   change   the   names   of   the   tables   before    pouring    the
file/docs/supernova.sql!

### Multiple universes for a single server
CH supports multiple universes on a single  server.  Different  universes  must
have distinct prefixes within the same space of variables xCache


## Short educational program on the GIT
Create a local copy of the supernova in the current directory (do not forget to
point at end!)
```
  git clone git://github.com/supernova-ws/SuperNova.git .
```
In the current directory will be the latest copy  of  the  supernova  from  the
branch master.

Now you want to copy the contents to the root server. In general, encouraged to
do so once on the server - to avoid problems with copy (this  was  already  the
case). But not all hosting companies are allowed to run program locally, so you
can first make a copy on your local drive.

In fact, have nothing else:) However, if you do modifications engine useful  to
you sleduyushaya team. Roll back all changes made to Local copy:
```
  git reset -=hard
```

## Upgrading to the latest version
0. WARNING! UPDATING TO MAKE A BACKUP DATABASE AND FILE ENGINE  THAT  would  be
   possible to roll back if the update fails!
1. Log in to the game through an account with administrator privileges
2. WARNING! Before you upgrade a server MUST be stopped. Done this  way:  under
   the Administrator in the left menu appears select "Administrator" - click on
   it. In the admin menu, select "Settings," put check  "Turn  off  the  game",
   press "Save" button at the bottom of the page. Only  then  can  perform  the
   update - otherwise the result may be far from expected.
3. If you have template caching enabled, you must delete all the files in
   directory/cache
4. Now update the files to the engine
   1. If you put the game out of GIT-repository, the server at the root
        directory, run game
        ```
          git pull
        ```
   2. In any other case - to upload an updated version of the engine
5. Wait for the NEW version of the engine is  on  the  server!  An  attempt  to
   update in the  process  of  copying  files  or  downloading  them  from  the
   repository can GIT lead to unpredictable results
6. Switch to the browser in which you are logged in as administrator and select
   "Browse." Wait for the page - this time updates the database
7. And finally, after all this is a game you can again turn (Admin ->  Options,
   uncheck "Turn off the game" and keep changes).
8. If something went wrong - see below section "Troubleshooting install/update"

### Upgrade from RR
Automatic upgrade from bases in the development of RR. Partially  upgrade  done
automatically launch the file update.php

In heart failure with respect to RR changed location banner. Earlier  reference
was
```
 /Scripts/createbanner.php
```
New Links
```
 /Banner.php
```

Use  the  tools  the  web  server  (mod_rewrite)  to  redirect   requests.   In
Specifically, the rule for lighttpd is as follows:

```
# Redirects old-style banners to new one
server.modules + = ("mod_rewrite")
url.rewrite-once = (
  "^/Scripts/createbanner.php (.*)" =>"/banner.php $ 1 "
)
```

## Troubleshooting install/update

### Message "Game is not configured yet"
If you see this message, then you are currently in the game under a
non-administrator account. In order to change your account:
1. Log out by opening the link `(URL of your server)/logout.php`
   1. If you were logged into the game in Impersonator mode, the game will
     immediately redirect you to the Administrator section.
   2. Otherwise, the game will redirect you to the login page. Enter login
     and Administrator password
   3. The game will redirect you to the Administrator section.
2. Configure the game as you wish.
3. On the server settings page /admin/settings.php, the "Status" tab, change
   the state games and save changes

### Other issues
It is impossible to predict all possible combinations of settings,  PHP,  MySQL
and Web server. CH focuses on the "default settings". Therefore, in some cases,
possible failures in the upgrade procedure.
 
An algorithm for automatic Pack  is
designed for a single run. However, in some configurations during the  upgrade,
errors occur. Some of them - critical, and some can  fix  restarting  procedure
(with or without editing database). 

Run the update again be using select "Force
update" menu "Utilities" page of the Administrator. Please pay attention! Using
the forced update may lead to damage to the database (if the update itself over
the previously successful)! Forced update  should  be  used  only  if  standard
update procedure was not successful!  If  it  did  not  help  -  should  go  to
Diagnostiek fault

## Fault diagnosis engine
The latest and most current version of the engine (with  the  most  recent  and
relevant bugs) can be downloaded at this link:
```
https://github.com/supernova-ws/SuperNova/zipball/trunk
```
To pay attention to the file name is not necessary.

! WARNING! Diagnosis should be performed on a separate database and separate copy
engine! DO NOT LIVE IN diagnosis SERVER!

The standard sequence of steps in the diagnosis of this:
1. Set in an empty database clean dump a database from an archive. Problems in this
   stage, say the following:
   1. The most likely - an error in setting up the MySQL server
   2. Errors in the dump database. Perhaps you are in a time of renewal dump.
      "Moment" here relative concept - it can take several days
2. Put on a clean server and a clean database, obtained at the last step, the engine
   from the archive. Try to run. Problems at this stage, say the following:
   1. Errors in the configuration engine. Check the file/config.php
   2. Errors in the HTTP-server settings (especially if the first run
   engine)
   3 Errors in avtoapdeytere
   4. The engine is in the process of writing, but you got to "moment" commit
3. Replace the clean database dump of your working database (do not forget if you want to AGAIN
   change the configuration - prefix tables there, logins/passwords, etc.).
   Problems at this stage, say the following:
   1. Errors in the configuration engine. Check the file/config.php
   2. Errors in avtoapdeytere
   3. Errors in the database itself. Then a deal themselves, or pay for my work
4. If there are no problems - then something is not specifically as a live server. Here we have
   have to understand more - either by themselves or pay for my work

Before each stage ALWAYS need to restart the web server and MySQL - otherwise
can be used by the old settings from the previous version.
Only a true and accurate diagnostic procedure to follow will help to effectively
identify problems in the SN on each individual server.

## URLs
The main project site: http://supernova.ws

### Forums
Forum Project: http://forum.supernova.ws

Support Forum: http://forum.supernova.ws/viewforum.php?f=73

Forum for bug reports: http://forum.supernova.ws/viewforum.php?f=65

### Supernova on github
Project page: http://github.com/supernova-ws/SuperNova

Download freshest release: https://github.com/supernova-ws/SuperNova/zipball/master

Repository: git://github.com/supernova-ws/SuperNova.git

### Supernova on sourceforge
Project page: http://sourceforge.net/projects/supernova-ws/

Download Engine: http://sourceforge.net/projects/supernova-ws/files/

Repository: git://supernova-ws.git.sourceforge.net/gitroot/supernova-ws/supernova-ws


# Donations
You can help by sending a WebMoney to purse:
  WMZ (WM-USD) Z409323360409
  WMR (WM-RUB) R961266352219
  WMU (WM-UAH) U726314912308

If you use WebMoney to buy various electronic products, you
can a) buy the usual electronic goods at a good price b) help the project.
http://gorlum.plati.ru - recharge cell, Skype, WoW - the game and the TC and other
electrical goods for WM. Each purchase made from this link will bring
I a small commission.

Remember! Nothing strengthens the belief in the usefulness to work as a donation!

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
Updated: 2020-07-27 V45d0
