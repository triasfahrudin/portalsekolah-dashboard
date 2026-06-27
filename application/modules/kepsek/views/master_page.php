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
   <title>KEPALA SEKOLAH | PRESENSI</title>
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

   <style>
      a.ditolak {
         color: #ff111f;
      }

      a.diterima {
         color: #266927;
      }

      a {
         cursor: pointer;
         cursor: hand;
      }


      .strikethrough {
         text-decoration: line-through;
         color: red;
      }
   </style>

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
               <a href="<?php echo site_url('kepsek') ?>" class="nav-link">Beranda</a>
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
                     <a href="<?php echo site_url('kepsek') ?>" class="nav-link" onclick="run_default_filter()">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                           Beranda
                        </p>
                     </a>
                  </li>
                  <li class="nav-item">
                     <a href="<?php echo site_url('kepsek/dokumen') ?>" class="nav-link">
                        <i class="nav-icon fas fa-file-word"></i>
                        <p>
                           Dokumen Pegawai
                        </p>
                     </a>
                  </li>

                  <li class="nav-header">IZIN</li>
                  <li class="nav-item">
                     <a href="<?php echo site_url('kepsek/izin-pegawai') ?>" class="nav-link" onclick="run_default_filter()">
                        <i class="nav-icon fas fa-envelope-open"></i>
                        <p>
                           Izin Pegawai
                        </p>
                     </a>
                  </li>
                  <li class="nav-item">
                     <a href="<?php echo site_url('kepsek/pengajuan-izin') ?>" class="nav-link" onclick="run_default_filter()">
                        <i class="nav-icon fas fa-envelope-open"></i>
                        <p>
                           Pengajuan Izin
                        </p>
                     </a>
                  </li>
                  <li class="nav-header">WALI KELAS & MAPEL</li>
                  <li class="nav-item">
                     <a href="<?php echo site_url('kepsek/kelola-walikelas') ?>" class="nav-link" onclick="run_default_filter()">
                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                        <p>
                           Guru Wali Kelas & Mapel
                        </p>
                     </a>
                  </li>
                  <!-- <li class="nav-item">
                        <a href="<?php echo site_url('kepsek/kelola-guru-mapel') ?>" class="nav-link">
                           <i class="nav-icon fas fa-book-reader"></i>
                           <p>
                              Guru Mapel
                           </p>
                        </a>
                     </li> -->

                  <li class="nav-header">MENGAJAR</li>
                  <li class="nav-item">
                     <a href="<?php echo site_url('kepsek/verifikasi-dokumen') ?>" class="nav-link" onclick="run_default_filter()">
                        <i class="nav-icon fas fa-file"></i>
                        <p>
                           Verifikasi Dokumen
                        </p>
                     </a>
                  </li>

                  <li class="nav-header">PRESENSI</li>



                  <li class="nav-item">
                     <a href="<?php echo site_url('kepsek/presensi-pegawai') ?>" class="nav-link" onclick="run_default_filter()">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                           Laporan Pegawai
                        </p>
                     </a>
                  </li>

                  <li class="nav-item">
                     <a href="<?php echo site_url('kepsek/presensi-siswa') ?>" class="nav-link" onclick="run_default_filter()">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>
                           Laporan Siswa
                        </p>
                     </a>
                  </li>
                  <li class="nav-header">PROFIL PENGGUNA</li>
                  <li class="nav-item">
                     <a href="<?php echo site_url('kepsek/profile/edit/' . $user_id) ?>" class="nav-link" onclick="run_default_filter()">
                        <i class="nav-icon fas fa-user-edit"></i>
                        <p>Profil</p>
                     </a>
                  </li>
                  <li class="nav-item">
                     <a href="<?php echo site_url('kepsek/ganti-password') ?>" class="nav-link" onclick="run_default_filter()">
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
            <p><a href="<?php echo site_url('kepsek/ubah-password') ?>">Ubah Password</a></p>
            <p><a href="<?php echo site_url('kepsek/profile') ?>">Profile</a></p>
            <p><a href="<?php echo site_url('signout') ?>">Keluar</a></p>
         </div>
      </aside>
      <!-- /.control-sidebar -->
      <!-- Main Footer -->
      <footer class="main-footer">
         <!-- To the right -->
         <div class="float-right d-none d-sm-inline">
            HK
         </div>
         <!-- Default to the left -->
         <strong>Copyright &copy; 2021.</strong> All rights reserved.
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

      // $(document).ready(function () {
      //    var index = Cookies.get('active');
      //    $('.main-menu').find('li a').removeClass('active');
      //    $(".main-menu").find('li a').eq(index).addClass('active');
      //    $('.main-menu').on('click', 'li a', function (e) {
      //        // e.preventDefault();
      //        $('.main-menu').find('li a').removeClass('active');
      //        $(this).addClass('active');
      //        Cookies.set('active', $('.main-menu li a').index(this));
      //    });

      //  });

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

      function run_default_filter() {
         Cookies.set('run_default_filter', 'on');
      }
     
   </script>

</body>

</html>