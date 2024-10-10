<?php 
$server = "localhost";
$user = "root";
$password = "";
$database = "dblatihan";

$koneksi = mysqli_connect($server, $user, $password, $database) or die(mysqli_error($koneksi));

session_start();

// Cek apakah pengguna adalah siswa atau instruktur
if ($_SESSION['level'] !== 'Siswa' && $_SESSION['level'] !== 'Instruktur') {
    header('Location: index.php'); 
    exit();
}

if ($_SESSION['level'] === 'Instruktur') {
    $fromPage = isset($_GET['from']) ? $_GET['from'] : 'home_instruktur.php'; 
} else if ($_SESSION['level'] === 'Siswa') {
    $fromPage = isset($_GET['from']) ? $_GET['from'] : 'home_siswa.php';
} else {
    // Default to a fallback page if needed
    $fromPage = 'home.php'; // or wherever you want to redirect to
}


$uploadStatus = ''; // Variable to store upload status

// Tentukan ekstensi file yang diizinkan
$allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

// Cek apakah ada file yang diupload
if (isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileError = $file['error'];
    $fileSize = $file['size'];
    
    // Ambil ekstensi file
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Tentukan direktori upload
    $uploadDir = 'uploads/';
    $filePath = $uploadDir . basename($fileName);

    // Periksa apakah direktori upload ada
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if ($fileError === 0) {
        if (in_array($fileExtension, $allowedExtensions)) {
            if (move_uploaded_file($fileTmpName, $filePath)) {
                // Update nama file di database
                $username = $_SESSION['username'];
                $query = "UPDATE tuser SET profile_picture = ? WHERE username = ?";
                $stmt = $koneksi->prepare($query);
                if ($stmt) {
                    $stmt->bind_param("ss", $fileName, $username);
                    if ($stmt->execute()) {
                        $uploadStatus = 'success'; // Set upload status
                    } else {
                        $uploadStatus = 'error'; // Set upload status
                    }
                    $stmt->close();
                } else {
                    $uploadStatus = 'error'; // Set upload status
                }
            } else {
                $uploadStatus = 'error'; // Set upload status
            }
        } else {
            $uploadStatus = 'invalid_extension'; // Set upload status for invalid extension
        }
    } else {
        $uploadStatus = 'error'; // Set upload status
    }
}

// Cek apakah permintaan untuk menghapus foto profil
if (isset($_POST['delete_picture']) && $_POST['delete_picture'] === 'true') {
    // Ambil nama file foto profil dari database
    $username = $_SESSION['username'];
    $query = "SELECT profile_picture FROM tuser WHERE username = ?";
    $stmt = $koneksi->prepare($query);
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!empty($user['profile_picture'])) {
            // Hapus file dari server
            $fileToDelete = 'uploads/' . $user['profile_picture'];
            if (file_exists($fileToDelete)) {
                unlink($fileToDelete);
            }

            // Hapus nama file dari database
            $query = "UPDATE tuser SET profile_picture = NULL WHERE username = ?";
            $stmt = $koneksi->prepare($query);
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->close();

                $uploadStatus = 'deleted'; // Set upload status
            } else {
                $uploadStatus = 'error'; // Set upload status
            }
        }
    } else {
        $uploadStatus = 'error'; // Set upload status
    }
}

// Ambil informasi pengguna
$query = "SELECT * FROM tuser WHERE username = ?";
$stmt = $koneksi->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "Error preparing statement: " . $koneksi->error;
}
function getInitials($name) {
    // Pecah nama menjadi array kata
    $words = explode(" ", $name);

    // Ambil hanya dua kata pertama
    $firstTwoWords = array_slice($words, 0, 2);

    // Ambil inisial dari dua kata pertama
    $initials = "";
    foreach ($firstTwoWords as $word) {
        $initials .= strtoupper($word[0]);
    }

    return $initials;
}


