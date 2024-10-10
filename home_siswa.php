<!-- KIWWWW <3 hahahahaha -->

<?php
include "koneksi.php"; // Pastikan koneksi database sudah benar
include 'navbar.php';


$_SESSION['previous_page'] = 'home_siswa.php'; // atau 'home_instruktur.php'

// Cek apakah pengguna adalah siswa atau instruktur
if ($_SESSION['level'] !== 'Siswa' && $_SESSION['level'] !== 'Instruktur') {
    header('Location: index.php'); // Hanya siswa dan instruktur yang boleh mengakses
    exit();
}

// Ambil kata kunci pencarian, tanggal, dan filter kelas dari parameter GET atau POST
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$date = isset($_GET['date']) ? mysqli_real_escape_string($koneksi, $_GET['date']) : '';
$kelas_filter = isset($_GET['kelas_filter']) ? mysqli_real_escape_string($koneksi, $_GET['kelas_filter']) : '';

// Cek level pengguna dan modifikasi query SQL sesuai level
$query = "SELECT * FROM data_nilai WHERE (nama LIKE '%$search%' OR no_absen LIKE '%$search%' OR kelas LIKE '%$search%')";

// Jika pengguna adalah instruktur, tambahkan filter instruktur_id
if ($_SESSION['level'] === 'Instruktur') {
    $instruktur_id = $_SESSION['id']; // Mengambil id instruktur dari session
    $query .= " AND instruktur_id='$instruktur_id'";
}

// Tambahkan filter tanggal jika disediakan
if (!empty($date)) {
    $query .= " AND DATE(tanggal) = '$date'";
}

// Tambahkan filter kelas jika disediakan
if (!empty($kelas_filter)) {
    $query .= " AND kelas = '$kelas_filter'";
}

