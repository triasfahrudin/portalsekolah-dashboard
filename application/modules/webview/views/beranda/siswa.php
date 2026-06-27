<div class="row">
   <div class="col text-center">      
      <figure class="figure">
         <img class="figure-img img-fluid" src="<?php echo $qr_image?>" alt="Kode QR" style="width: 80%;height: 80%">
         <figcaption class="figure-caption text-center">Kode QR</figcaption>
      </figure>    
      <div class="alert alert-warning" role="alert">
        Pastikan setting GPS sudah aktif. Tarik layar kebawah untuk update lokasi GPS
      </div>  
   </div>
</div>
<div class="row">
   <div class="col text-center">
      <figure class="figure">
         <a href="<?php echo site_url('webview/siswa/profile/'. $token_login .'/edit/'. $user_id)?>">
            <img src="https://img.icons8.com/dusk/64/000000/contract-job.png" class="figure-img img-fluid" />
         </a>
         <figcaption class="figure-caption text-center">Profile Pengguna</figcaption>
      </figure>
   </div>
   <div class="col text-center">
      <figure class="figure">
         <a href="<?php echo site_url('webview/siswa/ganti-password/' . $token_login)?>">
            <img src="https://img.icons8.com/dusk/64/000000/password.png" class="figure-img img-fluid" />
         </a>  
         <figcaption class="figure-caption text-center">Ganti Password</figcaption>
      </figure>
   </div>
</div>