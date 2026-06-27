<div class="form-row">
    <div class="col-md-2">
        <select class="form-control form-control-sm" id="tahun">
            <option value="<?php echo date('Y') ?>" selected><?php echo date('Y') ?></option>
            <option value="<?php echo date('Y') - 1 ?>"><?php echo date('Y') - 1 ?></option>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-control form-control-sm" id="bulan">
            <option <?php echo set_select('filter_bulan', '1', (1 == date('n') ? TRUE : FALSE)); ?> value="1">Januari</option>
            <option <?php echo set_select('filter_bulan', '2', (2 == date('n') ? TRUE : FALSE)); ?> value="2">Februari</option>
            <option <?php echo set_select('filter_bulan', '3', (3 == date('n') ? TRUE : FALSE)); ?> value="3">Maret</option>
            <option <?php echo set_select('filter_bulan', '4', (4 == date('n') ? TRUE : FALSE)); ?> value="4">April</option>
            <option <?php echo set_select('filter_bulan', '5', (5 == date('n') ? TRUE : FALSE)); ?> value="5">Mei</option>
            <option <?php echo set_select('filter_bulan', '6', (6 == date('n') ? TRUE : FALSE)); ?> value="6">Juni</option>
            <option <?php echo set_select('filter_bulan', '7', (7 == date('n') ? TRUE : FALSE)); ?> value="7">Juli</option>
            <option <?php echo set_select('filter_bulan', '8', (8 == date('n') ? TRUE : FALSE)); ?> value="8">Agustus</option>
            <option <?php echo set_select('filter_bulan', '9', (9 == date('n') ? TRUE : FALSE)); ?> value="9">September</option>
            <option <?php echo set_select('filter_bulan', '10', (10 == date('n') ? TRUE : FALSE)); ?> value="10">Oktober</option>
            <option <?php echo set_select('filter_bulan', '11', (11 == date('n') ? TRUE : FALSE)); ?> value="11">November</option>
            <option <?php echo set_select('filter_bulan', '12', (12 == date('n') ? TRUE : FALSE)); ?> value="12">Desember</option>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-control form-control-sm" id="status_mengajar">
            <option value="0" selected>Semua status</option>
            <option value="1">Mengajar</option>
            <option value="2">Tidak mengajar</option>
        </select>
    </div>
</div>


<div class="alert alert-warning" style="margin-top: 10px;" id="tgl">

</div>
<table id="table" class="table table-bordered table-striped" style="width:100%">
    <caption>Monitoring</caption>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Tanggal</th>
            <th>Hari</th>
            <th>Jam mulai</th>
            <th>Jam selesai</th>
            <th>Kelas</th>
            <th>Mata pelajaran</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<!-- <style>
    .dataTables_wrapper {
        width: 100%;
    }

    .modal-dialog {
        max-width: none;
    }
</style> -->



