<?php

// Panggil database
include "koneksi.php";


// Ambil data dari formulir
$username = mysqli_real_escape_string($koneksi, $_POST['username']);
$pass = $_POST['password']; // Jangan hash password saat login
$level = mysqli_real_escape_string($koneksi, $_POST['level']);

// Cek username dan level
$query = "SELECT * FROM tuser WHERE username = ? AND level = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("ss", $username, $level);
$stmt->execute();
$result = $stmt->get_result();
$user_valid = $result->fetch_assoc();

// Uji jika username terdaftar
if ($user_valid) {
    // Periksa password
    if (password_verify($pass, $user_valid['password'])) {
        session_start();
        $_SESSION['username'] = $user_valid['username'];
        $_SESSION['nama_lengkap'] = $user_valid['nama_lengkap'];
        $_SESSION['level'] = $user_valid['level'];
        $_SESSION['id'] = $user_valid['id']; 
        
        // Redirect berdasarkan level
        if ($user_valid['level'] == "Siswa") {
            header('Location: home_siswa.php');
        } elseif ($user_valid['level'] == "Instruktur") {
            header('Location: home_instruktur.php');
        }
        exit(); // Pastikan tidak ada output tambahan setelah redirect
    } else {
        echo "<script>alert('Maaf Login Gagal, Password Tidak Sesuai');document.location='login.php';</script>";
    }
} else {
    // Jika username tidak terdaftar, periksa jika level sesuai
    $query_check_username = "SELECT * FROM tuser WHERE username = ?";
    $stmt_check_username = $koneksi->prepare($query_check_username);
    $stmt_check_username->bind_param("s", $username);
    $stmt_check_username->execute();
    $result_check_username = $stmt_check_username->get_result();
    
    if ($result_check_username->num_rows > 0) {
        // Username terdaftar tetapi level tidak sesuai
        echo "<script>alert('Maaf Kamu Bukan $level');document.location='login.php';</script>";
    } else {
        // Username dan level tidak terdaftar
        echo "<script>alert('Maaf Login Gagal, Username Tidak Terdaftar');document.location='login.php';</script>";
    }
}

?>
