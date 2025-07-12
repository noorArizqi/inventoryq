-- Membuat database untuk sistem inventory
CREATE DATABASE inventory_system;
USE inventory_system;

-- Tabel untuk Barang Masuk
CREATE TABLE barang_masuk (
    no_urut INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL,
    kode_barang VARCHAR(50) NOT NULL,
    tanggal_masuk DATETIME NOT NULL,
    jumlah INT NOT NULL,
    UNIQUE (kode_barang, tanggal_masuk),
    INDEX idx_kode_barang (kode_barang)
);

-- Tabel untuk Barang Keluar
CREATE TABLE barang_keluar (
    no_urut INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL,
    kode_barang VARCHAR(50) NOT NULL,
    tanggal_keluar DATETIME NOT NULL,
    jumlah INT NOT NULL,
    FOREIGN KEY (kode_barang) REFERENCES barang_masuk(kode_barang) ON DELETE RESTRICT,
    INDEX idx_kode_barang (kode_barang)
);

-- Tabel untuk Sisa Stok
CREATE TABLE sisa_stok (
    kode_barang VARCHAR(50) PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL,
    jumlah INT NOT NULL DEFAULT 0,
    FOREIGN KEY (kode_barang) REFERENCES barang_masuk(kode_barang) ON DELETE CASCADE
);

-- Trigger untuk memperbarui sisa stok setelah insert ke barang_masuk
DELIMITER //
CREATE TRIGGER after_barang_masuk_insert
AFTER INSERT ON barang_masuk
FOR EACH ROW
BEGIN
    INSERT INTO sisa_stok (kode_barang, nama_barang, jumlah)
    VALUES (NEW.kode_barang, NEW.nama_barang, NEW.jumlah)
    ON DUPLICATE KEY UPDATE
        nama_barang = NEW.nama_barang,
        jumlah = jumlah + NEW.jumlah;
END //
DELIMITER ;

-- Trigger untuk memperbarui sisa stok setelah insert ke barang_keluar
DELIMITER //
CREATE TRIGGER after_barang_keluar_insert
AFTER INSERT ON barang_keluar
FOR EACH ROW
BEGIN
    DECLARE stok_tersedia INT;
    SELECT jumlah INTO stok_tersedia FROM sisa_stok WHERE kode_barang = NEW.kode_barang;
    
    IF stok_tersedia IS NULL OR stok_tersedia < NEW.jumlah THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Stok tidak cukup untuk kode barang tersebut!';
    ELSE
        UPDATE sisa_stok
        SET jumlah = jumlah - NEW.jumlah
        WHERE kode_barang = NEW.kode_barang;
        
        -- Hapus entri dari sisa_stok jika jumlah menjadi 0
        DELETE FROM sisa_stok WHERE kode_barang = NEW.kode_barang AND jumlah = 0;
    END IF;
END //
DELIMITER ;

-- Contoh data awal untuk pengujian
INSERT INTO barang_masuk (nama_barang, kode_barang, tanggal_masuk, jumlah)
VALUES
    ('Laptop', 'LPT001', '2025-07-12 09:00:00', 10),
    ('Mouse', 'MSE001', '2025-07-12 10:00:00', 50);

INSERT INTO barang_keluar (nama_barang, kode_barang, tanggal_keluar, jumlah)
VALUES
    ('Laptop', 'LPT001', '2025-07-12 11:00:00', 3),
    ('Mouse', 'MSE001', '2025-07-12 12:00:00', 20);

-- View untuk melihat sisa stok dengan nomor urut
CREATE VIEW view_sisa_stok AS
SELECT 
    ROW_NUMBER() OVER (ORDER BY kode_barang) AS no_urut,
    nama_barang,
    kode_barang,
    jumlah
FROM sisa_stok;