<table id="table" class="display" style="width:100%">
    <thead>
        <tr>
            <!-- <th>NISN</th> -->
            <th>Nama Siswa</th>            
            <th>Status</th>  
            <!-- <th></th>                        
            <th></th>                        
            <th></th>                        
            <th></th>      -->                   
        </tr>
    </thead>
    <tbody>
        <?php foreach ($kehadiran_siswa as $row) { ?>
        <tr>
            <td><?php echo $row['nama_lengkap']?></td>
            <td>
                <?php if($row['status'] === 'HADIR'){ ?>                
                <button onclick="set_link(<?php echo $row['id'];?>,'<?php echo $row['changeable']?>')" type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModal">
                    HADIR
                </button>

                <?php }elseif($row['status'] === 'IZIN'){ ?>
                <button onclick="set_link(<?php echo $row['id']?>,'<?php echo $row['changeable']?>')" type="button" class="btn btn-warning" data-toggle="modal" data-target="#exampleModal">
                    IZIN
                </button>
                <?php }elseif($row['status'] === 'SAKIT'){ ?>
                <button onclick="set_link(<?php echo $row['id']?>,'<?php echo $row['changeable']?>')" type="button" class="btn btn-info" data-toggle="modal" data-target="#exampleModal">
                    SAKIT
                </button>
                <?php }elseif($row['status'] === 'ALPA'){ ?>
                <button onclick="set_link(<?php echo $row['id']?>,'<?php echo $row['changeable']?>')" type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">
                    ALPA
                </button>
                <?php }else{ ?>
                <button onclick="set_link(<?php echo $row['id']?>,'<?php echo $row['changeable']?>')" type="button" class="btn btn-secondary" data-toggle="modal" data-target="#exampleModal">
                    PRESENSI
                </button>
                <?php } ?>
            </td>
           
        </tr>    
        <?php } ?>
    </tbody>	
</table>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Pilih status presensi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">        
        <div class="container-fluid">
           <div class="row">
             <div class="col text-center"><a id="btn_hadir" href="" class="btn btn-success">HADIR</a></div>
             <div class="col text-center"><a id="btn_ijin" href="" class="btn btn-warning">&nbsp;&nbsp;IJIN&nbsp;&nbsp;</a></div>          
           </div>
           <hr>
           <div class="row">             
             <div class="col text-center"><a id="btn_sakit" href="" class="btn btn-info">SAKIT</a></div>
             <div class="col text-center"><a id="btn_alpa" href="" class="btn btn-danger">ALPA</a></div>   
           </div>
        </div>
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div> -->
    </div>
  </div>
</div>

<script type="text/javascript">

    function set_link(siswa_id,changeable){

        if(changeable == 'YA'){
          $('#btn_hadir').attr('href','<?php echo site_url('webview/guru/set-kehadiran-siswa/' . $guru_mengajar_id . '/' . $jadwal_mengajar . '/')?>' + siswa_id + '/HADIR' );
          $('#btn_ijin').attr('href','<?php echo site_url('webview/guru/set-kehadiran-siswa/' . $guru_mengajar_id . '/' . $jadwal_mengajar . '/')?>' + siswa_id + '/IJIN' );
          $('#btn_sakit').attr('href','<?php echo site_url('webview/guru/set-kehadiran-siswa/' . $guru_mengajar_id . '/' . $jadwal_mengajar . '/')?>' + siswa_id + '/SAKIT' );
          $('#btn_alpa').attr('href','<?php echo site_url('webview/guru/set-kehadiran-siswa/' . $guru_mengajar_id . '/' . $jadwal_mengajar . '/')?>' + siswa_id + '/ALPA' );  
        }else{
          alert('Perubahan tidak dapat dilakukan! Izin telah diberikan oleh Wali kelas');
        }
        
    }

    <?php if($sesi_berakhir === 'true'){ ?>
      $('.btn').removeAttr("onclick");
    <?php } ?>  

	$('#table').DataTable({
      "pageLength": 100,  
      "ordering": false,
      "paging": false,
      "fnDrawCallback": function() {     
           
      },
      "initComplete": function(settings, json) {        
      
      }       
      
   });
</script>