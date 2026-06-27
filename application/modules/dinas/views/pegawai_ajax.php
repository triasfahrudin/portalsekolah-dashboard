<style>
   .modal-dialog {
      width: 60%;
      height: 60%;
      padding: 0;
      margin: 0;
   }

   .modal-content {
      height: 100%;
      min-height: 100%;
      height: auto;
      border-radius: 0;
   }

   .modal .modal-body {
      /*max-height: 520px;*/
      /*max-width: 900px;*/
      overflow-y: auto;
   }

   .vertical-alignment-helper {
      display: table;
      height: 100%;
      width: 100%;
      pointer-events: none;
   }

   .vertical-align-center {
      /* To center vertically */
      display: table-cell;
      vertical-align: middle;
      pointer-events: none;
   }

   .modal-content {
      width: inherit;
      height: inherit;
      margin: 0 auto;
      pointer-events: all;
   }

   img.displayed {
      display: block;
      margin-left: auto;
      margin-right: auto
   }

   div.dataTables_processing {
      z-index: 9999;
   }
</style>

<link href="https://cdn.datatables.net/fixedcolumns/3.2.2/css/fixedColumns.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/fixedcolumns/3.2.2/js/dataTables.fixedColumns.min.js"></script>
<div class="panel panel-default" id="panel_example">
   <div class="panel-heading">
      <?php echo $page_title ?>
      <!-- Single button -->


      <!-- <div class="float-right">
         <div class="btn-group mr-2">
            <div class="btn-group dropleft" role="group">
               <button id="btnGroupDrop1" type="button" class="btn btn-info">
                  <a class="dropdown-item" href="http://127.0.0.1/presensi-sekolah-2023-v2/dinas/export-all-pegawai">Exp. Semua Pegawai</a>
               </button>
               <button id="btnGroupDrop1" type="button" class="btn btn-success">
                  <a class="dropdown-item" href="http://127.0.0.1/presensi-sekolah-2023-v2/dinas/export-all-siswa">Exp. Semua Siswa</a>
               </button>


            </div>
         </div>
      </div> -->

      <div class="btn-group float-end mb-2 ml-2">
         <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            Export PPPK <span class="caret"></span>
         </button>
         <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?php echo site_url('dinas/export-data/pppk/' . base64url_encode('pdf-' . $this->uri->segment(3))) ?>">Export ke PDF</a></li>
            <li><a class="dropdown-item" href="<?php echo site_url('dinas/export-data/pppk/' . base64url_encode('excel-' . $this->uri->segment(3))) ?>">Export ke Excel</a></li>
         </ul>
      </div>

      <div class="btn-group float-end mb-2 ml-2">
         <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            Export PNS <span class="caret"></span>
         </button>
         <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?php echo site_url('dinas/export-data/pns/' . base64url_encode('pdf-' . $this->uri->segment(3))) ?>">Export ke PDF</a></li>
            <li><a class="dropdown-item" href="<?php echo site_url('dinas/export-data/pns/' . base64url_encode('excel-' . $this->uri->segment(3))) ?>">Export ke Excel</a></li>
         </ul>
      </div>

      <div class="btn-group float-end mb-2">
         <button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            Export Semua <span class="caret"></span>
         </button>
         <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?php echo site_url('dinas/export-data/semua/' . base64url_encode('pdf-' . $this->uri->segment(3))) ?>">Export ke PDF</a></li>
            <li><a class="dropdown-item" href="<?php echo site_url('dinas/export-data/semua/' . base64url_encode('excel-' . $this->uri->segment(3))) ?>">Export ke Excel</a></li>
         </ul>
      </div>

   </div>
   <div class="panel-body">
      <table aria-describedby="mydesc" id="example" class="display nowrap">
         <thead>
            <tr>
               <th>NIP</th>
               <th>Nama</th>
               <th>Status</th>
               <th>Gelar depan</th>
               <th>Gelar belakang</th>
               <th>Jenis kelamin</th>
               <th>Tempat lahir</th>
               <th>Tanggal lahir</th>
               <th>Jml Istri/Suami</th>
               <th>Jml Anak</th>
               <th>SKPD</th>
               <th>SATKER</th>
               <th>Dokumen</th>
            </tr>
         </thead>
         <tbody>

         </tbody>
      </table>
   </div>
</div>

<!-- Modal -->

<div class="modal fade" id="modalDokumen" role="dialog">
   <div class="vertical-alignment-helper">
      <div class="modal-dialog vertical-align-center" style="width: 70%;  height: 80%; padding: 0; margin: 0;">
         <!-- Modal content-->
         <div class="modal-content" style="height: 60%; min-height: 80%;  border-radius: 0; ">
            <div class="modal-header">
               <h5 class="modal-title">Dokumen</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDokumenBody">

            </div>

         </div>
      </div>
   </div>
