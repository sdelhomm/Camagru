<?php

include ("./ft_account.php");
include($_SERVER["DOCUMENT_ROOT"]."/Camagru/config/setup.php");

if (isset($_POST["submit"]) && $_POST["submit"] == "S'inscrire")
{
	if (isset($_POST["login"]) && isset($_POST["email"]) && isset($_POST["passwd"]))
	{
		if (ft_check_login($_POST["login"], $db))
		{
			$v_login = TRUE;
			if (ft_check_email($_POST["email"], $db) == TRUE)
			{
				$v_email = TRUE;
				$confirm_id = md5(uniqid(rand(), TRUE));
				$req = $db->prepare("INSERT INTO `users` (`login`,`email`,`password`,`key`)
					VALUES (?, ?, ?, ?)");
				$req->execute(array($_POST["login"], $_POST["email"], hash("whirlpool" ,$_POST["passwd"]), $confirm_id));
				ft_mail_confirm($_POST["login"], $_POST["email"], $confirm_id);
				header("Location: /Camagru/account/login.php");
				exit;
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

?>
<!DOCTYPE html>
<html>
<head>
	<title>Register</title>
	<link rel="stylesheet" type="text/css" href="/Camagru/style/style.css">
</head>
<body>
	<?php include("../content/banner.php"); ?>
	<div class="form" >
		<?php if (isset($v_login) && $v_login === FALSE)
			echo "<span class=\"invalid-info\" >Pseudo déjà utilisé<br/></span>";
		if (isset($v_email) && $v_email === FALSE)
			echo "<span class=\"invalid-info\" >Email déjà utilisé<br/></span>";
		?>
		<form action="./register.php" method="POST" >
			<input type="text" name="login" required="required" placeholder="Pseudo" autofocus/> <!-- AJOUTER LIMITES DE CHAR -->
			<br />
			<input type="email" name="email" required="required" placeholder="Email" />
			<br />
			<input type="password" name="passwd" required="required" placeholder="Mot de Passe" />
			<br />
			<input type="submit" name="submit" value="S'inscrire" placeholder="Pseudo" />
			<br />
			<a href="/Camagru/account/login.php" >Déjà membre ?</a>
		</form>
	</div>
</body>
</html>