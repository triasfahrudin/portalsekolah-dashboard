<?= $this->extend('layout/theme') ?>


<?= $this->section('content'); ?>
<div class="row">
    <!-- PEGAWAI -->
    <div class="col-md-6 col-lg-3">
        <a href="<?= base_url('/pegawai') ?>">
            <div class="card card-body">
                <div class="row">
                    <div class="col pr-0 align-self-center">
                        <h2 class="text-bold">Pegawai</h2>
                        <h4 class="font-weight-light mb-0"><?= number_format($totalPegawai, 0, ',', '.'); ?></h4>
                    </div>

                    <div class="col text-right align-self-center">
                        <div data-label="" class="css-bar mb-0 css-bar-primary css-bar-100">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <!-- END PEGAWAI -->

    <!-- SEKOLAH -->
    <div class="col-md-6 col-lg-3">
        <a href="<?= base_url('/sekolah') ?>">
            <div class="card card-body">
                <div class="row">
                    <div class="col pr-0 align-self-center">
                        <h2 class="text-bold">Sekolah</h2>
                        <h4 class="font-weight-light mb-0"><?= number_format($totalSekolah, 0, ',', '.'); ?></h4>
                    </div>

                    <div class="col text-right align-self-center">
                        <div data-label="" class="css-bar mb-0 css-bar-danger css-bar-100">
                            <i class="fa-solid fa-school"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <!-- END SEKOLAH -->

    <!-- SISWA -->
    <div class="col-md-6 col-lg-3">
        <a href="<?= base_url('/siswa') ?>">
            <div class="card card-body">
                <div class="row">
                    <div class="col pr-0 align-self-center">
                        <h2 class="text-bold">Siswa</h2>
                        <h4 class="font-weight-light mb-0"><?= number_format($totalSiswa, 0, ',', '.'); ?></h4>
                    </div>

                    <div class="col text-right align-self-center">
                        <div data-label="" class="css-bar mb-0 css-bar-info css-bar-100">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <!-- END SISWA -->

    <!-- SISWA AKHIR -->
    <div class="col-md-6 col-lg-3">
        <a href="<?= base_url('/siswa_akhir') ?>">
            <div class="card card-body">
                <div class="row">
                    <div class="col pr-0 align-self-center">
                        <h2 class="text-bold">Siswa Akhir (IX)</h2>
                        <h4 class="font-weight-light mb-0"><?= number_format($totalSiswaAkhir, 0, ',', '.'); ?></h4>
                    </div>

                    <div class="col text-right align-self-center">
                        <div data-label="" class="css-bar mb-0 css-bar-success css-bar-100">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <!-- END SISWA AKHIR -->

    <!-- SISWA TIDAK SEKOLAH -->
    <div class="col-md-6 col-lg-3">
        <a href="<?= base_url('/siswa_tidak_sekolah') ?>">
            <div class="card card-body">
                <div class="row">
                    <div class="col pr-0 align-self-center">
                        <h2 class="text-bold">Siswa Tdk Sekolah</h2>
                        <h4 class="font-weight-light mb-0"><?= number_format($totalSiswaTidakSekolah, 0, ',', '.'); ?></h4>
                    </div>

                    <div class="col text-right align-self-center">
                        <div data-label="" class="css-bar mb-0 css-bar-warning css-bar-100">
                            <i class="fa-solid fa-user-slash"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <!-- END SISWA TIDAK SEKOLAH -->

    <!-- //======================================== KODE GRAFIK PEGAWAI ========================================// -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body analytics-info">
                <h4 class="card-title">Grafik Pegawai</h4>
                <div id="basic-pie" style="height:400px;"></div>
            </div>
        </div>
    </div>
    <!-- //======================================== END KODE GRAFIK PEGAWAI ========================================// -->


    


    <!-- //======================================== KODE GRAFIK SEKOLAH ========================================// -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Grafik Sekolah</h4>
                    <!-- <div id="grafik_sekolah"></div> -->
                    <canvas id="grafik_sekolah"></canvas>
                </div>
            </div>
        </div>
    <!-- //======================================== END KODE GRAFIK SEKOLAH ========================================// -->


    <!-- //======================================== KODE GRAFIK SISWA ========================================// -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Grafik Siswa</h4>
                    <canvas id="grafik_siswa" style="margin-right: 100px;"></canvas>
                </div>
            </div>
        </div>
    <!-- //======================================== END KODE GRAFIK SISWA ========================================// -->


    <!-- //======================================== KODE GRAFIK SISWA AKHIR ========================================// -->
        <!-- <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Grafik Siswa Akhir</h4>
                    <canvas id="grafik_siswa_akhir" style="margin-right: 100px;"></canvas>
                </div>
            </div>
        </div> -->

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Grafik Siswa Akhir (IX)</h4>
                    Sumber Data : https://pelayanan.data.kemendikdasmen.go.id/
                    <div id="grafik_siswa_akhir"></div>
                </div>
            </div>
        </div>
    <!-- //======================================== END KODE GRAFIK SISWA AKHIR ========================================// -->

    <!-- //======================================== KODE GRAFIK SISWA TIDAK SEKOLAH ========================================// -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Grafik Siswa Tidak Sekolah</h4>
                    <canvas id="grafik_siswa_tidak_sekolah" style="margin-right: 100px;"></canvas>
                </div>
            </div>
        </div>
    <!-- //======================================== END KODE GRAFIK SISWA TIDAK SEKOLAH ========================================// -->
</div>
<?= $this->endSection(); ?>