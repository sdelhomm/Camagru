<?php

include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/config/database.php");
date_default_timezone_set("Europe/Paris");
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
		`nom` TEXT NOT NULL,
		`prenom` TEXT NOT NULL,
		`birthday` TEXT NOT NULL,
		`country` TEXT NOT NULL,
		`notif` BOOLEAN DEFAULT 1,
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
	$db->query("CREATE TABLE IF NOT EXISTS `recuperation` (
		`id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
		`userid` SMALLINT UNSIGNED NOT NULL,
		`key` CHAR(128) CHARACTER SET ascii NOT NULL,
		PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	$db->query("CREATE TABLE IF NOT EXISTS `country` (
		`id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
		`name` TEXT NOT NULL,
		PRIMARY KEY (id)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	$req = $db->prepare("SELECT COUNT(*) FROM `country`");
	$req->execute(array());
	$count = $req->fetch();
	if ($count["COUNT(*)"] < 1)
	{
		$countrylist = array("Afghanistan", "Afrique_Centrale", "Afrique_du_Sud", "Albanie", "Algerie", "Allemagne", "Andorre", "Angola", "Anguilla", "Arabie_Saoudite", "Argentine", "Armenie", "Australie", "Autriche", "Azerbaidjan", "Bahamas", "Bangladesh", "Barbade", "Bahrein", "Belgique", "Belize", "Benin", "Bermudes", "Bielorussie", "Bolivie", "Botswana", "Bhoutan", "Boznie_Herzegovine", "Bresil", "Brunei", "Bulgarie", "Burkina_Faso", "Burundi", "Caiman", "Cambodge", "Cameroun", "Canada", "Canaries", "Cap_Vert", "Chili", "Chine", "Chypre", "Colombie", "Colombie", "Congo", "Congo_democratique", "Cook", "Coree_du_Nord", "Coree_du_Sud", "Costa_Rica", "Côte_d_Ivoire", "Croatie", "Cuba", "Danemark", "Djibouti", "Dominique", "Egypte", "Emirats_Arabes_Unis", "Equateur", "Erythree", "Espagne", "Estonie", "Etats_Unis", "Ethiopie", "Falkland", "Feroe", "Fidji", "˙Finlande", "France", "Gabon", "Gambie", "Georgie", "Ghana", "Gibraltar", "Grece", "Grenade", "Groenland", "Guadeloupe", "Guam", "Guernesey", "Guinee", "Guinee_Bissau", "Guinee_Equatoriale", "Guyana", "Guyane_Francaise", "Haiti", "Hawaii", "Honduras", "Hong_Kong", "Hongrie", "Inde", "Indonesie", "Iran", "Iraq", "Irlande", "Islande", "Israel", "Italie", "Jamaique", "JanMayen", "Japon", "Jersey", "Jordanie", "Kazakhstan", "Kenya", "Kirghizistan", "Kiribati", "Koweit", "Laos", "Lesotho", "Lettonie", "Liban", "Liberia", "Liechtenstein", "Lituanie", "Luxembourg", "Lybie", "Macao", "Macedoine", "Madagascar", "Madère", "Malaisie", "Malawi", "Maldives", "Mali", "Malte", "Man", "MariannesduNord", "Maroc", "Marshall", "Martinique", "Maurice", "Mauritanie", "Mayotte", "Mexique", "Micronesie", "Midway", "Moldavie", "Monaco", "Mongolie", "Montserrat", "Mozambique", "Namibie", "Nauru", "Nepal", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk", "Norvege", "Nouvelle_Caledonie", "Nouvelle_Zelande", "Oman", "Ouganda", "Ouzbekistan", "Pakistan", "Palau", "Palestine", "Panama", "Papouasie_Nouvelle_Guinee", "Paraguay", "Pays_Bas", "Perou", "Philippines", "Pologne", "Polynesie", "Porto_Rico", "Portugal", "Qatar", "Republique_Dominicaine", "Republique_Tcheque", "Reunion", "Roumanie", "Royaume_Uni", "Russie", "Rwanda", "SaharaOccidental", "Sainte_Lucie", "Saint_Marin", "Salomon", "Salvador", "Samoa_OccidSamoa_Americaine", "Sao_Tome_et_Principe", "Senegal", "Seychelles", "SierraLeone", "Singapour", "Slovaquie", "Somalie", "Soudan", "Sri_Lanka", "Suede", "Suisse", "Surinam", "Swaziland", "Syrie", "Tadjikistan", "Taiwan", "Tonga", "Tanzanie", "Tchad", "Thailande", "Tibet", "Timor_Oriental", "Togo", "Trinite_et_Tobago", "Tristandecuncha", "Tunisie", "Turmenistan", "Turquie", "Ukraine", "Uruguay", "Vanuatu", "Vatican", "Venezuela", "Vierges_Americaines", "Vierges_Britanniques", "Vietnam", "Wake", "WallisetFutuma", "Yemen", "Yougoslavie", "Zambie", "Zimbabwe");
		$valstr = str_repeat("(?),", count($countrylist));
		$valstr = substr($valstr, 0, -1);
		$req = $db->prepare("INSERT INTO `country` (`name`) VALUES ".$valstr."");
		$req->execute($countrylist);
	}
}
catch (Exception $e)
{
	die("Erreur : ".$e->getMessage());
}

?>
