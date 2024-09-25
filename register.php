<?php
// Panggil database
include "koneksi.php";

// Periksa apakah formulir dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari formulir
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $level = mysqli_real_escape_string($koneksi, $_POST['level']);

    // Periksa apakah username sudah ada
    $query_check_username = "SELECT * FROM tuser WHERE username = ?";
    $stmt_check_username = $koneksi->prepare($query_check_username);
    $stmt_check_username->bind_param("s", $username);
    $stmt_check_username->execute();
    $result_check_username = $stmt_check_username->get_result();

    if ($result_check_username->num_rows > 0) {
        $message = "<div class='alert alert-danger' role='alert'>Username sudah ada.</div>";
    } else {
        // Username belum ada, lanjutkan dengan pendaftaran
        $query_sql = "INSERT INTO tuser (nama_lengkap, username, password, level) VALUES (?, ?, ?, ?)";
        $stmt = $koneksi->prepare($query_sql);
        $stmt->bind_param("ssss", $nama_lengkap, $username, $password, $level);

        if ($stmt->execute()) {
            // Redirect ke halaman login dengan pesan sukses
            header('Location: login.php?status=success');
            exit(); // Pastikan tidak ada output tambahan setelah redirect
        } else {
            $message = "<div class='alert alert-danger' role='alert'>Error: Registrasi gagal.</div>";
        }

        $stmt->close();
    }

    $koneksi->close();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <title>Formulir Pendaftaran · Bootstrap</title>
    <!-- Bootstrap core CSS -->
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        
        .footer {
            background-color: #f8f9fa;
            text-align: center;
            margin-top: 40px;
            margin-bottom: 10px;
        }
      }

      .no-select {
         -webkit-user-select: none; /* Chrome, Safari, Opera */
         -moz-user-select: none; /* Firefox */
          -ms-user-select: none; /* Internet Explorer/Edge */
             user-select: none; /* Non-prefixed version, currently supported by Chrome, Opera, and Edge */
        }
    </style>
    <link href="assets/dist/css/floating-labels.css" rel="stylesheet">
</head>
<body>
    <form class="form-signin" method="POST" action="register.php">
        <div class="text-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="42" fill="currentColor" class="bi bi-person-plus" viewBox="0 0 16 16">
                <path d="M11.5 8a3.5 3.5 0 1 0-7 0 3.5 3.5 0 0 0 7 0zm-3.5 4a4.5 4.5 0 0 1 4.5 4.5V16H4V16a4.5 4.5 0 0 1 4.5-4.5zM9 7a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                <path d="M14.5 4.5a.5.5 0 0 1 .5.5v1.5h1.5a.5.5 0 0 1 0 1H15v1.5a.5.5 0 0 1-1 0V7h-1.5a.5.5 0 0 1 0-1H14V4.5a.5.5 0 0 1 .5-.5z"/>
            </svg>
            <h1 class="h3 mb-3 font-weight-normal no-select">Formulir Pendaftaran</h1>
            <p>Masukkan informasi pendaftaran Anda</p>
        </div>

        <?php if (isset($message)) echo $message; ?>

        <div class="form-label-group">
            <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap" required autofocus>
            <label for="inputNamaLengkap">Nama Lengkap</label>
        </div>

        <div class="form-label-group">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
            <label for="inputUsername">Username</label>
        </div>

        <div class="form-label-group">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <label for="inputPassword">Password</label>
        </div>

        <div class="form-labelq-group">
            <select class="form-control" name="level" required>
                <option value="" disabled selected>Kamu Adalah?</option>
                <option value="Siswa">Siswa</option>
            </select>
            <label for="inputLevel"></label>
        </div>
        <div>
            <small class="text-muted text-center no-select"s>Sudah Punya Akun? <a href="login.php">Login Disini</a></small>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Daftar</button>
        <!-- FOOTER -->
         <footer class="footer mt-5 mb-3 text-muted text-center">
        <div class="container no-select">
            <p class="mb-0">© <?php echo date("Y"); ?> Rangga Pasha. All rights reserved.</p>
            <p class="mb-0">Made With <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-balloon-heart" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="m8 2.42-.717-.737c-1.13-1.161-3.243-.777-4.01.72-.35.685-.451 1.707.236 3.062C4.16 6.753 5.52 8.32 8 10.042c2.479-1.723 3.839-3.29 4.491-4.577.687-1.355.587-2.377.236-3.061-.767-1.498-2.88-1.882-4.01-.721zm-.49 8.5c-10.78-7.44-3-13.155.359-10.063q.068.062.132.129.065-.067.132-.129c3.36-3.092 11.137 2.624.357 10.063l.235.468a.25.25 0 1 1-.448.224l-.008-.017c.008.11.02.202.037.29.054.27.161.488.419 1.003.288.578.235 1.15.076 1.629-.157.469-.422.867-.588 1.115l-.004.007a.25.25 0 1 1-.416-.278c.168-.252.4-.6.533-1.003.133-.396.163-.824-.049-1.246l-.013-.028c-.24-.48-.38-.758-.448-1.102a3 3 0 0 1-.052-.45l-.04.08a.25.25 0 1 1-.447-.224l.235-.468ZM6.013 2.06c-.649-.18-1.483.083-1.85.798-.131.258-.245.689-.08 1.335.063.244.414.198.487-.043.21-.697.627-1.447 1.359-1.692.217-.073.304-.337.084-.398"/>
            </svg>And GPT</p>
        </div>
    </footer>
    <!-- END OF FOOTER -->
    </form>
     <script src="vendors/jquery/jquery.min.js"></script>
     <script src="vendors/bootstrap/bootstrap.min.js"></script>
     <script src="vendors/owl-carousel/js/owl.carousel.min.js"></script>
     <script src="vendors/aos/js/aos.js"></script>
     <script src="js/landingpage.js"></script>
</body>
</html>
