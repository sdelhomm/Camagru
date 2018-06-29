<?php

include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/account/ft_account.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/config/setup.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/gallery/theme.php");
if (session_status() == PHP_SESSION_NONE)
	session_start();

if (isset($_POST["submit"]) && $_POST["submit"] == "S'inscrire")
{
	if (isset($_POST["login"]) && isset($_POST["email"]) && isset($_POST["nom"]) && isset($_POST["prenom"]) && isset($_POST["birthday"]) && isset($_POST["country"]) && isset($_POST["confpasswd"]) && isset($_POST["passwd"]))
	{
		$_POST["login"] = substr($_POST["login"], 0, 12);
		$_POST["email"] = substr($_POST["email"], 0, 254);
		$_POST["nom"] = substr($_POST["nom"], 0, 32);
		$_POST["prenom"] = substr($_POST["prenom"], 0, 32);
		$_POST["birthday"] = substr($_POST["birthday"], 0, 10);
		$_POST["country"] = substr($_POST["country"], 0, 32);
		$_POST["confpasswd"] = substr($_POST["confpasswd"], 0, 32);
		$_POST["passwd"] = substr($_POST["passwd"], 0, 32);
		if (preg_match("/^[a-zA-Z0-9-_]*$/", $_POST["login"]) && strlen($_POST["login"]) >= 3 && ft_check_login($_POST["login"], $db))
		{
			$v_login = TRUE;
			if (ft_check_email($_POST["email"], $db) == TRUE)
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
							if (preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/", $_POST["passwd"]) && strlen($_POST["passwd"]) >= 6 && $_POST["confpasswd"] == $_POST["passwd"])
							{
								$v_passwd = TRUE;
								$confirm_id = md5(uniqid(rand(), TRUE));
								$req = $db->prepare("INSERT INTO `users` (`login`,`email`, `nom`, `prenom`, `birthday`, `country`, `password`, `key`)
									VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
								$req->execute(array($_POST["login"], $_POST["email"], $_POST["nom"], $_POST["prenom"], $_POST["birthday"], $_POST["country"], hash("whirlpool" ,$_POST["passwd"]), $confirm_id));
								ft_mail_confirm($_POST["login"], $_POST["email"], $confirm_id);
								echo "<script>alert('Un mail de confirmation vous a été envoyé (Vérifiez vos spams)'); location='/Camagru/account/login.php';</script>";
								exit;
							}
							else
							{
								$v_passwd = FALSE;
							}
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

if (!ft_is_logged($db))
{
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Register</title>
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
			if (isset($v_passwd) && $v_passwd === FALSE)
				echo "<span class=\"invalid-info\" >Les mots de passes ne correspondent pas<br/></span>";
			?>
			<form action="./register.php" method="POST" >
				<span id="wrongLogin" >Votre Pseudo doit contenir<br />3 à 12 lettres, chiffres ou tirets !</span>
				<br />
				<input id="reglogin" type="text" name="login" required="required" placeholder="Pseudo" pattern="[a-zA-Z0-9-_]{3,12}" maxlength="12" autofocus onkeyup="ft_test_login('regsubmit');" />
				<br />
				<input type="email" name="email" required="required" placeholder="Email" maxlength="254"/>
				<br />
				<input type="text" name="nom" required="required" placeholder="Nom" pattern="[a-zA-Z-]{2,32}" maxlength="32"/>
				<br />
				<input type="text" name="prenom" required="required" placeholder="Prénom" pattern="[a-zA-Z-]{2,32}" maxlength="32"/>
				<br />
				<input type="date" name="birthday" max="<?php echo date('Y-m-d'); ?>" required="required" />
				<br />
				<select name="country" required="required" >
					<option value="no" selected="selected">Votre pays :</option>
					<?php include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/country.php"); ?>
				</select>
				<br />
				<span id="strongPasswd" >Votre mot de passe doit contenir<br />6 à 32 caractères 1 majuscule et 1 chiffre !</span>
				<br />
				<input id="regpassword" type="password" name="passwd" required="required" placeholder="Mot de Passe" onkeyup="ft_test_password('regsubmit');" maxlength="32" />
				<br />
				<input type="password" name="confpasswd" required="required" placeholder="Confirmer le mot de Passe" maxlength="32" />
				<br />
				<input id="regsubmit" type="submit" name="submit" value="S'inscrire" />
				<br />
				<a href="/Camagru/account/login.php" >Déjà membre ?</a>
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
	header("Location: /Camagru/index.php");
}
?>