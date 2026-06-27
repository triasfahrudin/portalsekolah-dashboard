<table id="example" class="table table-striped">
    <thead>
        <tr>
            <th>Nama Siswa</th>
            <!-- <th>Kelas</th> -->
            <th>Ekstrakulikuler</th>

        </tr>
    </thead>
    <tbody>
        <?php foreach ($rs->result_array() as $row) { ?>
            <tr>
                <td><?php echo $row['nama_lengkap'] ?></td>
                <!-- <td><?php echo $row['kelas'] ?></td> -->
                <td>
                    <?php
                    $search = array('1', '0');
                    $replace = array('<span class="badge badge-success">Aktif</span>', '<span class="badge badge-secondary">Tidak Aktif</span>');
                    echo str_replace($search, $replace, $row['ekstrakulikuler']);

                    ?>
                </td>
            </tr>
        <?php } ?>

    </tbody>
</table>

<script>
    $('#example').DataTable({
        "autoWidth": true
    });
</script>