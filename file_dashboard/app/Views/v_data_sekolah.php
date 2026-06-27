<?= $this->extend('layout/theme'); ?>

<?= $this->section('content') ?>
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="<?= base_url("/kecamatan_sekolah/" . $sekolah[0]['kabupten_id']) ?>" class="btn btn-warning">Kembali</a>
            </h4>
            <div class="table-responsive">
                <table id="tb_sekolah" class="table align-middle table-bordered text-center">
                    <thead>
                        <tr>
                            <th style="width: 2%;">No</th>
                            <th>Sekolah</th>
                            <th>Jenjang</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $no=1; foreach($sekolah as $row): ?>
                        <tr>
                            <td><?= $no++ ?></th>
                            <td><?= $row['sekolah'] ?></th>
                            <td><?= $row['level'] ?></th>
                            <td><?= $row['status'] ?></th>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>