// Hitung jumlah total data untuk pagination
$total_query = "SELECT COUNT(*) AS total FROM data_nilai WHERE 1=1";
if ($_SESSION['level'] === 'Instruktur') {
    $total_query .= " AND instruktur_id='$instruktur_id'";
}
$total_result = mysqli_query($koneksi, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_data = $total_row['total'];

// Tentukan halaman saat ini
$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_index = ($current_page - 1) * $items_per_page;

// Hitung jumlah total halaman
$total_pages = ceil($total_data / $items_per_page);

// Lakukan penyortiran jika ada parameter sort yang dikirimkan
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'no_absen';
$sort_order = isset($_GET['order']) && ($_GET['order'] == 'asc' || $_GET['order'] == 'desc') ? $_GET['order'] : 'asc';

// Tambahkan batasan untuk pagination dan sorting
$query .= " ORDER BY $sort_column $sort_order LIMIT $start_index, $items_per_page";

$result = mysqli_query($koneksi, $query);

// Query untuk mendapatkan nilai tertinggi dan terendah dari masing-masing kategori
$max_query = "SELECT 
    MAX(push_up) AS max_push_up, 
    MIN(push_up) AS min_push_up,
    MAX(pull_up) AS max_pull_up, 
    MIN(pull_up) AS min_pull_up,
    MAX(lari_12menit) AS max_lari_12menit, 
    MIN(lari_12menit) AS min_lari_12menit,
    MAX(sit_up) AS max_sit_up, 
    MIN(sit_up) AS min_sit_up
FROM data_nilai";

// Jika pengguna adalah instruktur, tambahkan kondisi WHERE untuk hanya menampilkan data terkait instruktur
if ($_SESSION['level'] === 'Instruktur') {
    $max_query .= " WHERE instruktur_id='$instruktur_id'";
}

$max_result = mysqli_query($koneksi, $max_query);
$max_values = mysqli_fetch_assoc($max_result);

// Menghitung rata-rata dari setiap kategori nilai
$avg_query = "SELECT 
    AVG(push_up) AS avg_push_up, 
    AVG(pull_up) AS avg_pull_up,
    AVG(lari_12menit) AS avg_lari_12menit, 
    AVG(sit_up) AS avg_sit_up
FROM data_nilai";

// Jika pengguna adalah instruktur, tambahkan kondisi WHERE untuk hanya menampilkan data terkait instruktur
if ($_SESSION['level'] === 'Instruktur') {
    $avg_query .= " WHERE instruktur_id='$instruktur_id'";
}

$avg_result = mysqli_query($koneksi, $avg_query);
$avg_values = mysqli_fetch_assoc($avg_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Siswa</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">.
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="vendors/owl-carousel/css/owl.carousel.min.css">
    <link rel="stylesheet" href="vendors/owl-carousel/css/owl.theme.default.css">
    <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendors/aos/css/aos.css">
    <link rel="stylesheet" href="css/style.min.css">
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Style untuk kartu utama */
        .profil {
            background-color: #781f04;
        }
        .profil:hover {
            background-color: #6e260f;
        }
        .main-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-bottom: 100px;
        }
        .statistik-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap; /* Membantu jika ada masalah dengan ruang pada perangkat kecil */
            gap: 20px; /* Jarak antar elemen dalam container */
        }
        .statistik-card {
            flex: 1 1 45%; /* Membuat setiap card mengisi 45% dari container dan fleksibel */
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        /* Styling untuk pagination */
        .pagination-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: inline-block;
        }
        .pagination-list li {
            display: inline;
            margin: 0 5px;
        }
        .pagination-list a {
            text-decoration: none;
            color: #007bff;
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .pagination-list .active a {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .custom-jumbotron {
            background: rgb(1,1,9);
            background: linear-gradient(90deg, rgba(1,1,9,1) 0%, rgba(9,9,121,1) 30%, rgba(255,0,194,1) 100%);
            color: #ffffff;
            margin-top: 30px;
            margin: 18px;
        }

        .btnpdf {
    background: linear-gradient(to right, #8E9E6B, #636e4a); /* Efek gradien */
    color: white;
    border: none;
    border-radius: 5px;
    width: 60px; /* Lebar yang lebih baik */
    height: 60px; /* Tinggi untuk menampung ikon */
    display: flex; /* Menggunakan flexbox untuk penataan */
    justify-content: center; /* Memusatkan secara horizontal */
    align-items: center; /* Memusatkan secara vertikal */
    cursor: pointer; /* Kursor berubah menjadi pointer */
    transition: background 0.3s ease-in; /* Transisi halus untuk efek hover */
}

.btnpdf:hover {
    background: linear-gradient(to right, #636e4a, #8E9E6B); /* Efek hover dengan gradien terbalik */
    border: none;
    border-width: small;
}

.btn {
    background: linear-gradient(to right, #8E9E6B, #636e4a); /* Efek gradien */
    border: none;
    transition: background 0.3s ease-in; /* Transisi halus untuk efek hover */
}

.btn:hover {
    background: linear-gradient(to right, #636e4a, #8E9E6B); /* Efek hover dengan gradien terbalik */
    border: none;
    color: #fff;
}

    </style>
    <script>
        // Fungsi untuk melakukan sorting pada tabel berdasarkan kolom dan jenis sorting (asc/desc)
        function sortTable(columnName) {
            var order = 'asc'; // Default: ascending order

            // Jika kolom yang sama di-klik lagi, ubah arah sortirnya
            if (columnName === '<?php echo $sort_column; ?>') {
                order = '<?php echo $sort_order === 'asc' ? 'desc' : 'asc'; ?>';
            }

            // Redirect ke halaman dengan parameter sort dan order yang baru
            window.location.href = 'home_siswa.php?sort=' + columnName + '&order=' + order + '&search=<?php echo urlencode($search); ?>&date=<?php echo urlencode($date); ?>&kelas_filter=<?php echo urlencode($kelas_filter); ?>';
        }
    </script>
</head>
<body>
<div class="container mt-4">
    <!-- Kartu Utama -->
    <div class="main-card">
        <!-- Jumbotron -->
        <div class="jumbotron custom-jumbotron">
            <h1 class="display-4 bold">Selamat Datang <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></h1>
            <h5>Di Data Nilai Harian Catar & Taruna SMKN3 YOGYAKARTA</h5>
            <hr class="my-4">
        </div>
        <!-- END OF JUMBOTRON -->   

         <!-- Formulir Pencarian -->
    <form class="form-inline mb-4" method="GET" action="home_siswa.php">
        <div class="form-group mr-2">
            <label for="search" class="sr-only">Cari</label>
            <input type="text" class="form-control" id="search" name="search" placeholder="Cari Nama/No Absen/Kelas" value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <div class="form-group mr-2">
            <label for="date" class="sr-only">Tanggal</label>
            <input type="date" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
        </div>
        <div class="form-group">
                <select name="kelas_filter" class="form-control">
                        <option value="">Semua Kelas</option>
                        <option value="X TJ" <?php echo ($kelas_filter === 'X TJ') ? 'selected' : ''; ?>>X TJ</option>
                        <option value="X BP" <?php echo ($kelas_filter === 'X BP') ? 'selected' : ''; ?>>X BP</option>
                        <option value="X TE1" <?php echo ($kelas_filter === 'X TE1') ? 'selected' : ''; ?>>X TE1</option>
                        <option value="X TE2" <?php echo ($kelas_filter === 'X TE2') ? 'selected' : ''; ?>>X TE2</option>
                        <option value="X TM1" <?php echo ($kelas_filter === 'X TM1') ? 'selected' : ''; ?>>X TM1</option>
                        <option value="X TM2" <?php echo ($kelas_filter === 'X TM2') ? 'selected' : ''; ?>>X TM2</option>
                        <option value="X TM3" <?php echo ($kelas_filter === 'X TM3') ? 'selected' : ''; ?>>X TM3</option>
                        <option value="X TM4" <?php echo ($kelas_filter === 'X TM4') ? 'selected' : ''; ?>>X TM4</option>
                        <option value="X TO1" <?php echo ($kelas_filter === 'X TO1') ? 'selected' : ''; ?>>X TO1</option>
                        <option value="X TO2" <?php echo ($kelas_filter === 'X TO2') ? 'selected' : ''; ?>>X TO2</option>
                        <option value="X TO3" <?php echo ($kelas_filter === 'X TO3') ? 'selected' : ''; ?>>X TO3</option>
                        <option value="X TO4" <?php echo ($kelas_filter === 'X TO4') ? 'selected' : ''; ?>>X TO4</option>
                        <option value="X TL1" <?php echo ($kelas_filter === 'X TL1') ? 'selected' : ''; ?>>X TL1</option>
                        <option value="X TL2" <?php echo ($kelas_filter === 'X TL2') ? 'selected' : ''; ?>>X TL2</option>
                        <option value="X TL3" <?php echo ($kelas_filter === 'X TL3') ? 'selected' : ''; ?>>X TL3</option>
                        <option value="X TL4" <?php echo ($kelas_filter === 'X TL4') ? 'selected' : ''; ?>>X TL4</option>
                        <option value="X DP1" <?php echo ($kelas_filter === 'X DP1') ? 'selected' : ''; ?>>X DP1</option>
                        <option value="X DP2" <?php echo ($kelas_filter === 'X DP2') ? 'selected' : ''; ?>>X DP2</option>
                        <option value="X DP3" <?php echo ($kelas_filter === 'X DP3') ? 'selected' : ''; ?>>X DP3</option>
                        <option value="X KP" <?php echo ($kelas_filter === 'X KP') ? 'selected' : ''; ?>>X KP</option>
                        <!-- Tambahkan pilihan kelas lainnya sesuai dengan kebutuhan -->
                    </select>
                    <button type="submit" class="btn btn-primary ml-2">Cari</button>
                </div>
            </form>
    </form>
    <!-- end of search -->

       
       <!-- Tabel Data Nilai -->


    <div class="table-responsive mt-5">
        <table class="table table-striped table-bordered">
            <thead>
                <small>Tekan Text Warna Biru Untuk Mensortir Dari Yang tertinggi/Terendah</small>
                <tr>
                    <th>No Absen</th>
                    <th>Nama</th>
                    <th>Kelas</th>
                    <th><a href="javascript:void(0);" onclick="sortTable('push_up');">Push Up</a></th>
                    <th><a href="javascript:void(0);" onclick="sortTable('pull_up');">Pull Up</a></th>
                    <th><a href="javascript:void(0);" onclick="sortTable('lari_12menit');">Lari 12 Menit</a></th>
                    <th><a href="javascript:void(0);" onclick="sortTable('sit_up');">Sit Up</a></th>
                    <th><a href="javascript:void(0);" onclick="sortTable('tanggal');">Tanggal</a></th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['no_absen']); ?></td>
                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                            <td><?php echo htmlspecialchars($row['push_up']); ?></td>
                            <td><?php echo htmlspecialchars($row['pull_up']); ?></td>
                            <td><?php echo htmlspecialchars($row['lari_12menit']); ?></td>
                            <td><?php echo htmlspecialchars($row['sit_up']); ?></td>
                            <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($row['tanggal']))); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data yang ditemukan</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    

    <!-- Navigasi Pagination -->
    <div class="pagination">
        <?php if ($total_pages > 1): ?>
            <ul class="pagination-list">
                <?php if ($current_page > 1): ?>
                    <li><a href="home_siswa.php?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&date=<?php echo urlencode($date); ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>">« Prev</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="<?php echo $i == $current_page ? 'active' : ''; ?>">
                        <a href="home_siswa.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&date=<?php echo urlencode($date); ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <li><a href="home_siswa.php?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&date=<?php echo urlencode($date); ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>">Next »</a></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>
    </div>

    <!-- end of pagination  -->

    <hr>

    <h4 class="text-center mt-4" >Statistik Nilai & Rata Rata</h4>
    <div class="statistik-container mt-3">
    
        <div class="statistik-card">
            <!-- Nilai Tertinggi / Terendah -->
            <p><strong>Push Up:</strong> Tertinggi: <?php echo htmlspecialchars($max_values['max_push_up']); ?> | Terendah: <?php echo htmlspecialchars($max_values['min_push_up']); ?></p>
            <p><strong>Pull Up:</strong> Tertinggi: <?php echo htmlspecialchars($max_values['max_pull_up']); ?> | Terendah: <?php echo htmlspecialchars($max_values['min_pull_up']); ?></p>
            <p><strong>Lari 12 Menit:</strong> Tertinggi: <?php echo htmlspecialchars($max_values['max_lari_12menit']); ?> | Terendah: <?php echo htmlspecialchars($max_values['min_lari_12menit']); ?></p>
            <p><strong>Sit Up:</strong> Tertinggi: <?php echo htmlspecialchars($max_values['max_sit_up']); ?> | Terendah: <?php echo htmlspecialchars($max_values['min_sit_up']); ?></p>
        </div>

        <!-- Rata-Rata Nilai -->
        <div class="statistik-card">
            <p><strong>Push Up:</strong> Rata-rata: <?php echo htmlspecialchars($avg_values['avg_push_up']); ?></p>
            <p><strong>Pull Up:</strong> Rata-rata: <?php echo htmlspecialchars($avg_values['avg_pull_up']); ?></p>
            <p><strong>Lari 12 Menit:</strong> Rata-rata: <?php echo htmlspecialchars($avg_values['avg_lari_12menit']); ?></p>
            <p><strong>Sit Up:</strong> Rata-rata: <?php echo htmlspecialchars($avg_values['avg_sit_up']); ?></p>
        </div>
    </div>
</div>

<?php
       
       include 'footer.php'
       
       ?>

</body>
</html>
