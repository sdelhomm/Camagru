<?php

if (session_status() == PHP_SESSION_NONE)
	session_start();

$_SESSION["user-id"] = 0;

header("Location: /Camagru/index.php");

?>