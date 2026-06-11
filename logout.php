<?php
// Wajib panggil session_start() dulu
session_start();

// Hapus semua data session
session_destroy();

// Arahkan kembali ke halaman login
header("Location: login.php");
exit;