</div>

<script>
   function base64url_encode(data) {
      var str = base64.encode(data);
      str = str.replace('+', '-');
      str = str.replace('==', '');

      return str.replace('/', '_');
   }

   var datatable = $('#example').dataTable({
      "processing": true,
      "serverSide": true,
      "scrollX": true,
      "bSort": true,
      "pageLength": 10,
      "fixedColumns": {
         leftColumns: 1
      },
      ajax: {
         url: "<?php echo base_url(); ?>restapi/pegawai_ajax",
         type: "POST",
         data: function(d) {
            d.slug = "<?php echo $this->uri->segment(3) ?>"
         },
         complete: function() {

         },
         beforeSend: function() {


         }
      },

      "oLanguage": {
         "sProcessing": "Memproses Data....."
      },

      "columns": [{
            "data": "nip" //0
         },
         {
            "data": "nama_lengkap"
         },
         {
            "data": "status_pegawai"
         },
         {
            "data": "gelar_depan"
         },
         {
            "data": "gelar_belakang"
         },
         {
            "data": "jk"
         },
         {
            "data": "tempat_lahir"
         },
         {
            "data": "tgl_lahir" //7
         },
         {
            "data": "jml_istri"
         },
         {
            "data": "jml_anak"
         },
         {
            "data": "nama_skpd"
         },
         {
            "data": "nama_satker" //11
         },
         {
            "data": "dokumen"
         }
      ],
      "columnDefs": [
         // {
         //    "targets":0,
         //    "orderable": false

         // },
         {
            "targets": 10,
            "orderable": false,
            "searchable": false
         },
         {
            "targets": 11,
            "orderable": false,
            "searchable": false
         },

         {
            "render": function(data, type, row) {
               var dok_count = data.split('-');
               var jml_upload = '<span class="badge bg-secondary">' + dok_count[0] + '</span>&nbsp';
               var jml_diterima = '<span class="badge bg-success">' + dok_count[1] + '</span>&nbsp';
               var jml_ditolak = '<span class="badge bg-danger">' + dok_count[2] + '</span>';

               if (dok_count[0] === '00') {
                  return '<span class="badge bg-secondary">Belum ada dokumen yang diunggah</span>';
               } else {
                  return '<a onclick="load_dokumen(\'' + row['id'] + '\')" class="btn btn-default">' + jml_upload + jml_diterima + jml_ditolak + '</a>';
               }

            },
            "targets": 12,
            "orderable": false,
            "searchable": false
         }
      ],
      "fnDrawCallback": function() {

      },
   });




   $('#example')
      .on('processing.dt', function(e, settings, processing) {
         //  $('#processingIndicator').css( 'display', processing ? 'block' : 'none' );
         if (processing) {
            $('#panel_example').block({
               message: "",
               css: {
                  width: '100px',
                  left: '50%'
               },
               centerX: false,
            });
         } else {
            $('#panel_example').unblock({});
         }
      })
      .dataTable();

   function load_dokumen(pegawai_id) {
      // alert(pendaftar_id);
      event.preventDefault();
      $.get("<?php echo site_url('dinas/load_dokumen') ?>", {
            pegawai_id: pegawai_id,
            slug: base64url_encode('<?php echo $this->uri->segment(3) ?>')
         })
         .done(function(data) {

            $('#modalDokumenBody').html(data);
            $('#modalDokumen').modal('show');
         })

   }




   function change_status(id_dokumen_pegawai, status) {
      //diterima,ditolak
      if (status == 'diterima') {
         var return_confirm = confirm('Apakah anda yakin ingin menerima berkas ini?');
         if (return_confirm) {
            $.ajax({
               url: "<?php echo site_url('dinas/set_verifikasi_berkas/diterima/') ?>" + id_dokumen_pegawai
            }).done(function(msg) {
               $('#td_' + id_dokumen_pegawai).html(msg);
            });
         }

      } else {
         var return_confirm = window.prompt('Alasan penolakan berkas?');
         if (return_confirm) {
            const data = {
               alasan: return_confirm
            };

            $.ajax({
               url: "<?php echo site_url('dinas/set_verifikasi_berkas/ditolak/') ?>" + id_dokumen_pegawai,
               method: "POST",
               data: data // Menambahkan objek data ke dalam permintaan Ajax
            }).done(function(msg) {
               $('#td_' + id_dokumen_pegawai).html(msg);
            });
         }
      }

      // datatable.ajax.reload(null,false);

   }

   $('#modalDokumen').on('hidden.bs.modal', function() {
      $('#example').DataTable().ajax.reload(null, false)
   })
</script>