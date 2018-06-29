<?php

function ft_check_login($login, $db)
{
	if (!isset($login) || $login == NULL || $login == "")
		return (FALSE);
	$req = $db->prepare("SELECT `login` FROM `users` WHERE `login` = ?");
	$req->execute(array($login));
	if ($req->rowCount() > 0)
		return (FALSE);
	return (TRUE);
}
function ft_check_email($email, $db)
{
	if (!isset($email) || $email == NULL || $email == "" || !filter_var($email, FILTER_VALIDATE_EMAIL))
		return (FALSE);
	$req = $db->prepare("SELECT `email` FROM `users` WHERE `email` = ?");
	$req->execute(array($email));
	if ($req->rowCount() > 0)
		return (FALSE);
	return (TRUE);
}

function ft_check_user($login, $password, $db)
{
	$req = $db->prepare("SELECT `confirm` FROM `users` WHERE `login` = ? AND `password` = ?");
	$req->execute(array($login, hash("whirlpool", $password)));
	$conf = $req->fetch();
	if ($req->rowCount() == 1)
	{
		if ($conf["confirm"] == 1)
			return (2);
		return (1);
	}
	return (0);
}
function ft_get_userid($login, $db)
{
	$req = $db->prepare("SELECT `id` FROM `users` WHERE `login` = ?");
	$req->execute(array($login));
	if ($req->rowCount() == 1)
	{
		$id = $req->fetch();
		return ($id["id"]);
	}
	return (0);
}
function ft_login_byid($id, $db)
{
	$req = $db->prepare("SELECT `login` FROM `users` WHERE `id` = ?");
	$req->execute(array($id));
	$login = $req->fetch();
	if ($req->rowCount() == 1)
		return ($login["login"]);
	return (FALSE);
}

function ft_is_logged($db)
{
	if (isset($_SESSION["user-id"]) && $_SESSION["user-id"] !== NULL && $_SESSION["user-id"] != 0 && $_SESSION["user-id"] != "" && ft_login_byid($_SESSION["user-id"], $db) !== FALSE)
		return (TRUE);
	return (FALSE);
}

function ft_mail_confirm($login, $email, $uid)
{
	$subject = "Confirmez votre compte Camagru";
	$message = "Bonjour ".$login.",\n
	Bienvenue sur Camagru !\n\n
	Pour confirmer votre compte merci de cliquer sur le lien ci-dessous ou le copier-coller dans la barre de recherche de votre navigateur.\n
	Lien de confirmation : http://localhost:8080/Camagru/account/confirm.php?login=".urlencode($login)."&uid=".urlencode($uid)."\n\n
	Merci de votre confiance !";
	mail($email, $subject, $message);
}
function ft_check_confirmation($login, $uid, $db)
{
	$req = $db->prepare("SELECT `key`, `confirm` FROM `users` WHERE `login` = ?");
	$req->execute(array($login));
	if ($req->rowCount() == 1)
	{
		$key = $req->fetch();
		if ($key["key"] == $uid)
		{
			if ($key["confirm"] == 1)
				return (2);
			return (1);
		}
	}
	return (0);
}

function ft_liked($userid, $picid, $db)
{
	$req = $db->prepare("SELECT `id` FROM `likes` WHERE `userid` = ? AND `picid` = ?");
	$req->execute(array($userid, $picid));
	if ($req->rowCount() == 1)
		return (TRUE);
	return (FALSE);
}
function ft_pic_exists($picid, $db)
{
	$req = $db->prepare("SELECT `path` FROM `pictures` WHERE `id` = ?");
	$req->execute(array($picid));
	if ($req->rowCount() == 1)
	{
		$path = $req->fetch();
		if (file_exists($_SERVER["DOCUMENT_ROOT"].$path["path"]))
			return (TRUE);
	}
	return (FALSE);
}
function ft_notif($userid, $db)
{
	$req = $db->prepare("SELECT `notif` FROM `users` WHERE `id` = ?");
	$req->execute(array($userid));
	if ($req->rowCount() == 1)
	{
		$notif = $req->fetch();
		if ($notif["notif"] == 1)
			return (2);
		else if ($notif["notif"] == 0)
			return (1);
	}
	return (0);
}

function ft_get_autor($picid, $db)
{
	$req = $db->prepare("SELECT `userid` FROM `pictures` WHERE `id` = ?");
	$req->execute(array($picid));
	if ($req->rowCount() == 1)
	{
		$userid = $req->fetch();
		return ($userid["userid"]);
	}
	return (FALSE);
}
function ft_notif_mail($picid, $name, $db)
{
	$req = $db->prepare("SELECT * FROM `users` WHERE `id` = ?");
	$req->execute(array(ft_get_autor($picid, $db)));
	if ($req->rowCount() == 1)
	{
		$user = $req->fetch();
		if ($user["notif"] == 1)
		{
			$subject = "Nouveau commentaire sur une de vos photos ! - Camagru";
			$message = "Bonjour ".$user["login"].",\n
			".ft_login_byid($name, $db)." vien de poster un commentaire sur un de vos montages !\n\n
			Lien de la photo : http://localhost:8080/Camagru/gallery/gallery.php?picture=".$picid."\n\n
			Merci !";
			mail($user["email"], $subject, $message);
		}
	}
}

function ft_mail_recup($userid, $key, $db)
{
	$req = $db->prepare("SELECT * FROM `users` WHERE `id` = ?");
	$req->execute(array($userid));
	$user = $req->fetch();
	$subject = "Réinitialisation de votre mot de passe Camagru";
	$message = "Bonjour ".$user["login"].",\n
	Une demande de réinitialisation de votre mot de passe viens d'être effectué depuis le site Camagru,\n
	veuillez cliquer ou le copier-coller dans la barre de recherche de votre navigateur.\n
	Lien de réinitialisation : http://localhost:8080/Camagru/account/forgot.php?userid=".$userid."&uid=".$key."\n\n
	ATTTENTION : Si cette demande ne viens pas de vous veuillez ignorer cet email et supprimer ce message !\n\n
	Merci de votre confiance !";
	mail($user["email"], $subject, $message);
}

function ft_check_country($name, $db)
{
	$req = $db->prepare("SELECT * FROM `country` WHERE `name` = ?");
	$req->execute(array($name));
	if ($req->rowCount() == 1)
		return (TRUE);
	return (FALSE);
}
?>