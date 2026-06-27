<!DOCTYPE html>
<html lang="en">

<head>
   <link rel="shortcut icon" href="https://portalsekolah.disdik.jambiprov.go.id/favicon.ico">
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Portal Sekolah Disdik Prov. Jambi</title>
   <meta name="Dapodik & The Backbone" content="Portal Sekolah Disdik Prov Jambi">
   <meta name="Hely Kurniawan, S.Kom., M.S.I" content="LayoutIt!">
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.0/css/bootstrap.min.css" /> -->
   <link rel="stylesheet" type="text/css" href="<?php echo site_url('assets/signin/css/bootstrap.css'); ?>">
   <link href='https://fonts.googleapis.com/css?family=Work+Sans:400,300,700' rel='stylesheet' type='text/css'>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/jquery.jqplot.min.css" />
   <link rel="stylesheet" type="text/css" href="<?php echo site_url('assets/css/login-page.css'); ?>">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/red/pace-theme-flash.css" />

   <!-- <link href="css/style.css" rel="stylesheet"> -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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

   <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js" integrity="sha512-wT7uPE7tOP6w4o28u1DN775jYjHQApdBnib5Pho4RB0Pgd9y7eSkAV1BTqQydupYDB9GBhTcQQzyNMPMV3cAew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

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
            <nav class="navbar navbar-expand-lg navbar-dark bg-info" style="margin-bottom:15px">
               <img src="<?= site_url('uploads/logo_dinas.png'); ?>" width="45" alt="" class="d-inline-block align-middle mr-2">
               <a class="navbar-brand" href="https://portalsekolah.disdik.jambiprov.go.id/"><b>PORTAL SEKOLAH</b></a>
               <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
               </button>
               <div class="collapse navbar-collapse" id="navbarSupportedContent">
                  <ul class="navbar-nav ml-auto">



                     <!-- <li class="nav-item">
                           <a class="nav-link" href="">USER LOGIN</a>
                        </li> -->
                        
                        
                        
                           </li>
                     
                       <li class="nav-item">
                        <a class="nav-link" href="#" target="_blank"><i class="fas fa-map-signs" aria-hidden="true"></i>&nbsp;DASHBOARD</a>
                     </li>

                     <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           <i class="fa fa-search" aria-hidden="true"></i>&nbsp;MONITORING GURU
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                           <a class="dropdown-item" onclick="Cookies.set('kabupaten_id', '')" href="<?php echo site_url('signin/get-monitoring/sma') ?>">Guru SMA</a>
                           <a class="dropdown-item" onclick="Cookies.set('kabupaten_id', '')" href="<?php echo site_url('signin/get-monitoring/smk') ?>">Guru SMK</a>
                           <a class="dropdown-item" onclick="Cookies.set('kabupaten_id', '')" href="<?php echo site_url('signin/get-monitoring/slb') ?>">Guru SLB</a>
                        </div>
                     </li>

                     <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           <i class="fa fa-user" aria-hidden="true"></i>&nbsp;SIBISMAKLB
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                           <a class="dropdown-item" onclick="Cookies.set('kabupaten_id', '')" href="<?php echo site_url('signin/get-sibismaklb/sma') ?>">SIBISMA</a>
                           <a class="dropdown-item" onclick="Cookies.set('kabupaten_id', '')" href="<?php echo site_url('signin/get-sibismaklb/smk') ?>">SIBISMK</a>
                           <a class="dropdown-item" onclick="Cookies.set('kabupaten_id', '')" href="<?php echo site_url('signin/get-sibismaklb/slb') ?>">SIBISLB</a>
                        </div>
                     </li>

                     <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           <i class="fa fa-users" aria-hidden="true"></i>&nbsp;INFORMASI GURU
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                           <a class="dropdown-item" onclick="Cookies.set('kabupaten_id', '')" href="<?php echo site_url('signin/get-pegawai-pensiun') ?>">Proyeksi PNS Pensiun</a>
                           <a class="dropdown-item" onclick="Cookies.set('kabupaten_id', '')" href="<?php echo site_url('signin/get-pegawai-ultah') ?>">PNS Ulang Tahun</a>

                        </div>
                     </li>
                     
                       <li class="nav-item">
                        <a class="nav-link" href="https://wa.me/6285383868643?text=Hallo%20Disdik Prov. Jambi,%20Ada yang bisa kami bantu?%20" target="_blank"><i class="fa fa-phone-square" aria-hidden="true"></i>&nbsp;SUPPORT</a>
                     </li>
                     
                    
                      <li class="nav-item">
                        <a class="nav-link" href="#modal-login" data-toggle="modal" data-target="#modal-login"><i class="fa fa-user-circle"></i>&nbsp;USER LOGIN</a>
                     </li>
                        
                     
                  </ul>
               </div>
            </nav>
            
             <marquee direction="" scrollamount="4" width="100%" style="background: rgba(0,0,0,5);font-family: Verdana; font-size: 13px; color: rgb(255, 215, 0); width: 100%;"><b>#mantap_bekerja_tumbuh_bersama_melayani_dengan_prima</b></marquee>
            
            <center>
               <div class="card-body">
                  <?php if (isset($output)) {
                     echo $output;
                  } else {
                     include $page_name . ".php";
                  } ?>
               </div>
            </center>
            <center>
               <img src="<?php echo site_url('uploads/pemprov.png') ?>" class="img-responsive" style="width:60px;height:70px" title="Pemerintah Provinsi Jambi" />&nbsp;&nbsp;

               <img src="<?php echo site_url('uploads/disdik_new.png') ?>" class="img-responsive" style="width:80px;height:80px" title="Dinas Pendidikan Provinsi Jambi" />&nbsp;&nbsp;


               <img src="<?php echo site_url('uploads/jm.png') ?>" class="img-responsive" style="width:140px;height:80px" title="Jambi MANTAP" />&nbsp;

               <img src="<?php echo site_url('uploads/logo_berakhlak.png') ?>" class="img-responsive" style="width:160px;height:60px" title="Implementasi Core Values" />

               <img src="<?php echo site_url('uploads/logo_evp.png') ?>" class="img-responsive" style="width:160px;height:60px" title="Employer Branding ASN" />
            </center><br>

            <h6 class="card-footer">
                <!-- <center><font size="1.5" color="#2986CC">Hak Cipta © Disdik Prov. Jambi. 2024. All right Reserved. <b>BTIKP</b></font></center>
                                        
                                         <center><font size="1.5" color="#2986CC">Sumber Data : API Backbone Dapodik Pusdatin Kemendikbud Ristek RI</a></font></center>-->

         </div>
      </div>
   </div>


   <script type='text/javascript' src="https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js"></script>

   <div class="modal fade" id="modal-login" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title-login" id="exampleModalLabel">Login</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <form method="POST" action="<?php echo site_url('signin/index') ?>" id="form_login" class="form_login">
                  <input type="hidden" name="login_qr_code" id="login_qr_code" value="">
                  <fieldset>
                     <div class="form-group">
                        <label for="exampleInputEmail1">Username</label>
                        <input type="text" class="form-control" placeholder="" name="username" id="username" placeholder="">
                        <small id="emailHelp" class="form-text text-muted">Gunakan NIK sebagai username anda jika login sebagai pegawai.</small>
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
                     <div class="form-group d-flex justify-content-between align-items-center">
                        <button type="submit" class="btn btn-primary" name="submit" value="login">Submit</button>
                        <a href="#" class="ml-2" onclick="$('.form_login').hide();$('.reset-form').show();$('.modal-title-login').html('Reset Password');">Lupa password ?</a>
                     </div>
                  </fieldset>
               </form>
               <form onsubmit="showLoading()" aria-labelledby="form-reset" class="form reset-form" role="form" method="post" action="" accept-charset="UTF-8" style="display:none">
                  <div class="mb-3">
                     
                     <label class="form-label sr-only" for="exampleInputPassword3" style="color:#333"><b>Masukan NIK (Nomor Induk Kependudukan)</b></label>
                     <input type="text" name="txtNik" class="form-control mb-3" data-inputmask="'mask':'9999999999999999'" id="exampleInputPassword3" placeholder="16 Digit Nomor Induk Kependudukan" required>

                     <!-- <label class="form-label sr-only" for="exampleInputPassword3" style="color:#333"><b>Masukan Tanggal Lahir</b></label> -->
                     <!-- <input type="text" name="txtTglLahir" class="form-control mb-3" data-inputmask="'alias': 'date'" id="exampleInputPassword3" placeholder="Tanggal Lahir (Format: tanggal/bulan/tahun)" required> -->

                     <label class="form-label sr-only" for="exampleInputPassword3" style="color:#333"><b>Masukan Nama Ibu Kandung</b></label>
                     <input type="text" name="txtNamaIbu" class="form-control mb-3" placeholder="Nama Ibu Kandung" required>
         
                     <label class="form-label sr-only" for="exampleInputPassword2" style="color:#333"><b>Masukan No.HP/WhatsApp (WA)</b></label>
                     <input type="text" name="txtWa" class="form-control mb-3" id="exampleInputPassword2" placeholder="No.HP/WhatsApp (Password akan dikirim kenomor ini)" required>

                     <div class="form-text text-end">
                        Sudah punya akun? <a href="#" onclick="$('.form_login').show();$('.reset-form').hide();$('.modal-title-login').html('Silahkan Login');">Login</a>
                     </div>
                  </div>
                  <div class="mb-3">
                  </div>
                  <div class="mb-3">
                     <button type="submit" class="btn btn-danger btn-block" name="submit" value="reset-password">Reset Password</button>
                  </div>
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


     


      $(document).ready(function() {
         $(":input").inputmask();
       
      });
   </script>
</body>

</html>