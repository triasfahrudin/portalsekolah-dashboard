<div class="row">
   <div class="col-lg-8">
      <h3 class="m-0 text-dark">Nama pegawai: <?php echo $nama_pegawai ?></h3>
   </div>
   <div class="col-lg-4">
      <div class="text-right">
         <button class="btn btn-primary">Terima semua</button>
         
      </div>
   </div>
</div>

<table class="table table-hover table-striped" id="datatable">
   <thead class="thead-dark">
      <tr>
         <th scope="col">Tanggal</th>
         <th scope="col">Hari</th>
         <th scope="col">Jam Mulai</th>
         <th scope="col">Jam Selesai</th>
         <th scope="col">Kelas</th>
         <th scope="col">Mata pelajaran</th>
         <th scope="col">Foto absen</th>
         <th scope="col">Uraian kegiatan</th>
         <th scope="col">Verifikasi</th>
      </tr>
   </thead>
   <tbody>
      <?php $i = 1; ?>
      <?php foreach ($jadwal_mengajar->result_array() as $key) { ?>

         <tr id="tr_<?php echo $i ?>">
            <td><?php echo convert_sql_date_to_date($key['tgl']); ?></td>
            <td><?php echo $key['hari']; ?></td>
            <td><?php echo $key['jam_mulai']; ?></td>
            <td><?php echo $key['jam_selesai']; ?></td>
            <td><?php echo $key['nama_kelas']; ?></td>
            <td><?php echo $key['matapelajaran']; ?></td>
            <td>
               <?php if ($key['foto'] === 'belum') { ?>
                  <span class="badge bg-secondary">
                     <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                     &nbsp;Belum diunggah
                  </span>
               <?php } else { ?>

                  <?php
                  $imagePath = 'uploads/dokumentasi/' . $key['foto'];
                  if (file_exists($imagePath)) {
                     $imageUrl = site_url($imagePath) . '?random=' . date("YmdHis");
                  } else {
                     $imageUrl = 'https://t3.ftcdn.net/jpg/04/62/93/66/360_F_462936689_BpEEcxfgMuYPfTaIAOC1tCDurmsno7Sp.jpg';
                  }
                  ?>

                  <!-- <img src="<?php echo $imageUrl ?>" alt="gambar presensi"  class="img-fluid" width="200" height="200"> -->
                  <img src="<?php echo $imageUrl; ?>" alt="Contoh Gambar" class="img-fluid" width="100" height="100" data-bs-toggle="modal" data-bs-target="#imageModal" data-image="<?php echo $imageUrl; ?>">

               <?php } ?>
            </td>
            <td>
               <div>
                  <textarea readonly jadwal_mengajar_id="<?php echo $key['jadwal_mengajar_id']; ?>" tgl="<?php echo $key['tgl']; ?>" class="form-control update_me" name="uraian" rows="3" placeholder="Uraian kegiatan"><?php echo $key['uraian']; ?></textarea>
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

            </td>
            <td id="td_<?php echo $key['id']?>">
               <?php if ($key['verifikasi'] === 'pending') { ?>
                  <!-- <span class="badge bg-secondary">
                     <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                     &nbsp;Pending
                  </span> -->


                  <a id="link_diterima_<?php echo $key['id']; ?>" style="color:#266927" class="diterima" onclick="change_status('<?php echo $key['id']; ?>','terima')">TERIMA</a> |
                  <a id="link_ditolak_<?php echo  $key['id']; ?>" style="color:#ff111f" class="ditolak" onclick="change_status('<?php echo $key['id']; ?>','tolak')">TOLAK</a>


                  <script>
                     const trElement = document.querySelector('#tr_<?php echo $i; ?>');
                     trElement.removeAttribute('data-bs-toggle');
                  </script>

               <?php } elseif ($key['verifikasi'] === 'terima') { ?>
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
      <?php $i++;
      } ?>
   </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="imageModalLabel">Gambar Memperbesar</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body text-center">
            <img id="modalImage" src="" alt="Contoh Gambar" class="img-fluid">
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
         </div>
      </div>
   </div>
</div>

<script>
   var modal = new bootstrap.Modal(document.getElementById('imageModal'));
   var modalImage = document.getElementById('modalImage');

   document.querySelectorAll('.img-fluid').forEach(img => {
      img.addEventListener('click', function() {
         var imageUrl = this.getAttribute('data-image');
         modalImage.src = imageUrl;
         modal.show();
      });
   });

   function change_status(id_dokumen_pendaftar, status) {
      //diterima,ditolak
      if (status == 'terima') {
         var return_confirm = confirm('Apakah anda yakin ingin menerima?');
         if (return_confirm) {
            $.ajax({
               url: "<?php echo site_url('kepsek/set_verifikasi_dokumen/terima/') ?>" + id_dokumen_pendaftar
            }).done(function(msg) {
               $('#td_' + id_dokumen_pendaftar).html(msg);
            });
         }

      } else {
         var return_confirm = window.prompt('Alasan penolakan?');
         if (return_confirm) {
            const data = {
               catatan: return_confirm
            };

            $.ajax({
               url: "<?php echo site_url('kepsek/set_verifikasi_dokumen/tolak/') ?>" + id_dokumen_pendaftar,
               method: "POST",
               data: data // Menambahkan objek data ke dalam permintaan Ajax
            }).done(function(msg) {
               $('#td_' + id_dokumen_pendaftar).html(msg);
            });
         }
      }

      // datatable.ajax.reload(null,false);

   }

</script>

