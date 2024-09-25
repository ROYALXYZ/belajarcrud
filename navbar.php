<?php 
// Mulai session dan koneksi database
session_start();

$server = "localhost";
$user = "root";
$password = "";
$database = "dblatihan";

$koneksi = mysqli_connect($server, $user, $password, $database) or die(mysqli_error($koneksi));

// Ambil informasi pengguna
$query = "SELECT profile_picture FROM tuser WHERE username = ?";
$stmt = $koneksi->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $_SESSION['profile_picture'] = $user['profile_picture']; // Simpan di session
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags, styles, and scripts here -->
    <link rel="stylesheet" href="vendors/owl-carousel/css/owl.carousel.min.css">
    <link rel="stylesheet" href="vendors/owl-carousel/css/owl.theme.default.css">
    <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="vendors/aos/css/aos.css">
    <link rel="stylesheet" href="css/style.min.css">
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles */
        .profile-img-navbar, .profile-initials-navbar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex; /* Use flex to center the text */
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
            border: 2px solid #0a83c9;
            
        }

        .profile-img-navbar {
            object-fit: cover;
        }

        .profile-initials-navbar {
            background-color: #6b0909;
        }

        .dropdown-menu {
            margin-top: 10px; /* Ensure there's space between the dropdown and the profile picture */
        }
    </style>
</head>
<body>
<!-- Navbar -->
<header id="header-section">
    <nav class="navbar navbar-expand-lg pl-3 pl-sm-0" id="navbar">
        <div class="container">
            <div class="navbar-brand-wrapper d-flex w-100">
                <img src="images/logosmk3.png" alt="" width="54" height="54">
                <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="mdi mdi-menu navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse navbar-menu-wrapper" id="navbarSupportedContent">
                <ul class="navbar-nav align-items-lg-center align-items-start ml-auto">
                    <li class="nav-item dropdown">
                        <a class="" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php if (!empty($user['profile_picture'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-img-navbar">
                            <?php else: ?>
                                <div class="profile-initials-navbar">
                                    <?php echo getInitials($_SESSION['nama_lengkap']); ?>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                             <a class="dropdown-item" href="index.html"><i class="fa-solid fa-house fa-sm"></i> Home</a>    
                            <a class="dropdown-item" href="profile.php"> <i class="fa-solid fa-address-card fa-sm"></i> Your profile</a>
                            <a class="dropdown-item" href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket fa-sm"></i> Logout</a>
                           
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<!-- END OF NAVBAR -->

<script src="https://kit.fontawesome.com/9358831063.js" crossorigin="anonymous"></script>
<script src="vendors/jquery/jquery.min.js"></script>
<script src="vendors/popper/popper.min.js"></script>
<script src="vendors/bootstrap/bootstrap.min.js"></script>
<script src="js/landingpage.js"></script>
</body>
</html>
