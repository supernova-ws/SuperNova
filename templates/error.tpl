{include file="head.tpl"}
<center>
<center><hr>- <a onMouseOver="overlib('<center> Strona Glowna!</center>', FGCOLOR, 'black', BGCOLOR, '#FAEBD7', TEXTCOLOR, '#FFFFFF', STATUS, 'Dymek zwykly')" onMouseOut="nd();" href="http://gamextra.sytes.net/"><b><font class=test>Strona G³ówna</font></a></b> -<hr></center><br>
<h3><font class=admin>B³êdny e-mail</font></h3>
<br /><br />
{if $Action != "haslo"}
Najprawdopodobniej wpisa³e¶ z³y e-mail, lub ¿aden z Usery na ogamek'u, nie posiada takiego e-maila. Upewnij siê, czy nie pomyli³e¶ siê wpisuj±c maila, oraz czy napewno jeste¶ zarejestrowany w grze, a adres, który poda³e¶ przy rejestracji, zgadza siê z tym, który wpisa³e¶ w poni¿szej tabelce! Je¶li wszystko siê zgadza, a nadal wystêpuje ten sam b³±d, skontaktuj siê z <a href="gg:4396785">Administratorem</a> stony. <br>Je¶li jednak uzna³e¶ ¿e pope³ni³e¶ gdzie¶ b³±d, wpisz swój e-mail ponownie.</td></tr><tr><td align="center" colspan="2" align="center" cellSpacing=0 cellPadding=-00 width=700 bgColor=#1c1c1c border=0>
	<form method="post" action="lost.php?step=lostpasswd&amp;action=haslo">
	<table bgColor=#1c1c1c border=0>
	<tr><TD><font class=test>E-Mail:</font></td><td><input type="text" name="email" /></td></tr>
	<tr><td><input type="submit" value="Wy¶lij" /></td></tr>
	</table>
	</form>
	</td></tr>
	<tr><TD cellSpacing=0 cellPadding=-00 width=156 bgColor=#1c1c1c>
{/if}
</center>
{if $Action == "haslo"}
    </td></tr></table><TABLE cellSpacing=0 cellPadding=0 width=700 bgColor=#1c1c1c border=0>
        <TR>
          <TD cellSpacing=0 cellPadding=-00 width=700 bgColor=#1c1c1c border=0><center>Mail z has³em zosta³ wys³any na podany adres e-mail.</tr></td>
{/if}
<TABLE cellSpacing=0 cellPadding=-00 width=700 bgColor=#1c1c1c border=0>
<tr><td>&nbsp;</td></tr>
</table>
{include file="foot1.tpl"}