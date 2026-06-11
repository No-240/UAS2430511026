<?php
// Wajib ditaruh paling atas untuk memulai session
session_start();
include "koneksi.php";

// Kalau tombol login ditekan
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Cari user berdasarkan username saja
    $stmt = mysqli_prepare($conn, "SELECT * FROM user WHERE username=?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $cek = mysqli_stmt_get_result($stmt);  

    // 2. Cek apakah username ditemukan
    if (mysqli_num_rows($cek) === 1) {
        
        // Ambil data user tersebut
        $data = mysqli_fetch_assoc($cek);
        
        // 3. Verifikasi password yang diketik dengan hash di database
        if (password_verify($password, $data['password'])) {
            // Jika cocok, buat session
            $_SESSION['status'] = "login";
            $_SESSION['username'] = $username;
            
            // Pindahkan ke halaman utama
            header("Location: index.php");
            exit; // Hentikan script
        } else {
            $error = "Password yang Anda masukkan salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Sistem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="text-center mb-4">Login</h3>
                    
                    <?php if(isset($error)) { ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php } ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-4">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100">Masuk</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
