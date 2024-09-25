<?php
require '/autoload.php';

use PhpOffice\PhpSpreadsheet\src\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_POST['export'])) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Data Nilai');

    // Menambahkan header kolom
    $sheet->setCellValue('A1', 'Pilih');
    $sheet->setCellValue('B1', 'Absen');
    $sheet->setCellValue('C1', 'Nama');
    $sheet->setCellValue('D1', 'Kelas');
    $sheet->setCellValue('E1', 'Push Up');
    $sheet->setCellValue('F1', 'Pull Up');
    $sheet->setCellValue('G1', 'Lari 12 Menit');
    $sheet->setCellValue('H1', 'Sit Up');
    $sheet->setCellValue('I1', 'Tanggal');

    // Mengambil data dari database
    include 'koneksi.php';

    $instruktur_id = mysqli_real_escape_string($koneksi, $_SESSION['id']);
    $query = "SELECT * FROM data_nilai WHERE instruktur_id='$instruktur_id' AND (no_absen LIKE '%$search%' OR nama LIKE '%$search%' OR kelas LIKE '%$search%')";

    if (!empty($kelas_filter)) {
        $query .= " AND kelas = '$kelas_filter'";
    }
    if (!empty($date_from) && !empty($date_to)) {
        $query .= " AND tanggal BETWEEN '$date_from' AND '$date_to'";
    } elseif (!empty($date_from)) {
        $query .= " AND tanggal >= '$date_from'";
    } elseif (!empty($date_to)) {
        $query .= " AND tanggal <= '$date_to'";
    }

    $query .= " ORDER BY $sort_column $sort_order";

    $result = mysqli_query($koneksi, $query);

    $rowNumber = 2;
    while ($row = mysqli_fetch_assoc($result)) {
        $sheet->setCellValue('A' . $rowNumber, $row['id']);
        $sheet->setCellValue('B' . $rowNumber, $row['no_absen']);
        $sheet->setCellValue('C' . $rowNumber, $row['nama']);
        $sheet->setCellValue('D' . $rowNumber, $row['kelas']);
        $sheet->setCellValue('E' . $rowNumber, $row['push_up']);
        $sheet->setCellValue('F' . $rowNumber, $row['pull_up']);
        $sheet->setCellValue('G' . $rowNumber, $row['lari_12menit']);
        $sheet->setCellValue('H' . $rowNumber, $row['sit_up']);
        $sheet->setCellValue('I' . $rowNumber, $row['tanggal']);
        $rowNumber++;
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'data_nilai_' . date('Y-m-d') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit();
}
?>
