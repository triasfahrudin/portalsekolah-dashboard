<div class="row">
    <div class="col-lg-12">
        <div class="section-title">

        </div>
        <div class="section-body">
            <div class="card container-fluid" id="panel_example">
                <div class="card-header row">
                    <h2>Daftar PNS yang Pensiun</h2>

                </div>
                <div class="card-body">

                    <div class="form-group">
                        <select class="form-control" id="satuan_pendidikan">
                            <option value="ALL" selected>Pilih satuan pendidikan...</option>
                            <option value="SMA">SMA</option>
                            <option value="SMK">SMK</option>
                            <option value="SLB">SLB</option>
                        </select>

                    </div>
                    <div class="form-group">
                        <select class="form-control" id="timeline">
                            <option value="0" selected>Tahun ini</option>
                            <option value="1">Tahun depan</option>
                            <option value="2">2 Tahun kedepan</option>
                            <option value="3">3 Tahun kedepan</option>
                            <option value="4">4 Tahun kedepan</option>
                            <option value="5">5 Tahun kedepan</option>
                        </select>
                    </div>
                    <table id="table_ultah" class="table table-bordered table-striped" style="width:100%">
                        <caption>Proyeksi PNS Pensiun</caption>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Satuan Pendidikan</th>
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

<script type="text/javascript">
    Cookies.set("satuan_pendidikan", 'ALL');
    Cookies.set("timeline", '0');
    Cookies.set("kabupaten_id", '');

    var datatable = $('#table_ultah').dataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "bSort": true,
        "pageLength": 10,
        "order": [
            [0, "asc"]
        ],
        ajax: {
            "url": "<?php echo site_url('signin/get-pegawai-pensiun'); ?>",
            "type": "POST",
            "data": function(d) {
                d.satuan_pendidikan = Cookies.get('satuan_pendidikan');
                d.timeline = Cookies.get('timeline');
                d.kabupaten_id = Cookies.get('kabupaten_id');
            }
        },

        "oLanguage": {
            "sProcessing": "Memproses Data....."
        },

        "columns": [

            {
                "data": "nama_lengkap"
            },
            {
                "data": "nama_sekolah"
            }

        ],
        // "columnDefs": [{
        //     "targets": 2,
        //     "orderable": false
        // }],
        // "fnDrawCallback": function() {

        // },
        // "initComplete": function(oSettings, json) {

        // }
    });



    let satuan_pendidikan = document.getElementById('satuan_pendidikan');
    // $("#satuan_pendidikan").val(Cookies.get('satuan_pendidikan'));

    satuan_pendidikan.addEventListener("change", function() {
        Cookies.set("satuan_pendidikan", satuan_pendidikan.value);
        datatable.fnDraw(false);
    });

    let timeline = document.getElementById('timeline');
    // $("#timeline").val(Cookies.get('timeline'));

    timeline.addEventListener("change", function() {

        Cookies.set("timeline", timeline.value);
        datatable.fnDraw(false);
    });

    $(document).ready(function() {

        var select = $('<select/>', {
            'class': '',
            'aria-label': 'Show entries',
            'style': 'text-align:right;float:right;height:30px;',
            'id': 'kabupaten_id'
        }).appendTo($('#table_ultah_filter'));

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