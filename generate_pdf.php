<?php
require('fpdf/fpdf.php'); // Pastikan path ini benar sesuai lokasi FPDF kamu
require('koneksi.php'); // Menyertakan file koneksi

// Mengambil parameter filter dari URL
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($koneksi, $_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($koneksi, $_GET['date_to']) : '';
$kelas_filter = isset($_GET['kelas_filter']) ? mysqli_real_escape_string($koneksi, $_GET['kelas_filter']) : '';

// Membuat instance FPDF dengan orientasi landscape
$pdf = new FPDF('L', 'mm', 'A4'); // 'L' untuk landscape, 'mm' untuk milimeter, 'A4' untuk ukuran kertas
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Menambahkan Judul
$judul = sprintf(
    'Data Nilai Ketarunaan Kelas %s',
    $kelas_filter,
    $date_from,
    $date_to
);
$pdf->Cell(0, 10, $judul, 0, 1, 'C');
$pdf->Image('images/logosmk3.png', 138, 19, 20, 20);

// Menambahkan Header Tabel

$pdf->SetFont('Arial', 'B', 10);
$pdf->Ln(30); // Beri jarak dari gambar

$pdf->Cell(30, 10, 'No Absen', 1);
$pdf->Cell(50, 10, 'Nama', 1);
$pdf->Cell(30, 10, 'Kelas', 1);
$pdf->Cell(30, 10, 'Push Up', 1);
$pdf->Cell(30, 10, 'Pull Up', 1);
$pdf->Cell(40, 10, 'Lari 12 Menit', 1);
$pdf->Cell(30, 10, 'Sit Up', 1);
$pdf->Cell(30, 10, 'Tanggal', 1);
$pdf->Ln();

// Mengambil Data dari Database
$pdf->SetFont('Arial', '', 10);
$query = "SELECT * FROM data_nilai WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (no_absen LIKE '%$search%' OR nama LIKE '%$search%' OR kelas LIKE '%$search%')";
}

if (!empty($date_from) && !empty($date_to)) {
    $query .= " AND tanggal BETWEEN '$date_from' AND '$date_to'";
} elseif (!empty($date_from)) {
    $query .= " AND tanggal >= '$date_from'";
} elseif (!empty($date_to)) {
    $query .= " AND tanggal <= '$date_to'";
}

if (!empty($kelas_filter)) {
    $query .= " AND kelas = '$kelas_filter'";
}

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die('Query error: ' . mysqli_error($koneksi));
}


while ($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(30, 10, $row['no_absen'], 1);
    $pdf->Cell(50, 10, $row['nama'], 1);
    $pdf->Cell(30, 10, $row['kelas'], 1);
    $pdf->Cell(30, 10, $row['push_up'], 1);
    $pdf->Cell(30, 10, $row['pull_up'], 1);
    $pdf->Cell(40, 10, $row['lari_12menit'], 1);
    $pdf->Cell(30, 10, $row['sit_up'], 1);
    $pdf->Cell(30, 10, $row['tanggal'], 1);
    $pdf->Ln();
}

// Menghasilkan PDF
$pdf->Output('I', 'data_nilai.pdf'.$date_from,$date_to,'Taruna');

// Tutup koneksi
mysqli_close($koneksi);
?>
