<?php
session_start();

// Cek apakah user sudah punya tiket login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    // Kalau belum login, tendang balik ke halaman login
    header("Location: login.php");
    exit(); // Hentikan eksekusi kode di bawahnya
}

include "koneksi.php";

$query = mysqli_query($conn, "SELECT * FROM barang ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pencatatan Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>

<body class="bg-light">
<nav class="navbar navbar-dark bg-dark px-4 mb-4">
    <a class="navbar-brand fw-bold" href="index.php">SIMANGU</a>
    <a href="logout.php" class="btn btn-danger" onclick="return confirm('Yakin Mau Keluar?')">Log Out</a>
</nav>

<div class="container py-4">
    <h2 class="mb-4">Data Pencatatan Barang</h2>

    <a href="tambah.php" class="btn btn-primary mb-3 me-2">Tambah Barang</a>
    <a href="keluar.php" class="btn btn-primary mb-3">Barang Keluar</a>

    <table class="table table-bordered table-striped" id="tabelBarang">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
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
                <td><?= htmlspecialchars($data['kategori']) ?></td>                
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
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#tabelBarang').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        },
        dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rtip',
        buttons: [
            {
                extend: 'pdfHtml5',
                text: '<i class="bi bi-file-pdf"></i> Export PDF',
                className: 'btn btn-danger btn-sm',
                title: 'Data Pencatatan Barang',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                },
                customize: function(doc) {
                    doc.styles.tableHeader.fillColor = '#343a40';
                    doc.styles.tableHeader.color = 'white';
                    doc.footer = function(page, pages) {
                        return {
                            text: 'Dicetak: ' + new Date().toLocaleDateString('id-ID'),
                            alignment: 'right',
                            margin: [0, 0, 20, 0],
                            fontSize: 9,
                            color: '#666'
                        };
                    };
                }
            },
            {
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-excel"></i> Export Excel',
                className: 'btn btn-success btn-sm',
                title: 'Data Pencatatan Barang',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            },
            {
                extend: 'print',
                text: '<i class="bi bi-printer"></i> Print',
                className: 'btn btn-secondary btn-sm',
                title: 'Data Pencatatan Barang',
                exportOptions: {
                    columns: [0, 1, 2, 3]
                }
            }
        ]
    });

    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');

    console.log('Status dari URL:', status); // Untuk debug

    if (status === 'add' || status === 'delete') {
        // Cek apakah elemen modal ada
        const modalElement = document.getElementById('modalSukses');
        if (modalElement) {
            try {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                console.log('Modal ditampilkan');

                // Putar audio
                const audio = document.getElementById('audioSukses');
                if (audio) {
                    audio.play().catch(e => console.log('Autoplay diblokir:', e));
                } else {
                    console.warn('Audio element tidak ditemukan');
                }

                // Hapus parameter dari URL agar tidak muncul lagi saat refresh
                window.history.replaceState({}, document.title, window.location.pathname);
            } catch (error) {
                console.error('Error saat menampilkan modal:', error);
            }
        } else {
            console.error('Modal dengan id "modalSukses" tidak ditemukan di DOM');
        }
    }
});
</script>

<!-- Modal Pop-up Sukses -->
<div class="modal fade" id="modalSukses" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <!-- GIF -->
                <img src="video/sukses.gif" alt="Sukses" class="img-fluid mb-3" style="max-width: 200px;">
                <!-- Audio (autoplay saat modal muncul) -->
                <audio id="audioSukses" src="audio/sukses.mp3" preload="auto"></audio>
                <h4 class="mb-3">Operasi Berhasil!</h4>
                <button class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
