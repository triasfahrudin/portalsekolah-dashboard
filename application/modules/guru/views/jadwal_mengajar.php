<div class="table-responsive">
   <table class="table table-hover table-striped" id="datatable">
      <thead class="thead-dark">
         <tr>
            <!-- <th scope="col">Tanggal</th> -->
            <th scope="col">Hari</th>
            <th scope="col">Mulai</th>
            <th scope="col">Selesai</th>
            <th scope="col">Kelas</th>
            <th scope="col">Pelajaran</th>
            <th scope="col">Foto Mulai</th>
            <th scope="col">Foto Selesai</th>
            <th scope="col">Uraian kegiatan</th>
            <th scope="col">Verifikasi</th>
         </tr>
      </thead>
      <tbody>
         <?php foreach ($jadwal_mengajar->result_array() as $key) { ?>

            <?php if (
               ($key['foto_mulai'] === 'belum' || $key['foto_selesai'] === 'belum') ||
               ($key['dokumentasi'] === 'belum' && $key['uraian'] === '')
            ) { ?>
               <tr class="bg-warning">
               <?php } else { ?>
               <tr>
               <?php } ?>

               <!-- <td><?php echo convert_sql_date_to_date($key['tgl']); ?></td> -->
               <td><?php echo $key['hari']; ?></td>
               <td><?php echo $key['jam_mulai']; ?></td>
               <td><?php echo $key['jam_selesai']; ?></td>
               <td><?php echo $key['nama_kelas']; ?></td>
               <td><?php echo $key['matapelajaran']; ?></td>
               <td>
                  <?php if ($key['foto_mulai'] === 'belum') { ?>
                     <span class="badge bg-secondary">
                        <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                        &nbsp;Belum diunggah
                     </span>
                  <?php } else { ?>
                     <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
                        &nbsp;Sudah diunggah
                     </span> -
                     <a href="<?php echo site_url('uploads/dokumentasi/' . $key['foto_mulai']) ?>?random=<?php echo date("YmdHis") ?>" target="_blank">Lihat</a>
                  <?php } ?>

                  <?php if (date('Y-m-d') >= $key['tgl']) { ?>  
                     <!-- button foto mulai-->                   
                     <!-- <?php echo form_open_multipart('guru/upload-dokumentasi/'); ?>
                     <input type="hidden" name="tgl" value="<?php echo $key['tgl'] ?>">
                     <input type="hidden" name="jadwal_mengajar_id" value="<?php echo $key['jadwal_mengajar_id']; ?>">
                     <input class="upload" name="foto_mulai" onchange="this.form.submit()" multiple="" type="file">
                     <?php echo form_close(); ?> -->
                  <?php } ?>
               </td>
               <td>
                  <?php if ($key['foto_selesai'] === 'belum') { ?>
                     <span class="badge bg-secondary">
                        <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                        &nbsp;Belum diunggah
                     </span>
                  <?php } else { ?>
                     <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
                        &nbsp;Sudah diunggah
                     </span> -
                     <a href="<?php echo site_url('uploads/dokumentasi/' . $key['foto_selesai']) ?>?random=<?php echo date("YmdHis") ?>" target="_blank">Lihat</a>
                  <?php } ?>

                  <?php if (date('Y-m-d') >= $key['tgl']) { ?>
                     <!-- button foto selesai-->
                     <!-- <?php echo form_open_multipart('guru/upload-dokumentasi/'); ?>
                     <input type="hidden" name="tgl" value="<?php echo $key['tgl'] ?>">
                     <input type="hidden" name="jadwal_mengajar_id" value="<?php echo $key['jadwal_mengajar_id']; ?>">
                     <input class="upload" name="foto_selesai" onchange="this.form.submit()" multiple="" type="file">
                     <?php echo form_close(); ?> -->

                  <?php } ?>



               </td>
               <td>
                  <div>
                     <textarea jadwal_mengajar_id="<?php echo $key['jadwal_mengajar_id']; ?>" tgl="<?php echo $key['tgl']; ?>" class="form-control update_me" name="uraian" rows="3" placeholder="Uraian kegiatan"><?php echo $key['uraian']; ?></textarea>
                  </div>
                  <?php if ($key['dokumentasi'] === 'belum') { ?>
                     <span class="badge bg-secondary">
                        <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                        &nbsp;Belum diunggah
                     </span>
                  <?php } else { ?>
                     <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
                        &nbsp;Sudah diunggah
                     </span> -
                     <a href="<?php echo site_url('uploads/dokumentasi/' . $key['dokumentasi']) ?>?random=<?php echo date("YmdHis") ?>" target="_blank">Lihat</a>
                  <?php } ?>

                  <?php if (date('Y-m-d') >= $key['tgl']) { ?>
                     <?php echo form_open_multipart('guru/upload-dokumentasi/'); ?>
                     <input type="hidden" name="jadwal_mengajar_id" value="<?php echo $key['jadwal_mengajar_id']; ?>">
                     <input type="hidden" name="tgl" value="<?php echo $key['tgl'] ?>">
                     <input class="upload" name="dokumentasi" onchange="this.form.submit()" multiple="" type="file">
                     <?php echo form_close(); ?>
                  <?php } ?>
               </td>
               <td>
                  <?php if ($key['verifikasi'] === 'belum' || $key['verifikasi'] === 'pending') { ?>
                     <span class="badge bg-secondary">
                        <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                        &nbsp;Pending
                     </span>
                  <?php } elseif ($key['verifikasi'] === 'ok') { ?>
                     <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
                        &nbsp;Diterima
                     </span>
                  <?php } else { ?>
                     <span class="badge bg-danger">
                        <i class="bi bi-x-circle me-1" aria-hidden="true"></i>
                        &nbsp;Ditolak
                     </span>
                  <?php } ?>
               </td>

               </tr>
            <?php } ?>
      </tbody>
   </table>
</div>

<div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="videoModalLabel">Video dan Capture Foto</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <video id="video" width="440" height="280" autoplay></video>
            <button id="captureBtn" class="btn btn-primary">Capture Foto</button>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
         </div>
      </div>
   </div>
</div>

<script type="text/javascript">
   $('#datatable').DataTable({
      "fnDrawCallback": function() {},
      "initComplete": function(settings, json) {},
      "sorting": false
   });

   $('.update_me').keyup(function() {
      var jadwal_mengajar_id = $(this).attr('jadwal_mengajar_id');
      var tgl = $(this).attr('tgl');
      var uraian = $(this).val();

      $.post("<?php echo base_url() . 'guru/upload-dokumentasi'; ?>", {
         jadwal_mengajar_id: jadwal_mengajar_id,
         tgl: tgl,
         uraian: uraian

      });
   });

   
</script>