<style>
   .card-header {
   padding: 0rem;
   margin-bottom: 0;
   }
</style>
<table class="table table-striped">
   <thead>
      <tr>
         <th scope="col">Jam Mulai</th>
         <th scope="col">Jam Selesai</th>
         <th scope="col">Pelajaran</th>
         <th scope="col">Pengampu</th>
         <th scope="col">Presensi</th>
      </tr>
   </thead>
   <tbody>
      <?php foreach ($data as $key) { ?>
      <tr>
         <td><?php echo $key['jam_mulai']?></td>
         <td><?php echo $key['jam_selesai']?></td>
         <td><?php echo $key['matapelajaran']?></td>
         <td><?php echo $key['pengampu']?></td>
         <td><?php echo $key['presensi']?></td>
      </tr>
      <?php } ?>
   </tbody>
</table>

<?php if($cek_izin->num_rows() > 0){ ?>
    <?php $izin = $cek_izin->row_array();?>

    <div class="card p-3">
       <div class="card-header">
          
          <div class="alert alert-success">
             <h4><i class="icon fa fa-file"></i> Pengajuan Izin</h4>
             Anda telah mengajuan izin tidak masuk untuk tanggal ini
          </div>
       </div>
       <div class="card-body">
         <form class="">
           <div class="form-group">
            <label for="exampleInputEmail1">Status Permohonan</label>
            <?php if($izin['status_izin'] === 'PENDING'){ ?>
            <input type="text" class="form-control" value="MENUNGGU RESPON WALIKELAS" name=""  readonly="">
             <div class="text-center"> 
              <a href="<?php echo site_url('siswa/batalkan-izin/' . simple_crypt($izin['id'],'e'))?>" class="btn btn-danger mt-2 btn-block">Batalkan pengajuan izin</a>
            </div>
            <?php }elseif ($izin['status_izin'] === 'TOLAK') { ?>
            <input type="text" class="form-control text-danger" value="PENGAJUAN IZIN TOLAK" name=""  readonly="">  
            <?php }else{ ?>
            <input type="text" class="form-control text-success" value="PENGAJUAN IZIN DITERIMA" name=""  readonly="">    
            <?php } ?>  
           
         </form>
       </div>
     </div>
<?php }else{ 

  $now = new DateTime();
  $tanggal_izin = new DateTime($tanggal);
  if($tanggal_izin > $now){ ?>
    <div class="card p-3">
       <div class="card-header">
          
          <div class="alert alert-info">
             <h4><i class="icon fa fa-file"></i> Pengajuan Izin</h4>
             Pengajuan izin untuk tanggal ini
          </div>
       </div>
       <div class="card-body">
          <form method="POST" action="<?php echo site_url('siswa/pengajuan-izin')?>" enctype="multipart/form-data">
            <input type="hidden" name="tanggal_izin" value="<?php echo $tanggal?>">
             <div class="form-group">
                <label for="exampleInputEmail1">Alasan tidak masuk</label>
                <select class="form-control" name="jenis_izin">
                  <option value="SAKIT">Sakit</option>
                  <option value="LAINNYA">Lainnya</option>
                </select>
                <!-- <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email"> -->
                <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
             </div>
             <div class="form-group">
                <label for="exampleInputPassword1">Keterangan</label>
                <!-- <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password"> -->
                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" required="" name="keterangan"></textarea>
             </div>
             <div class="form-group">
              <label for="exampleFormControlFile1">Masukkan file pendukung</label>
              <input type="file" class="form-control-file" id="exampleFormControlFile1" name="file_pendukung">
              <small id="emailHelp" class="form-text text-muted">
                File pendukung ini dapat berupa foto surat keterangan dari dokter atau surat tertulis dari orangtua atau wali <br/>(**File format :jpg & pdf, maks. ukuran file 512 KB)
              </small>
            </div>
             <!-- <div class="form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Check me out</label>
             </div> -->
             <button type="submit" class="btn btn-primary">Kirimkan</button>
          </form>
       </div>
    </div>
    <?php } ?>
<?php } ?>