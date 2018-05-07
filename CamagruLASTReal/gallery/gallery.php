<?php

include ($_SERVER["DOCUMENT_ROOT"]."/Camagru/config/setup.php");
include ($_SERVER["DOCUMENT_ROOT"]."/Camagru/account/ft_account.php");
//TOUT PASSER EN INCLUDE_ONCE
if (session_status() == PHP_SESSION_NONE)
	session_start();

if (isset($_POST["like"]))
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
else if (isset($_POST["comment"]) && isset($_POST["picid"]))
{
	$req = $db->prepare("INSERT INTO `comments` (`userid`, `picid`, `content`, `time`) VALUES (?, ?, ?, ?)");
	$req->execute(array($_SESSION["user-id"], $_POST["picid"], $_POST["comment"], time()));
}

if (isset($_GET["picture"]))
{
	date_default_timezone_set("Europe/Paris");
	$req = $db->prepare("SELECT * FROM `pictures` WHERE `id` = ?");
	$req->execute(array($_GET["picture"]));
	$pic = $req->fetch();
	$req = $db->prepare("SELECT * FROM `comments` WHERE `picid` = ?");
	$req->execute(array($_GET["picture"]));
	$comments = $req->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo ft_login_byid($pic["userid"], $db); ?></title>
	<link rel="stylesheet" type="text/css" href="/Camagru/style/style.css">
</head>
<body>
	<script type="text/javascript">
		function ft_like(id)
		{
			var req = new XMLHttpRequest();
			req.onreadystatechange = function()
			{
				if (req.readyState == XMLHttpRequest.DONE && req.readyState == 4 && req.status == 200)
				{
					if (req.responseText == 1)
					{
						document.getElementById("like-path"+id).style.fill = "#D75A4A";
						document.getElementById("like-nbr"+id).innerHTML = parseInt(document.getElementById("like-nbr"+id).innerHTML) + 1;
					}
					else
					{
						document.getElementById("like-path"+id).style.fill = "#b2b2b2";
						document.getElementById("like-nbr"+id).innerHTML = parseInt(document.getElementById("like-nbr"+id).innerHTML) - 1;
					}
				}
			}
			req.open("POST", "gallery.php", true); 
			req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			req.send("like="+id);
		}
		function post_comment(login, picid, date)
		{
			var comment = new XMLHttpRequest();
			var cont = document.commentform.comment.value;
			comment.onreadystatechange = function()
			{
				if (comment.readyState == XMLHttpRequest.DONE && comment.readyState == 4 && comment.status == 200)
				{
					var post = document.createElement("div");
					post.className = "post";
					var postlogin = document.createElement("div");
					postlogin.className = "post-login";
					postlogin.innerHTML += login;
					var postdate = document.createElement("div");
					postdate.className = "post-date";
					postdate.innerHTML += date;
					var posttext = document.createElement("div");
					posttext.className = "post-text";
					posttext.innerHTML += cont;
					post.appendChild(postlogin);
					post.appendChild(postdate);
					post.appendChild(document.createElement("br"));
					post.appendChild(posttext);
					document.commentform.comment.value = "";
					document.getElementById("comment-div").appendChild(post);
				}
			}
			comment.open("POST", "gallery.php", true); 
			comment.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			comment.send("comment="+encodeURIComponent(cont)+"&picid="+picid);
		}
	</script>
	<?php
	if (ft_is_logged($db) == TRUE)
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner_logged.php");}
	else
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner.php");}
	?>
	<div class="gallery">
		<h1><?php echo ft_login_byid($pic["userid"], $db); ?></h1>
		<div class="comment-section">
			<img src="<?php echo $pic["path"]; ?>">
			<svg class="like-button" onclick="ft_like(<?php echo $pic["id"]; ?>)" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
				<path id="like-path<?php echo $pic["id"]; ?>" style="fill:<?php if (ft_liked($_SESSION["user-id"], $pic["id"], $db)) echo "#D75A4A"; else echo "#b2b2b2"; ?>;" d="M24.85,10.126c2.018-4.783,6.628-8.125,11.99-8.125c7.223,0,12.425,6.179,13.079,13.543c0,0,0.353,1.828-0.424,5.119c-1.058,4.482-3.545,8.464-6.898,11.503L24.85,48L7.402,32.165c-3.353-3.038-5.84-7.021-6.898-11.503c-0.777-3.291-0.424-5.119-0.424-5.119C0.734,8.179,5.936,2,13.159,2C18.522,2,22.832,5.343,24.85,10.126z"/>
			</svg>
			<span class="like-nbr" id="like-nbr<?php echo $pic["id"]; ?>"><?php echo $pic["likes"]; ?></span>
			<div class="comments" id="comment-div">
				<?php
				foreach ($comments as $element)
				{
					?>
					<div class="post" >
						<div class="post-login" ><?php echo ft_login_byid($element["userid"], $db); ?></div>
						<div class="post-date" ><?php echo date("F j, H:i", $element["time"]); ?></div>
						<br/>
						<div class="post-text" ><?php echo $element["content"] ?></div>
					</div>
					<?php
				}
				?>
			</div>
			<div class="post-comment" >
				<form name="commentform" onsubmit="post_comment(<?php echo "'".ft_login_byid($_SESSION["user-id"], $db)."'"; ?>, <?php echo $pic["id"]; ?>, <?php echo "'".date("F j, H:i")."'"; ?>); return false;">
					<input class="comment-text" type="text" name="comment" required maxlength="180" autocomplete="off">
					<input class="submit-button" type="image" src="/Camagru/images/send-button.svg" alt="submit" />
				</form>
			</div>
		</div>
	</div>
</body>
</html>
<?php
}
else
{
	$req = $db->prepare("SELECT * FROM `pictures` ORDER BY `time` DESC");
	$req->execute(array());
	$pics = $req->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Gallerie</title>
	<link rel="stylesheet" type="text/css" href="/Camagru/style/style.css">
</head>
<body>
	<script type="text/javascript">
		function ft_like(id)
		{
			var req = new XMLHttpRequest();
			req.onreadystatechange = function()
			{
				if (req.readyState == XMLHttpRequest.DONE && req.readyState == 4 && req.status == 200)
				{
					if (req.responseText == 1)
					{
						document.getElementById("like-path"+id).style.fill = "#D75A4A";
						document.getElementById("like-nbr"+id).innerHTML = parseInt(document.getElementById("like-nbr"+id).innerHTML) + 1;
					}
					else
					{
						document.getElementById("like-path"+id).style.fill = "#b2b2b2";
						document.getElementById("like-nbr"+id).innerHTML = parseInt(document.getElementById("like-nbr"+id).innerHTML) - 1;
					}
				}
			}
			req.open("POST", "gallery.php", true); 
			req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			req.send("like="+id);
		}
	</script>
	<?php
	if (ft_is_logged($db) == TRUE)
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner_logged.php");}
	else
		{include($_SERVER["DOCUMENT_ROOT"]."/Camagru/content/banner.php");}
	?>
	<div class="gallery">
		<h1>Gallerie</h1>
		<div class="gallery-pics">
			<?php
			foreach ($pics as $element)
			{
				if (file_exists($_SERVER["DOCUMENT_ROOT"].$element["path"]))
				{
					?>
					<div class="pics" >
						<span class="login" ><?php echo ft_login_byid($element["userid"], $db); ?></span>
						<a href="?picture=<?php echo $element["id"]; ?>"><img src="<?php echo $element["path"]; ?>"></a>
						<svg class="like-button" onclick="ft_like(<?php echo $element["id"]; ?>)" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
							<path id="like-path<?php echo $element["id"]; ?>" style="fill:<?php if (ft_liked($_SESSION["user-id"], $element["id"], $db)) echo "#D75A4A"; else echo "#b2b2b2"; ?>;" d="M24.85,10.126c2.018-4.783,6.628-8.125,11.99-8.125c7.223,0,12.425,6.179,13.079,13.543c0,0,0.353,1.828-0.424,5.119c-1.058,4.482-3.545,8.464-6.898,11.503L24.85,48L7.402,32.165c-3.353-3.038-5.84-7.021-6.898-11.503c-0.777-3.291-0.424-5.119-0.424-5.119C0.734,8.179,5.936,2,13.159,2C18.522,2,22.832,5.343,24.85,10.126z"/>
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
	</div>
</body>
</html>
<?php
}
?>