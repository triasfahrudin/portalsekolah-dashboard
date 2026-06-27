<div class="row">
    <div class="col-lg-12">
        <div class="section-title">

        </div>
        <div class="section-body">
            <div class="card container-fluid" id="panel_example">
                <div class="card-header row">
                    <h2>SIBISMAKLB</h2>

                </div>
                <div class="card-body">
                    <table id="table_sekolah" class="table table-bordered table-striped" style="width:100%">
                        <caption>Data Sekolah</caption>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Prestasi</th>
                                <th>Ekstrakulikuler</th>
                                <th>Lulusan</th>
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
                <h5 class="modal-title">Detail Data Diri</h5>
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
    Cookies.set("kabupaten_id", '');

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
            "url": "<?php echo site_url('signin/get-sibismaklb/'); ?>" + bentuk_pendidikan,
            "type": "POST",
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
            },
            {
                "data": "id"
            },
            {
                "data": "id"
            }

        ],
        "columnDefs": [{
            "render": function(data, type, row) {
                let show = '<a href="#" class="detail" id="' + row['id'] + '" onclick="show_detail(1,\'' + row['id'] + '\')">Lihat</a>';
                let download = '<a href="#" class="detail" onclick="getfile(1,\'' + row['id'] + '\',\'prestasi-' + slugify(row['nama']) + '\')">Download</a>';
                return show + ' - ' + download;

            },
            "targets": 1,
            "orderable": false
        }, {
            "render": function(data, type, row) {
                let show = '<a href="#" class="detail" id="' + row['id'] + '" onclick="show_detail(2,\'' + row['id'] + '\')">Lihat</a>';
                let download = '<a href="#" class="detail" onclick="getfile(2,\'' + row['id'] + '\',\'ekstrakulikuler-' + slugify(row['nama']) + '\')">Download</a>';
                return show + ' - ' + download;
            },
            "targets": 2,
            "orderable": false
        }, {
            "render": function(data, type, row) {
                let show = '<a href="#" class="detail" id="' + row['id'] + '" onclick="show_detail(3,\'' + row['id'] + '\')">Lihat</a>';
                let download = '<a href="#" class="detail" onclick="getfile(3,\'' + row['id'] + '\',\'alumni-' + slugify(row['nama']) + '\')">Download</a>';
                return show + ' - ' + download;


            },
            "targets": 3,
            "orderable": false
        }],
        // "fnDrawCallback": function() {

        // },
        // "initComplete": function(oSettings, json) {

        // }
    });



    /**
     * The function "download" is used to send a POST request to a PHP file and create a direct
     * download link for the response.
     */
    /* The `download` function is used to send a POST request to a PHP file and create a direct
    download link for the response. It takes two parameters: `jenis` and `sekolah_id`. */
    function getfile(jenis, sekolah_id, nama_file) {
        $.ajax({
            url: "<?php echo base_url('signin/download-sibismaklb'); ?>",
            type: "POST",
            data: {
                jenis: jenis,
                sekolah_id: sekolah_id,
                nama_file: nama_file

            },
            success: function(response) {
                // Membuat tautan unduhan langsung
                window.open("<?php echo site_url('temp/') ?>" + nama_file + ".pdf", '_blank');
            }
        });


    }

    function show_detail(jenis, sekolah_id) {
        // Membuat objek yang memetakan nilai "jenis" ke URL yang sesuai
        const urlMap = {
            1: "<?php echo site_url('signin/show-prestasi') ?>",
            2: "<?php echo site_url('signin/show-ekstrakulikuler') ?>",
            3: "<?php echo site_url('signin/show-alumni') ?>"
        };

        // Memeriksa apakah "jenis" terdapat dalam pemetaan objek
        if (urlMap.hasOwnProperty(jenis)) {
            $.get(urlMap[jenis], {
                    sekolah_id: sekolah_id,
                })
                .done(function(data) {
                    $('#myModal').find('.modal-body').html(data);
                    $('#myModal').modal('show');
                });
        } else {
            // Handle other cases or provide an error message if needed
        }
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
</script>