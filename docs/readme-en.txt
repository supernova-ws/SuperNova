New version 31a9 contains locale editor.  It's  accessible  via  "Localization"
menu item in admin interface
1. Choosing "Localization" menu item will open page to select "domain" to edit.
Domain is a set of string which belongs to one aspect of the  game.  Domain  is
equivalent for language file with respect name
2. After selecting domain opens page for  editing  locale  string.  Opening  of
large files and/or slow connection can take pretty much time. Please be patient
3. After editing locale  string  and  confirming  it  editor  will  make  files
"<domain name>.mo.new" in each language folder
4. When starting locale  string  editor  files  with  ".mo.new"  extension  has
priority over ".mo" files. I.e. if in any folder exists  both  types  of  files
editor will load values from ".mo.new". This is made to make easier editing  of
large files
5. If you want to use  new  locale  strings  you  should  change  extension  of
".mo.new" file to ".mo". Usually it will effectivly  overwrite  current  locale
file - so you should make backup before this operation
6. WARNING!!! You should be very carefull when changing old locale  files  with
newly generated ones! Editor will resolve constant's IDs to their real  values,
will not honor comments  nor  empty  lines,  will  ignore  extra  PHP  code  in
lang-files - including substitution of values in  infos.mo!  As  result  simple
replacing old locale file with new one can somewhat cripple localization files!
For some files it may be necessary to make manual merge between old and new one
files!
7. Following domains contains additional PHP-code and  REQUIRE  manual  merging
after editing language files: fleet, infos, login, market,  messages,  options,
system
8. Following domains using constants: alliance,  tech,  quest.  Manual  editing
recommended to maintain compatibility with possible  future  constants  changes
but currently didn't required
