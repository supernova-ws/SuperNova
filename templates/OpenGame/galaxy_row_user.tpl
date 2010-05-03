
    <a style="cursor: pointer;" onmouseover="
this.T_WIDTH=200;
this.T_OFFSETX=-20;
this.T_OFFSETY=-30;
this.T_STICKY=true;
this.T_TEMP={T_TEMP};
return escape('<table width=\'190\'><tr><td class=\'c\' colspan=\'2\'>Jugador {username}</td></tr><tr><td><a href=\'messages.php?mode=write&id={user_id}\'>Escribir mensaje</a></td></tr><tr><td><a href=\'buddy.php?a=2&u={user_id}\'>Solicitud de compañeros</a></td></tr></table>');">
      <span class="noob">{username}</span>
    </a>
	 $Result .= "<img src=".    $GalaxyRowUser['avatar'] ." height=45 width=45>"; 
//        $Result .= $GalaxyRowPlanet["name"]; 
        $Result .= "</a>"; 
