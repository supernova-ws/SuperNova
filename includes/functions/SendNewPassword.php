<?php

/**
 * SendNewPassword.php
 *
 * @version 1.0
 * @copyright 2008 by Tom1991 for XNova
 */



  function sendnewpassword($mail){

  	$ExistMail = doquery("SELECT `email` FROM {{table}} WHERE `email` = '". $mail ."' LIMIT 1;", 'users', true);

    if (empty($ExistMail['email']))	{
	   message('L\'adresse n\'existe pas !','Erreur');
	}

	else{
	//Caractere qui seront contenus dans le nouveau mot de passe
    $Caracters="aazertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN1234567890";

    $Count=strlen($Caracters);

    $NewPass="";
    $Taille=6;


    srand((double)microtime()*1000000);

     for($i=0;$i<$Taille;$i++){

      $CaracterBoucle=rand(0,$Count-1);

      $NewPass=$NewPass.substr($Caracters,$CaracterBoucle,1);
      }

    //Et un nouveau mot de passe tout chaud ^^

    //On va maintenant l'envoyer au destinataire
    $Title = "XNova : Nouveau mot de passe";
    $Body = "Voici votre nouveau mot de passe : ";
    $Body .= $NewPass;

    mail($mail,$Title,$Body);

    //Email envoyé, maintenant place au changement dans la BDD

    $NewPassSql = md5($NewPass);

    $QryPassChange = "UPDATE {{table}} SET ";
    $QryPassChange .= "`password` ='". $NewPassSql ."' ";
    $QryPassChange .= "WHERE `email`='". $mail ."' LIMIT 1;";

    doquery( $QryPassChange, 'users');

    }
}
?>