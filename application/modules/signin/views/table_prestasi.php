<table id="example" class="table table-striped">
    <thead>
        <tr>
            <th>Nama Siswa</th>
            <!-- <th>Kelas</th> -->
            <th>Prestasi</th>
            <th>Tahun</th>
            <th>Sertifikat</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rs->result_array() as $row) { ?>
            <tr>
                <td><?php echo $row['nama_lengkap'] ?></td>
                <!-- <td><?php echo $row['kelas'] ?></td> -->
                <td><?php echo $row['prestasi'] ?></td>
                <td><?php echo $row['tahun'] ?></td>
                <td>
                    <?php
                    if ($row['sertifikat'] === 'belum_upload') {
                        echo '-';
                    } else {
                        // Gantilah 'URL_ke_situ' dengan URL yang sesuai
                        echo '<a href="' . site_url('uploads/' . $row['sertifikat']) . '">Download</a>';
                    }
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