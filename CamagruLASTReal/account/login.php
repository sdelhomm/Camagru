<?php

include ("./ft_account.php");
include($_SERVER["DOCUMENT_ROOT"]."/Camagru/config/setup.php");
if (session_status() == PHP_SESSION_NONE)
	session_start();

if (isset($_POST["submit"]) && $_POST["submit"] == "Se connecter")
{
	if (isset($_POST["login"]) && isset($_POST["passwd"]))
	{
		if (($valid = ft_check_user($_POST["login"], $_POST["passwd"], $db)) == 2)
		{
			$_SESSION["user-id"] = ft_get_userid($_POST["login"], $db);
			header("Location: /Camagru/index.php");
			exit;
		}
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="/Camagru/style/style.css">
</head>
<body>
	<?php include("../content/banner.php"); ?>
	<div class="form" >
		<?php
		if (isset($valid) && $valid === 0)
			echo "<span class=\"invalid-info\" >Pseudo/Mot de passe invalide<br/></span>";
		else if (isset($valid) && $valid === 1)
			echo "<span class=\"invalid-info\" >Merci de valider votre email<br/></span>";
		?>
		<form action="./login.php" method="POST" >
			<input type="text" name="login" placeholder="Pseudo" required autofocus /> <!-- AJOUTER LIMITES DE CHAR -->
			<br />
			<input type="password" name="passwd" placeholder="Mot de passe" required />
			<br />
			<input type="submit" name="submit" value="Se connecter" />
			<br />
			<a href="/Camagru/account/register.php" >Pas encore inscrit ?</a>
		</form>
	</div>
</body>
</html>