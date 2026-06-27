<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col">Nama Kelas</th>
      <th scope="col">Siswa</th>      
    </tr>
  </thead>
  <tbody>
    <?php foreach ($list_kelas->result_array() as $key) { ?>
    <tr>
      <th scope="row"><?php echo $key['nama_kelas']?></th>
      <td><a href="<?php echo site_url('guru/kelola-siswa-kelas/' . $key['id'])?>">Kelola</a></td>
    </tr>  
    <?php } ?>
    
    
  </tbody>
</table>