<style>
    .custom-file-upload {
        display: inline-block;
        padding: 6px 12px;
        cursor: pointer;
        background-color: #f0ad4e;
        color: #fff;
        border: none;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    .custom-file-upload:hover {
        background-color: #ec971f;
    }

    .blocked-fieldset {
        border: none;
        padding: 20px;
        position: relative;

    }

    .blocked-fieldset::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        /* Warna latar belakang semi-transparan */
        z-index: 2;
        /* Mengatur tumpukan elemen, memastikan elemen ini berada di atas elemen lain */
    }

    .blocked-fieldset legend {
        position: relative;
        color: red;
        background-color: #333;
        padding: 10px 20px;
        z-index: 3;
        /* Mengatur tumpukan elemen, memastikan elemen ini berada di atas elemen lain */
    }
</style>


<div class="card mt-2">
    <div class="card-header">
        Dokumen
    </div>
    <div class="card-body">
        <div class="alert alert-success mt-2" role="alert">
            <h4>Informasi</h4>
            <ul>
                <li>Ekstensi file unggah yang diijinkan : Arsip(zip;rar) - Gambar(jpg;png;bmp) - Dokumen(pdf;doc;docx;xls;xlsx)</li>
                <li>Besar file max 1 MBytes (1024 KB) </li>
                <li>Anda dapat memperbaiki berkas yang sudah terupload dengan mengupload kembali berkas yang baru (Berkas lama otomatis akan tertimpa)</li>
                <li>Mohon lengkapi profile jika belum</li>
            </ul>
        </div>

       
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>Dokumen</th>
                        <th>Unggah</th>
                        <th>Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    foreach ($dokumen->result_array() as $jd) {
                        $statusClass = '';
                        $statusBadge = '';

                        if ($jd['user_file_dokumen'] === 'belum' && $jd['verifikasi'] === '-') {
                            $statusClass = 'table-default';
                        } elseif ($jd['user_file_dokumen'] !== 'belum' && $jd['verifikasi'] === 'belum') {
                            $statusClass = 'table-warning';
                        } else {
                            $statusClass = 'table-success';
                        }

                        if ($jd['verifikasi'] === 'ditolak') {
                            $statusBadge = '<span class="badge bg-danger toggle-collapse"><i class="bi bi-x-circle" aria-hidden="true"></i>&nbsp;DITOLAK <i class="bi bi-chevron-down"></i></span>';
                        } elseif ($jd['verifikasi'] === 'diterima') {
                            $statusBadge = '<span class="badge bg-success"><i class="bi bi-check-lg" aria-hidden="true"></i>&nbsp;DITERIMA</span>';
                        } elseif ($jd['verifikasi'] === 'pending') {
                            $statusBadge = '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split" aria-hidden="true"></i>&nbsp;MENUNGGU</span>';
                        } else {
                            $statusBadge = '-';
                        }
                    ?>
                        <tr class="<?php echo $statusClass; ?>" id="tr_<?php echo $i ?>">
                            <th scope="row"><?php echo $i ?></th>
                            <td>
                                <?php echo $jd['nama'] ?>
                                
                            </td>
                            <td>
                                <?php if ($jd['user_file_dokumen'] === 'belum') { ?>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                                        &nbsp;Belum diunggah
                                    </span>
                                <?php } else { ?>
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
                                        &nbsp;Sudah diunggah
                                    </span> -
                                    <a href="<?php echo site_url('uploads/dokumen/' . $jd['user_file_dokumen']) ?>?random=<?php echo date("YmdHis") ?>" target="_blank">Lihat</a>
                                <?php } ?>

                                    <form style="margin-top:10px" method="post" action="<?php echo site_url('guru/upload_dokumen') ?>" enctype="multipart/form-data">
                                        <input type="hidden" name="jenis_dokumen_id" value="<?php echo $jd['id'] ?>" />
                                        <input name="file_dokumen" type="file" onchange="form.submit()" aria-label="Upload Dokumen" />
                                    </form>
                                
                            </td>
                            <td>
                                <?php echo $statusBadge; ?>
                                <?php if ($jd['verifikasi'] === 'ditolak') { ?>
                        <tr class="table-danger " id="r<?php echo $i; ?>" data-bs-parent=".table">
                            <td colspan="4" class="link-danger text-center">Alasan penolakan: <?php echo $jd['alasan']; ?></td>
                        </tr>
                    <?php } ?>
                    </td>
                    </tr>
                <?php
                        $i++;
                    }
                ?>
                </tbody>
            </table>
        </div>

        <script>
            const toggleCollapse = document.querySelectorAll('.toggle-collapse');

            toggleCollapse.forEach((element) => {
                element.addEventListener('click', function() {
                    const targetRow = this.closest('tr');
                    targetRow.nextElementSibling.classList.toggle('collapse');
                });
            });

            function showModal(jenis_dokumen_id) {
                $('#uploadModal' + jenis_dokumen_id).modal('show');
            }

            function handleFileSelection(jenis_dokumen_id, bobot) {
                $('#bobot' + jenis_dokumen_id).val(bobot);
                $('#file_dokumen' + jenis_dokumen_id).click();
            }
        </script>
    </div>
</div>