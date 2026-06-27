<?= $this->extend('layout/theme') ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <a href="<?= base_url('siswa_tidak_sekolah') ?>" class="btn btn-warning">Kembali</a>
                    Data Siswa Tidak Sekolah per Kecamatan
                </h4>
                <div class="table-responsive">
                    <table id="tb_kabupaten_siswa_tidak_sekolah" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Kecamatan</th>
                                <?php foreach ($status_list as $status): ?>
                                    <th colspan="3"><?= esc($status) ?></th>
                                <?php endforeach; ?>
                                <th colspan="3">Total</th>
                            </tr>
                            <tr>
                                <th></th>
                                <?php foreach ($status_list as $status): ?>
                                    <th>Jumlah</th><th>L</th><th>P</th>
                                <?php endforeach; ?>
                                <th>Jumlah</th><th>L</th><th>P</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data_siswa as $kec_id => $kec): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('kecamatan_siswa_tidak_sekolah/' . $kec_id) ?>">
                                            <?= esc($kec['kecamatan_nama']) ?>
                                        </a>
                                    </td>
                                    <?php foreach ($status_list as $status): ?>
                                        <td><?= number_format($kec[$status]['Jml'] ?? 0, 0, ',', '.') ?></td>
                                        <td><?= number_format($kec[$status]['L'] ?? 0, 0, ',', '.') ?></td>
                                        <td><?= number_format($kec[$status]['P'] ?? 0, 0, ',', '.') ?></td>
                                    <?php endforeach; ?>
                                    <td><?= number_format($kec['total']['Jml'], 0, ',', '.') ?></td>
                                    <td><?= number_format($kec['total']['L'], 0, ',', '.') ?></td>
                                    <td><?= number_format($kec['total']['P'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#tb_kabupaten_siswa_tidak_sekolah').DataTable({
            destroy: true,
            autoWidth: false,
            ordering: false,
            columnDefs: [{
                orderable: false,
                targets: '_all'
            }]
        });
    });
</script>
<?= $this->endSection(); ?>
