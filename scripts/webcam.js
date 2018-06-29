/* Webcam activation */

var filter = "unset"
var cam = false;
navigator.mediaDevices.getUserMedia(
{
	audio: false,
	video:
	{
		width: 600,
		height: 450
	}
}).then(stream =>
{
	var video = document.getElementById("video");
	video.srcObject = stream;
	cam = true;
}).catch(function()
{
cam = false;
});