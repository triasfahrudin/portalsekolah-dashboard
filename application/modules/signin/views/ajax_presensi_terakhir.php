 <?php foreach ($presensi->result_array() as $row) { ?>
 <div class="card b-1 hover-shadow mb-10">
   <div class="media card-body">
      <div class="media-left pr-12">
         <img onclick="window.open(this.src)" width="40" src="<?php echo site_url('uploads/' . $row['foto']) ?>" alt="...">
      </div>
      <div class="media-body">
         <div class="mb-1">
            <span class="fs-16 pr-16"><?php echo $row['nama_pegawai']?></span>
         </div>
         <small class="fs-14 fw-200 ls-1"><?php echo $row['nama_sekolah']?></small>
      </div>      
   </div>
   <footer class="card-footer flexbox align-items-center">
      <div>
         <i class="far fa-clock"></i>
         <span><?php echo nicetime($row['tgl_update']);?></span>
      </div>
   </footer>
</div>                           
 <?php } ?>
 