<script type="text/javascript">
    function formatDate(date) {
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        return day + "/" + month + "/" + year;
    }

    var sekolah_id = "<?php echo $sekolah_id ?>";

    var datatable = $('#table').dataTable({
        "processing": true,
        "serverSide": true,
        "scrollX": true,
        "bSort": true,
        "pageLength": 10,
        "order": [
            [0, "asc"]
        ],
        ajax: {
            "url": "<?php echo site_url('signin/get-detail-monitoring'); ?>",
            "type": "POST",
            "data": function(d) {
                d.sekolah_id = sekolah_id;
                d.tahun = $('#tahun').val();
                d.bulan = $('#bulan').val();
                d.tanggal = Cookies.get('tanggal');
                d.status_mengajar = $('#status_mengajar').val();

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
                "data": "tgl"
            },
            {
                "data": "hari"
            },
            {
                "data": "jam_mulai"
            },
            {
                "data": "jam_selesai"
            },
            {
                "data": "nama_kelas"
            },
            {
                "data": "matapelajaran"
            },
            {
                "data": "status"
            }

        ],
        "columnDefs": [{
            "render": function(data, type, row) {

                if (row['status'] == 'terima') {
                    return '<span class="badge badge-success">Mengajar</span>';
                } else {
                    // return '<span class="badge badge-danger">Tidak Mengajar</span>';
                    var today = new Date();
                    var tglParts = row['tgl'].split('/');
                    var tglRow = new Date(tglParts[2], tglParts[1] - 1, tglParts[0]);

                    if (tglRow.toDateString() === today.toDateString()) {
                        return '<span class="badge badge-warning">Menunggu verifikasi</span>';
                    } else {
                        return '<span class="badge badge-danger">Tidak Mengajar</span>';
                    }
                }

            },
            "targets": 7,
            "orderable": false
        }, {
            "render": function(data, type, row) {
                return '<div class="text-success">' + row['tgl'] + '</div>';
            },
            "visible": false,
            "targets": 1
        }, {

            "targets": 7,
            "searchable": false

        }],
        drawCallback: function(settings) {
            var api = this.api();
            var rows = api.rows({
                page: 'current'
            }).nodes();
            var last = null;

            api.column(1, {
                    page: 'current'
                })
                .data()
                .each(function(group, i) {
                    if (last !== group) {
                        $(rows)
                            .eq(i)
                            .before(
                                '<tr class="group text-success text-center"><td colspan="7">' +
                                group +
                                '</td></tr>'
                            );

                        last = group;
                    }
                });
        }

    });

    // Order by the grouping
    $('#table tbody').on('click', 'tr.group', function() {
        var currentOrder = table.order()[0];
        if (currentOrder[0] === 1 && currentOrder[1] === 'asc') {
            table.order([1, 'desc']).draw();
        } else {
            table.order([1, 'asc']).draw();
        }
    });


    var tahun = document.getElementById('tahun');
    tahun.addEventListener("change", function() {
        //Cookies.set("satuan_pendidikan", satuan_pendidikan.value);
        Cookies.set("tanggal", 0);
        $.post("<?php echo site_url('signin/get-list-hari-aktif'); ?>", {
            tahun: $('#tahun').val(),
            bulan: $('#bulan').val(),

        }, function(data) {
            $('#tgl').html(data);
            datatable.fnDraw(false);
        })


    });

    var bulan = document.getElementById('bulan');
    bulan.addEventListener("change", function() {
        Cookies.set("tanggal", 0);
        $.post("<?php echo site_url('signin/get-list-hari-aktif'); ?>", {
            tahun: $('#tahun').val(),
            bulan: $('#bulan').val(),

        }, function(data) {
            $('#tgl').html(data);
            datatable.fnDraw(false);
        })
    });

    var status_mengajar = document.getElementById('status_mengajar');
    status_mengajar.addEventListener("change", function() {
        //Cookies.set("timeline", timeline.value);
        datatable.fnDraw(false);
    });


    function set_cookie(hari) {
        // Menghapus kelas "badge badge-success" dari semua link
        var links = document.querySelectorAll(".link-hari");
        for (var i = 0; i < links.length; i++) {
            links[i].classList.remove("badge", "badge-success");

            if (!links[i].classList.contains("text-success")) {
                links[i].classList.add("text-success");
            }
        }

        // Menambahkan kelas "badge badge-success" ke link yang diklik
        var clickedLink = document.querySelector('[onclick="set_cookie(\'' + hari + '\')"]');
        if (clickedLink) {
            clickedLink.classList.remove("text-success");
            clickedLink.classList.add("badge", "badge-success");
        }

        Cookies.set("tanggal", hari);
        datatable.fnDraw(false);
    }

    $.post("<?php echo site_url('signin/get-list-hari-aktif'); ?>", {
        tahun: $('#tahun').val(),
        bulan: $('#bulan').val(),
    }, function(data) {
        $('#tgl').html(data);
        Cookies.set("tanggal", 0);
        datatable.fnDraw(false);
        // $('#link-semua-hari').click(function() {
            // Lakukan sesuatu setelah link terclick
            

            // alert('text');
        // });
    })

    $(document).ready(function() {
        // Klik link secara otomatis
        // datatable.fnDraw(true);
        // alert('test');
        // $('#link-semua-hari').click(function() {
            // Lakukan sesuatu setelah link terclick
        // });
        // document.getElementById('link-semua-hari').click();
    });

    $('#myModal').on('shown.bs.modal', function () {
        // Tambahkan kode JavaScript di sini
        console.log('Modal muncul sepenuhnya');
        // Misalnya, lakukan sesuatu ketika modal muncul
        datatable.fnDraw(true);
    });
</script>