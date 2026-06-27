 <div class="row">
   <div class="col-md-6">
     <div class="card">
       <div class="card-header text-white bg-primary">Form Verifikasi dan Validasi Data Siswa</div>
       <div class="card-body">
         <form id="myForm" method="POST" action="" enctype="multipart/form-data">
           <div class="form-group">
             <label for="exampleInputEmail1">Kolom Nama</label>
             <input type="text" class="form-control" name="txtNama" value="E">
           </div>

           <div class="form-group">
             <label for="exampleInputEmail1">Kolom Asal sekolah</label>
             <input type="text" class="form-control" name="txtAsalSekolah" value="L" required>
           </div>

           <div class="form-row">
             <div class="form-group col-md-4">
               <label for="inputEmail4">Kolom NIK</label>
               <input type="text" class="form-control" id="txtNik" name="txtNik" value="B" required>
             </div>
             <div class="form-group col-md-4">
               <label for="inputPassword4">Kolom No.KK</label>
               <input type="text" class="form-control" id="txtKk" name="txtKk" value="C" required>
             </div>
             <div class="form-group col-md-4">
               <label for="inputPassword4">Kolom NISN</label>
               <input type="text" class="form-control" id="txtNisn" name="txtNisn" value="D" required>
             </div>

           </div>

           <div class="form-group">
             <label for="exampleFormControlFile1">Masukkan file excel</label>
             <input type="file" class="form-control-file" id="exampleFormControlFile1" name="file" required>
             <small id="emailHelp" class="form-text text-muted">

             </small>
           </div>
           <!-- <div class="form-check">
              <input type="checkbox" class="form-check-input" id="exampleCheck1">
              <label class="form-check-label" for="exampleCheck1">Check me out</label>
            </div> -->
           <button id="clickme" class="btn btn-primary">Submit</button>
         </form>
       </div>
     </div>
   </div>

   <div class="col-md-8">


   </div>

 </div>

 <script>
//    $(document).ready(function() {
//      // Menangkap event klik pada link
//      $('#clickme').on('click', function(e) {
//        e.preventDefault(); // Mencegah link untuk melakukan navigasi langsung

//        // Mulai AJAX
//        // Mengambil nilai dari form
//        var formData = new FormData($('#myForm')[0]);

//        // Menambahkan Loader sebelum memulai AJAX
//        Loader.open();

//        // Mengirim data melalui AJAX
//        $.ajax({
//          url: '<?php echo site_url('dinas/verval_data_siswa') ?>',
//          type: 'POST',
//          data: formData,
//          processData: false,
//          contentType: false,
//          success: function(response) {
//            // Menutup Loader setelah AJAX selesai
//            Loader.close();

//            // Lakukan redirect jika diperlukan
//            //  window.location.href = 'halaman_baru.php';

//            // Mengonversi respons menjadi Blob
//            var blob = new Blob([response], {
//              type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
//            });

//            // Membuat URL untuk Blob
//            var blobUrl = URL.createObjectURL(blob);

//            // Membuat link untuk mendownload file
//            var link = document.createElement('a');
//            link.href = blobUrl;
//            link.download = 'nama_file.xlsx'; // Ganti 'nama_file.xlsx' dengan nama file yang diinginkan
//            document.body.appendChild(link);

//            // Klik link untuk memulai proses download
//            link.click();

//            // Menghapus link setelah proses download selesai
//            document.body.removeChild(link);
//          },
//          error: function() {
//            // Menutup Loader jika terjadi kesalahan
//            Loader.close();

//            // Tampilkan pesan kesalahan atau lakukan tindakan lainnya
//            alert('Terjadi kesalahan. Silakan coba lagi.');
//          }
//        });
//      });
//    });
//  </script>
