<?php
include "koneksi.php"; // Pastikan koneksi database sudah benar
include 'navbar.php';



// Pastikan hanya instruktur yang boleh mengakses
if ($_SESSION['level'] !== 'Instruktur') {
    header('Location: index.php');
    exit();
}

// Mengedit Data
if (isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $no_absen = mysqli_real_escape_string($koneksi, $_POST['no_absen']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $push_up = mysqli_real_escape_string($koneksi, $_POST['push_up']);
    $pull_up = mysqli_real_escape_string($koneksi, $_POST['pull_up']);
    $lari_12menit = mysqli_real_escape_string($koneksi, $_POST['lari_12menit']);
    $sit_up = mysqli_real_escape_string($koneksi, $_POST['sit_up']);
    $instruktur_id = mysqli_real_escape_string($koneksi, $_SESSION['id']);

    $query = "UPDATE data_nilai SET no_absen='$no_absen', nama='$nama', push_up='$push_up', pull_up='$pull_up', lari_12menit='$lari_12menit', sit_up='$sit_up' WHERE id='$id' AND instruktur_id='$instruktur_id'";
    if (!mysqli_query($koneksi, $query)) {
        echo "Error: " . mysqli_error($koneksi);
    }
}

// Menghapus Data
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['delete']);
    $instruktur_id = mysqli_real_escape_string($koneksi, $_SESSION['id']);

    $query = "DELETE FROM data_nilai WHERE id='$id' AND instruktur_id='$instruktur_id'";
    if (!mysqli_query($koneksi, $query)) {
        echo "Error: " . mysqli_error($koneksi);
    }
}

// Pencarian dan Filter Berdasarkan Kelas dan tanggal
$search = isset($_POST['search']) ? mysqli_real_escape_string($koneksi, $_POST['search']) : '';
$date_from = isset($_POST['date_from']) ? mysqli_real_escape_string($koneksi, $_POST['date_from']) : '';
$date_to = isset($_POST['date_to']) ? mysqli_real_escape_string($koneksi, $_POST['date_to']) : '';
$kelas_filter = isset($_POST['kelas_filter']) ? mysqli_real_escape_string($koneksi, $_POST['kelas_filter']) : '';



// Sorting
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'no_absen';
$sort_order = isset($_GET['order']) && in_array($_GET['order'], ['asc', 'desc']) ? $_GET['order'] : 'asc';

// Paginasi
$limit = 10; // Jumlah data per halaman
$page = max(1, isset($_GET['page']) ? (int)$_GET['page'] : 1);
$offset = ($page - 1) * $limit;

$instruktur_id = mysqli_real_escape_string($koneksi, $_SESSION['id']);

// Query dengan pencarian, pengurutan, dan filter berdasarkan kelas
$query = "SELECT * FROM data_nilai WHERE instruktur_id='$instruktur_id' AND (no_absen LIKE '%$search%' OR nama LIKE '%$search%' OR kelas LIKE '%$search%')";

$query = "SELECT * FROM data_nilai WHERE 1=1";

