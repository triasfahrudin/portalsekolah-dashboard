<table class="table table-hover table-striped table-bordered" id="datatable">
   <thead  class="thead-dark">
      <tr>
         <th scope="col">Tanggal</th>
         <th scope="col" class="text-center">Status</th>          
      </tr>
   </thead>
   <tbody>
   	<?php 
    	$total = 0;
    	foreach ($presensi->result_array() as $row) { ?>

      <?php if ($row['status'] === 'LIBUR') { ?>
         <tr class="table-danger">  
      <?php } elseif($row['status'] === 'HADIR') { ?>
         <tr class="table-success">
      <?php } else { ?>
         <tr class="table-warning">
      <?php } ?>        

    	
    		<td><?php echo convert_sql_date_to_date($row['fulldate']);?></td>
        
        <?php if($row['libur'] === 'LIBUR'){ ?>
        <td class="text-center">LIBUR</td>  
        <?php }else{ ?>         
        <td class="text-center"><?php echo $row['status'];?></td>               
        <?php }?>    		
        
    	</tr>
	 
	<?php } ?>
	
   </tbody>
</table>