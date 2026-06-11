<?php
include "koneksi.php";

$id     = (int) $_GET['id'];
$query  = mysqli_query($conn, "SELECT gambar, tanda_tangan FROM barang WHERE id=$id");
$data   = mysqli_fetch_assoc($query);
$folder = './img/';

// Hapus file gambar jika ada
if ($data['gambar'] && file_exists($folder . $data['gambar'])) {
    unlink($folder . $data['gambar']);
}

// Hapus file tanda tangan jika ada
if ($data['tanda_tangan'] && file_exists($folder . $data['tanda_tangan'])) {
    unlink($folder . $data['tanda_tangan']);
}

// Hapus data dari database
mysqli_query($conn, "DELETE FROM barang WHERE id=$id");

header("Location: index.php");
exit;
?>
