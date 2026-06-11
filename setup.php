<?php
include "koneksi.php";

$username = "admin";
$password_asli = "admin123";
// Mengenkripsi password
$password_hash = password_hash($password_asli, PASSWORD_DEFAULT);

// Masukkan ke database
mysqli_query($conn, "INSERT INTO user (username, password) VALUES ('$username', '$password_hash')");

echo "Admin berhasil dibuat! Silahkan hapus file setup.php ini.";
?>
