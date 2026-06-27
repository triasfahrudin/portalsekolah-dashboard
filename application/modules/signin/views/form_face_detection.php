<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>PRESENSI | SIGNIN</title>
      <meta name="description" content="Source code generated using layoutit.com">
      <meta name="author" content="LayoutIt!">
      <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.0/css/bootstrap.min.css" /> -->
      <link rel="stylesheet" type="text/css" href="<?php echo site_url('assets/signin/css/bootstrap.css');?>">
      <link href='https://fonts.googleapis.com/css?family=Work+Sans:400,300,700' rel='stylesheet' type='text/css'>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" />      
      <link rel="stylesheet" type="text/css" href="<?php echo site_url('assets/css/login-page.css');?>">

      <style type="text/css">

        @font-face {
          font-family: 'Lato';
          font-style: normal;
          font-weight: 900;
          src: local('Lato Black'), local('Lato-Black'), url(https://fonts.gstatic.com/s/lato/v16/S6u9w4BMUTPHh50XSwiPHA.ttf) format('truetype');
        }

          #countdown {
          /*position: relative;*/
          /*font-family: sans-serif;*/
          text-transform: uppercase;
          /*font-size: 2em;*/
          /*letter-spacing: 4px;*/
          overflow: hidden;
          background: linear-gradient(90deg, #000, #fff, #000);
          background-repeat: no-repeat;
          background-size: 80%;
          animation: animate 5s linear infinite;
          -webkit-background-clip: text;
          -webkit-text-fill-color: rgba(207, 0, 15, 0.5)
        }

        @keyframes animate {
          0% { background-position: -500%; }
          100% {  background-position: 500%; }
        }
       

      </style>
      
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.0/js/bootstrap.min.js"></script>      
      <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/js/all.min.js"></script>
      
      <script src="<?php echo site_url('assets/signin/js/camvas.js');?>"></script>
      <script src="<?php echo site_url('assets/signin/js/pico.js');?>"></script>
      <script src="<?php echo site_url('assets/signin/js/lploc.js');?>"></script>   

      <script type="text/javascript">
        function countdownTimer(endquiz) {
          const difference = +new Date(endquiz) - +new Date();
          let remaining = "0";

          if (difference > 0) {
            const parts = {
              days: Math.floor(difference / (1000 * 60 * 60 * 24)),
              hours: Math.floor((difference / (1000 * 60 * 60)) % 24),
              minutes: Math.floor((difference / 1000 / 60) % 60),
              seconds: Math.floor((difference / 1000) % 60)
            };

            remaining = Object.keys(parts)
              .map(part => {
                if (!parts[part]) return;
                return `${parts[part]} ${part}`;
              })
              .join(" ");
          }

          return remaining;
        }
      </script>   

      
   </head>
   <body>          
      <div class="container-fluid">
         <div class="row">
            <div class="col-md-12">
               <nav class="navbar navbar-expand-lg navbar-dark bg-primary" style="margin-bottom:15px">
                  <a class="navbar-brand" href="#">PRESENSI PEGAWAI</a>    
                  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                  </button>  

                  <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                      <li class="nav-item active">
                        <a class="nav-link" href="<?php echo site_url('signin')?>">Beranda <span class="sr-only">(current)</span></a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('signin/form-presensi-masuk')?> ">Presensi Masuk</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" href="<?php echo site_url('signin/form-presensi-pulang')?> ">Presensi Pulang</a>
                      </li>                            
                    </ul>
                  </div>            
               </nav>
               <div class="row">
                  <div class="col-md-12">
                     <div class="card">
                        <h5 class="card-header">
                         <?php if($jenis_presensi === 'masuk'){ ?>
                           <i class="fas fa-calendar-alt"></i>&nbsp;<?php echo $tgl_sekarang?>&nbsp;Presensi Masuk
                         <?php }else{ ?>
                           <i class="fas fa-calendar-alt"></i>&nbsp;<?php echo $tgl_sekarang?>&nbsp;Presensi Keluar
                         <?php } ?> 
                        </h5>
                        <div class="card-body">
                          <!-- <div id="container"> -->
                            <div id="countdown" class="align-middle" style="padding-bottom: 0px; padding-top: 0px;text-align: center"></div>
                            <center>
                              <canvas width=640 height=480 id="video"></canvas>
                            </center> 
                          <!-- </div> -->

                          <!-- <button onclick="takeSnapshot()">Ambil Gambar</button>                   -->
                           
                           <!-- <div class="alert alert-danger" role="alert" style="font-size: 0.928571rem">
                              Tidak dapat login dengan Kode QR ? <a href="#modal-login" data-toggle="modal" data-target="#modal-login">Klik disini</a>
                           </div> -->
                        </div>
                           
                        </div>
                     </div>
                     
                  </div>

               </div>
            </div>
         </div>
    
      
     <form method="POST" action="" id="form_login">
        <input type="hidden" name="face" id="face" value="">   
     </form>

     <script>


          function takeSnapshot(){
             // buat elemen img
              var img = document.createElement('img');
              var context;

              // ambil ukuran video
              var width = 640 , height = 480;

              // buat elemen canvas
              canvas = document.createElement('canvas');
              canvas.width = width;
              canvas.height = height;

              // ambil gambar dari video dan masukan 
              // ke dalam canvas
              context = canvas.getContext('2d');
              context.drawImage(video, 0, 0, width, height);

              // render hasil dari canvas ke elemen img
              return img.src = canvas.toDataURL('image/png');
              // document.body.appendChild(img);            
          }

           (function worker() {
                var remaining = countdownTimer('<?php echo $selesai->format('Y-m-d H:i:s');?>');
                if(remaining === '0'){
                    document.getElementById("countdown").innerHTML = '<h4>Memproses data...</h4>';  
                    // window.location.replace("<?php echo site_url('signin');?>");
                   
                    var dataurl = takeSnapshot();
                    var data = new FormData();
                    data.append('imgBase64',dataurl.replace('data:image/png;base64,',''));
                    $.ajax({
                        type: "POST",
                        url: "<?php echo site_url('signin/face-detection-' . $jenis_presensi . '/' . $this->uri->segment(3))?>",
                        data: data,
                        processData: false,
                        contentType: false
                      }).done(function(o) {
                        window.location.replace("<?php echo site_url('signin/form-presensi-' . $jenis_presensi);?>");                        
                      });

                    return;

                }else{
                   document.getElementById("countdown").innerHTML = '<h4>' + "Mengambil Foto... " + remaining + '</h4>';              
                }
                
                setTimeout(worker, 100);
            })();  


          var initialized = false;
          function button_callback() {
            /*
              (0) check whether we're already running face detection
            */
            if(initialized)
              return; // if yes, then do not initialize everything again
            /*
              (1) initialize the pico.js face detector
            */
            var update_memory = pico.instantiate_detection_memory(5); // we will use the detecions of the last 5 frames
            var facefinder_classify_region = function(r, c, s, pixels, ldim) {return -1.0;};
            var cascadeurl = 'https://raw.githubusercontent.com/nenadmarkus/pico/c2e81f9d23cc11d1a612fd21e4f9de0921a5d0d9/rnt/cascades/facefinder';
            fetch(cascadeurl).then(function(response) {
              response.arrayBuffer().then(function(buffer) {
                var bytes = new Int8Array(buffer);
                facefinder_classify_region = pico.unpack_cascade(bytes);
                console.log('* facefinder loaded');
              })
            })
            /*
              (2) initialize the lploc.js library with a pupil localizer
            */
            var do_puploc = function(r, c, s, nperturbs, pixels, nrows, ncols, ldim) {return [-1.0, -1.0];};
            //var puplocurl = '../puploc.bin';
            var puplocurl = 'https://f002.backblazeb2.com/file/tehnokv-www/posts/puploc-with-trees/demo/puploc.bin'
            fetch(puplocurl).then(function(response) {
              response.arrayBuffer().then(function(buffer) {
                var bytes = new Int8Array(buffer);
                do_puploc = lploc.unpack_localizer(bytes);
                console.log('* puploc loaded');
              })
            })
            /*
              (3) get the drawing context on the canvas and define a function to transform an RGBA image to grayscale
            */
            var ctx = document.getElementsByTagName('canvas')[0].getContext('2d');
            function rgba_to_grayscale(rgba, nrows, ncols) {
              var gray = new Uint8Array(nrows*ncols);
              for(var r=0; r<nrows; ++r)
                for(var c=0; c<ncols; ++c)
                  // gray = 0.2*red + 0.7*green + 0.1*blue
                  gray[r*ncols + c] = (2*rgba[r*4*ncols+4*c+0]+7*rgba[r*4*ncols+4*c+1]+1*rgba[r*4*ncols+4*c+2])/10;
              return gray;
            }


            /*
              (4) this function is called each time a video frame becomes available
            */
            var processfn = function(video, dt) {
              // render the video frame to the canvas element and extract RGBA pixel data
              ctx.drawImage(video, 0, 0);
              var rgba = ctx.getImageData(0, 0, 640, 480).data;
              // prepare input to `run_cascade`
              image = {
                "pixels": rgba_to_grayscale(rgba, 480, 640),
                "nrows": 480,
                "ncols": 640,
                "ldim": 640
              }
              params = {
                "shiftfactor": 0.1, // move the detection window by 10% of its size
                "minsize": 100,     // minimum size of a face
                "maxsize": 1000,    // maximum size of a face
                "scalefactor": 1.1  // for multiscale processing: resize the detection window by 10% when moving to the higher scale
              }
              // run the cascade over the frame and cluster the obtained detections
              // dets is an array that contains (r, c, s, q) quadruplets
              // (representing row, column, scale and detection score)
              dets = pico.run_cascade(image, facefinder_classify_region, params);
              dets = update_memory(dets);
              dets = pico.cluster_detections(dets, 0.2); // set IoU threshold to 0.2
              // draw detections
              for(i=0; i<dets.length; ++i)
                // check the detection score
                // if it's above the threshold, draw it
                // (the constant 50.0 is empirical: other cascades might require a different one)
                if(dets[i][3]>50.0)
                {
                  var r, c, s;
                  //
                  ctx.beginPath();
                  ctx.arc(dets[i][1], dets[i][0], dets[i][2]/2, 0, 2*Math.PI, false);
                  ctx.lineWidth = 3;
                  ctx.strokeStyle = 'red';
                  ctx.stroke();
                  //
                  // find the eye pupils for each detected face
                  // starting regions for localization are initialized based on the face bounding box
                  // (parameters are set empirically)
                  // first eye
                  r = dets[i][0] - 0.075*dets[i][2];
                  c = dets[i][1] - 0.175*dets[i][2];
                  s = 0.35*dets[i][2];
                  [r, c] = do_puploc(r, c, s, 63, image)
                  if(r>=0 && c>=0)
                  {
                    ctx.beginPath();
                    ctx.arc(c, r, 1, 0, 2*Math.PI, false);
                    ctx.lineWidth = 3;
                    ctx.strokeStyle = 'red';
                    ctx.stroke();
                  }
                  // second eye
                  r = dets[i][0] - 0.075*dets[i][2];
                  c = dets[i][1] + 0.175*dets[i][2];
                  s = 0.35*dets[i][2];
                  [r, c] = do_puploc(r, c, s, 63, image)
                  if(r>=0 && c>=0)
                  {
                    ctx.beginPath();
                    ctx.arc(c, r, 1, 0, 2*Math.PI, false);
                    ctx.lineWidth = 3;
                    ctx.strokeStyle = 'red';
                    ctx.stroke();
                  }
                }
            }
            /*
              (5) instantiate camera handling (see https://github.com/cbrandolino/camvas)
            */
            var mycamvas = new camvas(ctx, processfn);
            /*
              (6) it seems that everything went well
            */
            initialized = true;
          }

          button_callback();

        </script>
      
   </body>
</html>