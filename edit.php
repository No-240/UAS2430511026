<?php
include "koneksi.php";

$id    = (int) $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM barang WHERE id=$id");
$data  = mysqli_fetch_assoc($query);

if (isset($_POST['submit'])) {

    $kode        = mysqli_real_escape_string($conn, $_POST['kode']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kategori    = mysqli_real_escape_string($conn, $_POST['kategori']);
    $stok        = (int) $_POST['stok'];
    $folder      = './img/';

    // Update Gambar
    $nama_gambar = $data['gambar'];
    if ($_FILES['gambar']['name'] != '') {
        if ($nama_gambar && file_exists($folder . $nama_gambar)) unlink($folder . $nama_gambar);
        $nama_gambar = time() . '_' . $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], $folder . $nama_gambar);
    }

    // Update Tanda Tangan
    $nama_ttd = $data['tanda_tangan'];
    if ($_FILES['tanda_tangan']['name'] != '') {
        if ($nama_ttd && file_exists($folder . $nama_ttd)) unlink($folder . $nama_ttd);
        $nama_ttd = time() . '_ttd_' . $_FILES['tanda_tangan']['name'];
        move_uploaded_file($_FILES['tanda_tangan']['tmp_name'], $folder . $nama_ttd);
    }

    mysqli_query($conn, "UPDATE barang SET
        kode='$kode', nama_barang='$nama_barang', kategori='$kategori', stok=$stok,
        gambar='$nama_gambar', tanda_tangan='$nama_ttd'
        WHERE id=$id");

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body class="bg-light">

<div class="container mt-5 bg-white p-4 rounded-5 shadow mb-5">
    <h2 class="mb-4">Edit Barang</h2>

    <form method="POST" enctype="multipart/form-data">

        <div class="mb-3">
            <label>Kode Barang</label>
            <input type="text" name="kode" class="form-control" value="<?= htmlspecialchars($data['kode']) ?>" required>
        </div>

        <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" value="<?= htmlspecialchars($data['nama_barang']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="kategori" class="form-label">Kategori</label>
            <select name="kategori" id="kategori" class="form-select" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="Mudah Pecah">Mudah Pecah</option>
                <option value="Tahan Banting">Tahan Banting</option>
                <option value="Elektronik">Elektronik</option>
                <option value="Makanan">Makanan</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control" value="<?= $data['stok'] ?>" min="0" required>
        </div>

        <div class="mb-3">
            <label>Gambar Barang</label><br>
            <?php if ($data['gambar']): ?>
                <img src="img/<?= $data['gambar'] ?>" width="100" class="mb-2 rounded"><br>
            <?php endif; ?>
            <input type="file" name="gambar" class="form-control" accept="image/*">
            <small class="text-danger">*Abaikan jika tidak ingin ganti gambar</small>
        </div>

        <div class="mb-3">
            <label>Tanda Tangan</label><br>
            <?php if ($data['tanda_tangan']): ?>
                <img src="img/<?= $data['tanda_tangan'] ?>" width="120" class="mb-2 rounded border"><br>
            <?php endif; ?>
            <input type="file" name="tanda_tangan" class="form-control" accept="image/*">
            <small class="text-danger">*Abaikan jika tidak ingin ganti tanda tangan</small>
        </div>

        <button name="submit" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>
