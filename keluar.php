<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("Location: login.php");
    exit;
}
include "koneksi.php";

// Ambil daftar barang untuk dropdown
$queryBarang = mysqli_query($conn, "SELECT id, kode, nama_barang, stok FROM barang ORDER BY nama_barang");

// Proses simpan data keluar
if (isset($_POST['submit'])) {
    $barang_id = (int) $_POST['barang_id'];
    $jumlah    = (int) $_POST['jumlah'];
    $tanggal   = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    // Cek stok cukup
    $cekStok = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stok FROM barang WHERE id = $barang_id"));
    if ($cekStok['stok'] < $jumlah) {
        $error = "Stok tidak mencukupi! Sisa stok: " . $cekStok['stok'];
    } else {
        // Mulai transaksi
        mysqli_begin_transaction($conn);
        try {
            // Kurangi stok barang
            mysqli_query($conn, "UPDATE barang SET stok = stok - $jumlah WHERE id = $barang_id");

            // Catat barang keluar
            mysqli_query($conn, "INSERT INTO barang_keluar (barang_id, jumlah, tanggal_keluar, keterangan) 
                                  VALUES ($barang_id, $jumlah, '$tanggal', '$keterangan')");

            mysqli_commit($conn);
            header("Location: keluar.php?status=keluar");
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Gagal menyimpan: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Barang Keluar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Pencatatan Barang Keluar</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Data berhasil disimpan!</div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Pilih Barang</label>
            <select name="barang_id" class="form-select" required>
                <option value="">-- Pilih --</option>
                <?php while ($barang = mysqli_fetch_assoc($queryBarang)): ?>
                    <option value="<?= $barang['id'] ?>">
                        <?= $barang['kode'] . ' - ' . $barang['nama_barang'] . ' (Stok: ' . $barang['stok'] . ')' ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Jumlah Keluar</label>
            <input type="number" name="jumlah" class="form-control" min="1" required>
        </div>
        <div class="mb-3">
            <label>Tanggal Keluar</label>
            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="mb-3">
            <label>Keterangan (opsional)</label>
            <textarea name="keterangan" class="form-control" rows="2"></textarea>
        </div>
        <button type="submit" name="submit" class="btn btn-danger">Proses Keluar</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>

    <hr>
    <h4>Riwayat Barang Keluar</h4>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $queryRiwayat = mysqli_query($conn, "
                SELECT bk.*, b.kode, b.nama_barang 
                FROM barang_keluar bk 
                JOIN barang b ON bk.barang_id = b.id 
                ORDER BY bk.tanggal_keluar DESC, bk.created_at DESC
            ");
            $no = 1;
            while ($row = mysqli_fetch_assoc($queryRiwayat)):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['kode'] ?></td>
                <td><?= $row['nama_barang'] ?></td>
                <td><?= $row['jumlah'] ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal_keluar'])) ?></td>
                <td><?= $row['keterangan'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>