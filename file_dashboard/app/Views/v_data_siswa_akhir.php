<?= $this->extend('layout/theme') ?>


<?= $this->section('content'); ?>
<style>
    /*  */
    #tb_siswa th:nth-child(2),
    #tb_siswa td:nth-child(2) {
        width: 25% !important;
        white-space: nowrap !important;
    }

    .collapse-icon {
        transition: transform 0.3s ease-in-out;
    }

    .collapsed .collapse-icon {
        transform: rotate(0deg);
    }

    .expanded .collapse-icon {
        transform: rotate(90deg);
    }

    .table>tbody>tr>* {
        vertical-align: middle;
    }

    .table>thead>tr>* {
        vertical-align: middle;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href="<?= base_url("/sekolah_siswa_akhir/$kecamatan_id") ?>" class="btn btn-warning">Kembali</a>
                </h4>
                <div class="table-responsive" id="table-container">
                    <table id="tb_siswa" class="table table-bordered table-striped text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>nama</th>
                                <th>Tempat Lahir</th>
                                <th>Tanggal Lahir</th>
                                <th>Jenis Kelamin</th>
                                <th>NIK</th>
                                <th>Nomor KK</th>
                                <th>NISN</th>*/
                                <th>Alamat Jalan</th>
                                <th>Desa Kelurahan</th>
                                <th>RT</th>
                                <th>RW</th>
                                <th>Nama Dusun</th>
                                <th>Nama Ibu Kandung</th>
                                <th>Pekerjaan Ibu</th>
                                <th>Penghasilan Ibu</th>
                                <th>Nama Ayah</th>
                                <th>Pekerjaan Ayah</th>
                                <th>Penghasilan Ayah</th>
                                <th>Nama Wali</th>
                                <th>Pekerjaan Wali</th>
                                <th>Penghasilan Wali</th>
                                <th>Kebutuhan Khusus</th>
                                <th>Nomor KIP</th>
                                <th>Nomor PKH</th>
                                <th>Lintang</th>
                                <th>Bujur</th>
                                <th>Flag PIP</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($siswa)): ?>
                                <tr>
                                    <td colspan="30">Data kosong, bukan kiamat 😴</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($siswa as $row): ?>
                                    <tr>
                                        <?php foreach ($row as $val): ?>
                                            <td><?= esc($val) ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>