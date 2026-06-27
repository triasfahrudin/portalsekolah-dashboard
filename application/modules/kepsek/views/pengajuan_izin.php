 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />

 <div class="row">                     
    <div class="col-md-4">
      <div class="card">
        <div class="card-header text-white bg-primary">Form Pengajuan Izin</div>
        <div class="card-body">
          <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
              <label for="exampleInputEmail1">Izin Untuk tanggal</label>
              <input type="text" class="form-control" name="tgl_izin" id="datepicker" aria-describedby="emailHelp" placeholder="Izin Untuk tanggal">
              <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
            </div>
            <div class="form-group">
              <label for="exampleInputPassword1">Izin</label>
              <!-- <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password"> -->
              <select name="jenis_izin" class="form-control">
                <option value="MASUK-TELAT">TELAT MASUK</option>
                <option value="PULANG-CEPAT">PULANG CEPAT</option>
                <option value="IZIN-TIDAK-MASUK">IZIN TIDAK MASUK</option>
                <option value="IZIN-SAKIT">IZIN SAKIT</option>
              </select>
            </div>
            <div class="form-group">
                <label for="exampleInputPassword1">Keterangan</label>
                <!-- <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password"> -->
                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" required="" name="keterangan"></textarea>
             </div>
            <div class="form-group">
              <label for="exampleFormControlFile1">Masukkan file pendukung</label>
              <input type="file" class="form-control-file" id="exampleFormControlFile1" name="file">
              <small id="emailHelp" class="form-text text-muted">
                File pendukung ini dapat berupa foto surat keterangan dari dokter atau surat keterangan lainnya <br/>(**File format :jpg & pdf, maks. ukuran file 512 KB)
              </small>
            </div>
            <!-- <div class="form-check">
              <input type="checkbox" class="form-check-input" id="exampleCheck1">
              <label class="form-check-label" for="exampleCheck1">Check me out</label>
            </div> -->
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-8">
      
      <table class="table table-hover table-striped" id="datatable">
         <thead  class="thead-dark">
            <tr>
               <th scope="col">Tanggal</th>
               <!-- <th scope="col" class="text-center">Untuk Tgl.</th> -->
               <th scope="col" class="text-center">Jenis</th>
               <th scope="col">Keterangan</th> 
               <th scope="col">File</th> 
               <th scope="col">Status</th>                            
            </tr>
         </thead>
         <tbody>
          <?php foreach ($izin->result_array() as $key) { ?>
            <tr>
              <td>
                <!-- Dibuat :<?php echo $key['tgl_pengajuan']?><br/> -->
                <?php echo $key['tgl_izin']?>                  
              </td>
              <!-- <td></td> -->
              <td><?php echo $key['jenis_izin']?></td>
              <td><?php echo $key['keterangan']?></td>
              <td>
                 <?php if(!empty($key['file'])){ ?>
                  <a href="<?php echo site_url('uploads/' . $key['file'])?>">Download</a>
                 <?php }else{ ?>
                  Tidak tersedia
                 <?php } ?>
              </td>
              <td><?php echo $key['status_izin']?></td>
            </tr>
          <?php } ?>
      
         </tbody>
      </table>
    </div>

  </div>
  <script>
    $('#datepicker').datepicker({
      format: 'yyyy-mm-dd',
      todayHighlight:true
    });
  </script>

