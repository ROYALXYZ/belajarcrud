<?php 
$server = "localhost";
$user = "root";
$password = "";
$database = "dblatihan";

$koneksi = mysqli_connect($server, $user, $password, $database) or die(mysqli_error($koneksi));

session_start();

// Cek apakah pengguna telah login dan memiliki level yang sesuai
if (!isset($_SESSION['level']) || ($_SESSION['level'] !== 'Siswa' && $_SESSION['level'] !== 'Instruktur')) {
    header('Location: index.php');
    exit();
}

// Ambil username dari sesi
$username = $_SESSION['username'];

// Ambil informasi pengguna dari database
$query = "SELECT profile_picture FROM tuser WHERE username = ?";
$stmt = $koneksi->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
} else {
    echo "Error preparing statement: " . $koneksi->error;
    exit();
}

// Hapus gambar profil dari database
$query = "UPDATE tuser SET profile_picture = NULL WHERE username = ?";
$stmt = $koneksi->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $username);
    if ($stmt->execute()) {
        // Gambar profil berhasil dihapus
        // Hapus file gambar dari server (opsional)
        if (!empty($user['profile_picture'])) {
            $uploadDir = 'uploads/';
            $filePath = $uploadDir . basename($user['profile_picture']);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        // Redirect atau tampilkan pesan sukses
        header('Location: profile.php?from=profile.php');
        exit();
    } else {
        // Kesalahan saat menghapus gambar profil
        echo "Error executing query: " . $koneksi->error;
    }
    $stmt->close();
} else {
    echo "Error preparing statement: " . $koneksi->error;
}

$koneksi->close();
?>
