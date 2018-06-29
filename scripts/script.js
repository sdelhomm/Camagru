/* Gallery page functions */

function escapeHtml(text)
{
	return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
function get_get()
{
	var $_GET = {};
	if(document.location.toString().indexOf('?') !== -1)
	{
		var query = document.location
		.toString()
		.replace(/^.*?\?/, '')
		.replace(/#.*$/, '')
		.split('&');

		for(var i = 0, l = query.length; i < l; i++)
		{
			var aux = decodeURIComponent(query[i]).split('=');
			$_GET[aux[0]] = aux[1];
		}
		return ($_GET);
	}
	else
	{
		return(undefined);
	}
}

function ft_like(id)
{
	var req = new XMLHttpRequest();
	req.onreadystatechange = function()
	{
		if (req.readyState == XMLHttpRequest.DONE && req.readyState == 4 && req.status == 200)
		{
			if (req.responseText.trim() == "1")
			{
				document.getElementById("like-path"+id).style.fill = "#D75A4A";
				document.getElementById("like-nbr"+id).innerHTML = parseInt(document.getElementById("like-nbr"+id).innerHTML) + 1;
			}
			else if (req.responseText.trim() == "0")
			{
				document.getElementById("like-path"+id).style.fill = "#b2b2b2";
				document.getElementById("like-nbr"+id).innerHTML = parseInt(document.getElementById("like-nbr"+id).innerHTML) - 1;
			}
			else
				window.location.replace("/Camagru/account/login.php?from=like");
		}
	}
	req.open("POST", "/Camagru/gallery/gallery.php", true);
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	req.send("like="+encodeURIComponent(id));
}
function post_comment()
{
	var comment = new XMLHttpRequest();
	var cont = document.commentform.comment.value.substr(0, 180);
	comment.onreadystatechange = function()
	{
		if (comment.readyState == XMLHttpRequest.DONE && comment.readyState == 4 && comment.status == 200)
		{
			if (comment.responseText.trim().split(";")[0] == "ok")
			{
				cont = escapeHtml(cont);
				tab = comment.responseText.trim().split(";");
				var post = document.createElement("div");
				post.className = "post";
				var postlogin = document.createElement("div");
				postlogin.className = "post-login";
				postlogin.innerHTML += tab[1];
				var postdate = document.createElement("div");
				postdate.className = "post-date";
				postdate.innerHTML += tab[2];
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
			else
			{
				window.location.replace("/Camagru/account/login.php?from=comment");
			}
		}
	}
	comment.open("POST", "/Camagru/gallery/gallery.php", true); 
	comment.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	comment.send("comment="+encodeURIComponent(cont)+"&picid="+encodeURIComponent(get_get()['picture']));
}


/* Edit page functions */

function ft_add_delete(id)
{
	document.getElementById(id).getElementsByClassName("delete")[0].onclick = function() { ft_delete(id); }
}
function ft_update_filter()
{
	document.getElementById("no").style.filter = "grayscale(100%)";
	document.getElementById("superman").style.filter = "grayscale(100%)";
	document.getElementById("stars").style.filter = "grayscale(100%)";
	document.getElementById("shining").style.filter = "grayscale(100%)";
	document.getElementById("confetti").style.filter = "grayscale(100%)";
	document.getElementById(filter).style.filter = "grayscale(0%)";

	document.getElementById("no").style.border = "solid 3px #42A5F5";
	document.getElementById("superman").style.border = "solid 3px #42A5F5";
	document.getElementById("stars").style.border = "solid 3px #42A5F5";
	document.getElementById("shining").style.border = "solid 3px #42A5F5";
	document.getElementById("confetti").style.border = "solid 3px #42A5F5";
	document.getElementById(filter).style.border = "solid 3px #fd746c";
}
function ft_save()
{
	if (filter != "unset" && cam != false)
	{
		var vid = document.getElementById("video");
		var cnvs = document.getElementById("canvas");
		var ctx = cnvs.getContext("2d");
		cnvs.width = vid.videoWidth;
		cnvs.height = vid.videoHeight;
		ctx.drawImage(vid, 0, 0, video.videoWidth, video.videoHeight);
		var sendimg = new XMLHttpRequest();
		sendimg.onreadystatechange = function()
		{
			if (sendimg.readyState == XMLHttpRequest.DONE && sendimg.readyState == 4 && sendimg.status == 200 && sendimg.responseText.trim().split(";")[0] == "ok")
			{
				var tab = sendimg.responseText.trim().split(";");
				var div = document.createElement("div");
				div.id = tab[2];
				var img = document.createElement("img");
				img.className += " photo";
				img.src = tab[1];
				var btn = document.createElement("img");
				btn.className += " delete";
				btn.src = "/Camagru/images/x-button.svg";
				btn.onclick = function() { ft_delete(tab[2]); }
				div.prepend(btn);
				div.prepend(img);
				pics = document.getElementById("pics");
				pics.prepend(div);
			}
			else if (sendimg.readyState == 4)
			{
				alert("Erreur serveur, la photo n'a pas été prise.");
			}
		}
		sendimg.open("POST", "/Camagru/gallery/edit.php", true);
		sendimg.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		sendimg.send("sendimage="+encodeURIComponent(cnvs.toDataURL())+"&filter="+encodeURIComponent(filter));
	}
	else
	{
		alert("Ta webcam ne semble pas fonctionner correctement !")
	}
}
function ft_delete(picid)
{
	r = confirm("Es-tu certain de vouloir supprimer cette photo ?");
	if (r == true)
	{
		var delimg = new XMLHttpRequest();
		delimg.onreadystatechange = function()
		{
			if (delimg.readyState == XMLHttpRequest.DONE && delimg.readyState == 4 && delimg.status == 200 && delimg.responseText.trim() == "ok")
			{
				document.getElementById(picid).remove();
			}
			else if (delimg.readyState == 4)
			{
				alert("Erreur serveur, merci de réessayer plus tard.");
			}
		}
		delimg.open("POST", "/Camagru/gallery/edit.php", true);
		delimg.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		delimg.send("delete="+encodeURIComponent(picid));
	}
}
function ft_upload()
{
	if (document.getElementById("pngfile").files.length == 0)
		alert("Merci de sélectionner un fichier")
	else
	{
		if (filter != "unset")
		{
			var file = document.getElementById("pngfile").files[0];
			if ((file.size / 1000 / 1000) > 5)
			{
				alert("Le fichier entré est plus gros que 5mo !");
			}
			else if (file["type"] != "image/png")
			{
				alert("Le fichier entré n'est pas sous un format png valide !");
			}
			else
			{
				var reader = new FileReader();
				reader.readAsDataURL(file);
				reader.onload = function ()
				{
					var sendimg = new XMLHttpRequest();
					sendimg.onreadystatechange = function()
					{
						if (sendimg.readyState == XMLHttpRequest.DONE && sendimg.readyState == 4 && sendimg.status == 200 && sendimg.responseText.trim().split(";")[0] == "ok")
						{
							var tab = sendimg.responseText.trim().split(";");
							var div = document.createElement("div");
							div.id = tab[2];
							var img = document.createElement("img");
							img.className += " photo";
							img.src = tab[1];
							var btn = document.createElement("img");
							btn.className += " delete";
							btn.src = "/Camagru/images/x-button.svg";
							btn.onclick = function() { ft_delete(tab[2]); }
							div.prepend(btn);
							div.prepend(img);
							pics = document.getElementById("pics");
							pics.prepend(div);
						}
						else if (sendimg.readyState == 4)
						{
							alert("Erreur serveur, la photo n'a pas été sauvegardée. Merci de réessayer avec un autre fichier");
						}
					}
					sendimg.open("POST", "/Camagru/gallery/edit.php", true);
					sendimg.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					sendimg.send("sendimage="+encodeURIComponent(reader.result)+"&filter="+encodeURIComponent(filter));
				};
				reader.onerror = function (error)
				{
					alert("Erreur, le fichier n'a pas été upload");
				};
			}
			document.getElementById("pngfile").value = "";
		}
		else
		{
			alert("Merci de sélectionner un filtre.")
		}
	}
}


/* Accounts pages function */

function ft_test_password(id)
{
	var passwd = document.getElementById("regpassword").value;
	if (passwd.search(/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])/) < 0 || passwd.length < 6 || passwd.length > 32)
	{
		document.getElementById("strongPasswd").style.display = "inline-block";
		document.getElementById(id).disabled = true;
		document.getElementById(id).className = "disabledSubmit";
	}
	else
	{
		document.getElementById("strongPasswd").style.display = "none";
		document.getElementById(id).disabled = false;
		document.getElementById(id).className = "";
	}
}
function ft_test_login(id)
{
	var login = document.getElementById("reglogin").value;
	if (login.search(/^[a-zA-Z0-9-_]*$/) < 0 || login.length < 3 || login.length > 12)
	{
		document.getElementById("wrongLogin").style.display = "inline-block";
		document.getElementById(id).disabled = true;
		document.getElementById(id).className = "disabledSubmit";
	}
	else
	{
		document.getElementById("wrongLogin").style.display = "none";
		document.getElementById(id).disabled = false;
		document.getElementById(id).className = "";
	}
}
function ft_switch_notif()
{
	var req = new XMLHttpRequest();
	req.onreadystatechange = function()
	{
		if (req.readyState == XMLHttpRequest.DONE && req.readyState == 4 && req.status == 200)
		{
			if (req.responseText.trim() == "1")
			{
				document.getElementById("notifBtn").style.textAlign = "right";
				document.getElementById("notifBtn").style.backgroundColor = "#45bf45";
			}
			else if (req.responseText.trim() == "0")
			{
				document.getElementById("notifBtn").style.textAlign = "left";
				document.getElementById("notifBtn").style.backgroundColor = "#9c9c9c";
			}
			else
			{
				alert("Erreur serveur, merci de réessayer plus tard.");
			}
		}
	}
	req.open("POST", "/Camagru/account/manage.php", true); 
	req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	req.send("notif="+encodeURIComponent("ok"));
}