update game_users set
`rpg_geologue`=0, `rpg_amiral`=0,`rpg_ingenieur`=0,`rpg_technocrate`=0,`rpg_espion`=0,`rpg_constructeur`=0,`rpg_scientifique`=0,`rpg_commandant`=0,`rpg_stockeur`=0,`rpg_defenseur`=0,`rpg_destructeur`=0,`rpg_general`=0,`rpg_bunker`=0,`rpg_raideur`=0,`rpg_empereur`=0,rpg_points = 
(`rpg_geologue`*3 +`rpg_amiral`*3 +`rpg_ingenieur`*3 +`rpg_technocrate`*3 +`rpg_espion`*3 +`rpg_constructeur`*3 +`rpg_scientifique`*3 +`rpg_commandant`*3 +`rpg_stockeur`*3 +`rpg_defenseur`*3 +`rpg_destructeur`*3 +`rpg_general`*3 +`rpg_bunker`*3 +`rpg_raideur`*3 +`rpg_empereur`*3) 
+ rpg_points - lvl_raid - lvl_minier,
`lvl_minier`=0,
`lvl_raid`=0
-- where id=2
;
update game_users set rpg_points=0 where rpg_points<0;