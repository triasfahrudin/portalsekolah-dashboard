<!DOCTYPE html>
<html lang="en">

<head>
   <link rel="shortcut icon" href="https://tpp.disdik.jambiprov.go.id/presensi/favicon.ico">
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>PRESENSI ONLINE V.02</title>
   <meta name="description" content="Source code generated using layoutit.com">
   <meta name="author" content="LayoutIt!">
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.0/css/bootstrap.min.css" /> -->
   <link rel="stylesheet" type="text/css" href="<?php echo site_url('assets/signin/css/bootstrap.css'); ?>">
   <link href='https://fonts.googleapis.com/css?family=Work+Sans:400,300,700' rel='stylesheet' type='text/css'>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/jquery.jqplot.min.css" />
   <link rel="stylesheet" type="text/css" href="<?php echo site_url('assets/css/login-page.css'); ?>">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/red/pace-theme-flash.css" />

   <!-- <link href="css/style.css" rel="stylesheet"> -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.0/js/bootstrap.min.js"></script>
   <script src="https://www.google.com/recaptcha/api.js?onload=CaptchaCallback&render=explicit&hl=id" async defer></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/js/all.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/jquery.jqplot.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/plugins/jqplot.barRenderer.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/plugins/jqplot.categoryAxisRenderer.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/plugins/jqplot.pointLabels.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/plugins/jqplot.enhancedLegendRenderer.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/jsqr@1.1.1/dist/jsQR.js"></script>
   <link href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">
   <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>

   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
   <!-- <script src="js/scripts.js"></script> -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.1/howler.min.js"></script>
   <style type="text/css">
   </style>
</head>

<body>
   <div class="container-fluid">
      <div class="row">
         <div class="col-md-12">
            <nav class="navbar navbar-expand-lg navbar-dark bg-success" style="margin-bottom:15px">
               <img src="<?= site_url('uploads/logo_dinas.png'); ?>" width="45" alt="" class="d-inline-block align-middle mr-2">
               <a class="navbar-brand" href="http://tpp.disdik.jambiprov.go.id/presensi">PORTAL SEKOLAH</a>
               <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
               </button>
               <div class="collapse navbar-collapse" id="navbarSupportedContent">
                  <ul class="navbar-nav ml-auto">

                     

                     <!-- <li class="nav-item">
                           <a class="nav-link" href="">USER LOGIN</a>
                        </li> -->

                     <!-- <li class="nav-item">
                           <a class="nav-link" href="<?php echo site_url('signin/form-presensi-pulang') ?> ">Presensi Pulang</a>
                        </li> -->
                     <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           INFORMASI PEGAWAI
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                           <a class="dropdown-item" href="#">PNS Ulangtahun</a>
                           <a class="dropdown-item" href="#">Proyeksi PNS Pensiun</a>
                           
                        </div>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="#modal-login" data-toggle="modal" data-target="#modal-login">USER LOGIN</a>
                     </li>
                  </ul>
               </div>
            </nav>
            <div class="row">
               <div class="col-md-8">
                  <?php if(isset($output)){ echo $output; }else{ include $page_name . ".php"; } ?>
               </div>
               <div class="col-md-4">

                  <div class="card">
                     <h5 class="card-header">
                        <i class="fas fa-id-card-alt"></i> Pegawai Terakhir Presensi
                     </h5>
                     <div class="card-body" id="presensi_terakhir_div">
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="modal fade" id="modal-penjelasan-presensi" tabindex="-1" role="dialog" aria-labelledby="modalSayaLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="modalSayaLabel">Penjelasan Nilai Presensi</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <p>Nilai presensi sekolah dihitung berdasarkan nilai total presensi pegawai dengan ketentuan perhitungan sebagai berikut</p>
               <table class="table table-hover table-striped" id="datatable_presensi_sma">
                  <thead class="thead-dark">
                     <tr>
                        <th scope="col">Masuk</th>
                        <th scope="col">Pulang</th>
                        <th scope="col">Nilai</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach ($penjelasan_presensi->result_array() as $row) { ?>
                        <tr>
                           <td><?php echo $row['status_masuk']; ?></td>
                           <td><?php echo $row['status_pulang']; ?></td>
                           <td><?php echo $row['nilai']; ?></td>
                        </tr>
                     <?php } ?>
                  </tbody>
               </table>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
               <!-- <button type="button" class="btn btn-primary">Oke</button> -->
            </div>
         </div>
      </div>
   </div>
   <div class="modal fade" id="modal-login" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="exampleModalLabel">Login</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <form method="POST" action="" id="form_login">
                  <input type="hidden" name="login_qr_code" id="login_qr_code" value="">
                  <fieldset>
                     <div class="form-group">
                        <label for="exampleInputEmail1">Username</label>
                        <input type="text" class="form-control" placeholder="" name="username" id="username">
                     </div>
                     <div class="form-group">
                        <label for="exampleInputPassword1">Password</label>
                        <input type="password" class="form-control" placeholder="" name="password" id="password">
                     </div>
                     <div class="form-group">
                        <?php if (!stripos(base_url(), '127.0.0.1')) { ?>
                           <?php echo $this->recaptcha->render(); ?>
                        <?php } ?>
                     </div>
                     <button type="submit" class="btn btn-primary">Submit</button>
                  </fieldset>
               </form>
            </div>

         </div>
      </div>
   </div>

   <script type="text/javascript">
      <?php if (has_alert()) :
         foreach (has_alert() as $type => $message) : ?>
            <?php if ($type === 'alert-danger') { ?>
               swal({
                  html: true,
                  title: 'Error !',
                  text: '<?php echo trim(preg_replace("/\s+/", " ", $message)); ?>',
                  type: 'error',
                  confirmButtonText: 'OK'
               });
            <?php } elseif ($type === 'alert-warning') { ?>
               swal({
                  html: true,
                  title: 'Peringatan',
                  text: '<?php echo $message; ?>',
                  type: 'warning',
                  confirmButtonText: 'Ok'
               });
            <?php } elseif ($type === 'alert-success') { ?>
               swal({
                  html: true,
                  title: 'Berhasil',
                  text: '<?php echo $message; ?>',
                  type: 'success',
                  confirmButtonText: 'Ok'
               });
            <?php } elseif ($type === 'alert-info') { ?>

               swal({
                  html: true,
                  title: 'Informasi',
                  text: '<?php echo $message; ?>',
                  type: 'info',
                  confirmButtonText: 'Ok'
               });

            <?php }; ?>
      <?php endforeach;
      endif; ?>
   </script>

   <script>
      <?php $this->load->config('recaptcha', true); ?>
      var CaptchaCallback = function() {
         $('.g-recaptcha').each(function(index, el) {
            grecaptcha.render(el, {
               'sitekey': '<?php echo $this->config->item('recaptcha_sitekey', 'recaptcha') ?>'
            });
         });
      };


      (function worker() {
         $.ajax({
            url: '<?php echo site_url('signin/pegawai-terakhir-presensi') ?>',
            success: function(data) {
               $('#presensi_terakhir_div').html(data.presensi_terakhir_div);

               if (data.play_sound == 'true') {
                  var sound = new Howl({
                     src: ['<?php echo site_url('uploads/ting_sound.mp3') ?>'],
                     volume: 1.0,
                     onend: function() {

                     }
                  });
                  sound.play()
               }

            },
            complete: function() {
               setTimeout(worker, (1000 * 10));
            }
         });
      })();
   </script>
</body>

</html>