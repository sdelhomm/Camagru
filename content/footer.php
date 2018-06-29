<?php
include_once ($_SERVER["DOCUMENT_ROOT"]."/Camagru/gallery/theme.php");
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="/Camagru/style/style.css">
</head>
<body>
	<div class="footer" style="background-color: <?php if (check_theme()) echo $_COOKIE["themeColor3"]; else echo "#65aee8"; ?>;">
		<span>Camagru - 2018 © <span id="pipe" >|</span> <a href="/Camagru/credits.php" style="color: #ffffff;" >Crédits</a></span>
		<span class="themesLink" onclick="ft_show_theme()" >Thèmes</span>
	</div>
	<div id="picker" class="themePicker" style="display: none;">
		<a href="/Camagru/gallery/theme.php?theme=orange" ><div class="themeColor" style="background-color: #FC826D; border: inset 4px #48a7f2;" ></div></a>
		<a href="/Camagru/gallery/theme.php?theme=green" ><div class="themeColor" style="background-color: #4ACF8D; border: inset 4px #da5a47;"  ></div></a>
		<a href="/Camagru/gallery/theme.php?theme=purple" ><div class="themeColor" style="background-color: #D15FAF; border: inset 4px #ea3d50;"  ></div></a>
		<a href="/Camagru/gallery/theme.php?theme=red" ><div class="themeColor" style="background-color: #E6535E; border: inset 4px #94bd34;"  ></div></a>
	</div>
	<script type="text/javascript">
		function ft_show_theme()
		{
			if (document.getElementById("picker").style.display == "none")
				document.getElementById("picker").style.display = "inline-block";
			else
				document.getElementById("picker").style.display = "none";
		}
	</script>
</body>
</html>