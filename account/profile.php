<?php
include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/account/ft_account.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/config/setup.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/gallery/theme.php");
if (session_status() == PHP_SESSION_NONE)
	session_start();

if (!isset($_GET["login"]) || ft_check_login($_GET["login"], $db))
{
	header("Location: /Camagru/index.php");
	exit ;
}

$req = $db->prepare("SELECT * FROM `users` WHERE `login` = ?");
$req->execute(array($_GET["login"]));
$user = $req->fetch();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo htmlspecialchars(ft_login_byid($user["id"], $db)); ?></title>
	<link rel="stylesheet" type="text/css" href="/Camagru/style/style.css">
</head>
<body>
	<?php
	if (ft_is_logged($db) == TRUE)
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner_logged.php");}
	else
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner.php");}
	?>
	<div class="gallery" >
		<h1 style="color: <?php if (check_theme()) echo $_COOKIE["themeColor3"]; else echo "#48A7F2"; ?>;" ><?php echo htmlspecialchars(ft_login_byid($user["id"], $db)); ?></h1>
		<br/>
		<img src="/Camagru/images/user.svg" width="200px" height="200px" />
		<br/>
		<div class="text-box" >
			<span id="box-text">
				<b>Nom :</b>
				<br/>
				<?php echo htmlspecialchars($user["nom"]); ?>
				<br/>
				<br />
				<b>Prénom :</b>
				<br/>
				<?php echo htmlspecialchars($user["prenom"]); ?>
				<br/>
				<br />
				<b>Email :</b>
				<br/>
				<?php echo htmlspecialchars($user["email"]); ?>
				<br/>
				<br />
				<b>Age :</b>
				<br/>
				<?php $age = time() - strtotime($user["birthday"]);
				echo htmlspecialchars(floor($age / 31536000)." ans"); ?>
				<br/>
				<br />
				<b>Pays :</b>
				<br/>
				<?php echo htmlspecialchars($user["country"]); ?>
				<br/>
				<br/>
				<b>Likes :</b>
				<br/>
				<?php $req = $db->prepare("SELECT SUM(`likes`) FROM `pictures` WHERE `userid` = ?");
				$req->execute(array($user["id"]));
				$likes = $req->fetch();
				if (isset($likes["SUM(`likes`)"]) && $likes["SUM(`likes`)"] !== NULL)
					echo htmlspecialchars($likes["SUM(`likes`)"]);
				else
					echo "0";
				?>
				<br/>
			</span>
		</div>
	</div>
</body>
</html>