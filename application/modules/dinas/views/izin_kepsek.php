<table class="table table-hover table-striped table-bordered" id="datatable">
   <thead  class="thead-dark">
      <tr>
         <th scope="col">Tanggal</th>
         <!-- <th scope="col">Untuk Tgl</th> -->
         <!-- <th scope="col">NUPTK</th> -->
         <th scope="col" class="text-center">Nama</th> 
         <!-- <th scope="col">Sekolah</th>  -->
         <th scope="col">Izin</th>
         <th scope="col">Keterangan</th>
         <th scope="col">File</th>
         <th scope="col">Pilihan</th> 
      </tr>
   </thead>
   <tbody>

	   
     <?php foreach ($izin as $key) { ?>
     <tr> 
        <td>
          Pengajuan: <?php echo convert_sql_date_to_date($key->tgl_pengajuan)?><br/>
          Digunakan: <?php echo convert_sql_date_to_date($key->tgl_izin)?>          
        </td>        
        <td>
          <?php echo $key->nama_lengkap?><br/>
          <?php echo $key->nuptk?><br/>
          <?php echo $key->sekolah?>            
        </td>
        <!-- <td></td> -->
        <td><?php echo $key->jenis_izin?></td>
        <td><?php echo $key->keterangan?></td>
        <td>
          <?php if(!empty($key->file)){ ?>
          <a href="<?php echo site_url('uploads/' . $key['file'])?>">Download</a>
          <?php }else{ ?>
          Tidak tersedia
          <?php } ?>  
          
        </td>
        <td>
          <a onclick="return confirm('anda yakin untuk menerima permohonan izin ini?')" href="<?php echo site_url('dinas/proses-izin-kepsek/' . $key->id . '/terima')?>" class="btn btn-success">TERIMA</a>&nbsp; - 
          <a onclick="return confirm('anda yakin untuk menolak permohonan izin ini?')" href="<?php echo site_url('dinas/proses-izin-kepsek/' . $key->id . '/tolak')?>" class="btn btn-danger">TOLAK</a>
        </td>
      </tr>    
     <?php } ?>
   </tbody>
</table>

<script type="text/javascript">
  
  $('#datatable').DataTable({
      "fnDrawCallback": function() { },
      "initComplete": function(settings, json) { }
   });
</script>
