<?php

include($_SERVER["DOCUMENT_ROOT"]."/Camagru/config/database.php");

try
{
	$db = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);

	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$db->query("CREATE DATABASE IF NOT EXISTS `camagrudb`");
	$db->query("USE `camagrudb`");
	$db->query("CREATE TABLE IF NOT EXISTS `users` (
		`id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
		`login` VARCHAR(24) NOT NULL,
		`email` TEXT NOT NULL,
		`confirm` BOOLEAN DEFAULT 0,
		`key` CHAR(32) CHARACTER SET ascii NOT NULL,
		`password` CHAR(128) CHARACTER SET ascii NOT NULL,
		PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	$db->query("CREATE TABLE IF NOT EXISTS `pictures` (
		`id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
		`userid` SMALLINT UNSIGNED NOT NULL,
		`path` TEXT NOT NULL,
		`time` INT,
		`likes` INT DEFAULT 0,
		PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	$db->query("CREATE TABLE IF NOT EXISTS `likes` (
		`id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
		`userid` SMALLINT UNSIGNED NOT NULL,
		`picid` SMALLINT UNSIGNED NOT NULL,
		PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	$db->query("CREATE TABLE IF NOT EXISTS `comments` (
		`id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
		`userid` SMALLINT UNSIGNED NOT NULL,
		`picid` SMALLINT UNSIGNED NOT NULL,
		`content` TEXT NOT NULL,
		`time` INT,
		PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}
catch (Exception $e)
{
	die("Erreur : ".$e->getMessage());
}

?>
