<?php

include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/account/ft_account.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/config/setup.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/gallery/theme.php");
if (session_status() == PHP_SESSION_NONE)
	session_start();

$req = $db->prepare("SELECT * FROM `users` WHERE `id` = ?");
$req->execute(array($_SESSION["user-id"]));
$user = $req->fetch();

if (isset($_POST["submit"]) && $_POST["submit"] == "Modifier mes informations" )
{
	if (isset($_POST["login"]) && isset($_POST["email"]) && isset($_POST["nom"]) && isset($_POST["prenom"]) && isset($_POST["birthday"]) && isset($_POST["country"]))
	{
		$_POST["login"] = substr($_POST["login"], 0, 12);
		$_POST["email"] = substr($_POST["email"], 0, 254);
		$_POST["nom"] = substr($_POST["nom"], 0, 32);
		$_POST["prenom"] = substr($_POST["prenom"], 0, 32);
		$_POST["birthday"] = substr($_POST["birthday"], 0, 10);
		$_POST["country"] = substr($_POST["country"], 0, 32);
		if (preg_match("/^[a-zA-Z0-9-_]*$/", $_POST["login"]) && strlen($_POST["login"]) >= 3 && ($_POST["login"] == $user["login"] || ft_check_login($_POST["login"], $db)))
		{
			$v_login = TRUE;
			if ($_POST["email"] == $user["email"] || ft_check_email($_POST["email"], $db) == TRUE)
			{
				$v_email = TRUE;
				if (preg_match("/^[a-zA-Z-]*$/", $_POST["nom"]) && preg_match("/^[a-zA-Z-]*$/", $_POST["prenom"]) && strlen($_POST["nom"]) >= 2 && strlen($_POST["prenom"]) >= 2)
				{
					$v_names = TRUE;
					if (ft_check_country($_POST["country"], $db) == TRUE)
					{
						$v_country = TRUE;
						if (strtotime($_POST["birthday"]) < time() && strtotime($_POST["birthday"]) !== FALSE)
						{
							$v_birthday = TRUE;
							$req = $db->prepare("UPDATE `users` SET `login` = ?, `email` = ?, `nom` = ?, `prenom` = ?, `birthday` = ?, `country` = ? WHERE `id` = ?");
							$req->execute(array($_POST["login"], $_POST["email"], $_POST["nom"], $_POST["prenom"], $_POST["birthday"], $_POST["country"], $_SESSION["user-id"]));
							header("Location: /Camagru/account/manage.php");
							exit ;
						}
						else
						{
							$v_birthday = FALSE;
						}
					}
					else
					{
						$v_country = FALSE;
					}
				}
				else
				{
					$v_names = FALSE;
				}
			}
			else
			{
				$v_email = FALSE;
			}
		}
		else
		{
			$v_login = FALSE;
		}
	}
}
if (isset($_POST["submit"]) && $_POST["submit"] == "Modifier mon mot de passe" )
{
	if (isset($_POST["oldpasswd"]) && isset($_POST["newpasswd"]) && isset($_POST["confpasswd"]))
	{
		if (ft_check_user($user["login"], $_POST["oldpasswd"], $db) == 2)
		{
			$v_oldpasswd = TRUE;
			if ($_POST["newpasswd"] == $_POST["confpasswd"])
			{
				$v_confpasswd = TRUE;
				$req = $db->prepare("UPDATE `users` SET `password` = ? WHERE id = ?");
				$req->execute(array(hash("whirlpool", $_POST["newpasswd"]), $_SESSION["user-id"]));
				header("Location: /Camagru/account/manage.php");
				exit ;
			}
			else
			{
				$v_confpasswd = FALSE;
			}
		}
		else
		{
			$v_oldpasswd = FALSE;
		}
	}
}

if (isset($_POST["notif"]) && $_POST["notif"] == "ok")
{
	if (ft_notif($_SESSION["user-id"], $db) == 2)
	{
		$req = $db->prepare("UPDATE `users` SET `notif` = 0 WHERE `id` = ?");
		$req->execute(array($_SESSION["user-id"]));
		echo "0";
		return "0";
	}
	else if (ft_notif($_SESSION["user-id"], $db) == 1)
	{
		$req = $db->prepare("UPDATE `users` SET `notif` = 1 WHERE `id` = ?");
		$req->execute(array($_SESSION["user-id"]));
		echo "1";
		return "1";
	}
	else
	{
		echo "fail";
		return "fail";
	}
}

