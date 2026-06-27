<div class="table-responsive">
<table class="table">
  <thead class="thead-dark">
    <tr>      
      <th scope="col">Nama</th>
      <th scope="col">Kelas</th>
      <th scope="col">Jenis</th>
      <th scope="col">Keterangan</th>
      <th scope="col">File Pendukung</th>
      <th scope="col">Pilihan</th>
    </tr>
  </thead>
  <tbody>
    <?php if($izin->num_rows() == 0){  ?>
      <tr>
        <td colspan="6" class="text-center">Belum ada data permohonan</td>
      </tr>
    <?php }else{
      foreach ($izin->result_array() as $key) { 
        $file_pendukung = !empty($key['file']) ? site_url('uploads/' . $key['file']) : 'TIDAK-ADA'; ?>
    <tr>
      <td><?php echo $key['nama_lengkap']?></td>
      <td><?php echo $key['nama_kelas']?></td>
      <td><?php echo $key['jenis_izin']?></td>
      <td><?php echo $key['keterangan']?></td>
      <td><?php echo $file_pendukung; ?></td>
      <td>
        <a onclick="return confirm('anda yakin untuk menerima permohonan izin ini?')" class="btn btn-success" href="<?php echo site_url('guru/kelola-izin-siswa/terima/' . simple_crypt($key['izin_siswa_id'],'e'))?>">TERIMA</a>&nbsp; - &nbsp;
        <a onclick="return confirm('anda yakin untuk menolak permohonan izin ini?')" href="<?php echo site_url('guru/kelola-izin-siswa/tolak/' . simple_crypt($key['izin_siswa_id'],'e'))?>" class="btn btn-danger">TOLAK</a></td>
    </tr>  
    <?php } ?>    
<?php } ?>    

  </tbody>
</table>
</div>