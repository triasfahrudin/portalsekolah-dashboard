<div class="card">
   <div class="card-header bg-primary text-white">
      Daftar Jadwal Mengajar
   </div>
   <div class="card-body">
      <table class="table table-hover table-striped" id="datatable">
         <thead class="thead-dark">
            <tr>
               <th scope="col">Hari</th>
               <th scope="col">Jam Mulai</th>
               <th scope="col">Jam Selesai</th>
               <th scope="col">Kelas</th>
               <th scope="col">Mata pelajaran</th>
               <th scope="col">Aksi</th>
            </tr>
         </thead>
         <tbody>
            <?php foreach ($jadwal_mengajar->result_array() as $key) { ?>
               <tr>
                  <td><?php echo $key['hari']; ?></td>
                  <td><?php echo $key['jam_mulai']; ?></td>
                  <td><?php echo $key['jam_selesai']; ?></td>
                  <td><?php echo $key['nama_kelas']; ?></td>
                  <td><?php echo $key['matapelajaran']; ?></td>
                  <td><a href="<?php echo site_url('operator/hapus-jadwal/' . $pegawai_id . '/' . $key['id']) ?>" style="color: red">Hapus</a></td>
               </tr>
            <?php } ?>
         </tbody>
      </table>
   </div>
</div>

<div class="card">
   <div class="card-header bg-success text-white">
      Form Tambah Jadwal
   </div>
   <div class="card-body">
      <form style="padding: 15px" method="POST">
         <fieldset>
            <input type="hidden" name="pegawai_id" value="<?php echo $pegawai_id ?>">
            <div class="form-group">
               <label for="exampleSelect1">Mata pelajaran</label>
               <select class="form-control" id="jadwal_mengajar_select_mp" name="matapelajaran_id" required="">
                  <option value="">Pilih Mata pelajaran</option>
                  <?php foreach ($mp_tersedia->result_array() as $key) { ?>
                     <option value="<?php echo $key['id'] ?>"><?php echo $key['nama'] ?></option>
                  <?php } ?>
               </select>
            </div>
            <div class="form-group">
               <label for="exampleSelect1">Hari</label>
               <select class="form-control" name="hari" required="">
                  <option value="SENIN">SENIN</option>
                  <option value="SELASA">SELASA</option>
                  <option value="RABU">RABU</option>
                  <option value="KAMIS">KAMIS</option>
                  <option value="JUMAT">JUMAT</option>
                  <option value="SABTU">SABTU</option>
               </select>
            </div>
            <div class="form-group">
               <label for="exampleSelect1">Jam Mulai</label>
               <input type="text" class="form-control" placeholder="" name="jam_mulai" required="">
               <small id="emailHelp" class="form-text text-muted">Format 24 jam, misal : 13:00 </small>
            </div>
            <div class="form-group">
               <label for="exampleSelect1">Jam Selesai</label>
               <input type="text" class="form-control" placeholder="" name="jam_selesai" required="">
               <small id="emailHelp" class="form-text text-muted">Format 24 jam, misal : 13:00 </small>
            </div>
            
            <div class="form-group">
               <label for="exampleSelect1">Kelas</label>
               <select class="form-control" id="jadwal_mengajar_select_kelas" name="kelas_id" required="">
                  <option value="">Pilih Kelas</option>
               </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
         </fieldset>
      </form>

   </div>
</div>



<script type="text/javascript">
   $('#datatable').DataTable({
      "fnDrawCallback": function() {},
      "initComplete": function(settings, json) {}
   });
</script>