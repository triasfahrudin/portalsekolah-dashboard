<?= $this->extend('layout/theme') ?>


<?= $this->section('content'); ?>
<style>
    /*  */
    #tb_pegawai th:nth-child(2),
    #tb_pegawai td:nth-child(2) {
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
                    <a href="<?= base_url() ?>" class="btn btn-warning">Kembali</a>
                </h4>
                <div class="table-responsive" id="table-container">
                    <table id="tb_pegawai" class="table align-middle table-bordered text-center">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Provinsi</th>
                                <th colspan="3">Total</th>
                                <?php foreach ($status_list as $status): ?>
                                    <th colspan="3"><?= $status ?></th>
                                <?php endforeach; ?>
                            </tr>

                            <tr>
                                <th>Jml</th>
                                <th>L</th>
                                <th>P</th>
                                <?php foreach ($status_list as $status): ?>
                                    <th>Jml</th>
                                    <th>L</th>
                                    <th>P</th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $no = 1; ?>
                            <?php foreach ($data_pegawai as $provinsi_id => $data): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <a href="<?= base_url("/kabupaten_pegawai/$provinsi_id") ?>">
                                            <?= $data['provinsi_nama'] ?>
                                        </a>
                                    </td>
                                    <td><?= $data['total']['Jml'] ?></td>
                                    <td><?= $data['total']['L'] ?></td>
                                    <td><?= $data['total']['P'] ?></td>

                                    <?php foreach ($status_list as $status): ?>
                                        <td><?= $data[$status]['Jml'] ?? 0 ?></td>
                                        <td><?= $data[$status]['L'] ?? 0 ?></td>
                                        <td><?= $data[$status]['P'] ?? 0 ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>