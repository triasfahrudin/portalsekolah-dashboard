<table class="table table-hover table-striped table-bordered" id="datatable">
   <thead  class="thead-dark">
      <tr>
         <th scope="col">Tanggal</th>
         <th scope="col" class="text-center">Masuk</th>
         <th scope="col" class="text-center">Pulang</th>
         <th scope="col" class="text-center">Status</th> 
         <th scope="col" class="text-center">Nilai Presensi</th>          
      </tr>
   </thead>
   <tbody>
   	<?php 
    	$total = 0;
    	foreach ($presensi->result_array() as $row) { ?>
    	<tr <?php echo ($row['libur'] === 'LIBUR' ? 'class="table-danger"' : '')?>  >
    		<td><?php echo convert_sql_date_to_date($row['fulldate']);?></td>
        <?php if($row['libur'] === 'LIBUR'){ ?>
        <td class="text-center" colspan="4">LIBUR</td>  
        <?php }else{ ?> 
        <td class="text-center"><?php echo $row['jam_masuk'];?></td>
        <td class="text-center"><?php echo $row['jam_pulang'];?></td>
        <td class="text-center"><?php echo $row['status'];?></td>       
        <td class="text-center"><?php echo $row['nilai'];?></td>        
        <?php }?>    		
        
    	</tr>
	    <?php $total += $row['nilai']; 
	} ?>
	
	<tr>
		<td colspan="4" class="text-right">Total Nilai Presensi</td>
		<td class="text-center"><?php echo $total?></td>
	</tr>    
   </tbody>
</table>