// Cek apakah sudah ada foto profil
$hasProfilePicture = !empty($user['profile_picture']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="images/logosmk3.png" type="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="vendors/owl-carousel/css/owl.carousel.min.css">
    <link rel="stylesheet" href="vendors/owl-carousel/css/owl.theme.default.css">
    <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendors/aos/css/aos.css">
    <link rel="stylesheet" href="css/style.min.css">
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .card-container {
            border: 2px solid #ddd;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            padding: 30px;
            margin-top: 30px;
            background: #ffffff;
        }
        .card-container h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }
        /* ini ada profile */
        .profile-imgada {
            display: block;
            margin: 0 auto;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #0a83c9;
            font-size: 60px;
            font-weight: bold;
            color: white;
            background-color: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            text-transform: uppercase;
        }
        /* ini ga ada profile */
        .profile-img {
            display: block;
            margin: 0 auto;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #0a83c9;
            font-size: 60px;
            font-weight: bold;
            color: white;
            background-color: ;
            display: flex;
            justify-content: center;
            align-items: center;
            text-transform: uppercase;
        }
        .form-group {
            margin-top: 20px;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
        }
        .form-control {
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 10px;
        }
        .btn {
            border-radius: 5px;
            padding: 10px 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .custom-file-upload {
            border: 1px solid #ced4da;
            display: inline-block;
            padding: 6px 12px;
            cursor: pointer;
            border-radius: 4px;
            background-color: #f8f9fa;
            width: 100%;
            text-align: center;
        }
        .custom-file-input {
            display: none;
        }
        .custom-file-upload:hover {
            background-color: #e2e6ea;
        }
        .btn-hapus {
            border-radius: 50%;
            padding: 5px 10px; /* Mengurangi padding untuk ukuran tombol yang lebih kecil */
            font-size: 12px; /* Ukuran teks tombol */
            height: 30px; /* Tinggi tombol */
            width: auto; /* Biarkan lebar otomatis agar sesuai dengan konten */
            margin: 0 auto;
            background-color: #7a0a0a;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .btn-hapus:hover {
            background-color: #541818;
        }
        
        .container-sampah {
            display: flex;
            justify-content: flex-end; /* Menyusun elemen di akhir container */
        }
        .sampah:hover {
            color: #7a0a0a;
        }
        .btn-sampah {
            display: flex;
            justify-content: flex-end;
        }
    </style>
    <title>Profile</title>
</head>
<body>
<div class="container">
    <div class="jumbotron card-container">
        <h2 class="text-top text-center">Profile Kamu</h2>

        <!-- Display Profile Picture or Initials -->
        <div class="profile-container">
            <!-- Tampilkan gambar profil atau inisial jika gambar tidak ada -->
            <?php if ($hasProfilePicture): ?>
                <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-img" style="object-fit: cover;">
            <?php else: ?>
                <div class="profile-imgada">
                    <?php echo getInitials($_SESSION['nama_lengkap']); ?>
                </div>
            <?php endif; ?>

            <?php if ($hasProfilePicture): ?>
                <div class="container-sampah">
                    <form action="profile.php" method="post" style="margin-top: 20px;" class="btn-sampah ">
                        <input type="hidden" name="delete_picture" value="true">
                        <button type="submit" class="btn btn-hapus"><i class="fa-solid fa-trash fa-sm sampah"></i></button>
                    </form>
                </div>
            <?php endif; ?>

            <hr class="mx-1">
            <h5 class="display-5">Nama Lengkap : <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></h5>
            <h5>Username : <?php echo htmlspecialchars($_SESSION['username']); ?></h5>
            <h5>Level: <?php echo htmlspecialchars($_SESSION['level']); ?></h5>

            <!-- Form Upload hanya ditampilkan jika belum ada foto profil -->
            <?php if (!$hasProfilePicture): ?>
                <form action="profile.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="profile_picture">Tambahkan Gambar Profil:</label>
                        <div class="custom-file">
                            <input type="file" name="profile_picture" id="profile_picture" class="custom-file-input" aria-describedby="fileHelp" onchange="updateFileName()">
                            <label class="custom-file-upload" for="profile_picture">
                                <span id="file-name">Choose file</span>
                            </label>
                            <small id="fileHelp" class="form-text text-muted mb-5">Max file size is 2MB. Allowed extensions: jpg, jpeg, png, gif.</small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            <?php endif; ?>

            <p class="lead mt-4">
                <a class="btn btn-primary btn-sm" href="<?php echo htmlspecialchars($fromPage); ?>" role="button"> <i class="fa-solid fa-chevron-left"></i> Kembali </a>
                <a class="btn btn-danger btn-sm" href="logout.php" role="button">Logout <i class="fa-solid fa-exclamation"></i></a>
            </p>
        </div>
    </div>
</div>

       <?php
       
       include 'footer.php'
       
       ?>

<script>
    function updateFileName() {
        const fileInput = document.getElementById('profile_picture');
        const fileNameSpan = document.getElementById('file-name');
        fileNameSpan.textContent = fileInput.files[0].name;
    }
</script>
<script src="https://kit.fontawesome.com/9358831063.js" crossorigin="anonymous"></script>
<script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc" crossorigin="anonymous"></script>
</body>
</html>
