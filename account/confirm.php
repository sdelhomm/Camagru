<?php

include ("./ft_account.php");
include($_SERVER["DOCUMENT_ROOT"]."/Camagru/config/setup.php");

if (isset($_GET["login"]) && isset($_GET["uid"]) && ft_check_confirmation($_GET["login"], $_GET["uid"], $db) == 1)
{
	$req = $db->prepare("UPDATE users SET confirm = 1 WHERE login = ?");
	$req->execute(array($_GET["login"]));
?>
<!DOCTYPE html>
<html>
<head>
	<title>Confirmation</title>
</head>
<body>
	<?php include("../content/banner.php"); ?>
	<div class="text-box" >
			<span id="box-title" >Bienvenue sur Camagru !</span><br/><br/>
			<span id="box-text">Votre compte a bien été validé, vous pouvez donc vous connecter en cliquant <a href="/Camagru/account/login.php" >ici</a> et commencer vos premiers montages.<br/><br/>
			Merci et amusez vous bien !</span>
	</div>
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
	<title></title>
</head>
<body>
	<?php include("../content/banner.php"); ?>
	<div class="text-box" >
			<span id="box-title" >Erreur !</span><br/><br/>
			<span id="box-text">Le lien de confirmation entré est invalide !<br/><br/>
			Merci de réessayer avec un lien valide.</span>
	</div>
</body>
</html>
<?php
}

?>