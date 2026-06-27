<div class="row">
    <div class="col-lg-12">
        <div class="section-title">

        </div>
        <div class="section-body">
            <div class="card container-fluid" id="panel_example">
                <div class="card-header row">
                    <h2>MONITORING</h2>

                </div>
                <div class="card-body">
                    <table id="table_sekolah" class="table table-bordered table-striped" style="width:100%">
                        <caption>Data Sekolah</caption>
                        <thead>
                            <tr>
                                <th>Nama sekolah</th>
                                <th>Rekap Mengajar</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    /* CSS untuk modal fullscreen */
    .modal.modal-fullscreen .modal-dialog {
        width: 80vw;
        height: 100vh;
        margin: auto;
        padding: 0;
        max-width: none;
    }

    .modal.modal-fullscreen .modal-content {
        height: auto;
        height: 100vh;
        border-radius: 0;
        border: none;
    }

    .modal.modal-fullscreen .modal-body {
        overflow-y: auto;
    }
</style>
<div class="modal fade modal-fullscreen" id="myModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail kegiatan mengajar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Isi modal Anda di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
    // Cookies.set("kabupaten_id", '');

    function slugify(text) {
        return text.toString().toLowerCase()
            .replace(/\s+/g, '-') // Ganti spasi dengan tanda -
            .replace(/[^\w-]+/g, '') // Hapus karakter khusus
            .replace(/--+/g, '-'); // Ganti beberapa tanda - berturut-turut dengan satu -
    }


    var bentuk_pendidikan = '<?php echo $bp ?>';
    var datatable = $('#table_sekolah').dataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "bSort": true,
        "pageLength": 50,
        "order": [
            [0, "asc"]
        ],
        ajax: {
            url: "<?php echo site_url('signin/get-monitoring/'); ?>" + bentuk_pendidikan,
            type: "POST",
            data: function(d) {
                d.loaddata = 1;
                d.kabupaten_id = Cookies.get('kabupaten_id');
            }
        },

        "oLanguage": {
            "sProcessing": "Memproses Data....."
        },

        "columns": [

            {
                "data": "nama"
            },
            {
                "data": "id"
            }

        ],
        "columnDefs": [{
            "render": function(data, type, row) {
                let show = '<a href="#" class="detail" id="' + row['id'] + '" onclick="show_detail(\'' + row['id'] + '\')">Lihat</a>';

                return show;

            },
            "targets": 1,
            "orderable": false
        }, ],
        // "fnDrawCallback": function() {

        // },
        // "initComplete": function(oSettings, json) {

        // }
    });

    function show_detail(sekolah_id) {

        // Memeriksa apakah "jenis" terdapat dalam pemetaan objek
        $.get('<?php echo site_url('signin/show-monitoring') ?>', {
                sekolah_id: sekolah_id,
            })
            .done(function(data) {
                $('#myModal').find('.modal-body').html(data);
                $('#myModal').modal('show');     
            });

    }


    $(document).ready(function() {

        var select = $('<select/>', {
            'class': '',
            'aria-label': 'Show entries',
            'style': 'text-align:right;float:right;height:30px;',
            'id': 'kabupaten_id'
        }).appendTo($('#table_sekolah_filter'));

        $('<option/>', {
            'value': '',
            'text': 'Semua kabupaten'
        }).appendTo(select);

        <?php foreach ($kabupaten->result_array() as $k) { ?>
            $('<option/>', {
                'value': '<?php echo $k['id'] ?>',
                'text': '<?php echo $k['nama'] ?>'
            }).appendTo(select);
        <?php } ?>


        let kabupaten_id = document.getElementById('kabupaten_id');

        $("#kabupaten_id").val(Cookies.get('kabupaten_id'));

        kabupaten_id.addEventListener("change", function() {
            Cookies.set("kabupaten_id", kabupaten_id.value);
            datatable.fnDraw(true);
        });
    });

    $('#myModal').on('hidden.bs.modal', function () {
        // Mereload atau memuat ulang halaman
        location.reload();
    });
</script>