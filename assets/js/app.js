$(document).ready(function () {
  var index = Cookies.get('active');
  $('.main-menu').find('li a').removeClass('active');
  $(".main-menu").find('li a').eq(index).addClass('active');
  $('.main-menu').on('click', 'li a', function (e) {
      // e.preventDefault();
      $('.main-menu').find('li a').removeClass('active');
      $(this).addClass('active');
      Cookies.set('active', $('.main-menu li a').index(this));
  });


  //---------------- operator -----------------------------
  $('#jadwal_mengajar_select_mp').change(function() {
    
    $.get( site_url + "get_kelas_mapel", { pegawai_id: int_pegawai_id, matapelajaran:  $(this).val() } )
    .done(function( data ) {
      //alert( "Data Loaded: " + data );
      $('#jadwal_mengajar_select_kelas').html(data);
    });

  });
  
});
//-----------------------------------------------------------


function filter_field(jenis,status){
    var index_field;

    if(status === 'open'){
       for (index_field = 0; index_field < jenis.length; ++index_field) {
            $('#' + jenis[index_field] + '_field_box').css('visibility','visible').show();
       }
    }else if(status === 'close'){
       for (index_field = 0; index_field < jenis.length; ++index_field) {
            $('#' + jenis[index_field] + '_field_box').css('visibility','hidden').hide();
        }
    }
 }   

 

// alert('text');