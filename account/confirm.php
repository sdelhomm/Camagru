<?php

include_once("./ft_account.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/config/setup.php");
if (session_status() == PHP_SESSION_NONE)
	session_start();

if (isset($_GET["login"]) && isset($_GET["uid"]) && ft_check_confirmation($_GET["login"], $_GET["uid"], $db) == 1)
{
	$req = $db->prepare("UPDATE `users` SET `confirm` = 1 WHERE `login` = ?");
	$req->execute(array($_GET["login"]));
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Confirmation</title>
</head>
<body>
	<?php
	if (ft_is_logged($db) == TRUE)
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner_logged.php");}
	else
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner.php");}
	?>
	<div class="gallery">
		<div class="text-box" >
				<span id="box-title" >Bienvenue sur Camagru !</span><br/><br/>
				<span id="box-text">Votre compte a bien été validé, vous pouvez donc vous connecter en cliquant <a href="/Camagru/account/login.php" >ici</a> et commencer vos premiers montages.<br/><br/>
				Merci et amusez vous bien !</span>
		</div>
	</div>
	<?php include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/footer.php"); ?>
</body>
</html>
<?php
}
else if (isset($_GET["login"]) && isset($_GET["uid"]) && ft_check_confirmation($_GET["login"], $_GET["uid"], $db) == 2)
{
	header("Location: /Camagru/index.php");
}
else
{
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Erreur</title>
</head>
<body>
	<?php
	if (ft_is_logged($db) == TRUE)
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner_logged.php");}
	else
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner.php");}
	?>
	<div class="gallery" >
		<div class="text-box" >
				<span id="box-title" >Erreur !</span><br/><br/>
				<span id="box-text">Le lien de confirmation entré est invalide !<br/><br/>
				Merci de réessayer avec un lien valide.</span>
		</div>
	</div>
	<?php include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/footer.php"); ?>
</body>
</html>
<?php
}

?>