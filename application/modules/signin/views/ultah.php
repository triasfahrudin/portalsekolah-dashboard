<link href="https://cdn.datatables.net/fixedcolumns/3.2.2/css/fixedColumns.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/fixedcolumns/3.2.2/js/dataTables.fixedColumns.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js" integrity="sha512-wT7uPE7tOP6w4o28u1DN775jYjHQApdBnib5Pho4RB0Pgd9y7eSkAV1BTqQydupYDB9GBhTcQQzyNMPMV3cAew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<div class="row">
    <div class="col-lg-12">
        <div class="section-title">
            <!-- <h2>Daftar PNS yang ulang tahun</h2> -->
        </div>
        <div class="section-body">
            <div class="card container-fluid" id="panel_example">
                <div class="card-header row">
                    <h2>Daftar PNS yang Ulang Tahun</h2>

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
                            <option value="hari-ini" selected>Hari ini</option>
                            <option value="besok">Besok</option>
                            <option value="minggu-depan">Minggu depan</option>
                        </select>
                    </div>

                    <table id="table_ultah" class="table table-bordered table-striped" style="width:100%">
                        <caption>PNS Yang Ulang Tahun</caption>
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
    Cookies.set("timeline", 'hari-ini');
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
            "url": "<?php echo site_url('signin/get-pegawai-ultah'); ?>",
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
        // "columnDefs": [
        //     {
        //         "targets": 2,
        //         "orderable": false
        //     }
        // ],
        // "fnDrawCallback": function() {

        // },
        // "initComplete": function(oSettings, json) {

        // }
    });





    let satuan_pendidikan = document.getElementById('satuan_pendidikan');


    satuan_pendidikan.addEventListener("change", function() {
        Cookies.set("satuan_pendidikan", satuan_pendidikan.value);
        datatable.fnDraw(false);
    });

    let timeline = document.getElementById('timeline');

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