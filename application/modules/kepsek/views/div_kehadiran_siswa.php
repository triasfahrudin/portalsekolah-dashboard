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
    	<tr <?php echo ($row['libur'] === 'LIBUR' ? 'class="table-danger"' : '')?>  >
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