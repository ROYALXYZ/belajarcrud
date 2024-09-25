<?php
include "koneksi.php"; // Pastikan koneksi database sudah benar
include 'navbar.php'; 

if ($_SESSION['level'] !== 'Instruktur') {
    header('Location: index.php'); // Hanya instruktur yang boleh mengakses
    exit();
}

// Menambah Data
if (isset($_POST['add'])) {
    $no_absen = mysqli_real_escape_string($koneksi, $_POST['no_absen']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']); // Pastikan ini adalah string
    $push_up = mysqli_real_escape_string($koneksi, $_POST['push_up']);
    $pull_up = mysqli_real_escape_string($koneksi, $_POST['pull_up']);
    $lari_12menit = mysqli_real_escape_string($koneksi, $_POST['lari_12menit']);
    $sit_up = mysqli_real_escape_string($koneksi, $_POST['sit_up']);
    $instruktur_id = $_SESSION['id']; // Mengambil id instruktur dari session

    // Buat query SQL
    $query = "INSERT INTO data_nilai (no_absen, nama, kelas, push_up, pull_up, lari_12menit, sit_up, instruktur_id) VALUES ('$no_absen', '$nama', '$kelas', '$push_up', '$pull_up', '$lari_12menit', '$sit_up', '$instruktur_id')";

    // Eksekusi query dan tangani error
    if (mysqli_query($koneksi, $query)) {
        header('Location: home_instruktur.php'); // Redirect ke halaman utama setelah menambah data
        exit();
    } else {
        echo "Error: " . mysqli_error($koneksi); // Tampilkan error SQL jika ada
    }
}
?>


<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <title>Tambah Data</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container-tambah {
            max-width: 800px; /* Batasi lebar formulir */
            margin: 40px auto; /* Pusatkan formulir */
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            background-color: #fff;
        }
        .form-group {
            margin-bottom: 15px; /* Jarak bawah antar elemen form */
        }
        .form-control {
            border-radius: 5px; /* Membuat sudut input membulat */
            padding: 10px; /* Padding di dalam input */
            font-size: 16px; /* Ukuran font dalam input */
        }
        .btn-primary {
            border-radius: 5px; /* Membuat sudut button membulat */
            padding: 10px 20px; /* Padding di dalam button */
            font-size: 16px; /* Ukuran font dalam button */
        }
        .align-right {
            text-align: right; /* Menyusun link kembali ke kanan */
            display: block; /* Menjadikan link tampil dalam baris terpisah */
            margin-top: 10px; /* Jarak atas dari elemen lain */
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding: 10px; /* Padding di dalam footer */
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
</head>
<body>
    <div class="container-tambah mt-4">
        <h2>Tambah Data</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label>No Absen:</label>
                <input type="number" name="no_absen" class="form-control" autofocus required="required">
            </div>
            <div class="form-group">
                <label>Nama:</label>
                <input type="text" name="nama" class="form-control" required="required">
            </div>
                    <div class="form-group">
            <label for="kelas">Kelas:</label>
            <select name="kelas" class="form-control" required="required" id="kelas">
                <option value="">Kelas</option>
                <option value="X TJ">X TJ</option>
                <option value="X BP">X BP</option>
                <option value="X TE1">X TE1</option>
                <option value="X TE2">X TE1</option>
                <option value="X TM1">X TM1</option>
                <option value="X TM2">X TM2</option>
                <option value="X TM3">X TM3</option>
                <option value="X TM4">X TM4</option>
                <option value="X TO1">X TO1</option>
                <option value="X TO2">X TO2</option>
                <option value="X TO3">X TO3</option>
                <option value="X TO4">X TO4</option>
                <option value="X TL1">X TL1</option>
                <option value="X TL2">X TL2</option>
                <option value="X TL3">X TL3</option>
                <option value="X TL4">X TL4</option>
                <option value="X DP1">X DP1</option>
                <option value="X DP2">X DP2</option>
                <option value="X DP3">X DP3</option>
                <option value="X KP">X KP</option>
                <!-- Tambahkan opsi lain sesuai kebutuhan -->
            </select>
            </div>


            <div class="form-group">
                <label>Push Up:</label>
                <input type="number" name="push_up" class="form-control" required="required">
            </div>
            <div class="form-group">
                <label>Pull Up:</label>
                <input type="number" name="pull_up" class="form-control" required="required">
            </div>
            <div class="form-group">
                <label>Lari 12 Menit:</label>
                <input type="number" name="lari_12menit" class="form-control" required="required">
            </div>
            <div class="form-group">
                <label>Sit Up:</label>
                <input type="number" name="sit_up" class="form-control" required="required">
            </div>
            <button type="submit" name="add" class="btn btn-primary btn-md">Tambah Data</button>
            <small class="align-right"><a href="home_instruktur.php">Kembali?</a></small>
        </form>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <p class="mb-0">© <?php echo date("Y"); ?> Rangga Pasha. All rights reserved.</p>
                <p class="mb-0">Made With <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-balloon-heart" viewBox="0 0 16 16"><path fill-rule="evenodd" d="m8 2.42-.717-.737c-1.13-1.161-3.243-.777-4.01.72-.35.685-.451 1.707.236 3.062C4.16 6.753 5.52 8.32 8 10.042c2.479-1.723 3.839-3.29 4.491-4.577.687-1.355.587-2.377.236-3.061-.767-1.498-2.88-1.882-4.01-.721zm-.49 8.5c-10.78-7.44-3-13.155.359-10.063q.068.062.132.129.065-.067.132-.129c3.36-3.092 11.137 2.624.357 10.063l.235.468a.25.25 0 1 1-.448.224l-.008-.017c.008.11.02.202.037.29.054.27.161.488.419 1.003.288.578.235 1.15.076 1.629-.157.469-.422.867-.588 1.115l-.004.007a.25.25 0 1 1-.416-.278c.168-.252.4-.6.533-1.003.133-.396.163-.824-.049-1.246l-.013-.028c-.24-.48-.38-.758-.448-1.102a3 3 0 0 1-.052-.45l-.04.08a.25.25 0 1 1-.447-.224l.235-.468ZM6.013 2.06c-.649-.18-1.483.083-1.85.798-.131.258-.245.689-.08 1.335.063.244.414.198.487-.043.21-.697.627-1.447 1.359-1.692.217-.073.304-.337.084-.398"/></svg> And GPT</p>
            </div>
        </footer>
        <!-- END OF FOOTER -->

        <script>
        $(document).ready(function() {
         $('#kelas').select2({
        placeholder: "Pilih Kelas", // Teks placeholder
        allowClear: true // Memungkinkan pengguna untuk menghapus pilihan
         });
        });
        </script>
        <!-- Script -->
        <script src="vendors/jquery/jquery.min.js"></script>
        <script src="vendors/bootstrap/bootstrap.min.js"></script>
        <script src="vendors/owl-carousel/js/owl.carousel.min.js"></script>
        <script src="vendors/aos/js/aos.js"></script>
        <script src="js/landingpage.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    </body>
</html>
