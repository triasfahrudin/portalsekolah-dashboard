<!DOCTYPE html>
<!--
   This is a starter template page. Use this page to start your new project from
   scratch. This page gets rid of all links and provides the needed markup only.
   -->
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta http-equiv="x-ua-compatible" content="ie=edge">
   <title>Operator - Portal Sekolah</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/css/adminlte.min.css" />
   <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
   <link rel="stylesheet" href="<?php echo site_url('assets/css/app.css?uuid=' . uniqid()) ?>" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/red/pace-theme-flash.css" />


   <?php
   if (isset($css_files)) {
      foreach ($css_files as $file) : ?>
         <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
      <?php endforeach;
   } else { ?>
      <link href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet">
      <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-lite.css" rel="stylesheet">
   <?php }; ?>

   <?php
   if (isset($js_files)) {
      foreach ($js_files as $file) : ?>
         <script src="<?php echo $file; ?>"></script>
      <?php endforeach;
   } else { ?>
      <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script> -->
      <!-- jQuery -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
      <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
      <script src="<?php echo site_url('assets/summernote/dist/summernote-lite.js') ?>"></script>
      <!-- <script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script> -->
   <?php }; ?>

   <!-- REQUIRED SCRIPTS -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/js/adminlte.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/Cookies.js/0.3.1/cookies.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
   <script src="<?php echo site_url('assets/js/app.js?uuid=' . uniqid()) ?>"></script>

   <!-- loader -->

   <link rel="stylesheet" href="<?php echo site_url('assets/simple-loader/loader.css'); ?>" />
   <script src="<?php echo site_url('assets/simple-loader/loader.js'); ?>"></script>

   <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
   <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>


   <script>
      var site_url = '<?php echo site_url('operator/') ?>';
      var int_pegawai_id = '<?php echo !empty($pegawai_id) ? $pegawai_id : 0; ?>';
   </script>

</head>

