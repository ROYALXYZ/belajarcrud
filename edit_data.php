<!-- ngetest githubb -->

<?php
include "koneksi.php"; // Pastikan koneksi database sudah benar
include 'navbar.php';


if ($_SESSION['level'] !== 'Instruktur') {
    header('Location: index.php'); // Hanya instruktur yang boleh mengakses
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("ID tidak valid.");
}

// Query untuk mendapatkan data
$query = "SELECT * FROM data_nilai WHERE id = ? AND instruktur_id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("ii", $id, $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    die("Data tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_absen = mysqli_real_escape_string($koneksi, $_POST['no_absen']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $push_up = mysqli_real_escape_string($koneksi, $_POST['push_up']);
    $pull_up = mysqli_real_escape_string($koneksi, $_POST['pull_up']);
    $lari_12menit = mysqli_real_escape_string($koneksi, $_POST['lari_12menit']);
    $sit_up = mysqli_real_escape_string($koneksi, $_POST['sit_up']);
    
    $query_update = "UPDATE data_nilai SET no_absen=?, nama=?,kelas=?, push_up=?, pull_up=?, lari_12menit=?, sit_up=? WHERE id=? AND instruktur_id=?";
    $stmt_update = $koneksi->prepare($query_update);
    $stmt_update->bind_param("sssiiiisi", $no_absen, $nama,$kelas,$push_up, $pull_up, $lari_12menit, $sit_up, $id, $_SESSION['id']);
    
    if ($stmt_update->execute()) {
        header("Location: home_instruktur.php"); // Arahkan kembali ke halaman utama instruktur
        exit();
    } else {
        echo "Error updating record: " . $stmt_update->error;
    }
}

$stmt->close();
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Data</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/logosmk3.png" type="">
    <link rel="stylesheet" href="vendors/owl-carousel/css/owl.carousel.min.css">
    <link rel="stylesheet" href="vendors/owl-carousel/css/owl.theme.default.css">
    <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendors/aos/css/aos.css">
    <link rel="stylesheet" href="css/style.min.css">
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container-card {
            max-width: 800px; /* Batasi lebar formulir */
            margin: 40px auto; /* Pusatkan formulir */
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: #fff;
        }
        .form-group label {
            font-weight: bold;
        }
        .modal-content {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    
    <div class="container-card">
        <h1 class="text-center mt-4">Edit Data</h1>
        <form id="updateForm" method="POST" action="">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['id']); ?>">
            <div class="form-group">
                <label for="no_absen">No Absen</label>
                <input type="number" class="form-control" id="no_absen" name="no_absen" value="<?php echo htmlspecialchars($data['no_absen']); ?>" autofocus required>
            </div>
            <div class="form-group">
                <label for="nama">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
            </div>
            <div class="form-group">
                <label for="nama">Kelas</label>
                <input type="text" class="form-control" id="kelas" name="kelas" value="<?php echo htmlspecialchars($data['kelas']); ?>" required>
            </div>
            <div class="form-group">
                <label for="push_up">Push Up</label>
                <input type="number" class="form-control" id="push_up" name="push_up" value="<?php echo htmlspecialchars($data['push_up']); ?>" required>
            </div>
            <div class="form-group">
                <label for="pull_up">Pull Up</label>
                <input type="number" class="form-control" id="pull_up" name="pull_up" value="<?php echo htmlspecialchars($data['pull_up']); ?>" required>
            </div>
            <div class="form-group">
                <label for="lari_12menit">Lari 12 Menit</label>
                <input type="number" class="form-control" id="lari_12menit" name="lari_12menit" value="<?php echo htmlspecialchars($data['lari_12menit']); ?>" required>
            </div>
            <div class="form-group">
                <label for="sit_up">Sit Up</label>
                <input type="number" class="form-control" id="sit_up" name="sit_up" value="<?php echo htmlspecialchars($data['sit_up']); ?>" required>
            </div>
            <button type="button" class="btn btn-primary btn-md" id="btnKonfirmasiUpdate" data-toggle="modal" data-target="#konfirmasiModal">
                Update
            </button>
        </form>
    </div>

    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="konfirmasiModal" tabindex="-1" aria-labelledby="konfirmasiModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="konfirmasiModalLabel">Konfirmasi Update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin melakukan update?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnUpdate">Ya, Update</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('btnKonfirmasiUpdate').addEventListener('click', function() {
            // Tampilkan alert konfirmasi sebelum mengizinkan form untuk disubmit
            if (confirm("Apakah Anda yakin ingin melakukan update?")) {
                document.getElementById('updateForm').submit(); // Submit form jika konfirmasi diterima
            }
        });
    </script>

<?php
       
       include 'footer.php'
       
       ?>

    <script src="vendors/jquery/jquery.min.js"></script>
        <script src="vendors/bootstrap/bootstrap.min.js"></script>
        <script src="vendors/owl-carousel/js/owl.carousel.min.js"></script>
        <script src="vendors/aos/js/aos.js"></script>
        <script src="js/landingpage.js"></script>
    </body>
    </html>
