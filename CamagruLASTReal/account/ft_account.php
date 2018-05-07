<?php

function ft_check_login($login, $db)
{
	$req = $db->prepare("SELECT `login` FROM `users` WHERE `login` = ?");
	$req->execute(array($login));
	if ($req->rowCount() > 0)
		return (FALSE);
	return (TRUE);
}
function ft_check_email($email, $db)
{
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
?>