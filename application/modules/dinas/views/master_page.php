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
   <title>Dinas - Portal Sekolah</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/css/adminlte.min.css" />
   <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
   <link rel="stylesheet" href="<?php echo site_url('assets/css/app.css?uuid=' . uniqid()) ?>" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/red/pace-theme-flash.css" />

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" />

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
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
      <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
      <!-- <script src="<?php echo site_url('assets/summernote/dist/summernote-lite.js') ?>"></script> -->
      <!-- <script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script> -->
   <?php }; ?>
   <!-- REQUIRED SCRIPTS -->
   <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script> -->

   <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.0.2/js/adminlte.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/Cookies.js/0.3.1/cookies.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.js"></script>

   <!-- loader -->

   <link rel="stylesheet" href="<?php echo site_url('assets/simple-loader/loader.css'); ?>" />
   <script src="<?php echo site_url('assets/simple-loader/loader.js'); ?>"></script>
   <script src="<?php echo site_url('assets/js/base64.js'); ?>"></script>

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

      .center-cell {
         text-align: center;
         vertical-align: middle;
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
               <a href="<?php echo site_url('dinas') ?>" class="nav-link">Beranda</a>
            </li>
            <!-- li class="nav-item d-none d-sm-inline-block">
                  <a href="#" class="nav-link">Contact</a>
                  </li> -->
         </ul>
         <!-- SEARCH FORM -->
         <!--  <form class="form-inline ml-3">
               <div class="input-group input-group-sm">
                  <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
                  <div class="input-group-append">
                     <button class="btn btn-navbar" type="submit">
                     <i class="fas fa-search"></i>
                     </button>
                  </div>
               </div>
               </form> -->
         <!-- Right navbar links -->
         <!--  <ul class="navbar-nav ml-auto">
               <li class="nav-item dropdown">
                  <a class="nav-link" data-toggle="dropdown" href="#">
                  <i class="far fa-comments"></i>
                  <span class="badge badge-danger navbar-badge">3</span>
                  </a>
                  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                     <a href="#" class="dropdown-item">
               
                        <div class="media">
                           <img src="dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                           <div class="media-body">
                              <h3 class="dropdown-item-title">
                                 Brad Diesel
                                 <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                              </h3>
                              <p class="text-sm">Call me whenever you can...</p>
                              <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                           </div>
                        </div>
               
                     </a>
                     <div class="dropdown-divider"></div>
                     <a href="#" class="dropdown-item">
                        
                        <div class="media">
                           <img src="dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                           <div class="media-body">
                              <h3 class="dropdown-item-title">
                                 John Pierce
                                 <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                              </h3>
                              <p class="text-sm">I got your message bro</p>
                              <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                           </div>
                        </div>
                        
                     </a>
                     <div class="dropdown-divider"></div>
                     <a href="#" class="dropdown-item">
                        
                        <div class="media">
                           <img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
                           <div class="media-body">
                              <h3 class="dropdown-item-title">
                                 Nora Silvester
                                 <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                              
                              <p class="text-sm">The subject goes here</p>
                              <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                           </div>
                        </div>
                        
                     </a>
                     <div class="dropdown-divider"></div>
                     <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
                  </div>
               </li>
               
               <li class="nav-item dropdown">
                  <a class="nav-link" data-toggle="dropdown" href="#">
                  <i class="far fa-bell"></i>
                  <span class="badge badge-warning navbar-badge">15</span>
                  </a>
                  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                     <span class="dropdown-header">15 Notifications</span>
                     <div class="dropdown-divider"></div>
                     <a href="#" class="dropdown-item">
                     <i class="fas fa-envelope mr-2"></i> 4 new messages
                     <span class="float-right text-muted text-sm">3 mins</span>
                     </a>
                     <div class="dropdown-divider"></div>
                     <a href="#" class="dropdown-item">
                     <i class="fas fa-users mr-2"></i> 8 friend requests
                     <span class="float-right text-muted text-sm">12 hours</span>
                     </a>
                     <div class="dropdown-divider"></div>
                     <a href="#" class="dropdown-item">
                     <i class="fas fa-file mr-2"></i> 3 new reports
                     <span class="float-right text-muted text-sm">2 days</span>
                     </a>
                     <div class="dropdown-divider"></div>
                     <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                  </div>
               </li>
               <li class="nav-item">
                  <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i
                     class="fas fa-th-large"></i></a>
               </li>
               </ul> -->
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
                     <a href="<?php echo site_url('dinas') ?>" class="nav-link" onclick="run_default_filter()">
                        <i class="nav-icon fas fa-home"></i>
                        <p>
                           Beranda
                        </p>
                     </a>
                  </li>
                  

                  <li class="nav-item">
                     <a href="<?php echo site_url('dinas/kelola-sekolah') ?>" class="nav-link" onclick="run_default_filter()">
                        <i class="nav-icon fas fa-school"></i>
                        <p>
                           Data Sekolah
                        </p>
                     </a>
                  </li>
               
                 <!-- <?php if (in_array($user_level, array('DINAS'))) { ?>
                     <li class="nav-item">
                        <a href="<?php echo site_url('dinas/izin-kepsek') ?>" class="nav-link" onclick="run_default_filter()">
                           <i class="nav-icon fas fa-envelope-open"></i>
                           <p>
                              Permohonan Izin
                           </p>
                        </a>
                     </li>
                  <?php } ?>-->

                  <li class="nav-item">
                     <a href="<?php echo site_url('dinas/profile/edit/' . $user_id) ?>" class="nav-link" onclick="run_default_filter()">
                        <i class="nav-icon fas fa-user-edit"></i>
                        <p>Profil</p>
                     </a>
                  </li>
                  <li class="nav-item">
                     <a href="<?php echo site_url('dinas/ganti-password') ?>" class="nav-link" onclick="run_default_filter()">
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
                     <h3 class="m-0 text-dark"><?php echo $page_title ?></h3>
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
                  <?php if ($this->uri->segment(2) === 'kelola-dinas-kabupaten' && ($this->uri->segment(3) !== 'edit' && $this->uri->segment(3) !== 'add')) { ?>
                     <div class="col-lg-12">
                        <div class="card card-primary card-outline">
                           <div class="card-body">
                              <?php if (isset($keterangan)) { ?>
                                 <div class="alert alert-warning">
                                    <strong class="animated infinite slow flash delay-1s">INFORMASI : </strong> <?php echo $keterangan; ?>
                                 </div>
                              <?php } ?>
                              <div class="row">
                                 <div class="col-md-2">
                                    <div class="card mb-3" style="max-width: 20rem;">
                                       <div class="card-header text-white bg-primary">Filter</div>
                                       <div class="card-body">
                                          <form method="POST" action="">
                                             <select class="form-control mb-2" name="filter_tahun" required="" id="filter_tahun">
                                                <option value="">PILIH TAHUN</option>
                                                <?php for ($i = (int)date('Y') - 1; $i <= (int) date('Y'); $i++) {  ?>
                                                   <option <?php echo set_select('filter_tahun', $i, ($i == date('Y') ? TRUE : FALSE)); ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                <?php } ?>
                                             </select>
                                             <select class="form-control mb-2" name="filter_bulan" required="" id="filter_bulan">
                                                <option value="">PILIH BULAN</option>
                                                <option <?php echo set_select('filter_bulan', '1', (1 == date('n') ? TRUE : FALSE)); ?> value="1">JANUARI</option>
                                                <option <?php echo set_select('filter_bulan', '2', (2 == date('n') ? TRUE : FALSE)); ?> value="2">FEBRUARI</option>
                                                <option <?php echo set_select('filter_bulan', '3', (3 == date('n') ? TRUE : FALSE)); ?> value="3">MARET</option>
                                                <option <?php echo set_select('filter_bulan', '4', (4 == date('n') ? TRUE : FALSE)); ?> value="4">APRIL</option>
                                                <option <?php echo set_select('filter_bulan', '5', (5 == date('n') ? TRUE : FALSE)); ?> value="5">MEI</option>
                                                <option <?php echo set_select('filter_bulan', '6', (6 == date('n') ? TRUE : FALSE)); ?> value="6">JUNI</option>
                                                <option <?php echo set_select('filter_bulan', '7', (7 == date('n') ? TRUE : FALSE)); ?> value="7">JULI</option>
                                                <option <?php echo set_select('filter_bulan', '8', (8 == date('n') ? TRUE : FALSE)); ?> value="8">AGUSTUS</option>
                                                <option <?php echo set_select('filter_bulan', '9', (9 == date('n') ? TRUE : FALSE)); ?> value="9">SEPTEMBER</option>
                                                <option <?php echo set_select('filter_bulan', '10', (10 == date('n') ? TRUE : FALSE)); ?> value="10">OKTOBER</option>
                                                <option <?php echo set_select('filter_bulan', '11', (11 == date('n') ? TRUE : FALSE)); ?> value="11">NOVEMBER</option>
                                                <option <?php echo set_select('filter_bulan', '12', (12 == date('n') ? TRUE : FALSE)); ?> value="12">DESEMBER</option>
                                             </select>
                                             <button type="submit" class="btn btn-block btn-success">Tampilkan</button>
                                          </form>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-md-10">
                                    <?php if (isset($output)) {
                                       echo $output;
                                    } else {
                                       include $page_name . ".php";
                                    } ?>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  <?php } else { ?>
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
                  <?php } ?>
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
            <p><a href="<?php echo site_url('dinas/ubah-password') ?>">Ubah Password</a></p>
            <p><a href="<?php echo site_url('dinas/profile') ?>">Profile</a></p>
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

      $(document).ready(function() {
         var index = Cookies.get('active');
         $('.main-menu').find('li a').removeClass('active');
         $(".main-menu").find('li a').eq(index).addClass('active');
         $('.main-menu').on('click', 'li a', function(e) {
            // e.preventDefault();
            $('.main-menu').find('li a').removeClass('active');
            $(this).addClass('active');
            Cookies.set('active', $('.main-menu li a').index(this));
         });

      });


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

      <?php $uri = $this->uri->segment(2); ?>
      <?php if ($uri == 'kelola-sekolah') { ?>

         document.addEventListener('DOMContentLoaded', function() {
            // Buat elemen tombol baru
            var tombolBaru = document.createElement('button');
            tombolBaru.textContent = 'Verval Data Siswa'; // Teks pada tombol baru
            tombolBaru.classList.add('btn', 'btn-primary', 'mr-2'); // Tambahkan kelas untuk gaya tombol


            // Tambahkan event listener untuk klik pada tombol baru
            tombolBaru.addEventListener('click', function() {
               window.location.href = '<?php echo site_url('dinas/verval-data-siswa') ?>';
            });


            // Ambil elemen dengan class dataTablesContainer
            var dataTablesContainer = document.querySelector('.dataTablesContainer');

            // Ambil div dengan class float-right di dalam dataTablesContainer
            var divFloatRight = dataTablesContainer.querySelector('.float-right');

            // Tambahkan tombol baru di bawah tombol cetak
            divFloatRight.appendChild(tombolBaru);

         });


         document.addEventListener('DOMContentLoaded', function() {
            // Buat elemen tombol baru
            var buttonGroup = document.createElement('div');
            buttonGroup.classList.add('btn-group', 'mr-2');

            // Isi dari button group
            buttonGroup.innerHTML = `
            <div class="btn-group dropleft" role="group">
               <button id="btnGroupDrop1" type="button" class="btn btn-info">
                  <a class="dropdown-item" href="<?php echo site_url('dinas/export-all-pegawai') ?>">Exp. Semua Pegawai</a>
               </button>
               <button id="btnGroupDrop1" type="button" class="btn btn-success">
                  <a class="dropdown-item" href="<?php echo site_url('dinas/export-all-siswa') ?>">Exp. Semua Siswa</a>
               </button>

               
            </div>`;


            // Ambil elemen dengan class dataTablesContainer
            var dataTablesContainer = document.querySelector('.dataTablesContainer');

            // Ambil div dengan class float-right di dalam dataTablesContainer
            var divFloatRight = dataTablesContainer.querySelector('.float-right');

            // Tambahkan tombol baru di bawah tombol cetak
            divFloatRight.appendChild(buttonGroup);
         });
      <?php } ?>
   </script>
</body>

</html>