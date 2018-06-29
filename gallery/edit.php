<?php

include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/account/ft_account.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/config/setup.php");
if (session_status() == PHP_SESSION_NONE)
	session_start();

if (ft_is_logged($db))
{
	$req = $db->prepare("SELECT * FROM `pictures` WHERE `userid` = ? ORDER BY `time` DESC");
	$req->execute(array($_SESSION["user-id"]));
	$pics = $req->fetchAll();
	if (isset($_POST["delete"]))
	{
		error_reporting(0);
		$req = $db->prepare("SELECT * FROM `pictures` WHERE `id` = ?");
		$req->execute(array($_POST["delete"]));
		$userid = $req->fetch();
		if ($req->rowCount() == 1 && $userid["userid"] == $_SESSION["user-id"])
		{
			unlink($_SERVER["DOCUMENT_ROOT"].$userid["path"]);
			$req = $db->prepare("DELETE FROM `pictures` WHERE `id` = ?");
			$req->execute(array($_POST["delete"]));
			$req = $db->prepare("DELETE FROM `comments` WHERE `picid` = ?");
			$req->execute(array($_POST["delete"]));
			$req = $db->prepare("DELETE FROM `likes` WHERE `picid` = ?");
			$req->execute(array($_POST["delete"]));
			echo "ok";
			return ("ok");
		}
		echo $_POST["delete"];
		return ("ko");
	}
	if (isset($_POST["sendimage"]) && $_POST["sendimage"] !== NULL && isset($_POST["filter"]))
	{
		error_reporting(0);
		if (!(file_exists($_SERVER["DOCUMENT_ROOT"]."/Camagru/pictures/")))
			mkdir($_SERVER["DOCUMENT_ROOT"]."/Camagru/pictures/");
		$image = $_POST["sendimage"];
		$image = str_replace("data:image/png;base64,", "", $image);
		$image = str_replace(" ", "+", $image);
		$image = base64_decode($image);
		if (imagecreatefromstring($image) !== FALSE)
		{
			$result = $db->query("SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = \"camagrudb\" AND TABLE_NAME = \"pictures\"");
			$id = $result->fetch();
			if (!(isset($id["AUTO_INCREMENT"])) || $id["AUTO_INCREMENT"] == NULL || $id["AUTO_INCREMENT"] == "")
				$id["AUTO_INCREMENT"] = 0;
			$path = $_SERVER["DOCUMENT_ROOT"]."/Camagru/pictures/".$id["AUTO_INCREMENT"]."-".$_SESSION["user-id"].".png";
			$pathbis = "/Camagru/pictures/".$id["AUTO_INCREMENT"]."-".$_SESSION["user-id"].".png";
			if (file_put_contents($path, $image) !== FALSE)
			{
				if (file_exists($path))
				{
					$tab = getimagesize($path);
					if ($tab[0] != 0 && $tab[1] != 0)
					{
						if (($dst = imagecreatefrompng($path)) === FALSE)
						{
							unlink($path);
						}
						else
						{
							if ($_POST["filter"] == "confetti")
							{
								$src = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"]."/Camagru/filters/confetti.png");
								imagecopyresampled ($dst, $src, 0, 0, 0, 0, $tab[0], $tab[1], 600, 450);
							}
							else if ($_POST["filter"] == "shining")
							{
								$src = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"]."/Camagru/filters/shining.png");
								imagecopyresampled ($dst, $src, 0, 0, 0, 0, $tab[0], $tab[1], 600, 450);
							}
							else if ($_POST["filter"] == "stars")
							{
								$src = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"]."/Camagru/filters/stars.png");
								imagecopyresampled ($dst, $src, 0, 0, 0, 0, $tab[0], $tab[1], 600, 450);
							}
							else if ($_POST["filter"] == "superman")
							{
								$src = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"]."/Camagru/filters/superman.png");
								imagecopyresampled ($dst, $src, 0, 0, 0, 0, $tab[0], $tab[1], 600, 450);
							}
							if (imagepng($dst, $path) == TRUE)
							{
								$req = $db->prepare("INSERT INTO `pictures`
									(`userid`,`path`,`time`)
									VALUES (?, ?, ?)");
								$req->execute(array($_SESSION["user-id"], $pathbis, time()));
								echo ("ok;".$pathbis.";".$id["AUTO_INCREMENT"]);
								return ("ok;".$pathbis.";".$id["AUTO_INCREMENT"]);
							}
						}
					}
				}
			}
		}
		echo "fail";
		return ("fail");
	}
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Montage</title>
		<link rel="stylesheet" type="text/css" href="/Camagru/style/style.css">
	</head>
	<body>
		<?php include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner_logged.php"); ?>
		<div class="gallery">
			<h1 style="color: <?php if (check_theme()) echo $_COOKIE["themeColor3"]; else echo "#48a7f2"; ?>;" >Montage</h1>
			<div class="montage">
				<div class="preview" >
					<img id="allowCam" src="/Camagru/images/allowcam.png" width="600" height="450" />
					<video autoplay="true" id="video">
					</video>
					<img class="preview-filter" id="prvw-filter">
					<div class="take-button" id="main-button" >
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 502 502" style="enable-background:new 0 0 502 502;" xml:space="preserve" width="512" height="512">
							<g>
								<g>
									<circle id="btn-color" style="fill:#b7b7b7;" cx="251" cy="251" r="241"/>
									<path d="M251,502c-67.044,0-130.076-26.108-177.484-73.516S0,318.044,0,251S26.108,120.924,73.516,73.516S183.956,0,251,0s130.076,26.108,177.484,73.516S502,183.956,502,251s-26.108,130.076-73.516,177.484S318.044,502,251,502z M251,20C123.626,20,20,123.626,20,251s103.626,231,231,231s231-103.626,231-231S378.374,20,251,20z"/>
								</g>
								<g>
									<path d="M251,461c-115.794,0-210-94.206-210-210c0-5.523,4.477-10,10-10s10,4.477,10,10c0,104.766,85.234,190,190,190c5.523,0,10,4.477,10,10S256.523,461,251,461z"/>
								</g>
								<g>
									<path d="M451,261c-5.523,0-10-4.477-10-10c0-78.745-47.358-148.202-120.651-176.95c-5.142-2.017-7.675-7.819-5.658-12.961c2.017-5.142,7.823-7.674,12.961-5.658c38.864,15.244,72.041,41.493,95.945,75.91C448.066,166.574,461,207.951,461,251C461,256.523,456.523,261,451,261z"/>
								</g>
								<g>
									<path d="M274.012,62.309c-0.38,0-0.764-0.021-1.15-0.066C265.659,61.418,258.304,61,251,61c-5.523,0-10-4.477-10-10s4.477-10,10-10c8.061,0,16.182,0.462,24.138,1.373c5.487,0.628,9.426,5.586,8.797,11.073C283.351,58.546,279.026,62.309,274.012,62.309z"/>
								</g>
							</g>
						</svg>
					</div>
				</div>
				<div class="my-pics">
					<h2 style="color: <?php if (check_theme()) echo $_COOKIE["themeColor1"]; else echo "#fd746c"; ?>;" >Mes photos</h2>
					<div id="pics">
						<?php
						foreach ($pics as $element)
						{
							if (file_exists($_SERVER["DOCUMENT_ROOT"].$element["path"]))
							{
								echo "<div id=\"".$element["id"]."\" ><img class=\"photo\" src=\"".$element["path"]."\"><img class=\"delete\" src=\"/Camagru/images/x-button.svg\" ></div>";
								$deletable[] = $element["id"];
							}
						}
						?>
					</div>
				</div>
				<h2 style="color: <?php if (check_theme()) echo $_COOKIE["themeColor1"]; else echo "#fd746c"; ?>;" >Upload</h2>
				<div class="uploadimage" >
					<form onsubmit="ft_upload(); return false" >
						<!-- CHECK EN PHP -->
						<label for="file">(PNG, 5mo maximum)</label>
						<input type="hidden" name="MAX_FILE_SIZE" value="5000" />
						<input type="file" id="pngfile" name="userupload" accept="image/png" />
						<input type="submit" name="submit" value="Valider" />
					</form>
				</div>
				<h2 style="color: <?php if (check_theme()) echo $_COOKIE["themeColor1"]; else echo "#fd746c"; ?>;" >Filtres</h2>
				<div class="filters" >
					<img id="no" src="/Camagru/images/hide.svg" onclick="filter = 'no'; document.getElementById('main-button').onclick = function() { ft_save(); };
					document.getElementById('main-button').style.cursor = 'pointer'; document.getElementById('btn-color').style.fill = '#4D93E8';
					document.getElementById('prvw-filter').removeAttribute('src'); ft_update_filter();" width="150px" height="112.5px" >
					<img id="superman" src="/Camagru/filters/superman.png" onclick="filter = 'superman'; document.getElementById('main-button').onclick = function() { ft_save(); };
					document.getElementById('main-button').style.cursor = 'pointer'; document.getElementById('btn-color').style.fill = '#4D93E8';
					document.getElementById('prvw-filter').src = '/Camagru/filters/superman.png'; ft_update_filter();" width="150px" height="112.5px" >
					<img id="stars" src="/Camagru/filters/stars.png" onclick="filter = 'stars'; document.getElementById('main-button').onclick = function() { ft_save(); };
					document.getElementById('main-button').style.cursor = 'pointer'; document.getElementById('btn-color').style.fill = '#4D93E8';
					document.getElementById('prvw-filter').src = '/Camagru/filters/stars.png'; ft_update_filter();" width="150px" height="112.5px" >
					<img id="shining" src="/Camagru/filters/shining.png" onclick="filter = 'shining'; document.getElementById('main-button').onclick = function() { ft_save(); };
					document.getElementById('main-button').style.cursor = 'pointer'; document.getElementById('btn-color').style.fill = '#4D93E8';
					document.getElementById('prvw-filter').src = '/Camagru/filters/shining.png'; ft_update_filter();" width="150px" height="112.5px" >
					<img id="confetti" src="/Camagru/filters/confetti.png" onclick="filter = 'confetti'; document.getElementById('main-button').onclick = function() { ft_save(); };
					document.getElementById('main-button').style.cursor = 'pointer'; document.getElementById('btn-color').style.fill = '#4D93E8';
					document.getElementById('prvw-filter').src = '/Camagru/filters/confetti.png'; ft_update_filter();" width="150px" height="112.5px" >
				</div>
			</div>
		</div>
		<canvas id="canvas" style="display: none;" ></canvas>
		<?php include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/footer.php"); ?>
		<script type="text/javascript" src="/Camagru/scripts/webcam.js" ></script>
		<script type="text/javascript" src="/Camagru/scripts/script.js" ></script>
		<script type="text/javascript">
			<?php
			if (isset($deletable) && $deletable !== NULL)
			{
				foreach ($deletable as $picid)
				{
					echo "ft_add_delete(".$picid.");\n";
				}
			}
			?>
		</script>
	</body>
	</html>
<?php
}
else
{
	header("Location: /Camagru/account/login.php?from=edit");
}
?>