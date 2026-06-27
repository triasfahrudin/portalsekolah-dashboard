<div class="row">
   <div class="col text-center"> 
		
		<div class="alert alert-primary" role="alert">
        Pastikan setting GPS sudah aktif. Usap layar kebawah untuk update lokasi GPS.
      </div>			

		<?php if($jarak > $max_jarak_presensi){ ?>

		<div class="alert alert-danger" role="alert">
		Anda berada diluar wilayah yang diperbolehkan untuk melakukan Presensi! (<?php echo $jarak ?>)
		</div>

		<?php }else{ ?>

		<div class="alert alert-success" role="alert">
		Anda berada didalam wilayah yang diperbolehkan untuk melakukan Presensi.
		</div>

		<?php } ?>		

		<div class="alert alert-warning" role="alert">
        <?php echo $status_presensi;?>
      </div>	

   </div>
</div>
<div class="row">
   <div class="col text-center">
      <figure class="figure">
         <a href="<?php echo site_url('webview/pegawai/profile/'. $token_login .'/edit/'. $user_id)?>">
            <img src="https://img.icons8.com/dusk/64/000000/contract-job.png" class="figure-img img-fluid" />
         </a>
         <figcaption class="figure-caption text-center">Profile Pengguna</figcaption>
      </figure>
   </div>
   <div class="col text-center">
      <figure class="figure">
         <a href="<?php echo site_url('webview/pegawai/ganti-password/' . $token_login)?>">
            <img src="https://img.icons8.com/dusk/64/000000/password.png" class="figure-img img-fluid" />
         </a>  
         <figcaption class="figure-caption text-center">Ganti Password</figcaption>
      </figure>
   </div>
</div>
