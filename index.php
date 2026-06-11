<?php
session_start();

// Cek apakah user sudah punya tiket login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    // Kalau belum login, tendang balik ke halaman login
    header("Location: login.php");
    exit(); // Hentikan eksekusi kode di bawahnya
}

include "koneksi.php";

$cari = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';

if ($cari != '') {
    $query = mysqli_query($conn, "SELECT * FROM barang 
        WHERE kode LIKE '%$cari%' OR nama_barang LIKE '%$cari%' 
        ORDER BY id DESC");
} else {
    $query = mysqli_query($conn, "SELECT * FROM barang ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pencatatan Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>

<body class="bg-light">

<div class="container mt-5">
    <h2 class="mb-4">Data Pencatatan Barang</h2>

    <a href="tambah.php" class="btn btn-primary mb-3">Tambah Barang</a>
    <a href="logout.php" class="btn btn-danger" onclick="return confirm('Yakin ingin keluar?')">Logout</a>

    <table class="table table-bordered table-striped" id="tabelBarang">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Stok</th>
                <th>Gambar</th>
                <th>Tanda Tangan</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php $no = 1; while ($data = mysqli_fetch_assoc($query)) { ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($data['kode']) ?></td>
                <td><?= htmlspecialchars($data['nama_barang']) ?></td>
                <td><?= $data['stok'] ?></td>
                <td>
                    <?php if ($data['gambar']): ?>
                        <img src="img/<?= $data['gambar'] ?>" width="70" class="rounded">
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($data['tanda_tangan']): ?>
                        <img src="img/<?= $data['tanda_tangan'] ?>" width="80" class="rounded border">
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="detail.php?id=<?= $data['id'] ?>" class="btn btn-info btn-sm">Detail</a>
                    <a href="edit.php?id=<?= $data['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="hapus.php?id=<?= $data['id'] ?>" onclick="return confirm('Yakin ingin hapus data ini?')" class="btn btn-danger btn-sm">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tabelBarang').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });
    });
</script>

<script>
    // Buat AudioContext sekali pakai
    function playSound(type) {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);

        if (type === 'click') {
        osc.frequency.value = 600;
        gain.gain.value = 0.08;
        osc.start();
        osc.stop(ctx.currentTime + 0.08);
        } else if (type === 'hapus') {
        osc.type = 'sawtooth';
        osc.frequency.value = 200;
        gain.gain.value = 0.1;
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
        osc.start();
        osc.stop(ctx.currentTime + 0.3);
        }
    }

    // Pasang ke semua tombol
    document.querySelectorAll('.btn-info, .btn-warning, .btn-primary').forEach(btn => {
        btn.addEventListener('click', () => playSound('click'));
    });

    document.querySelectorAll('.btn-danger').forEach(btn => {
        btn.addEventListener('mousedown', () => playSound('hapus'));
    });
</script>
</body>
</html>
