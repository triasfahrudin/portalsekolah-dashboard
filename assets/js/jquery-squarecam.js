/*!
 * jQuery SquareCam v1.0.0 jQuery Plugin for capturing square images from your
 * https://github.com/lluisma/jquery-squarecam
 *
 * @author Lluís Martí i Garro: http://github.com/Lluisma
 * @license MIT

 * Date: 2019-02-13
 */


(function ( $ ) {


	$.fn.squarecam = function( imgId, options = null) {

		// * Private variables & constants
		// .......................................................................

		var webcamStream,
		    divobj, image,
		    video, marc,
		    canvas1, canvas2,
		    btStart, btStop;

		var msgStart = (options && options.btStartMsg) ? options.btStartMsg : 'Start WebCam';
		var msgStop  = (options && options.btCaptureMsg) ? options.btCaptureMsg : 'Capture';
		var hideImg  = (options && options.hideImage) ? options.hideImage : false;
		var dimWH    = (options && options.size) ? parseInt(options.size) : dims;
		var dimPX    = dimWH + 'px';

	    const constraints = {audio:false, video: true};


		// * Private objects
		// ...........................................................................

		divobj = $(this);
		divobj.css('position', 'relative').css('margin', '0 auto').css('width', 'auto');

		image = $("#" + imgId);
		if (hideImg) {
			image.hide();
		}

		btStart = document.createElement("button");
		btStart.type = "button";
		 $(btStart).html( msgStart )
		           .css('position', 'absolute').css('bottom', '5px').css('right', '5px').css('z-index', 100);
		btStart.onclick = function() { start(); }

		btStop = document.createElement("button");
		btStop.type = "button";
		  $(btStop).html(msgStop)
		           .css('position', 'absolute').css('bottom', '5px').css('right', '5px').css('z-index', 100)
		           .hide();
		btStop.onclick = function() { capture(); }

		video = document.createElement("video");
		video.autoplay = true;
		$(video).css('position', 'absolute') /* centered */
    	         .css('top', '0').css('left', '0').css('right', '0').css('bottom', '0').css('margin', 'auto')
                 .hide();

		framed = document.createElement("div");
		$(framed).css('position', 'absolute').css('border','4px dashed white')  /* centered */
    	         .css('top', '0').css('left', '0').css('right', '0').css('bottom', '0').css('margin', 'auto')
                 .css('z-index', '10').hide();

		canvas1 = document.createElement('canvas');
		canvas2 = document.createElement('canvas');

		this.append( video );
		this.append( framed );
		this.append( btStart );
		this.append( btStop );


		// * Public methods
		// ...........................................................................


		/**
         * Starts webcam 
         */

		function start() {

			navigator.mediaDevices.getUserMedia(constraints).then(handleSuccess).catch(handleError);

		};


        /**
         * Gets stream & frame redim
         */

		function handleSuccess(stream) {

			// Show webcam elements
			divobj.css('height', dimPX);

			// Hide image
        	if (hideImg) {
        		image.hide();
        	}

		  	video.style.display  = 'block';
	  		video.srcObject      = stream;
	  		framed.style.display = 'block';

			webcamStream         = stream;
		  	
			track       = stream.getTracks()[0];
			trackWidth  = track.getSettings().width;
			trackHeight = track.getSettings().height;

			if (trackHeight > trackWidth) { 
				width = trackHeight;
				height = trackWidth;
			} else {
				width = trackWidth;
				height = trackHeight;
			}

			scale = height/dimWH;

			height = dimWH;
			width  = dimWH * scale;

			video.style.width   = width + 'px';
			video.style.height  = height + 'px';

			framed.style.width  = height + 'px';
			framed.style.height = height + 'px';

			$(btStop).toggle();
			$(btStart).toggle();

		}

		function handleError(error) {
	  		alert('Error ' + error.code + ': ' + error.message);
		}


  	    /**
         * Takes a screenshot & stops webcam
         */

		function capture() {

			if (video.videoWidth==0) {

				console.log("No video");

			} else {

				canvas1.width  = video.videoWidth;
				canvas1.height = video.videoHeight;
				canvas1.getContext('2d').drawImage(video, 0, 0);
		

				var sX = parseInt( Math.abs(canvas1.width - canvas1.height) / 2 );
				var sY = canvas1.height * 10;

				// Crop image

				var imageData = canvas1.getContext('2d').getImageData(sX, 0, sY, sY);

				// Secondary canvas with the desired sizes and use puImageData to set the pixels

				canvas2.width  = canvas1.height;
				canvas2.height = canvas1.height;
				var ctx2 = canvas2.getContext("2d");
				ctx2.rect(0, 0, canvas1.height, canvas1.height);
				ctx2.fillStyle = 'white';
				ctx2.fill();
				ctx2.putImageData(imageData, 0, 0);

				// Uppdate the image source
				image.css('width', dimPX).css('height', dimPX);
				image.attr('src', canvas2.toDataURL("image/png"));
				// Other browsers will fall back to image/png -> 'image/webp';
				image.show();

				// Hide webcam elements
				divobj.css('height', 0);

		        video.pause();

			  	video.style.display  = 'none';
		  		framed.style.display = 'none';

				webcamStream.getVideoTracks()[0].stop();

				$(btStop).toggle();
				$(btStart).toggle();

			}

	  	};

        return this;                    // Makes the plugin method chainable

    };

 
}( jQuery ));
