<? // Zmiana Jêzyka By ShadoV
$expiretime = time()+31536000;
$Nazwa = "Langs";

        if($_GET['Langs'] == "pl"){
			setcookie($Nazwa, "pl", $expiretime, "/", "", 0);
                header("Location: index.php");}
        elseif($_GET['Langs'] == "fr"){
			setcookie($Nazwa, "fr", $expiretime, "/", "", 0);
                header("Location: index.php");}
		elseif($_GET['Langs'] == "es"){
			setcookie($Nazwa, "es", $expiretime, "/", "", 0);
                header("Location: index.php");}
		elseif($_GET['Langs'] == "de"){
			setcookie($Nazwa, "de", $expiretime, "/", "", 0);
                header("Location: index.php");}
		elseif($_GET['Langs'] == "en"){
			setcookie($Nazwa, "en", $expiretime, "/", "", 0);
                header("Location: index.php");}
		elseif($_GET['Langs'] == "it"){
			setcookie($Nazwa, "it", $expiretime, "/", "", 0);
                header("Location: index.php");}
		else{header("Location: index.php");}
?>