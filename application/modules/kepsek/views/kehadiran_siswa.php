 <div class="row">                     
    <div class="col-md-2">
    	<div class="card mb-3" style="max-width: 20rem;">
		    <div class="card-header text-white bg-primary">Filter</div>
		    <div class="card-body">
		      <form method="POST" action="">
		      	<select class="form-control mb-2" name="filter_tahun" required="" id="filter_tahun">
		      	  <option value="">PILIH TAHUN</option>	
		      	  <?php for ($i = (int)2020; $i <= (int) date('Y') ; $i++) {  ?>
		      	 	<option <?php echo set_select('filter_tahun', $i); ?>  value="<?php echo $i;?>"><?php echo $i;?></option>
		      	  <?php } ?>		      		
		      	</select>
		      	<select class="form-control mb-2" name="filter_bulan" required="" id="filter_bulan">
		      	  <option value="">PILIH BULAN</option>	
		      	   <option <?php echo set_select('filter_bulan', '1');?> value="1">JANUARI</option>
		      	   <option <?php echo set_select('filter_bulan', '2');?> value="2">FEBRUARI</option>
		      	   <option <?php echo set_select('filter_bulan', '3');?> value="3">MARET</option>
		      	   <option <?php echo set_select('filter_bulan', '4');?> value="4">APRIL</option>
		      	   <option <?php echo set_select('filter_bulan', '5');?> value="5">MEI</option>
		      	   <option <?php echo set_select('filter_bulan', '6');?> value="6">JUNI</option>
		      	   <option <?php echo set_select('filter_bulan', '7');?> value="7">JULI</option>
		      	   <option <?php echo set_select('filter_bulan', '8');?> value="8">AGUSTUS</option>
		      	   <option <?php echo set_select('filter_bulan', '9');?> value="9">SEPTEMBER</option>
		      	   <option <?php echo set_select('filter_bulan', '10');?> value="10">OKTOBER</option>
		      	   <option <?php echo set_select('filter_bulan', '11');?> value="11">NOVEMBER</option>
		      	   <option <?php echo set_select('filter_bulan', '12');?> value="12">DESEMBER</option>
		      	</select>
		      	<button type="submit" class="btn btn-block btn-success">Tampilkan</button>
		      </form>
		    </div>
		  </div>
    </div>
    <div class="col-md-10">
    	
    	<table class="table table-hover table-striped" id="datatable">
		   <thead  class="thead-dark">
		      <tr>
		         <th scope="col">NISN</th>
		         <th scope="col">Nama Siswa</th>	
		         <th scope="col" class="text-center">Hadir</th>		         		         		         
		         <th scope="col" class="text-center">Izin</th>	
		         <th scope="col" class="text-center">Sakit</th>	
		         <th scope="col" class="text-center">Alpa</th>	
		         <th scope="col"></th>		         
		      </tr>
		   </thead>
		   <tbody>
		   	<?php if(!empty($_POST)){ ?>
			    <?php foreach ($presensi->result_array() as $row) { ?>
			    	<tr>
			    		<td><?php echo $row['nisn'];?></td>
			    		<td><?php echo $row['nama_lengkap'];?></td>
			    		<td class="text-center"><?php echo $row['hadir'];?></td>
			    		<td class="text-center"><?php echo $row['izin'];?></td>
			    		<td class="text-center"><?php echo $row['sakit'];?></td>
			    		<td class="text-center"><?php echo $row['alpa'];?></td>
			    		<td>
			    			<button onclick="detail_presensi(<?php echo $row['id']?>)" type="button" class="btn btn-primary" data-toggle="tooltip" title="Detail">
							  <i class="fas fa-eye"></i>
							</button>
							<a href="<?php echo site_url('kepsek/download-presensi-siswa/' . $row['id'] . '/' . $_POST['filter_tahun'] . '/' . $_POST['filter_bulan'])?>" class="btn btn-success" data-toggle="tooltip" title="Download">
							  <i class="fas fa-cloud-download-alt"></i>
							</button>
			    		</td>
			    		</td>
			    	</tr>
			    <?php } ?>
			<?php } ?>
		   </tbody>
		</table>


		<!-- Modal -->
		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" style=" min-width: 100%; margin: 0;"  role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">Detail Presensi</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button>
		      </div>
		      <div class="modal-body">
		        <div id="evoCalendar"></div>
		      </div>		     
		    </div>
		  </div>
		</div>

		<script type="text/javascript">

		  function detail_presensi(var_siswa_id){		    
              $.ajax({
                url : "<?php echo site_url('kepsek/detail-presensi-siswa')?>",
                type : "post",
                async: false,
                dataType: 'json',
                data:{
                  siswa_id: var_siswa_id,
                  filter_tahun : $('#filter_tahun').val(),
                  filter_bulan : $('#filter_bulan').val(),
                },
                success : function(data) {
                  
                },
                error: function() {
                   
                }
             }).done(function(data){

             	$('.modal-body').html(data.presensi);
             	
             });

			  $('#exampleModal').modal('show');
		  }

		  $('#datatable').DataTable({
		  	  "ordering": false,
		      "fnDrawCallback": function() { },
		      "initComplete": function(settings, json) { }
		   });
		</script>

    </div>
</div>
  