<body class="hold-transition sidebar-mini">
   <div class="wrapper">
      <!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">
         <!-- Left navbar links -->
         <ul class="navbar-nav">
            <li class="nav-item">
               <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
               <a href="<?php echo site_url('operator') ?>" class="nav-link">Beranda</a>
            </li>
            <!-- li class="nav-item d-none d-sm-inline-block">
                  <a href="#" class="nav-link">Contact</a>
               </li> -->
         </ul>
         <!-- SEARCH FORM -->

      </nav>
      <!-- /.navbar -->
      <!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
         <!-- Brand Logo -->

         <!-- Sidebar -->
         <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
               <div class="image">
                  <img src="https://ui-avatars.com/api/?name=<?php echo $nama_lengkap; ?>&rounded=true&background=0D8ABC&color=fff" class="img-circle elevation-2">
                  <!-- <img src="https://via.placeholder.com/150/FF0000" alt="User Image"> -->
               </div>
               <div class="info">
                  <a href="#" class="d-block"><?php echo $nama_lengkap; ?></a>
               </div>
            </div>
            <!-- Sidebar Menu -->
            <nav class="mt-2 main-menu">
               <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                  <li class="nav-item">
                     <a href="<?php echo site_url('operator') ?>" class="nav-link">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Beranda</p>
                     </a>
                  </li>
                  <li class="nav-item">
                     <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-file-pdf"></i>
                        <p>Petunjuk Penggunaan</p>
                     </a>
                  </li>
                  <li class="nav-header">DATA</li>
                  <li class="nav-item">
                      <!-- --> 
                     <!--<a href="<?php echo site_url('operator/kelola-kelas') ?>" class="nav-link">--> 
                     <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Data Kelas</p>
                     </a>
                  </li>
                  <li class="nav-item">
                         
                     <!-- <a href="<?php echo site_url('operator/kelola-matapelajaran') ?>" class="nav-link">-->
                     <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-book-open"></i>
                        <p>Data Mata Pelajaran</p>
                     </a>
                  </li>
                  <li class="nav-item">
                        
                      <!--<a href="<?php echo site_url('operator/kelola-pegawai') ?>" class="nav-link">-->
                     <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Data Pegawai</p>
                     </a>
                  </li>
           

                  <li class="nav-item">
                     <!--<a href="<?php echo site_url('operator/kelola-siswa') ?>" class="nav-link">-->
                        <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Data Siswa</p>
                     </a>
                  </li>

                  <li class="nav-item">
                     <!--<a href="<?php echo site_url('operator/jenis-prestasi') ?>" class="nav-link">-->
                          <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-trophy"></i>
                        <p>Prestasi Siswa</p>
                     </a>
                  </li>

                  <li class="nav-item">
                     <!--<a href="<?php echo site_url('operator/jenis-ekstrakulikuler') ?>" class="nav-link">-->
                          <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Ekstrakulikuler Siswa</p>
                     </a>
                  </li>


                  <li class="nav-item">
                     <a href="<?php echo site_url('operator/kelola-alumni') ?>" class="nav-link">
                        <i class="nav-icon fas fa-graduation-cap"></i>
                        <p>Alumni</p>
                     </a>
                  </li>



                  <li class="nav-header">SETTINGS</li>

                  <li class="nav-item">
                     <a href="<?php echo site_url('operator/profile-sekolah/edit/' . $sekolah_id) ?>" class="nav-link">
                        <i class="nav-icon fas fa-school"></i>
                        <p>Profile Sekolah</p>
                     </a>
                  </li>
                  <li class="nav-item">
                     <a href="<?php echo site_url('operator/ganti-password') ?>" class="nav-link">
                        <i class="nav-icon fas fa-lock"></i>
                        <p>Ganti Password</p>
                     </a>
                  </li>
                  <li class="nav-item">
                     <a href="<?php echo site_url('signout') ?>" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Keluar</p>
                     </a>
                  </li>


               </ul>
            </nav>
            <!-- /.sidebar-menu -->
         </div>
         <!-- /.sidebar -->
      </aside>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
         <!-- Content Header (Page header) -->
         <div class="content-header">
            <div class="container-fluid">
               <div class="row mb-2">
                  <div class="col-sm-6">
                     <h1 class="m-0 text-dark"><?php echo $page_title ?></h1>
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-6">
                     <?php echo $this->breadcrumbs->show(); ?>
                  </div>
                  <!-- /.col -->
               </div>
               <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
         </div>
         <!-- /.content-header -->
         <!-- Main content -->
         <div class="content">
            <div class="container-fluid">
               <div class="row">
                  <div class="col-lg-12">
                     <div class="card card-primary card-outline">
                        <div class="card-body">
                           <?php if (isset($keterangan)) { ?>
                              <div class="alert alert-warning">
                                 <strong class="animated infinite slow flash delay-1s">INFORMASI : </strong> <?php echo $keterangan; ?>
                              </div>
                           <?php } ?>
                           <?php if (isset($output)) {
                              echo $output;
                           } else {
                              include $page_name . ".php";
                           } ?>

                           <?php if (isset($script)) { ?>
                              <script>
                                 <?php echo $script; ?>
                              </script>
                           <?php } ?>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
         </div>
         <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
         <!-- Control sidebar content goes here -->
         <div class="p-3">
            <h5>MENU</h5>
            <p><a href="<?php echo site_url('operator/ubah-password') ?>">Ubah Password</a></p>
            <p><a href="<?php echo site_url('operator/profile') ?>">Profile</a></p>
            <p><a href="<?php echo site_url('signout') ?>">Keluar</a></p>
         </div>
      </aside>
      <!-- /.control-sidebar -->
      <!-- Main Footer -->
  <footer class="main-footer">
            <!-- To the right -->
            <div class="float-right d-none d-sm-inline">
               <font size="2" color="#2986CC">Portal Sekolah Versi 1. HK</font>
            </div>
            <!-- Default to the left -->
             <font size="2" color="#2986CC">© 2024-2025. Disdik Prov. Jambi</font>
         </footer>
   </div>
   <!-- ./wrapper -->

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

      <?php if ($this->uri->segment(2) === 'kelola-pegawai') { ?>

         // var field = [ 'pengampu' ];

         // filter_field(field,'close');
         // var jabatan = $('#field-jabatan').val();

         // if(jabatan === 'GURU'){
         //    filter_field(field,'open');
         // }

         // $('#field-jabatan').on('change', function() {
         //       var jabatan = $('#field-jabatan').val();

         //       filter_field(field,'close');

         //       if(jabatan === 'GURU'){
         //          filter_field(field,'open');
         //       }
         //  })

      <?php } ?>

      $(document).ready(function() {
         $('.texteditor').summernote({
            height: 300,
            maximumImageFileSize: 1048576,
            callbacks: {
               onImageUpload: function(files, editor, welEditable) {
                  for (var i = files.length - 1; i >= 0; i--) {
                     sendFile(files[i], this);
                  }
               },
               onChange: function(contents, $editable) {}
            }
         });
      });

      function sendFile(file, el) {
         var form_data = new FormData();
         form_data.append('file', file);
         $.ajax({
            data: form_data,
            type: "POST",
            url: '<?php echo site_url('editor/upload') ?>',
            cache: false,
            contentType: false,
            processData: false,
            success: function(url) {
               $(el).summernote('editor.insertImage', url);
            }
         });
      }

      // function set_status(status, id) {
      //    $.ajax({
      //       url: "<?php echo site_url('operator/ekstrakulikuler_set_status'); ?>",
      //       type: 'post',
      //       data: {
      //          status: status,
      //          ekstrakulikuler_id: id
      //       }
      //    }).done(function(msg) {
      //       $('#ekstra_id_' + id).html(msg);            
      //    });
      // }

      // $(function() {
      $('.togglebtn').change(function() {
         //$('#console-event').html('Toggle: ' + $(this).prop('checked'))
         //alert($(this).prop('checked'));

         var id = $(this).attr('id');
         var status = $(this).prop('checked');

         $.ajax({
            url: "<?php echo site_url('operator/ekstrakulikuler_set_status'); ?>",
            type: 'post',
            data: {
               status: status,
               ekstrakulikuler_id: id
            }
         }).done(function(msg) {
            // $('#ekstra_id_' + id).html(msg);
         });
      });
      // })

      $('#form-button-save').hide();
   </script>

</body>

</html>