if (!empty($search)) {
    $query .= " AND nama LIKE '%$search%'";
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



$query .= " ORDER BY $sort_column $sort_order LIMIT $limit OFFSET $offset";
$result = mysqli_query($koneksi, $query);

// Mendapatkan total data untuk paginasi
$query_count = "SELECT COUNT(*) as total FROM data_nilai WHERE instruktur_id='$instruktur_id' AND (no_absen LIKE '%$search%' OR nama LIKE '%$search%' OR kelas LIKE '%$search%')";

if (!empty($kelas_filter)) {
    $query_count .= " AND kelas = '$kelas_filter'";
}

if (!empty($date_from) && !empty($date_to)) {
    $query_count .= " AND tanggal BETWEEN '$date_from' AND '$date_to'";
} elseif (!empty($date_from)) {
    $query_count .= " AND tanggal >= '$date_from'";
} elseif (!empty($date_to)) {
    $query_count .= " AND tanggal <= '$date_to'";
}

$result_count = mysqli_query($koneksi, $query_count);
$row_count = mysqli_fetch_assoc($result_count);
$total = $row_count['total'];
$total_pages = ceil($total / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Instruktur</title>
    <link rel="stylesheet" href="vendors/owl-carousel/css/owl.carousel.min.css">
    <link rel="stylesheet" href="vendors/owl-carousel/css/owl.theme.default.css">
    <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendors/aos/css/aos.css">
    <link rel="stylesheet" href="css/style.min.css">
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 10px; /* Padding di dalam footer */
}

        .custom-jumbotron {
            background: rgb(0,9,1);
            background: linear-gradient(121deg, rgba(0,9,1,1) 0%, rgba(12,205,8,1) 30%, rgba(228,255,0,1) 95%);
            color: #fff;
            margin-top: 30px;
        }
        .custom-tambahdata {
            background-color: #1F3720;
            color: #000000;
        }
        .custom-shadowtext {
            text-shadow: -1.5px 1.5px 1px #ffff;
        }
        .custom-decoration {
            text-decoration: underline;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin: 10px;
        }
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
        .search-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .search-container input {
            flex: 1;
        }
        .search-container input[type="date"] {
            max-width: 200px;
        }
        .search-container input[type="text"] {
            max-width: 300px;
        }
        .psearch {
            text-shadow: 0.5px 0.5px;
        }
        .table td {
            word-wrap: break-word;
            max-width: 200px; /* Atur sesuai kebutuhan */
            overflow: hidden;
            text-overflow: ellipsis;
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

.btnpdf img {
    max-width: 80%; /* Menjaga ukuran ikon agar sesuai */
    max-height: 80%; /* Menjaga ukuran ikon agar sesuai */
}

.warnabtn{
    color:#fff;
}

.main-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }

    </style>
    <script>
        function sortTable(columnName) {
            var order = '<?php echo $sort_order === 'asc' ? 'desc' : 'asc'; ?>';
            if (columnName !== '<?php echo $sort_column; ?>') {
                order = 'asc';
            }
            window.location.href = 'home_instruktur.php?sort=' + columnName + '&order=' + order + '&search=<?php echo urlencode($search); ?>&kelas_filter=<?php echo urlencode($kelas_filter); ?>';
        }
    </script>
</head>
<body>
    <div class="container mt-4">
        <!-- Card -->
        <div class="card shadow">
            <div class="card-body">
                <!-- Jumbotron -->
                <div class="jumbotron custom-jumbotron">
                    <h1 class="display-4 bold">Selamat Datang <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></h1>
                    <h5>Di Pengisian Data Catar & Taruna SMKN3 YOGYAKARTA</h5>
                    <hr class="my-4">
                </div>
                <!-- END OF JUMBOTRON -->

                <form method="POST" action="">
                    <div class="search-container">
                        <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan No Absen, Nama, atau Kelas" value="<?php echo htmlspecialchars($search); ?>">
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
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="date" name="date_from" class="form-control" placeholder="Tanggal Dari" value="<?php echo htmlspecialchars($date_from); ?>">
                        </div>
                        <small>-</small>
                        <input type="date" name="date_to" class="form-control" placeholder="Tanggal Sampai" value="<?php echo htmlspecialchars($date_to); ?>">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        <a href="generate_pdf.php?search=<?php echo urlencode($search); ?>&date_from=<?php echo urlencode($date_from); ?>&date_to=<?php echo urlencode($date_to); ?>&kelas_filter=<?php echo urlencode($kelas_filter); ?>" class="btnpdf btn-primary"><i class="fa fa-print" style="font-size:24px"></i></a>
                    </div>
                </form>

                <!-- Pesan jika ada -->
                <?php if (isset($message)): ?>
                    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <!-- Formulir untuk Penghapusan Massal -->
                <table class="table table-striped table-bordered">
                    <a href="tambah_data.php" class="btn btn-success mr-2 mb-2">Tambah Data</a>
                    <thead>
                        <tr>
                            <th>Absen</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th><a href="javascript:void(0);" onclick="sortTable('push_up');">Push Up</a></th>
                            <th><a href="javascript:void(0);" onclick="sortTable('pull_up');">Pull Up</a></th>
                            <th><a href="javascript:void(0);" onclick="sortTable('lari_12menit');">Lari 12 Menit</a></th>
                            <th><a href="javascript:void(0);" onclick="sortTable('sit_up');">Sit Up</a></th>
                            <th>Tanggal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['no_absen']); ?></td>
                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                <td><?php echo htmlspecialchars($row['kelas']); ?></td>
                                <td><?php echo htmlspecialchars($row['push_up']); ?></td>
                                <td><?php echo htmlspecialchars($row['pull_up']); ?></td>
                                <td><?php echo htmlspecialchars($row['lari_12menit']); ?></td>
                                <td><?php echo htmlspecialchars($row['sit_up']); ?></td>
                                <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                                <td>
                                    <a href="edit_data.php?id=<?php echo $row['id']; ?>" class="btn btn-warning warnabtn">Edit</a>
                                    <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');" class="btn btn-danger">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Paginasi -->
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>&search=<?php echo urlencode($search); ?>&kelas_filter=<?php echo urlencode($kelas_filter); ?>">Previous</a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>&search=<?php echo urlencode($search); ?>&kelas_filter=<?php echo urlencode($kelas_filter); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>&search=<?php echo urlencode($search); ?>&kelas_filter=<?php echo urlencode($kelas_filter); ?>">Next</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- END OF CARD -->

        <!-- Footer -->
        <div class="footer mt-4">
            <p>&copy; 2024 Rangga Pasha C.W</p>
        </div>
        <!-- end of footer -->

        <script src="js/vendor.bundle.base.js"></script>
        <script src="js/template.js"></script>
    </body>
</html>
