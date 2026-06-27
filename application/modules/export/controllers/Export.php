<?php

defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;



class Export extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->database();
    }

    /**
     * Generates a PDF file based on the given parameters.
     *
     * @param string $fileName The name of the PDF file.
     * @param mixed $recordSet The data used to generate the PDF.
     * @param string $report_header The header of the report.
     * @throws Some_Exception_Class Exception description
     * @return void
     */
    public function pdf($fileName, $recordSet, $report_header = "")
    {

        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $outputFilePath = FCPATH . 'temp/' . $fileName . '.pdf';

        // Boost the memory limit if it's low
        ini_set("pcre.backtrack_limit", "5000000");
        ini_set('memory_limit', '256M');

        $pdf = new \Mpdf\Mpdf([
            'mode'        => 'utf-8',
            'format'      => [210, 330],
            'orientation' => 'L',
        ]);

        $pdf->AddPage();

        $footer = '<table width="100%"><tr><td>Halaman {PAGENO}/{nbpg}</td></tr></table>';
        $pdf->SetHTMLFooter($footer);

        $data = array('recordset' => $recordSet, 'header' => $report_header);

        $html = $this->load->view('template_export_data', $data, true); // render the view into HTML

        $css = 'table { width: 100%; border-collapse: collapse; } th, td { border: 0px solid black; padding: 0; }';
        $pdf->WriteHTML('<style>' . $css . '</style>', \Mpdf\HTMLParserMode::HEADER_CSS);
        $pdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

        $pdf->Output($outputFilePath, 'F');
        $this->load->helper('download');
        force_download($outputFilePath, null); // Removed file_get_contents and directly force download

    }


    /**
     * The function exports a record set to an Excel file.
     * 
     * Args:
     *   fileName: The name of the Excel file that will be generated.
     *   recordSet: The `` parameter is a result set object that contains the data to be
     * exported to the Excel file. It should have a method `list_fields()` that returns an array of
     * field names, and a method `result_array()` that returns the data as an array of rows.
     * 
     * Returns:
     *   a boolean value. If the  parameter is empty or false, the function returns false.
     * Otherwise, it saves the data to an Excel file and returns true.
     */
    /*
     public function excel($fileName, $recordSet)
    {

        $filePath = FCPATH . 'temp/' . $fileName . '-' . date('dMy') . '.xlsx';

        error_reporting(0);
        if (!$recordSet) {
            return false;
        }

        // Buat objek spreadsheet
        $spreadsheet = new Spreadsheet();

        // Set judul worksheet
        $spreadsheet->getActiveSheet()->setTitle('export');

        // Field names in the first row
        $fields = $recordSet->list_fields();

        // Set data ke worksheet
        $spreadsheet->getActiveSheet()->fromArray($recordSet->result_array(), null, 'A1');

        // Set lebar kolom secara otomatis
        foreach ($fields as $field) {
            $spreadsheet->getActiveSheet()->getColumnDimension($field)->setAutoSize(true);
        }

        // // Center align text
        // foreach ($fields as $field) {
        //     $spreadsheet->getActiveSheet()->getStyle($field . '1:' . $field . $spreadsheet->getActiveSheet()->getHighestRow())->getAlignment()->setVertical(PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        // }

        // Save file excel
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // Set header untuk mengatur jenis konten dan nama file yang akan diunduh
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $this->load->helper('download');
        $data = file_get_contents($filePath); // Read the file's contents
        
        @force_download($fileName, $data);

        echo $fileName;

        // Hapus file setelah diunduh (opsional, tergantung kebutuhan)
        unlink($filePath);

        error_reporting(E_ALL);
    }

    */


    public function excel($fileName, $recordSet)
    {
        // Membuat objek Spreadsheet baru
        $spreadsheet = new Spreadsheet();

        // Mendapatkan sheet aktif
        $sheet = $spreadsheet->getActiveSheet();

        // Mendapatkan nama kolom dari record set
        $columns = $recordSet->list_fields();

        // Menambahkan header kolom ke sheet
        $columnIndex = 'A';
        foreach ($columns as $column) {
            $sheet->setCellValue($columnIndex . '1', $column);
            $sheet->getStyle($columnIndex . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '1')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '1')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT); // Mengatur format sel sebagai teks
            $sheet->getColumnDimension($columnIndex)->setAutoSize(true); // Mengatur lebar kolom otomatis
            $columnIndex++;
        }

        // Menambahkan data dari record set ke sheet
        $rowIndex = 2;
        foreach ($recordSet->result_array() as $row) {
            $columnIndex = 'A';
            foreach ($columns as $column) {
                // $cell = $worksheet->setCellValueExplicit($cellCoordinate, $cellValue, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING); // Set the cell value as string
                $sheet->setCellValueExplicit($columnIndex . $rowIndex, $row[$column] , \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING); // Set the cell value as string
                $columnIndex++;
            }
            $rowIndex++;
        }

        
        // Membuat objek Writer untuk menyimpan spreadsheet ke file
        $writer = new Xlsx($spreadsheet);

        // Nama file yang akan diunduh (tanpa ekstensi)
        $filePath = FCPATH . 'temp/' . $fileName . '.xlsx';

        // Menyimpan spreadsheet ke file
        $writer->save($filePath);

        // Set header untuk mengatur jenis konten dan nama file yang akan diunduh
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        // Mengirim file ke output untuk diunduh
        $this->load->helper('download');
        $data = file_get_contents($filePath); // Read the file's contents

        @force_download($fileName . '-' . date('dMy') . '.xlsx', $data);

        // Hapus file setelah diunduh (opsional, tergantung kebutuhan)
        unlink($filePath);
    }



    // Fungsi untuk membuat file Excel dari record set
    public function excel_verval_siswa($fileName, $recordSet)
    {
        // Membuat objek Spreadsheet baru
        $spreadsheet = new Spreadsheet();

        // Mendapatkan sheet aktif
        $sheet = $spreadsheet->getActiveSheet();

        // Mendapatkan nama kolom dari record set
        $columns = $recordSet->list_fields();

        // Menambahkan header kolom ke sheet
        $columnIndex = 'A';
        foreach ($columns as $column) {
            $sheet->setCellValue($columnIndex . '1', $column);
            $sheet->getStyle($columnIndex . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($columnIndex . '1')->getFont()->setBold(true);
            $sheet->getStyle($columnIndex . '1')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT); // Mengatur format sel sebagai teks
            $sheet->getColumnDimension($columnIndex)->setAutoSize(true); // Mengatur lebar kolom otomatis
            $columnIndex++;
        }

        $sheet->getStyle('A:C')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);

        // Menambahkan data dari record set ke sheet
        $rowIndex = 2;
        $validasiColumnIndex = 'L';
        foreach ($recordSet->result_array() as $row) {
            $columnIndex = 'A';
            foreach ($columns as $column) {
                $sheet->setCellValue($columnIndex . $rowIndex, $row[$column]);
                
               
                // Conditionally set background color for column H
                if ($columnIndex == $validasiColumnIndex && $row[$column] == 'VALID'){ 
                    // $sheet->getStyle($columnIndex . $rowIndex)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00FF7F');
                    $cellCoordinate = $columnIndex . $rowIndex;
                    $sheet->getStyle($cellCoordinate)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                    $sheet->getStyle($cellCoordinate)->getFill()->getStartColor()->setARGB('00FF00');
                    // $sheet->getStyle('B3')->getFill()->getStartColor()->setARGB($warnaTidakAktif);
                }elseif($columnIndex == $validasiColumnIndex && $row[$column] == 'TIDAK VALID'){ 
                    $cellCoordinate = $columnIndex . $rowIndex;


                    $sheet->getStyle($cellCoordinate)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                    $sheet->getStyle($cellCoordinate)->getFill()->getStartColor()->setARGB('FF0000');

                    
                }elseif($columnIndex == $validasiColumnIndex && $row[$column] == 'PENDING'){ 
                    $cellCoordinate = $columnIndex . $rowIndex;
                    $nama_lengkap_dapodik = 'E' . $rowIndex;

                    $sheet->getStyle($cellCoordinate)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                    $sheet->getStyle($cellCoordinate)->getFill()->getStartColor()->setARGB('FFFF00');

                    $sheet->getStyle($nama_lengkap_dapodik)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                    $sheet->getStyle($nama_lengkap_dapodik)->getFill()->getStartColor()->setARGB('FFFF00');

                }

                $columnIndex++;
            }
            $rowIndex++;
        }

        
        $sheet->setCellValue('A' . ($rowIndex + 1), 'VALID: ');
        $sheet->setCellValue('B' . ($rowIndex + 1), '=COUNTIF(' . $validasiColumnIndex .'2:' . $validasiColumnIndex . $rowIndex . ', "VALID")');

        $sheet->setCellValue('A' . ($rowIndex + 2), 'TIDAK VALID: ');
        $sheet->setCellValue('B' . ($rowIndex + 2), '=COUNTIF(' . $validasiColumnIndex .'2:' . $validasiColumnIndex . $rowIndex . ', "TIDAK VALID")');

        $sheet->setCellValue('A' . ($rowIndex + 3), 'PENDING: ');
        $sheet->setCellValue('B' . ($rowIndex + 3), '=COUNTIF(' . $validasiColumnIndex .'2:' . $validasiColumnIndex . $rowIndex . ', "PENDING")');

        $sheet->setCellValue('A' . ($rowIndex + 4), 'TOTAL: ');
        $sheet->setCellValue('B' . ($rowIndex + 4), '=SUM(B' . $rowIndex . ':B' . ($rowIndex + 3) . ')');

        // $conditional1 = new Conditional();
        // $conditional1->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
        // $conditional1->setConditionValue('VALID');

        // $conditional1->getStyle()->setFill(
        //     array(
        //         'fillType' => PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        //         'startColor' => array(
        //             'argb' => 'FF00FF00', // Warna hijau
        //         ),
        //     )
        // );

        // $conditionalStyles = array(
        //     array(
        //         'condition' => array(
        //             'type' => 'cellIs',
        //             'value' => 'Lulus',
        //             'operator' => 'equal'
        //         ),
        //         'style' => array(
        //             'fill' => array(
        //                 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        //                 'startColor' => array(
        //                     'rgb' => '00FF00'
        //                 )
        //             )
        //         )
        //     ),
        //     array(
        //         'condition' => array(
        //             'type' => 'cellIs',
        //             'value' => 'Tidak Lulus',
        //             'operator' => 'equal'
        //         ),
        //         'style' => array(
        //             'fill' => array(
        //                 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        //                 'startColor' => array(
        //                     'rgb' => 'FF0000'
        //                 )
        //             )
        //         )
        //     )
        // );

        // $sheet->getStyle('A1:B10')->setConditionalStyles($conditionalStyles);

        // $conditionalStyles = [
        //     new Conditional([
        //         'conditionType' => Conditional::CONDITION_CONTAINSTEXT,
        //         'operator' => Conditional::OPERATOR_CONTAINSTEXT,
        //         'value' => 'VALID',
        //         'style' => [
        //             'fill' => [
        //                 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        //                 'startColor' => ['rgb' => '00FF00'], // Warna hijau untuk teks "Lulus"
        //                 'endColor' => ['rgb' => '00FF00'],
        //             ],
        //         ],
        //     ]),
        //     new Conditional([
        //         'conditionType' => Conditional::CONDITION_CONTAINSTEXT,
        //         'operator' => Conditional::OPERATOR_CONTAINSTEXT,
        //         'value' => 'TIDAK VALID',
        //         'style' => [
        //             'fill' => [
        //                 'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        //                 'startColor' => ['rgb' => 'FF0000'], // Warna merah untuk teks "Tidak Lulus"
        //                 'endColor' => ['rgb' => 'FF0000'], // Warna merah untuk teks "Tidak Lulus"
        //             ],
        //         ],
        //     ]),
        // ];


        // $sheet->getStyle('H3')->setConditionalStyles($conditionalStyles);


        // $conditional = new Conditional();

        // // Atur format warna untuk cell yang mengandung teks "VALID"
        // $conditional->addCondition('H2:H10', [
        //     'fill' => [
        //         'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        //         'startColor' => ['rgb' => 'FF0000'],
        //     ],
        // ])
        //     ->setConditionType(Conditional::CONDITION_CONTAINSTEXT) // Set condition type
        //     ->setText('VALID');

        // // Atur format warna untuk cell yang mengandung teks "TIDAK VALID"
        // $conditional->addCondition('H2:H10', [
        //     'fill' => [
        //         'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        //         'startColor' => ['rgb' => '0000FF'],
        //     ],
        // ])
        //     ->setConditionType(Conditional::CONDITION_CONTAINSTEXT)
        //     ->setText('TIDAK VALID');

        // // Terapkan aturan Conditional Formatting pada cell A1:B10
        // $sheet->getStyle('H3')->setConditionalStyles($conditional);

        // var_dump($sheet->getStyle('H3')->getConditionalStyles());
        // exit();


        // Tentukan warna untuk setiap status
        // $warnaAktif = '00FF00'; // Hijau untuk status "Aktif"
        // $warnaTidakAktif = 'FF0000'; // Merah untuk status "Tidak Aktif"

        // // Atur warna latar belakang sel berdasarkan status
        


        // Membuat objek Writer untuk menyimpan spreadsheet ke file
        $writer = new Xlsx($spreadsheet);

        // Nama file yang akan diunduh (tanpa ekstensi)
        $filePath = FCPATH . 'temp/' . $fileName . '.xlsx';

        // Menyimpan spreadsheet ke file
        $writer->save($filePath);

        // Set header untuk mengatur jenis konten dan nama file yang akan diunduh
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        // Mengirim file ke output untuk diunduh
        $this->load->helper('download');
        $data = file_get_contents($filePath); // Read the file's contents

        @force_download($fileName . '-' . date('dMy') . '.xlsx', $data);

        // Hapus file setelah diunduh (opsional, tergantung kebutuhan)
        unlink($filePath);
    }


    // public function pdf($fileName, $recordSet, $report_header = 'Laporan')
    // {

    //     error_reporting(0);
    //     $data = array();

    //     $pdfFilePath = FCPATH . 'temp/' . $fileName . '-' . date('dMy') . '.pdf';

    //     //boost the memory limit if it's low <img src="http://davidsimpson.me/wp-includes/images/smilies/icon_wink.gif" alt=";)" class="wp-smiley">
    //     ini_set('memory_limit', '512M');
    //     $data['rs']     = $recordSet;
    //     $data['header'] = $report_header;

    //     $html = $this->load->view('template_export_data', $data, true); // render the view into HTML
    //     //$this->load->view('admin/pdf_report', $data); // render the view into HTML
    //     //exit();

    //     include_once APPPATH . '/third_party/mpdf/mpdf.php';
    //     $param = '"en-GB-x","A4-L","","",10,10,10,10,6,3';
    //     $pdf   = new mPDF();

    //     $pdf->AddPage('L', '', '', '', '', 10, 10, 10, 10, 6, 3);

    //     $pdf->simpleTables  = true;
    //     $pdf->packTableData = true;

    //     // Add a footer for good measure <img src="http://davidsimpson.me/wp-includes/images/smilies/icon_wink.gif" alt=";)" class="wp-smiley">
    //     $pdf->SetFooter($_SERVER['HTTP_HOST'] . '|{PAGENO}|' . date(DATE_RFC822));
    //     $pdf->WriteHTML($html); // write the HTML into the PDF
    //     $pdf->Output($pdfFilePath, 'F'); // save to file because we can

    //     $this->load->helper('download');
    //     $data = file_get_contents($pdfFilePath); // Read the file's contents
    //     // $name = $fileName.'_'.date('dMy').'.pdf';

    //     @force_download($fileName, $data);
    //     error_reporting(E_ALL);
    // }

    // public function excel($fileName, $recordSet, $heightRow = 70)
    // {
    //     error_reporting(0);
    //     if (!$recordSet) {
    //         return false;
    //     }

    //     // Starting the PHPExcel library
    //     $this->load->library('PHPExcel');
    //     $this->load->library('PHPExcel/IOFactory');

    //     $objPHPExcel = new PHPExcel();
    //     $objPHPExcel->getProperties()->setTitle('export')->setDescription('none');

    //     $objPHPExcel->setActiveSheetIndex(0);
    //     // Field names in the first row
    //     $fields = $recordSet->list_fields();
    //     $col    = 0;
    //     foreach ($fields as $field) {
    //         $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, strtoupper(str_replace('_', ' ', $field)));
    //         $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);
    //         ++$col;
    //     }

    //     // Fetching the table data
    //     $row = 2;
    //     foreach ($recordSet->result() as $data) {
    //         $col = 0;
    //         foreach ($fields as $field) {
    //             $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $data->$field);
    //             ++$col;
    //         }

    //         //set row height
    //         $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight($heightRow);

    //         ++$row;
    //     }

    //     foreach (range('A', 'F') as $columnID) {
    //         $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
    //     }

    //     $num_rows = $recordSet->num_rows() + 1;

    //     foreach (range('A', 'F') as $columnID) {
    //         $objPHPExcel->getActiveSheet()->getStyle($columnID . '1:' . $columnID . $num_rows)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    //     }

    //     $objPHPExcel->setActiveSheetIndex(0);

    //     $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');

    //     // Sending headers to force the user to download the file
    //     header('Content-Type: application/vnd.ms-excel');
    //     header('Content-Disposition: attachment;filename="' . $fileName . '-' . date('dMy') . '.xls"');
    //     header('Cache-Control: max-age=0');

    //     $objWriter->save('php://output');
    //     error_reporting(E_ALL);
    // }

    //==============================

    public function pdf_rekap_harian_presensi_pegawai_dinas($data, $tahun, $bulan, $day, $recordSet)
    {
        ini_set('memory_limit', '512M');

        $pdfFilePath    = FCPATH . 'temp/' . slugify($data['kabupaten']) . '-' . date('dMy') . '.pdf';
        $arr['data']    = $data;
        $arr['tahun']   = $tahun;
        $arr['bulan']   = $bulan;
        $arr['tanggal'] = $day;
        $arr['rs']      = $recordSet;
        $html           = $this->load->view('rekap_harian_presensi_pegawai_dinas', $arr, true);

        $pdf = new \Mpdf\Mpdf();

        /*
        $pdf->AddPage('', // L - landscape, P - portrait
        '', '', '', '',
        5, // margin_left
        5, // margin right
        60, // margin top
        30, // margin bottom
        0, // margin header
        0); // margin footer
         */
        $pdf->AddPage(
            'P',
            '',
            '',
            '',
            '',
            10,
            10,
            10,
            10,
            6,
            3
        );

        $pdf->showImageErrors = true;

        $pdf->simpleTables  = true;
        $pdf->packTableData = true;

        $pdf->WriteHTML($html); // write the HTML into the PDF
        $pdf->Output($pdfFilePath, 'F'); // save to file because we can

        $this->load->helper('download');
        $file     = file_get_contents($pdfFilePath); // Read the file's contents
        $fileName = 'laporan-rekap_harian-presensi-' . slugify($data['kabupaten']) . '_' . $tahun . '_' . $bulan . '_' . $day . '.pdf';

        @force_download($fileName, $file);
        error_reporting(E_ALL);
    }

    public function pdf_rekap_harian_presensi_pegawai($data, $tahun, $bulan, $day, $recordSet)
    {
        ini_set('memory_limit', '512M');

        $pdfFilePath    = FCPATH . 'temp/' . slugify($data['nama_sekolah']) . '-' . date('dMy') . '.pdf';
        $arr['data']    = $data;
        $arr['tahun']   = $tahun;
        $arr['bulan']   = $bulan;
        $arr['tanggal'] = $day;
        $arr['rs']      = $recordSet;
        $html           = $this->load->view('rekap_harian_presensi_pegawai', $arr, true);

        $pdf = new \Mpdf\Mpdf();

        /*
        $pdf->AddPage('', // L - landscape, P - portrait
        '', '', '', '',
        5, // margin_left
        5, // margin right
        60, // margin top
        30, // margin bottom
        0, // margin header
        0); // margin footer
         */
        $pdf->AddPage(
            'P',
            '',
            '',
            '',
            '',
            10,
            10,
            10,
            10,
            6,
            3
        );

        $pdf->showImageErrors = true;

        $pdf->simpleTables  = true;
        $pdf->packTableData = true;

        $pdf->WriteHTML($html); // write the HTML into the PDF
        $pdf->Output($pdfFilePath, 'F'); // save to file because we can

        $this->load->helper('download');
        $file     = file_get_contents($pdfFilePath); // Read the file's contents
        $fileName = 'laporan-rekap_harian-presensi-' . slugify($data['nama_sekolah']) . '_' . $tahun . '_' . $bulan . '_' . $day . '.pdf';

        @force_download($fileName, $file);
        error_reporting(E_ALL);
    }

    public function xls_rekap_harian_presensi_pegawai($data, $tahun, $bulan, $day, $recordSet)
    {
        ini_set('memory_limit', '512M');

        $cell = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q',
            'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF',
            'AG', 'AH', 'AI', 'AJ', 'AK', 'AL'
        );

        $template = './uploads/rekap_harian.xlsx';
        $outfile  = './temp/rekap_harian_' . slugify($data['nama_sekolah']) . '_periode_' . $day . '_' . $bulan . '_' . $tahun . '.xlsx';

        $inputFileType = IOFactory::identify($template);

        $reader      = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($template);

        $spreadsheet->getActiveSheet()->setCellValue('A2', 'PEMERINTAH ' . $data['kabupaten']);
        $spreadsheet->getActiveSheet()->setCellValue('A4', $data['nama_sekolah']);
        $spreadsheet->getActiveSheet()->setCellValue('A5', $data['kecamatan']);

        $spreadsheet->getActiveSheet()->setCellValue('A7', 'Laporan Presensi Harian :' . $day . ' ' . $bulan . ' ' . $tahun);

        $spreadsheet->getActiveSheet()->setCellValue('F15', $data['kepsek_nama']);
        $spreadsheet->getActiveSheet()->setCellValue('F16', $data['kepsek_nuptk']);

        $baseRow = 8;

        $fields = $recordSet->list_fields();

        foreach ($recordSet->result_array() as $r) {
            $row = $baseRow + 1;
            $spreadsheet->getActiveSheet()->insertNewRowBefore($row, 1);

            $char = 0;
            foreach ($fields as $field) {
                $spreadsheet->getActiveSheet()->setCellValue($cell[$char] . $row, $r[$field]);
                $char += 1;
            }
        }

        $writer = IOFactory::createWriter($spreadsheet, $inputFileType);
        $writer->save($outfile);

        $this->load->helper('download');
        $file     = file_get_contents($outfile); // Read the file's contents
        $fileName = 'rekap_harian_' . slugify($data['nama_sekolah']) . '_periode_' . $day . '_' . $bulan . '_' . $tahun . '.xlsx';

        @force_download($fileName, $file);
        error_reporting(E_ALL);
    }

    public function xls_rekap_presensi_pegawai($data, $tahun, $bulan, $recordSet)
    {

        $cell = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q',
            'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF',
            'AG', 'AH', 'AI', 'AJ', 'AK', 'AL'
        );

        $template = './uploads/rekap_bulanan_' . $data['jml_hari'] . '_hari.xlsx';
        $outfile  = './temp/rekap_bulanan_' . slugify($data['nama_sekolah']) . '_periode_' . $bulan . '_' . $tahun . '.xlsx';

        $inputFileType = IOFactory::identify($template);

        $reader      = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($template);

        $spreadsheet->getActiveSheet()->setCellValue('A2', 'PEMERINTAH ' . $data['kabupaten']);
        $spreadsheet->getActiveSheet()->setCellValue('A4', $data['nama_sekolah']);
        $spreadsheet->getActiveSheet()->setCellValue('A5', $data['kecamatan']);
        $spreadsheet->getActiveSheet()->setCellValue('A7', 'Bulan:' . $bulan);

        switch ($data['jml_hari']) {
            case 28:
                $spreadsheet->getActiveSheet()->setCellValue('AE16', $data['kepsek_nama']);
                $spreadsheet->getActiveSheet()->setCellValue('AE17', $data['kepsek_nuptk']);
                break;
            case 29:
                $spreadsheet->getActiveSheet()->setCellValue('AF16', $data['kepsek_nama']);
                $spreadsheet->getActiveSheet()->setCellValue('AF17', $data['kepsek_nuptk']);
                break;
            case 30:
                $spreadsheet->getActiveSheet()->setCellValue('AG16', $data['kepsek_nama']);
                $spreadsheet->getActiveSheet()->setCellValue('AG17', $data['kepsek_nuptk']);
                break;
            case 31:
                $spreadsheet->getActiveSheet()->setCellValue('AH16', $data['kepsek_nama']);
                $spreadsheet->getActiveSheet()->setCellValue('AH17', $data['kepsek_nuptk']);
                break;

            default:
                # code...
                break;
        }

        $baseRow = 8;

        $fields = $recordSet->list_fields();

        foreach ($recordSet->result_array() as $r) {
            $row = $baseRow + 1;
            $spreadsheet->getActiveSheet()->insertNewRowBefore($row, 1);

            $char = 0;
            foreach ($fields as $field) {
                $exp = explode('|', $r[$field]);
                $spreadsheet->getActiveSheet()->setCellValue($cell[$char] . $row, $exp[0]);
                $char += 1;
            }
        }

        $conditionalStyles = array();

        $this->db->select('DISTINCT nilai,warna', false);
        $bobot = $this->db->get('bobot');

        foreach ($bobot->result_array() as $b) {
            $conditional = new Conditional();
            $conditional->setConditionType(Conditional::CONDITION_CELLIS)
                ->setOperatorType(Conditional::OPERATOR_EQUAL)
                ->addCondition($b['nilai']);
            // $conditional->getStyle()->getFont()->getColor()->setRGB(substr($b['warna'], 1));
            $conditional->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)->getEndColor()->setRGB(substr($b['warna'], 1));
            $conditional->getStyle()->getFont()->setBold(true);
            $conditionalStyles[] = $conditional;
        }

        $spreadsheet->getActiveSheet()->getStyle('D9')->setConditionalStyles($conditionalStyles);

        switch ($data['jml_hari']) {
            case 28:
                $spreadsheet->getActiveSheet()->duplicateConditionalStyle(
                    $spreadsheet->getActiveSheet()->getStyle('D9')->getConditionalStyles(),
                    'D9:AE' . ($recordSet->num_rows() + $baseRow)
                );
                break;
            case 29:
                $spreadsheet->getActiveSheet()->duplicateConditionalStyle(
                    $spreadsheet->getActiveSheet()->getStyle('D9')->getConditionalStyles(),
                    'D9:AF' . ($recordSet->num_rows() + $baseRow)
                );
                break;
            case 30:
                $spreadsheet->getActiveSheet()->duplicateConditionalStyle(
                    $spreadsheet->getActiveSheet()->getStyle('D9')->getConditionalStyles(),
                    'D9:AG' . ($recordSet->num_rows() + $baseRow)
                );
                break;
            case 31:
                $spreadsheet->getActiveSheet()->duplicateConditionalStyle(
                    $spreadsheet->getActiveSheet()->getStyle('D9')->getConditionalStyles(),
                    'D9:AH' . ($recordSet->num_rows() + $baseRow)
                );
                break;

            default:
                # code...
                break;
        }

        // $spreadsheet->getActiveSheet()->removeRow($baseRow - 1, 1);

        $writer = IOFactory::createWriter($spreadsheet, $inputFileType);
        $writer->save($outfile);

        $this->load->helper('download');
        $file     = file_get_contents($outfile); // Read the file's contents
        $fileName = 'rekap_bulanan_' . slugify($data['nama_sekolah']) . '_periode_' . $bulan . '_' . $tahun . '.xlsx';

        @force_download($fileName, $file);
        error_reporting(E_ALL);
    }

    public function pdf_rekap_presensi_pegawai($data, $tahun, $bulan, $recordSet)
    {
        ini_set('memory_limit', '512M');

        $pdfFilePath  = FCPATH . 'temp/' . slugify($data['nama_sekolah']) . '-' . date('dMy') . '.pdf';
        $arr['data']  = $data;
        $arr['tahun'] = $tahun;
        $arr['bulan'] = $bulan;
        $arr['rs']    = $recordSet;
        $html         = $this->load->view('rekap_presensi_pegawai', $arr, true);

        $pdf = new \Mpdf\Mpdf();

        /*
        $pdf->AddPage('', // L - landscape, P - portrait
        '', '', '', '',
        5, // margin_left
        5, // margin right
        60, // margin top
        30, // margin bottom
        0, // margin header
        0); // margin footer
         */
        $pdf->AddPage(
            'L',
            '',
            '',
            '',
            '',
            10,
            10,
            10,
            10,
            6,
            3
        );

        $pdf->showImageErrors = true;

        $pdf->simpleTables  = true;
        $pdf->packTableData = true;

        $pdf->WriteHTML($html); // write the HTML into the PDF
        $pdf->Output($pdfFilePath, 'F'); // save to file because we can

        $this->load->helper('download');
        $file     = file_get_contents($pdfFilePath); // Read the file's contents
        $fileName = slugify($data['nama_sekolah']) . '_' . date('dMy') . '.pdf';

        @force_download($fileName, $file);
        error_reporting(E_ALL);
    }

    public function pdf_presensi_pegawai($data, $tahun, $bulan, $recordSet)
    {

        ini_set('memory_limit', '512M');

        $pdfFilePath  = FCPATH . 'temp/' . slugify($data['nama_pegawai']) . '-' . date('dMy') . '.pdf';
        $arr['data']  = $data;
        $arr['tahun'] = $tahun;
        $arr['bulan'] = $bulan;
        $arr['rs']    = $recordSet;
        //$arr['kepsek_nama']  = $data['kepsek_nama'];
        //$arr['kepsek_nuptk'] = $data['kepsek_nuptk'];
        $html = $this->load->view('presensi_pegawai', $arr, true);

        $pdf = new \Mpdf\Mpdf();

        /*
        $pdf->AddPage('', // L - landscape, P - portrait
        '', '', '', '',
        5, // margin_left
        5, // margin right
        60, // margin top
        30, // margin bottom
        0, // margin header
        0); // margin footer
         */
        $pdf->AddPage(
            'P',
            '',
            '',
            '',
            '',
            10,
            10,
            10,
            10,
            6,
            3
        );

        $pdf->showImageErrors = true;

        $pdf->simpleTables  = true;
        $pdf->packTableData = true;

        $pdf->WriteHTML($html); // write the HTML into the PDF
        $pdf->Output($pdfFilePath, 'F'); // save to file because we can

        $this->load->helper('download');
        $file     = file_get_contents($pdfFilePath); // Read the file's contents
        $fileName = slugify($data['nama_pegawai']) . '_' . date('dMy') . '.pdf';

        @force_download($fileName, $file);
        error_reporting(E_ALL);
    }

    public function xls_presensi_pegawai($data, $tahun, $bulan, $recordSet)
    {

        ini_set('memory_limit', '512M');

        $cell = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q',
            'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF',
            'AG', 'AH', 'AI', 'AJ', 'AK', 'AL'
        );

        $template = './uploads/rekap_bulanan_pegawai.xlsx';
        $outfile  = './temp/rekap_bulanan_' . slugify($data['nama_pegawai']) . '_periode_' . $bulan . '_' . $tahun . '.xlsx';

        $inputFileType = IOFactory::identify($template);

        $reader      = IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($template);

        $spreadsheet->getActiveSheet()->setCellValue('A2', 'PEMERINTAH ' . $data['kabupaten']);
        $spreadsheet->getActiveSheet()->setCellValue('A4', $data['nama_sekolah']);
        $spreadsheet->getActiveSheet()->setCellValue('A5', $data['kecamatan']);

        $spreadsheet->getActiveSheet()->setCellValue('B7', $bulan . ' ' . $tahun);
        $spreadsheet->getActiveSheet()->setCellValue('B8', $data['nama_pegawai']);

        $spreadsheet->getActiveSheet()->setCellValue('D17', $data['kepsek_nama']);
        $spreadsheet->getActiveSheet()->setCellValue('D18', $data['kepsek_nuptk']);

        $baseRow = 10;

        $fields = $recordSet->list_fields();

        foreach ($recordSet->result_array() as $r) {
            $row = $baseRow + 1;
            $spreadsheet->getActiveSheet()->insertNewRowBefore($row, 1);

            $char = 0;
            foreach ($fields as $field) {
                $spreadsheet->getActiveSheet()->setCellValue($cell[$char] . $row, $r[$field]);
                $char += 1;
            }
        }

        $writer = IOFactory::createWriter($spreadsheet, $inputFileType);
        $writer->save($outfile);

        $this->load->helper('download');
        $file     = file_get_contents($outfile); // Read the file's contents
        $fileName = 'rekap_bulanan_' . slugify($data['nama_pegawai']) . '_periode_' . $bulan . '_' . $tahun . '.xlsx';

        @force_download($fileName, $file);
        error_reporting(E_ALL);
    }

    //=

    public function pdf_presensi_pegawai_dinas($data, $tahun, $bulan, $recordSet)
    {
        // error_reporting(0);
        ini_set('memory_limit', '512M');

        $pdfFilePath  = FCPATH . 'temp/' . slugify($data['nama_pegawai']) . '-' . date('dMy') . '.pdf';
        $arr['data']  = $data;
        $arr['tahun'] = $tahun;
        $arr['bulan'] = $bulan;
        $arr['rs']    = $recordSet;
        //$arr['kepsek_nama']  = $data['kepsek_nama'];
        //$arr['kepsek_nuptk'] = $data['kepsek_nuptk'];
        $html = $this->load->view('presensi_pegawai_dinas', $arr, true);

        $pdf = new \Mpdf\Mpdf();

        /*
        $pdf->AddPage('', // L - landscape, P - portrait
        '', '', '', '',
        5, // margin_left
        5, // margin right
        60, // margin top
        30, // margin bottom
        0, // margin header
        0); // margin footer
         */
        $pdf->AddPage(
            'P',
            '',
            '',
            '',
            '',
            10,
            10,
            10,
            10,
            6,
            3
        );

        $pdf->showImageErrors = true;

        $pdf->simpleTables  = true;
        $pdf->packTableData = true;

        $pdf->WriteHTML($html); // write the HTML into the PDF
        $pdf->Output($pdfFilePath, 'F'); // save to file because we can

        $this->load->helper('download');
        $file     = file_get_contents($pdfFilePath); // Read the file's contents
        $fileName = slugify($data['nama_pegawai']) . '_' . date('dMy') . '.pdf';

        @force_download($fileName, $file);
        error_reporting(E_ALL);
    }

    //===
    public function pdf_rekap_presensi_pegawai_dinas($data, $tahun, $bulan, $recordSet)
    {
        ini_set('memory_limit', '512M');

        $pdfFilePath  = FCPATH . 'temp/' . slugify($data['kabupaten']) . '-' . date('dMy') . '.pdf';
        $arr['data']  = $data;
        $arr['tahun'] = $tahun;
        $arr['bulan'] = $bulan;
        $arr['rs']    = $recordSet;
        $html         = $this->load->view('rekap_presensi_pegawai_dinas', $arr, true);

        $pdf = new \Mpdf\Mpdf();

        /*
        $pdf->AddPage('', // L - landscape, P - portrait
        '', '', '', '',
        5, // margin_left
        5, // margin right
        60, // margin top
        30, // margin bottom
        0, // margin header
        0); // margin footer
         */
        $pdf->AddPage(
            'L',
            '',
            '',
            '',
            '',
            10,
            10,
            10,
            10,
            6,
            3
        );

        $pdf->showImageErrors = true;

        $pdf->simpleTables  = true;
        $pdf->packTableData = true;

        $pdf->WriteHTML($html); // write the HTML into the PDF
        $pdf->Output($pdfFilePath, 'F'); // save to file because we can

        $this->load->helper('download');
        $file     = file_get_contents($pdfFilePath); // Read the file's contents
        $fileName = slugify($data['kabupaten']) . '_' . date('dMy') . '.pdf';

        @force_download($fileName, $file);
        error_reporting(E_ALL);
    }
}
