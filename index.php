<?php

include($_SERVER["DOCUMENT_ROOT"]."/Camagru/config/setup.php");
include ($_SERVER["DOCUMENT_ROOT"]."/Camagru/account/ft_account.php");
if (session_status() == PHP_SESSION_NONE)
	session_start();

?>
<!DOCTYPE html>
<html>
<head>
	<title>Camagru</title>
	<link rel="stylesheet" type="text/css" href="/Camagru/style/style.css">
</head>
<body>
	<?php
	if (ft_is_logged($db) == TRUE)
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner_logged.php");}
	else
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner.php");}
	?>
</body>
</html>
