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
      <!-- <link href="css/style.css" rel="stylesheet"> -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.0/js/bootstrap.min.js"></script>      
      <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/js/all.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/jsqr@1.1.1/dist/jsQR.js"></script>
      <!-- <script src="js/scripts.js"></script> -->
      <style type="text/css">
         
      </style>
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
                          <?php if(isset($keterangan)){ ?>           
                            <div class="alert alert-warning">
                              <strong class="animated infinite slow flash delay-1s"><?php echo $keterangan;?></strong>
                            </div>
                           <?php } ?>
                           
                           <div id="loadingMessage">Tidak dapat mengakses video stream (pastikan anda mengaktifkan webcam)</div>
                           <div class="text-center">
                            <canvas id="canvas" hidden style="width: 35%"></canvas>
                            <div id="output" hidden>
                              <div id="outputMessage" class="alert alert-warning"><h3>Hadapkan Kode QR ke Kamera</h3></div>
                              <div hidden><b>Data:</b> <span id="outputData"></span></div>
                           </div>
                           </div>                           
                           
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
      </div>
      
     <form method="POST" action="" id="form_login">
        <input type="hidden" name="login_qr_code" id="login_qr_code" value="">   
     </form>


      <script>
          
          var video = document.createElement("video");
          var canvasElement = document.getElementById("canvas");
          var canvas = canvasElement.getContext("2d");
          var loadingMessage = document.getElementById("loadingMessage");
          var outputContainer = document.getElementById("output");
          var outputMessage = document.getElementById("outputMessage");
          var outputData = document.getElementById("outputData");
          
         
          function drawLine(begin, end, color) {
            canvas.beginPath();
            canvas.moveTo(begin.x, begin.y);
            canvas.lineTo(end.x, end.y);
            canvas.lineWidth = 4;
            canvas.strokeStyle = color;
            canvas.stroke();
          }
         
          // Use facingMode: environment to attemt to get the front camera on phones
          navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
            
              video.srcObject = stream;
              video.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
              video.play();
              requestAnimationFrame(tick);  
                        
          });
         
          function tick() {
            loadingMessage.innerText = "⌛ Loading video..."
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
              loadingMessage.hidden = true;
              canvasElement.hidden = false;
              outputContainer.hidden = false;
         
              canvasElement.height = video.videoHeight;
              canvasElement.width = video.videoWidth;
              canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
              var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
              var code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: "dontInvert",
              });

              if (code) {
                drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
                drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
                drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
                drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
                var login = code.data;
                
                $('#login_qr_code').val(login);
                $("#form_login").submit();
                return;
         
              } else {
                // outputMessage.hidden = false;
                // outputData.parentElement.hidden = true;

              }
            }
            requestAnimationFrame(tick);
          }         
         
      </script>
   </body>
</html>