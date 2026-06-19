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
    $ttd_data = $_POST['tanda_tangan_data'] ?? '';
    $hapus_ttd = isset($_POST['hapus_ttd']) ? (int)$_POST['hapus_ttd'] : 0;

    if (!empty($ttd_data)) {
        $ttd_bersih = str_replace('data:image/png;base64,', '', $ttd_data);
        $ttd_bersih = str_replace(' ', '+', $ttd_bersih);
        $nama_ttd_baru = time() . '_ttd.png';
        if (file_put_contents($folder . $nama_ttd_baru, base64_decode($ttd_bersih))) {
            if ($data['tanda_tangan'] && file_exists($folder . $data['tanda_tangan'])) {
                unlink($folder . $data['tanda_tangan']);
            }
            $nama_ttd = $nama_ttd_baru;
        } else {
            $nama_ttd = $data['tanda_tangan']; 
        }
    } else if ($hapus_ttd == 1) {
        // User ingin menghapus tanda tangan
        if ($data['tanda_tangan'] && file_exists($folder . $data['tanda_tangan'])) {
            unlink($folder . $data['tanda_tangan']);
        }
        $nama_ttd = null; 
    } else {
        $nama_ttd = $data['tanda_tangan'];
    }

    mysqli_query($conn, "UPDATE barang SET
        kode='$kode', nama_barang='$nama_barang', kategori='$kategori', stok=$stok,
        gambar='$nama_gambar', tanda_tangan='$nama_ttd'
        WHERE id=$id");

    header("Location: index.php?status=add");
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
            <label>Tanda Tangan Digital</label>

            <?php if ($data['tanda_tangan']): ?>
                <div class="mb-2">
                    <img src="img/<?= $data['tanda_tangan'] ?>" width="120" class="rounded border" id="ttdLama">
                    <br><small class="text-muted">Tanda tangan saat ini (akan diganti jika menggambar di bawah)</small>
                </div>
            <?php endif; ?>

            <div class="border rounded" style="background:#fff; display:inline-block;">
                <canvas id="kanvasTTD" width="400" height="150" style="display:block; cursor:crosshair;"></canvas>
            </div>
            <br>
            <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="hapusTTD()">Hapus Tanda Tangan</button>
            <input type="hidden" name="tanda_tangan_data" id="tanda_tangan_data">
            <input type="hidden" name="hapus_ttd" id="hapus_ttd" value="0">
            <small class="d-block text-muted mt-1">*Gambar di kanvas untuk mengganti tanda tangan, atau klik tombol hapus untuk menghapus</small>
        </div>
        
        <button name="submit" class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script> 
    const canvas = document.getElementById('kanvasTTD');
    const ctx = canvas.getContext('2d');
    let menggambar = false;

    // Mouse
    canvas.addEventListener('mousedown', (e) => { menggambar = true; ctx.beginPath(); ctx.moveTo(e.offsetX, e.offsetY); });
    canvas.addEventListener('mousemove', (e) => { if (!menggambar) return; ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.strokeStyle = '#000'; ctx.lineTo(e.offsetX, e.offsetY); ctx.stroke(); });
    canvas.addEventListener('mouseup', () => { menggambar = false; simpanData(); });
    canvas.addEventListener('mouseleave', () => { menggambar = false; });

    // Touch
    canvas.addEventListener('touchstart', (e) => { e.preventDefault(); menggambar = true; const t = e.touches[0]; const r = canvas.getBoundingClientRect(); ctx.beginPath(); ctx.moveTo(t.clientX - r.left, t.clientY - r.top); });
    canvas.addEventListener('touchmove', (e) => { e.preventDefault(); if (!menggambar) return; const t = e.touches[0]; const r = canvas.getBoundingClientRect(); ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.strokeStyle = '#000'; ctx.lineTo(t.clientX - r.left, t.clientY - r.top); ctx.stroke(); });
    canvas.addEventListener('touchend', () => { menggambar = false; simpanData(); });

    function simpanData() {
        document.getElementById('tanda_tangan_data').value = canvas.toDataURL('image/png');
        document.getElementById('hapus_ttd').value = 0;
    }

    function hapusTTD() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        document.getElementById('tanda_tangan_data').value = '';
        document.getElementById('hapus_ttd').value = 1;
    }
</script>

</body>
</html>
