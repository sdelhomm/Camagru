<?php

include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/config/setup.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/account/ft_account.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/Camagru/gallery/theme.php");
if (session_status() == PHP_SESSION_NONE)
	session_start();

if (isset($_POST["like"]))
{
	if (ft_is_logged($db) && ft_pic_exists($_POST["like"], $db))
	{
		if (ft_liked($_SESSION["user-id"], $_POST["like"], $db) == FALSE)
		{
			$req = $db->prepare("INSERT INTO `likes`
								(`userid`,`picid`)
								VALUES (?, ?)");
			$req->execute(array($_SESSION["user-id"], $_POST["like"]));
			$req = $db->prepare("UPDATE `pictures` SET `likes` = `likes` + 1 WHERE id = ?");
			$req->execute(array($_POST["like"]));
			echo "1";
			exit;
		}
		else
		{
			$req = $db->prepare("DELETE FROM `likes` WHERE `userid` = ? AND `picid` = ?");
			$req->execute(array($_SESSION["user-id"], $_POST["like"]));
			$req = $db->prepare("UPDATE `pictures` SET `likes` = `likes` - 1 WHERE id = ?");
			$req->execute(array($_POST["like"]));
			echo "0";
			exit;
		}
	}
	else
	{
		echo "nolog";
		return ("nolog");
	}
}
else if (isset($_POST["comment"]) && isset($_POST["picid"]) && $_POST["comment"] !== NULL && $_POST["comment"] != "")
{
	if (ft_is_logged($db) && ft_pic_exists($_POST["picid"], $db))
	{
		$_POST["comment"] = substr($_POST["comment"], 0, 180);
		$req = $db->prepare("INSERT INTO `comments` (`userid`, `picid`, `content`, `time`) VALUES (?, ?, ?, ?)");
		$req->execute(array($_SESSION["user-id"], $_POST["picid"], $_POST["comment"], time()));
		ft_notif_mail($_POST["picid"], $_SESSION["user-id"], $db);
		echo "ok;".ft_login_byid($_SESSION["user-id"], $db).";".date("F j, H:i");
		return ("ok;".ft_login_byid($_SESSION["user-id"], $db).";".date("F j, H:i"));
	}
	else
	{
		echo "nolog";
		return ("nolog");
	}
}

if (isset($_GET["picture"]) && ft_pic_exists(htmlspecialchars($_GET["picture"]), $db))
{
	$req = $db->prepare("SELECT * FROM `pictures` WHERE `id` = ?");
	$req->execute(array(htmlspecialchars($_GET["picture"])));
	$pic = $req->fetch();
	$req = $db->prepare("SELECT * FROM `comments` WHERE `picid` = ?");
	$req->execute(array(htmlspecialchars($_GET["picture"])));
	$comments = $req->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo htmlspecialchars(ft_login_byid($pic["userid"], $db)); ?></title>
	<link rel="stylesheet" type="text/css" href="/Camagru/style/style.css">
</head>
<body>
	<div id="fb-root"></div>
	<script>(function(d, s, id){
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = 'https://connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v3.0';
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
	<?php
	if (ft_is_logged($db) == TRUE)
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner_logged.php");}
	else
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner.php");}
	?>
	<div class="gallery">
		<a href="/Camagru/account/profile.php?login=<?php echo htmlspecialchars(ft_login_byid($pic["userid"], $db)); ?>" ><h1 style="color: <?php if (check_theme()) echo $_COOKIE["themeColor3"]; else echo "#48A7F2"; ?>;" ><?php echo htmlspecialchars(ft_login_byid($pic["userid"], $db)); ?></h1></a>
		<div class="comment-section">
			<img src="<?php echo $pic["path"]; ?>">
			<svg class="like-button" onclick="ft_like(<?php echo $pic["id"]; ?>)" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
				<path id="like-path<?php echo $pic["id"]; ?>" style="fill:<?php if (ft_is_logged($db) && ft_liked($_SESSION["user-id"], $pic["id"], $db)) echo "#D75A4A"; else echo "#b2b2b2"; ?>;" d="M24.85,10.126c2.018-4.783,6.628-8.125,11.99-8.125c7.223,0,12.425,6.179,13.079,13.543c0,0,0.353,1.828-0.424,5.119c-1.058,4.482-3.545,8.464-6.898,11.503L24.85,48L7.402,32.165c-3.353-3.038-5.84-7.021-6.898-11.503c-0.777-3.291-0.424-5.119-0.424-5.119C0.734,8.179,5.936,2,13.159,2C18.522,2,22.832,5.343,24.85,10.126z"/>
			</svg>
			<span class="like-nbr" id="like-nbr<?php echo $pic["id"]; ?>"><?php echo $pic["likes"]; ?></span>
			<span class="picDate" style="color: <?php if (check_theme()) echo $_COOKIE["themeColor1"]; else echo "#fd746c"; ?>;" ><?php echo date("d/m/y", $pic["time"]); ?></span>
			<div class="comments" id="comment-div">
				<?php
				foreach ($comments as $element)
				{
					?>
					<div class="post" >
						<div class="post-login" ><?php echo htmlspecialchars(ft_login_byid($element["userid"], $db)); ?></div>
						<div class="post-date" ><?php echo date("F j, H:i", $element["time"]); ?></div>
						<br/>
						<div class="post-text" ><?php echo htmlspecialchars($element["content"]) ?></div>
					</div>
					<?php
				}
				?>
			</div>
			<div class="post-comment" >
				<form name="commentform" onsubmit="post_comment(); return false;">
					<input class="comment-text" type="text" name="comment" required maxlength="180" autocomplete="off" style="border-color: <?php if (check_theme()) echo $_COOKIE["themeColor1"]; else echo "#fd746c"; ?>">
					<input class="submit-button" type="image" src="/Camagru/images/send-button.svg" alt="submit" />
				</form>
			</div>
			<a style="margin: 0; padding: 0" href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-show-count="false">Tweet</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
			<br />
			<div style="margin: 5px;" class="fb-share-button" data-href="http://localhost:8080/Camagru/gallery/gallery.php?picture=8" data-layout="button_count" data-size="small" data-mobile-iframe="true"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2Flocalhost%3A8080%2FCamagru%2Fgallery%2Fgallery.php%3Fpicture%3D<?php echo $_GET["picture"]; ?>&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Partager</a></div>
		</div>
	</div>
	<?php include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/footer.php"); ?>
	<script type="text/javascript" src="/Camagru/scripts/script.js" ></script>
</body>
</html>
<?php
}
else if (isset($_GET["picture"]))
{
?>
Error 404, not found.
<?php
}
else
{
	$req = $db->prepare("SELECT COUNT(*) AS `total` FROM `pictures`");
	$req->execute();
	$fetch = $req->fetch();
	$total = $fetch["total"];
	$npages = ceil($total/20);
	if ($npages < 1)
		$npages = 1;

	if (isset($_GET["page"]))
	{
		$page = intval($_GET["page"]);
		if ($page > $npages)
			$page = $npages;
		else if ($page < 1)
			$page = 1;
	}
	else
		$page = 1;

	$start = ($page - 1) * 20;
	$req = $db->prepare("SELECT * FROM `pictures` ORDER BY `time` DESC LIMIT ".$start.", 20 ");
	$req->execute(array());
	$pics = $req->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Gallerie</title>
	<link rel="stylesheet" type="text/css" href="/Camagru/style/style.css">
	<style type="text/css">
		.pages
		{
			margin: 30px;
			display: inline-block;
			border: solid 3px <?php if (check_theme()) echo $_COOKIE["themeColor3"]; else echo "#48a7f2"; ?>;
			border-radius: 2px;
		}
		.pages .other
		{
			display: inline-block;
			border: solid 1px #efefef;
			font-size: 23px;
			padding: 10px;
			font-family: Raleway;
			background-color: <?php if (check_theme()) echo $_COOKIE["themeColor2"]; else echo "#ff9068"; ?>;
			color: #2b2b2b;
		}
		.pages .other:hover
		{
			display: inline-block;
			border: solid 2px #505050;
			border-style: inset;
			font-size: 23px;
			padding: 10px;
			font-family: Raleway;
			background-color: <?php if (check_theme()) echo $_COOKIE["themeColor1"]; else echo "#fd746c"; ?>;
			color: #2b2b2b;
		}
		.pages .actual
		{
			position: relative;
			display: inline-block;
			border: solid 2px #505050;
			border-style: inset;
			font-size: 23px;
			padding: 10px;
			font-family: Raleway;
			background-color: <?php if (check_theme()) echo $_COOKIE["themeColor1"]; else echo "#fd746c"; ?>;
			color: #2b2b2b;
		}
	</style>
</head>
<body>
	<?php
	if (ft_is_logged($db) == TRUE)
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner_logged.php");}
	else
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner.php");}
	?>
	<div class="gallery">
		<h1 style="color: <?php if (check_theme()) echo $_COOKIE["themeColor3"]; else echo "#48A7F2"; ?>;" >Gallerie</h1>
		<div class="gallery-pics">
			<?php
			foreach ($pics as $element)
			{
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$element["path"]))
				{
					?>
					<div class="pics" >
						<a href="/Camagru/account/profile.php?login=<?php echo htmlspecialchars(ft_login_byid($element["userid"], $db)); ?>" ><span class="login" ><?php echo htmlspecialchars(ft_login_byid($element["userid"], $db)); ?></span></a>
						<a href="?picture=<?php echo $element["id"]; ?>"><img src="<?php echo $element["path"]; ?>"></a>
						<svg class="like-button" onclick="ft_like(<?php echo $element["id"]; ?>)" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
							<path id="like-path<?php echo $element["id"]; ?>" style="fill:<?php if (ft_is_logged($db) && ft_liked($_SESSION["user-id"], $element["id"], $db)) echo "#D75A4A"; else echo "#b2b2b2"; ?>;" d="M24.85,10.126c2.018-4.783,6.628-8.125,11.99-8.125c7.223,0,12.425,6.179,13.079,13.543c0,0,0.353,1.828-0.424,5.119c-1.058,4.482-3.545,8.464-6.898,11.503L24.85,48L7.402,32.165c-3.353-3.038-5.84-7.021-6.898-11.503c-0.777-3.291-0.424-5.119-0.424-5.119C0.734,8.179,5.936,2,13.159,2C18.522,2,22.832,5.343,24.85,10.126z"/>
						</svg>
						<span class="like-nbr" id="like-nbr<?php echo $element["id"]; ?>"><?php echo $element["likes"]; ?></span>
						<a href="?picture=<?php echo $element["id"]; ?>">
							<div class="comment-button">
								<svg class="bulle" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 511.626 511.627" style="enable-background:new 0 0 511.626 511.627;" xml:space="preserve">
									<g>
										<path d="M477.364,127.481c-22.839-28.072-53.864-50.248-93.072-66.522c-39.208-16.274-82.036-24.41-128.479-24.41c-46.442,0-89.269,8.136-128.478,24.41c-39.209,16.274-70.233,38.446-93.074,66.522C11.419,155.555,0,186.15,0,219.269c0,28.549,8.61,55.299,25.837,80.232c17.227,24.934,40.778,45.874,70.664,62.813c-2.096,7.611-4.57,14.842-7.426,21.7c-2.855,6.851-5.424,12.467-7.708,16.847c-2.286,4.374-5.376,9.23-9.281,14.555c-3.899,5.332-6.849,9.093-8.848,11.283c-1.997,2.19-5.28,5.801-9.851,10.848c-4.565,5.041-7.517,8.33-8.848,9.853c-0.193,0.097-0.953,0.948-2.285,2.574c-1.331,1.615-1.999,2.419-1.999,2.419l-1.713,2.57c-0.953,1.42-1.381,2.327-1.287,2.703c0.096,0.384-0.094,1.335-0.57,2.854c-0.477,1.526-0.428,2.669,0.142,3.429v0.287c0.762,3.234,2.283,5.853,4.567,7.851c2.284,1.992,4.858,2.991,7.71,2.991h1.429c12.375-1.526,23.223-3.613,32.548-6.279c49.87-12.751,93.649-35.782,131.334-69.094c14.274,1.523,28.074,2.283,41.396,2.283c46.442,0,89.271-8.135,128.479-24.414c39.208-16.276,70.233-38.444,93.072-66.517c22.843-28.072,34.263-58.67,34.263-91.789C511.626,186.154,500.207,155.555,477.364,127.481z M445.244,292.075c-19.896,22.456-46.733,40.303-80.517,53.529c-33.784,13.223-70.093,19.842-108.921,19.842c-11.609,0-23.98-0.76-37.113-2.286l-16.274-1.708l-12.277,10.852c-23.408,20.558-49.582,36.829-78.513,48.821c8.754-15.414,15.416-31.785,19.986-49.102l7.708-27.412l-24.838-14.27c-24.744-14.093-43.918-30.793-57.53-50.114c-13.61-19.315-20.412-39.638-20.412-60.954c0-26.077,9.945-50.343,29.834-72.803c19.895-22.458,46.729-40.303,80.515-53.531c33.786-13.229,70.089-19.849,108.92-19.849c38.828,0,75.13,6.617,108.914,19.845c33.783,13.229,60.62,31.073,80.517,53.531c19.89,22.46,29.834,46.727,29.834,72.802S465.133,269.615,445.244,292.075z"/>
									</g>
								</svg>
								<svg class="bulle-text" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve">
									<g>
										<path d="M8,22c-4.411,0-8,3.589-8,8s3.589,8,8,8s8-3.589,8-8S12.411,22,8,22z"/>
										<path d="M52,22c-4.411,0-8,3.589-8,8s3.589,8,8,8s8-3.589,8-8S56.411,22,52,22z"/>
										<path d="M30,22c-4.411,0-8,3.589-8,8s3.589,8,8,8s8-3.589,8-8S34.411,22,30,22z"/>
									</g>
								</svg>
							</div>
						</a>
					</div>
					<?php
				}
			}
			?>
		</div>
		<?php
		if ($npages > 0)
		{
		?>
		<div class="pages" >
			<?php
			if ($npages < 10)
			{
				for ($i=1; $i <= $npages ; $i++)
				{
					if ($i == $page)
						echo "<a href=\"?page=".$i."\"><div class=\"actual\" >".$i."</div></a>";
					else
						echo "<a href=\"?page=".$i."\"><div class=\"other\" >".$i."</div></a>";
				}
			}
			else
			{
				for ($i=1; $i <= 4 ; $i++)
				{
					if ($i == $page)
						echo "<a href=\"?page=".$i."\"><div class=\"actual\" >".$i."</div></a>";
					else
						echo "<a href=\"?page=".$i."\"><div class=\"other\" >".$i."</div></a>";
				}
				echo "<div class=\"other\" >...</div>";
				for ($i=($npages - 3); $i <= $npages ; $i++)
				{
					if ($i == $page)
						echo "<a href=\"?page=".$i."\"><div class=\"actual\" >".$i."</div></a>";
					else
						echo "<a href=\"?page=".$i."\"><div class=\"other\" >".$i."</div></a>";
				}
			}
			?>
		</div>
		<?php
		}
		?>
	</div>
	<?php include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/footer.php"); ?>
	<script type="text/javascript" src="/Camagru/scripts/script.js" ></script>
</body>
</html>
<?php
}
?>