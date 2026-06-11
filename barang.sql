-- =============================================
-- Database: crud_barang
-- Tabel: barang
-- Kolom: id, kode, nama_barang, stok, gambar, tanda_tangan
-- =============================================

CREATE DATABASE IF NOT EXISTS crud_barang;
USE crud_barang;

CREATE TABLE IF NOT EXISTS barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(50) NOT NULL UNIQUE,
    nama_barang VARCHAR(150) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    gambar VARCHAR(255),
    tanda_tangan VARCHAR(255)
);

-- Contoh data awal (opsional)
INSERT INTO barang (kode, nama_barang, stok, gambar, tanda_tangan) VALUES
('BRG-001', 'Laptop Asus VivoBook', 10, NULL, NULL),
('BRG-002', 'Mouse Wireless Logitech', 25, NULL, NULL),
('BRG-003', 'Keyboard Mechanical', 15, NULL, NULL);
