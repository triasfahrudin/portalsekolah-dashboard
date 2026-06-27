<table id="dataTablesExample" class="table table-striped">
    <thead>
        <tr>
            <th>Nama Siswa</th>
            <th>Tahun Lulus</th>
            <th>Jurusan</th>
            <th>Perguruan Tinggi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($query->result_array() as $row) { ?>
            <tr>
                <td><?php echo $row['nama_lengkap'] ?></td>
                <td><?php echo $row['tahun_lulus'] ?></td>
                <td><?php echo $row['jur'] ?></td>
                <td><?php echo $row['pt'] ?> </td>
            </tr>
        <?php } ?>

        <!-- Tambahkan baris data lainnya sesuai kebutuhan -->
    </tbody>
</table>

<script>
    $(document).ready(function() {
        $('#dataTablesExample').DataTable();
    });
</script>