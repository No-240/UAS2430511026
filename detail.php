<?php
include "koneksi.php";

$id    = (int) $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM barang WHERE id=$id");
$data  = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Detail Barang</h2>

    <div class="card shadow" style="max-width: 450px;">
        <?php if ($data['gambar']): ?>
            <img src="img/<?= $data['gambar'] ?>" class="card-img-top" style="max-height:250px; object-fit:cover;">
        <?php endif; ?>

        <div class="card-body">
            <table class="table table-borderless mb-2">
                <tr>
                    <td><strong>Kode</strong></td>
                    <td><?= htmlspecialchars($data['kode']) ?></td>
                </tr>
                <tr>
                    <td><strong>Nama Barang</strong></td>
                    <td><?= htmlspecialchars($data['nama_barang']) ?></td>
                </tr>
                <tr>
                    <td><strong>Stok</strong></td>
                    <td><?= $data['stok'] ?></td>
                </tr>
            </table>

            <?php if ($data['tanda_tangan']): ?>
                <p><strong>Tanda Tangan:</strong></p>
                <img src="img/<?= $data['tanda_tangan'] ?>" width="150" class="border rounded">
            <?php endif; ?>

            <div class="mt-3">
                <a href="index.php" class="btn btn-secondary">Kembali</a>
                <a href="edit.php?id=<?= $data['id'] ?>" class="btn btn-warning">Edit</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>
