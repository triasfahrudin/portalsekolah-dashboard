 <link rel="stylesheet" type="text/css" href="<?php echo site_url('assets/vendors/event-calendar-evo/evo-calendar.css?uuid=' . uniqid())?>">
 <script src="<?php echo site_url('assets/vendors/event-calendar-evo/evo-calendar.js?uuid=' . uniqid())?>"></script>


<div id="evoCalendar"></div>

<script>
	function load_event(){
	    $.ajax({
	        url : "<?php echo site_url('siswa/load_events')?>",
	        type : "post",
	        async: false,
	        dataType: 'json',
	        data:{ },
	        success : function(data) { },
	        error: function() { }
	     }).done(function(data){

	     	var initialDate = new Date();	
	     	
	     	$("#evoCalendar").evoCalendar({
	      	    format: 'yyyy-mm-dd',
	      	    calendarEvents: data.events,
			    canAddEvent: false,
			    initialDate: initialDate.toString(),
	    		sidebarToggler: false,
	    		eventListToggler: true,	
	    		has_content:true			   
			  });
	     });		  
	  }

	  load_event();

	  function load_day_event(var_dayofweek,var_fullday){

	  	$.ajax({
	        url : "<?php echo site_url('siswa/load_day_event')?>",
	        type : "post",
	        async: false,
	        dataType: 'json',
	        data:{ dayofweek : var_dayofweek ,fullday:var_fullday},
	        success : function(data) { 

	        },
	        error: function() { }
	     }).done(function(data){
	     	// alert('test');

	     	$('.evo_calendar_content').html(data.table_jadwal);
	     	
	     });		  
	  }

</script>

