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
                    <a href="<?= base_url("/sekolah_pegawai/$kecamatan_id") ?>" class="btn btn-warning">Kembali</a>
                </h4>
                <div class="table-responsive" id="table-container">
                    <table class="table table-bordered table-striped table-sm align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>No</th>
                                <?php foreach ($fields as $f): ?>
                                    <th><?= ucwords(str_replace('_', ' ', $f)) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($pegawai)): ?>
                                <tr>
                                    <td colspan="<?= count($fields)+1 ?>" class="text-center">
                                        Data tidak ditemukan
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($pegawai as $row): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++ ?></td>
                                        <?php foreach ($fields as $f): ?>
                                            <td>
                                                <?= $row[$f] !== null && $row[$f] !== '' ? esc($row[$f]) : '-' ?>
                                            </td>
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