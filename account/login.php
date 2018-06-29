<?php

include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/account/ft_account.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/config/setup.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/gallery/theme.php");
if (session_status() == PHP_SESSION_NONE)
	session_start();

if (isset($_POST["submit"]) && $_POST["submit"] == "Se connecter")
{
	if (isset($_POST["login"]) && isset($_POST["passwd"]))
	{
		$_POST["login"] = substr($_POST["login"], 0, 64);
		$_POST["passwd"] = substr($_POST["passwd"], 0, 64);
		if (($valid = ft_check_user($_POST["login"], $_POST["passwd"], $db)) == 2)
		{
			$_SESSION["user-id"] = ft_get_userid($_POST["login"], $db);
			header("Location: /Camagru/index.php");
			exit;
		}
	}
}

if (!ft_is_logged($db))
{
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="/Camagru/style/style.css">
	<style type="text/css">
		.form > form > input[name=submit]:not(.disabledSubmit)
		{
			background-image: linear-gradient(45deg, <?php if (check_theme()) echo $_COOKIE["themeColor1"].", ".$_COOKIE["themeColor2"]; else echo "#fd746c, #ff9068"; ?>);
			border-color: <?php if (check_theme()) echo $_COOKIE["themeColor1"]; else echo "#fd746c"; ?>;
		}
		.form > form > input[name=submit]:hover:not(.disabledSubmit)
		{
			background-image: linear-gradient(0deg, <?php if (check_theme()) echo $_COOKIE["themeColor1"].", ".$_COOKIE["themeColor2"]; else echo "#fd746c, #ff9068"; ?>);
		}
	</style>
</head>
<body>
	<?php include("../content/banner.php"); ?>
	<div class="gallery">
		<?php
		if (isset($_GET["from"]))
		{
		?>
		<div class="text-box" >
				<span id="box-title" >Pas si vite !</span><br/><br/>
				<span id="box-text">Afin de pouvoir publier tes propres photos et pouvoir liker et commenter celles de 
					la gallerie connecte toi ou créé un compte en cliquant <a href="/Camagru/account/register.php" >ici</a> !</span>
		</div>
		<br/>
		<?php
		}
		?>
		<div class="form" >
			<?php
			if (isset($valid) && $valid === 0)
				echo "<span class=\"invalid-info\" >Pseudo/Mot de passe invalide<br/></span>";
			else if (isset($valid) && $valid === 1)
				echo "<span class=\"invalid-info\" >Merci de valider votre email<br/></span>";
			?>
			<form action="./login.php" method="POST" >
				<input type="text" name="login" placeholder="Pseudo" maxlength="64" required autofocus /> <!-- AJOUTER LIMITES DE CHAR -->
				<br />
				<input type="password" name="passwd" placeholder="Mot de passe" maxlength="64" required />
				<br />
				<input type="submit" name="submit" value="Se connecter" />
				<br />
				<a href="/Camagru/account/register.php" >Pas encore inscrit ?</a>
				<br />
				<a href="/Camagru/account/forgot.php" >Mot de passe oublié ?</a>
			</form>
		</div>
	</div>
	<?php include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/footer.php"); ?>
</body>
</html>
<?php
}
else
{
	header("Location: /Camagru/index.php");
}
?>