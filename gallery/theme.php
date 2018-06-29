<?php
function check_theme()
{
	if (isset($_COOKIE["themeColor1"]) && preg_match("/^#[a-f0-9]{6}$/i", $_COOKIE["themeColor1"]) &&
		isset($_COOKIE["themeColor2"]) && preg_match("/^#[a-f0-9]{6}$/i", $_COOKIE["themeColor2"]) &&
		isset($_COOKIE["themeColor3"]) && preg_match("/^#[a-f0-9]{6}$/i", $_COOKIE["themeColor3"]))
		return (TRUE);
	else
		return (FALSE);
}

if (isset($_GET["theme"]))
{
	if ($_GET["theme"] == "orange")
	{
		setcookie("themeColor1", "#fd746c", time() + (86400 * 30), "/");
		setcookie("themeColor2", "#ff9068", time() + (86400 * 30), "/");
		setcookie("themeColor3", "#48a7f2", time() + (86400 * 30), "/");
	}
	else if ($_GET["theme"] == "green")
	{
		setcookie("themeColor1", "#16b97f", time() + (86400 * 30), "/");
		setcookie("themeColor2", "#6ae899", time() + (86400 * 30), "/");
		setcookie("themeColor3", "#da5a47", time() + (86400 * 30), "/");
	}
	else if ($_GET["theme"] == "purple")
	{
		setcookie("themeColor1", "#d045a8", time() + (86400 * 30), "/");
		setcookie("themeColor2", "#d473b7", time() + (86400 * 30), "/");
		setcookie("themeColor3", "#ea3d50", time() + (86400 * 30), "/");
	}
	else if ($_GET["theme"] == "red")
	{
		setcookie("themeColor1", "#de3a4b", time() + (86400 * 30), "/");
		setcookie("themeColor2", "#f77373", time() + (86400 * 30), "/");
		setcookie("themeColor3", "#94bd34", time() + (86400 * 30), "/");
	}
	header ("Location: /Camagru/index.php");
}
?>