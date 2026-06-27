<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
   <link rel="shortcut icon" href="https://portalsekolah.disdik.jambiprov.go.id/favicon.png">
    <title>Dashboard Pendidikan Disdik. Prov. Jambi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="<?= base_url('/assets') ?>/extra-libs/css-chart/css-chart.css" rel="stylesheet">
    <link href="<?= base_url('/assets') ?>/libs/morris.js/morris.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= base_url('/assets') ?>/libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="<?= base_url('/assets') ?>/css/style.min.css" rel="stylesheet">
</head>

<body>

    <div id="main-wrapper">

        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)" style="visibility: hidden;">
                        <i class="ti-menu ti-close"></i>
                    </a>
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <a class="navbar-brand" href="/">
                        <!-- Logo icon -->
                        <b class="logo-icon">
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->
                           &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="<?= base_url('/assets') ?>/images/logo-icon.png" alt="homepage" class="dark-logo" />
                            <!-- Light Logo icon -->
                            <img src="<?= base_url('/assets') ?>/images/logo-light-icon.png" alt="homepage" style="width:60px;height:60px class="light-logo" />
                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text -->
                        <span class="logo-text">
                            <!-- dark Logo text -->
                            <img src="<?= base_url('/assets') ?>/images/logo-text.png" alt="homepage" style="width:200px" class="dark-logo" />
                            <!-- Light Logo text -->
                            <img src="<?= base_url('/assets') ?>/images/logo-light-text.png" class="light-logo" alt="homepage" style="width:200px"/>
                        </span>
                    </a>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Toggle which is visible on mobile only -->
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" style="visibility: hidden;"
                        data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i
                            class="ti-more"></i>
                    </a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav mr-auto float-left">

                    </ul>
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-right">

                        <!-- ============================================================== -->
                        <!-- Profile -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark" href="" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <img src="<?= base_url('/assets') ?>/images/users/1.png" alt="user" width="200" class="profile-pic rounded-circle" />
                            </a>
                            <div class="dropdown-menu mailbox dropdown-menu-right scale-up">
                                <ul class="dropdown-user list-style-none">
                                    <li>
                                        <div class="dw-user-box p-3 d-flex">
                                            <div class="u-img"><img src="<?= base_url('/assets') ?>/images/users/1.jpg" alt="user" class="rounded" width="80"></div>
                                            <div class="u-text ml-2">
                                                <h4 class="mb-0">Steave Jobs</h4>
                                                <p class="text-muted mb-1 font-14">varun@gmail.com</p>
                                                <a href="#" class="btn btn-rounded btn-danger btn-sm text-white d-inline-block">
                                                    View Profile
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                    <li role="separator" class="dropdown-divider"></li>
                                    <li class="user-list"><a class="px-3 py-2" href="#"><i class="ti-user"></i> My Profile</a></li>
                                    <li class="user-list"><a class="px-3 py-2" href="#"><i class="ti-wallet"></i> My Balance</a></li>
                                    <li class="user-list"><a class="px-3 py-2" href="#"><i class="ti-email"></i> Inbox</a></li>
                                    <li role="separator" class="dropdown-divider"></li>
                                    <li class="user-list"><a class="px-3 py-2" href="#"><i class="ti-settings"></i> Account Setting</a></li>
                                    <li role="separator" class="dropdown-divider"></li>
                                    <li class="user-list"><a class="px-3 py-2" href="#"><i class="fa fa-power-off"></i> Logout</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->

        <!-- ============================================================== -->
        <!-- Container fluid  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <div class="container-fluid">


                <!-- Row -->
                <?= $this->renderSection('content'); ?>
                <!-- endRow -->


            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Container fluid  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="<?= base_url('/assets') ?>/libs/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="<?= base_url('/assets') ?>/libs/popper.js/dist/umd/popper.min.js"></script>
    <!-- <script src="<?= base_url('/assets') ?>/libs/bootstrap/dist/js/bootstrap.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- apps -->
    <script src="<?= base_url('/assets') ?>/js/app.min.js"></script>
    <script src="<?= base_url('/assets') ?>/js/app.init.js"></script>
    <script src="<?= base_url('/assets') ?>/js/app-style-switcher.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="<?= base_url('/assets') ?>/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="<?= base_url('/assets') ?>/extra-libs/sparkline/sparkline.js"></script>
    <!--Wave Effects -->
    <script src="<?= base_url('/assets') ?>/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="<?= base_url('/assets') ?>/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="<?= base_url('/assets') ?>/js/custom.min.js"></script>

    <script src="<?= base_url('/assets') ?>/libs/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="<?= base_url('/assets') ?>/js/pages/datatable/custom-datatable.js"></script>
    <script src="<?= base_url('/assets') ?>/js/pages/datatable/datatable-basic.init.js"></script>

    <script src="<?= base_url('/assets') ?>/libs/echarts/dist/echarts-en.min.js"></script>
    <script src="<?= base_url('/assets') ?>/js/pages/echarts/pie-doughnut/pie-doghnut.js"></script>

    <script src="<?= base_url('/assets') ?>/libs/raphael/raphael.min.js"></script>
    <script src="<?= base_url('/assets') ?>/libs/morris.js/morris.min.js"></script>
    <script src="<?= base_url('/assets') ?>/js/pages/morris/morris-data.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        //==================== PEGAWAI ====================//
        $(document).ready(function() {
            $('#tb_pegawai').DataTable({
                "destroy": true, // menghindari inisialisasi ganda
                "autoWidth": false, // mencegah perubahan otomatis pada lebar kolom
                "ordering": false, // opsional: Mematikan fitur sorting jika tidak diperlukan
                "columnDefs": [{
                        "orderable": false,
                        "targets": "_all"
                    } // mencegah sorting otomatis
                ]
            });
        });
        //==================== END PEGAWAI ====================//


        //==================== SISWA ====================//
        $(document).ready(function() {
            $('#tb_siswa').DataTable({
                "destroy": true, 
                "autoWidth": false, 
                "ordering": false, 
                "columnDefs": [{
                        "orderable": false,
                        "targets": "_all"
                    } // Mencegah sorting otomatis
                ]
            });
        });
        //==================== END SISWA ====================//


        //==================== SEKOLAH ====================//
        $(document).ready(function() {
            $('#tb_sekolah').DataTable({
                "destroy": true, 
                "autoWidth": false, 
                "ordering": false, 
                "columnDefs": [{
                        "orderable": false,
                        "targets": "_all"
                    }
                ]
            });
        });
        //==================== END SEKOLAH ====================//
    </script>

    <?php if (service('uri')->getSegment(1) == '' || service('uri')->getSegment(1) == 'dashboard'): ?>
        <script>
            //======================================== KODE GRAFIK PEGAWAI ========================================//
                $(function() {
                    "use strict";
                    var basicpieChart = echarts.init(document.getElementById('basic-pie'));
                    
                    var chartData = [];
                    var chartLabels = [];
                    var colors = ['#ffbc34', '#00acc1', '#212529', '#f62d51', '#1e88e5'];
                    
                    <?php foreach ($data_pegawai as $provinsi): ?>
                        <?php foreach ($status_list_pegawai as $status): ?>
                            <?php if (isset($provinsi[$status])): ?>
                                chartData.push({
                                    value: <?= $provinsi[$status]['Jml'] ?>,
                                    name: "<?= $status ?>"
                                });
                                chartLabels.push("<?= $status ?>");
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    
                    var option = {
                        title: {
                            text: 'Pegawai',
                            subtext: 'Data grafik pegawai',
                            x: 'center'
                        },
                        tooltip: {
                            trigger: 'item',
                            formatter: "{a} <br/>{b}: {c} (L & P)"
                        },
                        legend: {
                            orient: 'vertical',
                            left: 'left',
                            data: chartLabels
                        },
                        color: colors,
                        series: [{
                            name: 'Pegawai',
                            type: 'pie',
                            radius: '70%',
                            center: ['50%', '57.5%'],
                            data: chartData
                        }]
                    };
                    
                    basicpieChart.setOption(option);
                });
            //======================================== END KODE GRAFIK PEGAWAI ========================================//

                // kode frontend grafik
                    // const ctx = document.getElementById('grafik_siswa').getContext('2d');
                    // const grafikSiswa = new Chart(ctx, {
                    //     type: 'bar',
                    //     data: {
                    //         labels: ['2006', '2007', '2008', '2009'],
                    //         datasets: [
                    //             {
                    //                 label: 'A',
                    //                 data: [100, 75, 50, 75],
                    //                 backgroundColor: '#fc4b6c'
                    //             },
                    //             {
                    //                 label: 'B',
                    //                 data: [90, 65, 40, 65],
                    //                 backgroundColor: '#00acc1'
                    //             },
                    //             {
                    //                 label: 'C',
                    //                 data: [60, 40, 30, 40],
                    //                 backgroundColor: '#1e88e5'
                    //             }
                    //         ]
                    //     },
                    //     options: {
                    //         indexAxis: 'y', // << Ini yang membuat grafik jadi horizontal
                    //         responsive: true,
                    //         plugins: {
                    //             legend: {
                    //                 position: 'top',
                    //             },
                    //             title: {
                    //                 display: true,
                    //                 text: 'Grafik Siswa'
                    //             }
                    //         }
                    //     }
                    // });
                // end kode frontend grafik

            //======================================== KODE GRAFIK SEKOLAH ========================================//
                const dataSekolah = <?= json_encode($data_sekolah) ?>;
                const levelList = <?= json_encode($level_list) ?>;

                const labels = [];
                const datasets = [];

                // Buat dataset untuk tiap jenjang
                levelList.forEach((level, idx) => {
                    datasets.push({
                        label: level,
                        data: [],
                        backgroundColor: ['#fc4b6c', '#00acc1', '#1e88e5', '#ffaa00', '#4caf50', '#9c27b0'][idx % 6]
                    });
                });

                const keteranganNS = [];

                let kabupatenIndex = 0;
                for (const kabID in dataSekolah) {
                    const kabupaten = dataSekolah[kabID];
                    labels.push(kabupaten.kabupaten_nama);

                    const rowKet = [];

                    levelList.forEach((level, levelIdx) => {
                        const info = kabupaten[level] || { Jml: 0, NEGERI: 0, SWASTA: 0 };
                        datasets[levelIdx].data.push(info.Jml);
                        rowKet.push(`(N: ${info.NEGERI}, S: ${info.SWASTA})`);
                    });

                    keteranganNS.push(rowKet);
                    kabupatenIndex++;
                }

                const ctxSekolah = document.getElementById('grafik_sekolah').getContext('2d');
                const grafikSekolah = new Chart(ctxSekolah, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        // indexAxis: 'y',
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Jumlah Sekolah per Jenjang per Kabupaten'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.dataset.label || '';
                                        const value = context.parsed.y || 0;
                                        const kabIndex = context.dataIndex;
                                        const levelIndex = context.datasetIndex;
                                        const extra = keteranganNS[kabIndex]?.[levelIndex] ?? '';
                                        return `${label}: ${value} ${extra}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            //======================================== END KODE GRAFIK SEKOLAH ========================================//


            //======================================== KODE GRAFIK SISWA ========================================//
                const statusList_siswa = <?= json_encode($status_list_siswa) ?>;
                const dataSiswa = <?= json_encode($data_siswa) ?>;

                const keteranganLP_siswa = [];

                for (const kabID in dataSiswa) {
                    const item = dataSiswa[kabID];
                    const row = [];

                    statusList_siswa.forEach(status => {
                        const L = item[status]?.L ?? 0;
                        const P = item[status]?.P ?? 0;
                        row.push(`(L: ${L}, P: ${P})`);
                    });

                    keteranganLP_siswa.push(row);
                }

                const labels_siswa = [];
                const datasets_siswa = [];

                statusList_siswa.forEach((status, idx) => {
                    datasets_siswa.push({
                        label: status,
                        data: [],
                        backgroundColor: ['#fc4b6c', '#00acc1', '#1e88e5', '#ffaa00', '#4caf50', '#9c27b0'][idx % 6]
                    });
                });

                let kabIndex = 0;
                for (const kabID in dataSiswa) {
                    const item = dataSiswa[kabID];
                    labels_siswa.push(item.kabupaten_nama); // ✅ ubah dari provinsi_nama ke kabupaten_nama

                    statusList_siswa.forEach((status, statusIdx) => {
                        const jumlah = item[status]?.Jml ?? 0;
                        datasets_siswa[statusIdx].data.push(jumlah);
                    });

                    kabIndex++;
                }

                const ctx_siswa = document.getElementById('grafik_siswa').getContext('2d');
                const grafikSiswa = new Chart(ctx_siswa, {
                    type: 'bar',
                    data: {
                        labels: labels_siswa,
                        datasets: datasets_siswa
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Jumlah Siswa per Jenjang per Kabupaten' // ✅ ubah judul
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.dataset.label || '';
                                        const value = context.parsed.x || 0;
                                        const kabIndex = context.dataIndex;
                                        const statusIndex = context.datasetIndex;
                                        const extra = keteranganLP_siswa[kabIndex]?.[statusIndex] ?? '';
                                        return `${label}: ${value} ${extra}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            //======================================== END KODE GRAFIK SISWA ========================================//


            //======================================== KODE GRAFIK SISWA AKHIR ========================================//
                <?php
                    $chartData = [];

                    foreach ($data_siswa_akhir as $kabupaten) {
                        $row = [
                            'kabupaten' => $kabupaten['kabupaten_nama']
                        ];

                        foreach ($status_list_siswa_akhir as $level) {
                            // bikin key aman buat JS
                            $key = strtolower(str_replace(' ', '_', $level));
                            $row[$key] = $kabupaten[$level]['Jml'] ?? 0;
                        }

                        $chartData[] = $row;
                    }

                    // ykeys & labels dipisah (penting)
                    $ykeys  = [];
                    $labels = [];

                    foreach ($status_list_siswa_akhir as $level) {
                        $ykeys[]  = strtolower(str_replace(' ', '_', $level));
                        $labels[] = $level;
                    }
                ?>

                $(function () {
                    "use strict";

                    const dataChart = <?= json_encode($chartData, JSON_NUMERIC_CHECK); ?>;

                    if (dataChart.length === 0) {
                        console.warn('Data grafik kosong');
                        return;
                    }

                    Morris.Area({
                        element: 'grafik_siswa_akhir',
                        data: dataChart,
                        xkey: 'kabupaten',
                        ykeys: <?= json_encode($ykeys); ?>,
                        labels: <?= json_encode($labels); ?>,
                        parseTime: false,

                        xLabelAngle: 60,        // lebih miring = lebih kebaca
                        gridTextSize: 10,       // kecilkan font
                        padding: 40,
                        marginLeft: 70,
                        marginBottom: 80,      // 🔥 ruang buat teks panjang

                        pointSize: 3,
                        fillOpacity: 0.25,
                        behaveLikeLine: true,
                        gridLineColor: '#e0e0e0',
                        lineWidth: 2,
                        hideHover: 'auto',
                        resize: true
                    });
                });
            //======================================== END KODE GRAFIK SISWA AKHIR ========================================//

            //======================================== KODE GRAFIK SISWA TIDAK SEKOLAH ========================================//
                const dataSiswaTidakSekolah = <?= json_encode($data_siswa_tidak_sekolah) ?>;
                const jkListATS = <?= json_encode(['L', 'P']) ?>;

                const labels_ats = [];
                const datasets_ats = [];

                // Buat dataset untuk L dan P
                jkListATS.forEach((jk, idx) => {
                    datasets_ats.push({
                        label: jk === 'L' ? 'Laki-laki' : 'Perempuan',
                        data: [],
                        backgroundColor: jk === 'L' ? '#1e88e5' : '#f62d51'
                    });
                });

                const keteranganDO = [];

                for (const kabID in dataSiswaTidakSekolah) {
                    const item = dataSiswaTidakSekolah[kabID];
                    labels_ats.push(item.kabupaten_nama);

                    jkListATS.forEach((jk, idx) => {
                        datasets_ats[idx].data.push(item.total[jk] ?? 0);
                    });

                    keteranganDO.push(`(L: ${item.total.L}, P: ${item.total.P})`);
                }

                const ctx_ats = document.getElementById('grafik_siswa_tidak_sekolah').getContext('2d');
                const grafikSiswaTidakSekolah = new Chart(ctx_ats, {
                    type: 'bar',
                    data: {
                        labels: labels_ats,
                        datasets: datasets_ats
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Jumlah Siswa Tidak Sekolah per Kabupaten'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.dataset.label || '';
                                        const value = context.parsed.x || 0;
                                        const kabIndex = context.dataIndex;
                                        const extra = keteranganDO[kabIndex] ?? '';
                                        return `${label}: ${value} ${extra}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            //======================================== END KODE GRAFIK SISWA TIDAK SEKOLAH ========================================//

        </script>
    <?php endif; ?>
</body>

</html>