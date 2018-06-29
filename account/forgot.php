<?php

include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/account/ft_account.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/config/setup.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/gallery/theme.php");
if (session_status() == PHP_SESSION_NONE)
	session_start();

if (isset($_POST["submit"]) && $_POST["submit"] == "Récupérer mon mot de passe")
{
	if (isset($_POST["login"]))
	{
		$_POST["login"] = substr($_POST["login"], 0, 12);
		if (ft_check_login($_POST["login"], $db) === FALSE)
		{
			$key = md5(uniqid(rand(), TRUE));
			$userid = ft_get_userid($_POST["login"], $db);
			$req = $db->prepare("DELETE FROM `recuperation` WHERE `userid` = ?");
			$req->execute(array($userid));
			$req = $db->prepare("INSERT INTO `recuperation` (`userid`, `key`) VALUES (?, ?)");
			$req->execute(array($userid, hash("whirlpool", $key)));
			ft_mail_recup($userid, $key, $db);
		}
		echo "<script>alert('Un mail contenant les instructions de récupération vous a été envoyé !')</script>";
	}
}

if (isset($_POST["submit"]) && $_POST["submit"] == "Modifier mon mot de passe")
{
	if (isset($_POST["newpasswd"]) && isset($_POST["confirmpasswd"]) && isset($_POST["userid"]) && isset($_POST["uid"]))
	{
		$_POST["newpasswd"] = substr($_POST["newpasswd"], 0, 32);
		$_POST["confirmpasswd"] = substr($_POST["confirmpasswd"], 0, 32);
		$_POST["userid"] = substr($_POST["userid"], 0, 255);
		$_POST["uid"] = substr($_POST["uid"], 0, 255);
		if (preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/", $_POST["newpasswd"]) && strlen($_POST["newpasswd"]) >= 6 && $_POST["newpasswd"] == $_POST["confirmpasswd"])
		{
			$req = $db->prepare("SELECT `key` FROM `recuperation` WHERE `userid` = ?");
			$req->execute(array(htmlspecialchars($_POST["userid"])));
			if ($req->rowCount() == 1)
				$key = $req->fetch();
			echo "<script>alert('1 : ".htmlspecialchars($_POST["uid"])." 2 : ".$key["key"]." ')</script>";
			if ($req->rowCount() == 1 && $key["key"] == hash("whirlpool", htmlspecialchars($_POST["uid"])))
			{
				$req = $db->prepare("DELETE FROM `recuperation` WHERE `userid` = ?");
				$req->execute(array($_POST["userid"]));
				$req = $db->prepare("UPDATE `users` SET `password` = ? WHERE `id` = ?");
				$req->execute(array(hash("whirlpool", $_POST["newpasswd"]), $_POST["userid"]));
				header("Location: /Camagru/account/login.php");
				return ;
			}
			else
			{
				echo "<script>alert('Erreur identification, merci de réessayer plus tard.')</script>";
			}
		}
		else
		{
			echo "<script>alert('Les mots de passe ne correspondent pas !'); location='/Camagru/account/forgot.php?userid=".$_POST["userid"]."&uid=".$_POST["uid"]."' </script>";
		}
	}
}

if (!ft_is_logged($db))
{
	if (isset($_GET["userid"]) && isset($_GET["uid"]))
	{
		$req = $db->prepare("SELECT `key` FROM `recuperation` WHERE `userid` = ?");
		$req->execute(array(htmlspecialchars($_GET["userid"])));
		if ($req->rowCount() == 1)
			$key = $req->fetch();
		if ($req->rowCount() == 1 && $key["key"] == hash("whirlpool", htmlspecialchars($_GET["uid"])))
		{
			?>
			<!DOCTYPE html>
			<html>
			<head>
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<title>Nouveau mot de passe</title>
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
					<div class="form" >
						<form action="./forgot.php" method="POST" >
							<span id="strongPasswd" >Votre mot de passe doit contenir<br />6 à 32 caractères 1 majuscule et 1 chiffre !</span>
							<br />
							<input id="regpassword" type="password" name="newpasswd" required="required" placeholder="Mot de Passe" onkeyup="ft_test_password('regsubmit');" maxlength="32" autofocus />
							<br />
							<input type="password" name="confirmpasswd" required="required" placeholder="Confirmer le mot de Passe" maxlength="32"/>
							<br />
							<input type="hidden" name="userid" value="<?php echo $_GET["userid"] ?>" />
							<input type="hidden" name="uid" value="<?php echo $_GET["uid"] ?>" />
							<input id="regsubmit" type="submit" name="submit" value="Modifier mon mot de passe" />
							<br />
							<a href="/Camagru/account/login.php" >Se connecter</a>
						</form>
					</div>
				</div>
				<?php include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/footer.php"); ?>
				<script type="text/javascript" src="/Camagru/scripts/script.js" ></script>
			</body>
			</html>
			<?php
			return ;
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
				<?php include("../content/banner.php"); ?>
				<div class="gallery" >
					<div class="text-box" >
							<span id="box-title" >Erreur !</span><br/><br/>
							<span id="box-text">Le lien de réinitialisation entré est invalide !<br/><br/>
							Merci de réessayer avec un lien valide.</span>
					</div>
				</div>
				<?php include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/footer.php"); ?>
			</body>
			</html>
			<?php
			return ;
		}
	}
	else
	{
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Mot de passe oublié</title>
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
			<div class="form" >
				<form action="./forgot.php" method="POST" >
					<input type="text" name="login" placeholder="Pseudo" maxlength="12" required autofocus />
					<br />
					<input type="submit" name="submit" value="Récupérer mon mot de passe" />
					<br />
					<a href="/Camagru/account/login.php" >Se connecter</a>
				</form>
			</div>
		</div>
		<?php include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/footer.php"); ?>
	</body>
	</html>
	<?php
	}
}
else
{
	header("Location: /Camagru/index.php");
}
?>