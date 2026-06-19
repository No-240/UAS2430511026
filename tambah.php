<?php
include "koneksi.php";

if (isset($_POST['submit'])) {

    $kode        = mysqli_real_escape_string($conn, $_POST['kode']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kategori    = mysqli_real_escape_string($conn, $_POST['kategori']);
    $stok        = (int) $_POST['stok'];
    $folder      = './img/';

    // Upload Gambar
    // Upload Multiple Gambar
    $tipe_diizinkan = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    $nama_gambar = '';
    if (!empty($_FILES['gambar']['name'])) {
        $tmp = $_FILES['gambar']['tmp_name'];
        $tipe = mime_content_type($tmp);
        if (!in_array($tipe, $tipe_diizinkan)) {
            die("File yang diizinkan hanya jpg, png, webp yang diizinkan.");
        }
        $nama_file = time() . '_' . $_FILES['gambar']['name'];
        move_uploaded_file($tmp, $folder . $nama_file);
        $nama_gambar = $nama_file;
    }

    // Upload Tanda Tangan
    // Simpan Tanda Tangan dari kanvas
    $nama_ttd = '';
    if (!empty($_POST['tanda_tangan_data'])) {
        $ttd_data = $_POST['tanda_tangan_data'];
        $ttd_bersih = str_replace('data:image/png;base64,', '', $ttd_data);
        $ttd_bersih = str_replace(' ', '+', $ttd_bersih);
        $nama_ttd = time() . '_ttd.png';
        file_put_contents($folder . $nama_ttd, base64_decode($ttd_bersih));
    }

    mysqli_query($conn, "INSERT INTO barang (kode, nama_barang, kategori, stok, gambar, tanda_tangan)
        VALUES ('$kode', '$nama_barang', '$kategori', $stok, '$nama_gambar', '$nama_ttd')");

    header("Location: index.php?status=add");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body class="bg-light">

<div class="container mt-5 bg-white p-4 rounded-5 shadow mb-5">
    <h2 class="mb-4">Tambah Barang</h2>

    <form method="POST" enctype="multipart/form-data">

        <div class="mb-3">
            <label>Kode Barang</label>
            <input type="text" name="kode" class="form-control" placeholder="Contoh: BRG-001" required>
        </div>

        <div class="mb-3">
            <label>Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" required>
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
            <input type="number" name="stok" class="form-control" min="0" value="0" required>
        </div>

        <div class="mb-3">
            <label>Upload Gambar Barang</label>
            <input type="file" name="gambar" class="form-control" accept="image/*">
            <small class="text-muted">*Opsional</small>
        </div>

        <div class="mb-3">
            <label>Tanda Tangan Digital</label>
            <div class="border rounded" style="background:#fff; display:inline-block;">
                <canvas id="kanvasTTD" width="400" height="150" style="display:block; cursor:crosshair;"></canvas>
            </div>
            <br>
            <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="hapusTTD()">Hapus</button>
            <input type="hidden" name="tanda_tangan_data" id="tanda_tangan_data">
            <small class="d-block text-muted mt-1">*Tanda tangan di atas kanvas menggunakan mouse atau sentuh layar</small>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<script>
const canvas = document.getElementById('kanvasTTD');
const ctx = canvas.getContext('2d');
let menggambar = false;

// Mouse
canvas.addEventListener('mousedown', (e) => { menggambar = true; ctx.beginPath(); ctx.moveTo(e.offsetX, e.offsetY); });
canvas.addEventListener('mousemove', (e) => { if (!menggambar) return; ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.strokeStyle = '#000'; ctx.lineTo(e.offsetX, e.offsetY); ctx.stroke(); });
canvas.addEventListener('mouseup', () => { menggambar = false; simpanData(); });
canvas.addEventListener('mouseleave', () => { menggambar = false; });

// Touch (HP)
canvas.addEventListener('touchstart', (e) => { e.preventDefault(); menggambar = true; const t = e.touches[0]; const r = canvas.getBoundingClientRect(); ctx.beginPath(); ctx.moveTo(t.clientX - r.left, t.clientY - r.top); });
canvas.addEventListener('touchmove', (e) => { e.preventDefault(); if (!menggambar) return; const t = e.touches[0]; const r = canvas.getBoundingClientRect(); ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.strokeStyle = '#000'; ctx.lineTo(t.clientX - r.left, t.clientY - r.top); ctx.stroke(); });
canvas.addEventListener('touchend', () => { menggambar = false; simpanData(); });

function simpanData() {
    document.getElementById('tanda_tangan_data').value = canvas.toDataURL('image/png');
}
function hapusTTD() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById('tanda_tangan_data').value = '';
}
</script>
</body>
</html>