if (ft_is_logged($db))
{
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Mon compte</title>
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
	<?php include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner_logged.php"); ?>
	<div class="gallery">
		<a href="/Camagru/account/profile.php?login=<?php echo htmlspecialchars(ft_login_byid($user["id"], $db)); ?>" ><h1 style="color: <?php if (check_theme()) echo $_COOKIE["themeColor3"]; else echo "#48a7f2"; ?>;" >Mon compte</h1></a>
		<div class="form">
			<?php if (isset($v_login) && $v_login === FALSE)
				echo "<span class=\"invalid-info\" >Pseudo déjà utilisé<br/></span>";
			if (isset($v_email) && $v_email === FALSE)
				echo "<span class=\"invalid-info\" >Email déjà utilisé ou invalide<br/></span>";
			if (isset($v_names) && $v_names === FALSE)
				echo "<span class=\"invalid-info\" >Nom/Prénom non-valides<br/></span>";
			if (isset($v_birthday) && $v_birthday === FALSE)
				echo "<span class=\"invalid-info\" >Merci de fournir une date valide<br/></span>";
			if (isset($v_country) && $v_country === FALSE)
				echo "<span class=\"invalid-info\" >Merci de choisir un pays<br/></span>";
			?>
			<form action="./manage.php" method="POST">
				<span id="wrongLogin" >Votre Pseudo doit contenir<br />3 à 12 lettres, chiffres ou tirets !</span>
				<br />
				<input id="reglogin" type="text" name="login" required="required" placeholder="Pseudo" pattern="[a-zA-Z0-9-_]{3,12}" maxlength="12" onkeyup="ft_test_login('modsubmit');" value="<?php echo htmlspecialchars($user["login"]); ?>"/>
				<br />
				<input type="email" name="email" required="required" placeholder="Email" maxlength="254" value="<?php echo htmlspecialchars($user["email"]); ?>" />
				<br />
				<input type="text" name="nom" required="required" placeholder="Nom" pattern="[a-zA-Z-]{2,32}" maxlength="32" value="<?php echo htmlspecialchars($user["nom"]); ?>" />
				<br />
				<input type="text" name="prenom" required="required" placeholder="Prénom" pattern="[a-zA-Z-]{2,32}" maxlength="32" value="<?php echo htmlspecialchars($user["prenom"]); ?>" />
				<br />
				<input type="date" name="birthday" max="<?php echo date('Y-m-d'); ?>" required="required" value="<?php echo htmlspecialchars($user["birthday"]); ?>" />
				<br />
				<select name="country" required="required">
					<option value="<?php echo htmlspecialchars($user["country"]); ?>" selected="selected"><?php echo htmlspecialchars($user["country"]); ?></option>
					<?php include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/country.php"); ?>
				</select>
				<br />
				<input id="modsubmit" type="submit" name="submit" value="Modifier mes informations" />
			</form>
		</div>
		<div class="form">
			<div class="notif" >
				<span class="textTitle" >Notifications :</span>
				<div id="notifBtn" class="switchBtn" onclick="ft_switch_notif()" <?php if ($user["notif"] == 1) echo "style=\"text-align: right; background-color: #45bf45;\""; else echo "style=\"text-align: left; background-color: #9c9c9c;\""; ?> >
					<span class="onoff" ></span>
				</div>
			</div>
			<br />
			<?php if (isset($v_oldpasswd) && $v_oldpasswd === FALSE)
				echo "<span class=\"invalid-info\" >Ancien mot de passe érroné<br/></span>";
			if (isset($v_confpasswd) && $v_confpasswd === FALSE)
				echo "<span class=\"invalid-info\" >Les mots de passes ne correspondent pas<br/></span>";
			?>
			<form action="./manage.php" method="POST">
				<input type="password" name="oldpasswd" placeholder="Ancien mot de passe" required="required" maxlength="32" />
				<br />
				<span id="strongPasswd" >Votre mot de passe doit contenir<br />6 à 32 caractères 1 majuscule et 1 chiffre !</span>
				<br />
				<input id="regpassword" type="password" name="newpasswd" required="required" placeholder="Mot de Passe" onkeyup="ft_test_password('modpsubmit');" maxlength="32" />
				<br />
				<input type="password" name="confpasswd" required="required" placeholder="Confirmer le mot de Passe" maxlength="32" />
				<br />
				<input id="modpsubmit" type="submit" name="submit" value="Modifier mon mot de passe" />
			</form>
		</div>
	</div>
	<?php include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/footer.php"); ?>
	<script type="text/javascript" src="/Camagru/scripts/script.js" ></script>
</body>
</html>
<?php
}
else
{
	header("Location: /Camagru/account/login.php?from=manage");
}
?>