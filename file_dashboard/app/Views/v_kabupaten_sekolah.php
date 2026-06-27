<?= $this->extend('layout/theme'); ?>

<?= $this->section('content') ?>
<style>
    /*  */
    #tb_sekolah th:nth-child(2),
    #tb_sekolah td:nth-child(2) {
        width: 80% !important;
        /* Sesuaikan lebar sesuai kebutuhan */
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

<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">
                <a href="<?= base_url("/sekolah") ?>" class="btn btn-warning">Kembali</a>
            </h4>
            <div class="table-responsive">
                <table id="tb_sekolah" class="table align-middle table-bordered text-center">
                    <thead>
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2">Kabupaten</th>
                            <th colspan="3">Total</th>
                            <?php
                            $levels = [];
                            foreach ($sekolah as $row) {
                                if (!in_array($row['level'], $levels)) {
                                    $levels[] = $row['level'];
                                }
                            }
                            foreach ($levels as $level) : ?>
                                <th colspan="3"><?= strtoupper($level) ?></th>
                            <?php endforeach; ?>
                        </tr>

                        <tr>
                            <th>Jml</th>
                            <th>N</th>
                            <th>S</th>
                            <?php foreach ($levels as $level) : ?>
                                <th>Jml</th>
                                <th>N</th>
                                <th>S</th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $no = 1;
                        $kabupatenData = [];
                        foreach ($sekolah as $row) {
                            $kabupatenData[$row['kabupaten_id']]['kabupaten'] = $row['kabupaten'];
                            $kabupatenData[$row['kabupaten_id']]['data'][] = $row;
                        }

                        foreach ($kabupatenData as $kabupaten_id => $kabupaten) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <a href="<?= base_url("/kecamatan_sekolah/" . $kabupaten_id) ?>">
                                        <?= $kabupaten['kabupaten'] ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                    $total = array_sum(array_column($kabupaten['data'], 'total'));
                                    echo $total;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $n = array_sum(array_column(array_filter($kabupaten['data'], fn($d) => $d['status'] == 'NEGERI'), 'total'));
                                    echo $n;
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $s = array_sum(array_column(array_filter($kabupaten['data'], fn($d) => $d['status'] == 'SWASTA'), 'total'));
                                    echo $s;
                                    ?>
                                </td>

                                <?php foreach ($levels as $level) : ?>
                                    <td>
                                        <?php
                                        $levelTotal = array_sum(array_column(array_filter($kabupaten['data'], fn($d) => $d['level'] == $level), 'total'));
                                        echo $levelTotal;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $levelN = array_sum(array_column(array_filter($kabupaten['data'], fn($d) => $d['level'] == $level && $d['status'] == 'NEGERI'), 'total'));
                                        echo $levelN;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $levelS = array_sum(array_column(array_filter($kabupaten['data'], fn($d) => $d['level'] == $level && $d['status'] == 'SWASTA'), 'total'));
                                        echo $levelS;
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>