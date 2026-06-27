<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <meta name="description" content="">
      <meta name="author" content="">
      <title>Webview | App Presensi</title>
      <!-- Bootstrap core CSS -->
      <!-- <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet"> -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha256-YLGeXaapI0/5IgZopewRJcFXomhRMlYYjugPLSyNjTY=" crossorigin="anonymous" />
      <!-- Custom styles for this template -->
      <!-- <link href="css/blog-home.css" rel="stylesheet"> -->
      <!-- <link rel="stylesheet" href="<?php echo site_url('assets/web/css/blog-home.css');?>"> -->
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/red/pace-theme-flash.css" />
      <?php
         if(isset($css_files)){
           foreach($css_files as $file): ?>
      <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
      <?php endforeach;
         }else{ ?>
      <link href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css" rel="stylesheet" >
      <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-lite.css" rel="stylesheet">
      <?php }; ?>
      <?php
         if(isset($js_files)){
           foreach($js_files as $file): ?>
      <script src="<?php echo $file; ?>"></script>
      <?php endforeach;
         }else{ ?>
      <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script> -->
      <!-- jQuery -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
      <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
      <script src="<?php echo site_url('assets/summernote/dist/summernote-lite.js')?>"></script> 
      <!-- <script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script> -->
      <?php }; ?>
      <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script> -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js" integrity="sha256-fzFFyH01cBVPYzl16KT40wqjhgPtq6FFUB6ckN2+GGw=" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js"></script>
      <style>
         #canvas { width: 100%; }
         overflow-y: scroll;
         overflow-x: hidden;
         .flexigrid input[type=text].form-control {
         width: 300px;
         }
      </style>
   </head>
   <body>
      <main role="main" class="container" style="padding-top: 5px">
         
         <?php if(isset($keterangan)){ ?>           
         <div class="alert alert-warning">
            <strong class="animated infinite slow flash delay-1s">INFORMASI : </strong> <?php echo $keterangan;?>
         </div>
         <?php } ?>
         <?php if(isset($output)){ echo $output; }else{ include $page_name . ".php"; } ?>
         <!-- </div>  -->
      </main>
      <script type="text/javascript">
         $(function(){
         
             var url = window.location.pathname, 
                 urlRegExp = new RegExp(url.replace(/\/$/,'') + "$"); // create regexp to match current url pathname and remove trailing slash if present as it could collide with the link in navigation in case trailing slash wasn't present there
                 // now grab every link from the navigation
                 $('.menu a').each(function(){
                     // and test its normalized href against the url pathname regexp
                     if(urlRegExp.test(this.href.replace(/\/$/,''))){
                         $(this).addClass('active');
                     }
                 });
         
         });
         
         window.onload = () => {
            let bannerNode = document.querySelector('[alt="www.000webhost.com"]').parentNode.parentNode;
            bannerNode.parentNode.removeChild(bannerNode);
         }
         
         <?php if(has_alert()):
            foreach(has_alert() as $type => $message): ?>
            <?php if($type === 'alert-danger'){ ?>
              swal({
                  html : true,
                  title: 'Error !',
                  text: '<?php echo trim(preg_replace("/\s+/", " ", $message)); ?>',
                  type: 'error',
                  confirmButtonText: 'OK'
              });
            <?php }elseif($type === 'alert-warning'){ ?>
              swal({          
                   html : true,         
                  title: 'Peringatan',
                  text: '<?php echo $message; ?>',
                  type: 'warning',
                  confirmButtonText: 'Ok'
              });
           <?php }elseif($type === 'alert-success'){ ?>
              swal({
                   html : true,
                  title: 'Berhasil',
                  text: '<?php echo $message; ?>',
                  type: 'success',
                  confirmButtonText: 'Ok'
              });
           <?php }elseif($type === 'alert-info'){ ?>
         
            swal({
                   html : true,
                  title: 'Informasi',
                  text: '<?php echo $message; ?>',
                  type: 'info',
                  confirmButtonText: 'Ok'
              });
         
           <?php }; ?>
            <?php endforeach;
            endif; ?>
         
         
      </script>
   </body>
</html>
