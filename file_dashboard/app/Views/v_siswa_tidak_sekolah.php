<?= $this->extend('layout/theme') ?>

<?= $this->section('content'); ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Data Siswa Tidak Sekolah</h4>
                <div class="table-responsive">
                    <table id="tb_siswa_tidak_sekolah" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Kabupaten</th>
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
                            <?php foreach ($data_siswa as $kab_id => $kab): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('kabupaten_siswa_tidak_sekolah/' . $kab_id) ?>">
                                            <?= esc($kab['kabupaten_nama']) ?>
                                        </a>
                                    </td>
                                    <?php foreach ($status_list as $status): ?>
                                        <td><?= number_format($kab[$status]['Jml'] ?? 0, 0, ',', '.') ?></td>
                                        <td><?= number_format($kab[$status]['L'] ?? 0, 0, ',', '.') ?></td>
                                        <td><?= number_format($kab[$status]['P'] ?? 0, 0, ',', '.') ?></td>
                                    <?php endforeach; ?>
                                    <td><?= number_format($kab['total']['Jml'], 0, ',', '.') ?></td>
                                    <td><?= number_format($kab['total']['L'], 0, ',', '.') ?></td>
                                    <td><?= number_format($kab['total']['P'], 0, ',', '.') ?></td>
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
        $('#tb_siswa_tidak_sekolah').DataTable({
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
