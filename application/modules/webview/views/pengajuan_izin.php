<video></video>
<script>
	var constraints = { video: { width: 800, height: 800 } };

	navigator.mediaDevices.getUserMedia(constraints)
	.then(function(mediaStream) {
	  var video = document.querySelector('video');
	  video.srcObject = mediaStream;
	  video.onloadedmetadata = function(e) {
	    video.play();
	  };
	})
	.catch(function(err) { console.log(err.name + ": " + err.message); }); // always check for errors at the end.
</script>