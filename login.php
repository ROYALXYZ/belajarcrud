<?php
// Cek apakah ada parameter status di URL
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $message = "<div class='alert alert-success' role='alert'>Registrasi berhasil! Silakan login.</div>";
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <title>Form Login · Bootstrap</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/4.5/examples/floating-labels/">
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
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        /* Tambahkan CSS untuk ukuran dan posisi GIF loading */
        #loadingGif {
            width: 20px; /* Ukuran kecil untuk GIF */
            display: none; /* Sembunyikan secara default */
            margin-left: 10px; /* Jarak antara teks Sign in dan GIF */
        }
        .no-select {
         -webkit-user-select: none; /* Chrome, Safari, Opera */
         -moz-user-select: none; /* Firefox */
          -ms-user-select: none; /* Internet Explorer/Edge */
             user-select: none; /* Non-prefixed version, currently supported by Chrome, Opera, and Edge */
        }
    </style>
    <!-- Custom styles for this template -->
    <link href="assets/dist/css/floating-labels.css" rel="stylesheet">
</head>
<body>
    <form class="form-signin" method="POST" action="cek_login.php" id="loginForm">
        <div class="text-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="52" height="42" fill="currentColor" class="bi bi-door-open-fill" viewBox="0 0 16 16">
                <path d="M1.5 15a.5.5 0 0 0 0 1h13a.5.5 0 0 0 0-1H13V2.5A1.5 1.5 0 0 0 11.5 1H11V.5a.5.5 0 0 0-.57-.495l-7 1A.5.5 0 0 0 3 1.5V15zM11 2h.5a.5.5 0 0 1 .5.5V15h-1zm-2.5 8c-.276 0-.5-.448-.5-1s.224-1 .5-1 .5.448.5 1-.224 1-.5 1"/>
            </svg>
            <h1 class="h3 mb-3 font-weight-normal no-select">Form Login</h1>
            <p class="no-select">Masukan Username Dan Password Anda</p>
        </div>

        <?php if (isset($message)) echo $message; ?>

        <div class="form-label-group">
            <input type="text" name="username" class="form-control" placeholder="Masukan Username Anda" required autofocus>
            <label for="inputEmail">Username</label>
        </div>

        <div class="form-label-group">
            <input type="password" name="password" class="form-control" placeholder="Masukan Password Anda" required>
            <label for="inputPassword">Password</label>
        </div>

        <div class="form-label-group">
            <select class="form-control" name="level" required>
                <option value="" disabled selected>Login Sebagai?</option>
                <option value="Siswa">Siswa</option>
                <option value="Instruktur">Instruktur</option>
            </select>
        </div>

        <div>
            <small class="fw-lighter text-muted mb-5 no-select">Catar Tetapi Tidak Punya Akun? <a href="register.php">Register Disini</a></small>
        </div>

        <!-- Tambahkan elemen untuk GIF loading di dalam tombol -->
        <button class="btn btn-lg btn-primary btn-block" type="submit">
            Sign in
            <img src="images/loading-buffering.gif" alt="Loading..." id="loadingGif"> <!-- GIF berada di sebelah teks "Sign in" -->
        </button>
        
        <footer class="footer mt-5 mb-3 text-muted text-center no-select">
        <div class="container">
            <p class="mb-0">© <?php echo date("Y"); ?> Rangga Pasha. All rights reserved.</p>
            <p class="mb-0">Made With <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-balloon-heart" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="m8 2.42-.717-.737c-1.13-1.161-3.243-.777-4.01.72-.35.685-.451 1.707.236 3.062C4.16 6.753 5.52 8.32 8 10.042c2.479-1.723 3.839-3.29 4.491-4.577.687-1.355.587-2.377.236-3.061-.767-1.498-2.88-1.882-4.01-.721zm-.49 8.5c-10.78-7.44-3-13.155.359-10.063q.068.062.132.129.065-.067.132-.129c3.36-3.092 11.137 2.624.357 10.063l.235.468a.25.25 0 1 1-.448.224l-.008-.017c.008.11.02.202.037.29.054.27.161.488.419 1.003.288.578.235 1.15.076 1.629-.157.469-.422.867-.588 1.115l-.004.007a.25.25 0 1 1-.416-.278c.168-.252.4-.6.533-1.003.133-.396.163-.824-.049-1.246l-.013-.028c-.24-.48-.38-.758-.448-1.102a3 3 0 0 1-.052-.45l-.04.08a.25.25 0 1 1-.447-.224l.235-.468ZM6.013 2.06c-.649-.18-1.483.083-1.85.798-.131.258-.245.689-.08 1.335.063.244.414.198.487-.043.21-.697.627-1.447 1.359-1.692.217-.073.304-.337.084-.398"/>
            </svg>And GPT</p>
        </div>
    </footer>
    </form>

    <script src="vendors/jquery/jquery.min.js"></script>
    <script src="vendors/bootstrap/bootstrap.min.js"></script>
    <script src="vendors/owl-carousel/js/owl.carousel.min.js"></script>
    <script src="vendors/aos/js/aos.js"></script>
    <script src="js/landingpage.js"></script>
    
    <script>
        // JavaScript untuk menampilkan GIF saat form disubmit dan menambahkan delay
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Mencegah submit langsung
            document.getElementById('loadingGif').style.display = 'inline-block'; // Tampilkan GIF

            // Tambahkan delay 1 detik sebelum submit
            setTimeout(function() {
                document.getElementById('loginForm').submit(); // Kirim form setelah 1 detik
            }, 1000); // 1000 milidetik = 1 detik
        });
    </script>
</body>
</html>
