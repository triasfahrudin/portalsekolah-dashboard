 <div class="row">                     
    <div class="col-md-2">
    	<div class="card mb-3" style="max-width: 20rem;">
		    <div class="card-header text-white bg-primary">Filter</div>
		    <div class="card-body">
		      <form method="POST" action="">
					<select class="form-control mb-2" name="filter_tahun" required="" id="filter_tahun">
						<option value="">PILIH TAHUN</option>
						<?php for ($i = (int)date('Y') - 1; $i <= (int) date('Y'); $i++) {  ?>
							<option <?php echo set_select('filter_tahun', $i, ($i == date('Y') ? TRUE : FALSE)); ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
						<?php } ?>
					</select>
					<select class="form-control mb-2" name="filter_bulan" required="" id="filter_bulan">
						<option value="">PILIH BULAN</option>
						<option <?php echo set_select('filter_bulan', '1', (1 == date('n') ? TRUE : FALSE)); ?> value="1">JANUARI</option>
						<option <?php echo set_select('filter_bulan', '2', (2 == date('n') ? TRUE : FALSE)); ?> value="2">FEBRUARI</option>
						<option <?php echo set_select('filter_bulan', '3', (3 == date('n') ? TRUE : FALSE)); ?> value="3">MARET</option>
						<option <?php echo set_select('filter_bulan', '4', (4 == date('n') ? TRUE : FALSE)); ?> value="4">APRIL</option>
						<option <?php echo set_select('filter_bulan', '5', (5 == date('n') ? TRUE : FALSE)); ?> value="5">MEI</option>
						<option <?php echo set_select('filter_bulan', '6', (6 == date('n') ? TRUE : FALSE)); ?> value="6">JUNI</option>
						<option <?php echo set_select('filter_bulan', '7', (7 == date('n') ? TRUE : FALSE)); ?> value="7">JULI</option>
						<option <?php echo set_select('filter_bulan', '8', (8 == date('n') ? TRUE : FALSE)); ?> value="8">AGUSTUS</option>
						<option <?php echo set_select('filter_bulan', '9', (9 == date('n') ? TRUE : FALSE)); ?> value="9">SEPTEMBER</option>
						<option <?php echo set_select('filter_bulan', '10', (10 == date('n') ? TRUE : FALSE)); ?> value="10">OKTOBER</option>
						<option <?php echo set_select('filter_bulan', '11', (11 == date('n') ? TRUE : FALSE)); ?> value="11">NOVEMBER</option>
						<option <?php echo set_select('filter_bulan', '12', (12 == date('n') ? TRUE : FALSE)); ?> value="12">DESEMBER</option>
					</select>
		      	<button type="submit" class="btn btn-block btn-success" id="btnTampilkan">Tampilkan</button>
		      </form>
		    </div>
		  </div>
    </div>
    <div class="col-md-10">    	
		<table class="table table-hover table-striped" id="datatable">
			<thead class="thead-dark">
				<tr>
					<th scope="col">NUPTK</th>
					<th scope="col" class="text-center">Nama Pegawai</th>
					<th scope="col" class="text-center">Jabatan</th>
					<th scope="col" class="text-center">Nilai Presensi</th>
					<th scope="col"></th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($_POST)) { ?>
					<?php foreach ($presensi->result_array() as $row) { ?>
						<tr>
							<td><?php echo $row['nuptk']; ?></td>
							<td><?php echo $row['nama_lengkap']; ?></td>
							<td><?php echo $row['jabatan']; ?></td>
							<td class="text-center"><?php echo $row['nilai_presensi']; ?></td>
							<td>
								<button onclick="detail_presensi('<?php echo $row['id'] ?>')" type="button" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Detail">
									<i class="fas fa-eye"></i>
								</button>
								<a href="<?php echo site_url('dinas/download_presensi_pegawai/' . $row['id'] . '/' . $_POST['filter_tahun'] . '/' . $_POST['filter_bulan']) ?>" class="btn btn-success btn-sm" data-toggle="tooltip" title="Download">
									<i class="fas fa-cloud-download-alt"></i>
									</button>
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
		        
		      </div>		     
		    </div>
		  </div>
		</div>
		
		<div class="modal fade bd-example-modal-sm" id="downloadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<a style="margin: 10px;" id="btn_download_pdf" href="" class="btn btn-danger">Download PDF</a>
  					<a style="margin: 10px;" id="btn_download_xls" href="" class="btn btn-success">Download Excel</a>
				</div>
			</div>
		</div>

		<script type="text/javascript">

          function download_file(pdf_link,xls_link){
			$('#btn_download_pdf').attr("href",pdf_link);
			$('#btn_download_xls').attr("href",xls_link);  
			$('#downloadModal').modal('show');
		  }
		  function detail_presensi(var_pegawai_id){		    
              $.ajax({
                url : "<?php echo site_url('dinas/detail-presensi-pegawai')?>",
                type : "post",
                async: false,
                dataType: 'json',
                data:{
                  pegawai_id: var_pegawai_id,
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
			   "pageLength": 100,
		      "fnDrawCallback": function() { },
		      "initComplete": function(settings, json) { }
		   });

		   
			if (Cookies.get('run_default_filter') === 'on') {
				$('#btnTampilkan').click();
				/* alert(Cookies.get('run_default_filter')); */
				Cookies.set('run_default_filter', 'off');

			}
 	
		</script>

    </div>
</div>
  