 <?php
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
</body>
</html>
