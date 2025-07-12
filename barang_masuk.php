<?php
header('Content-Type: application/json');
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = $_POST['nama_barang'];
    $kode_barang = $_POST['kode_barang'];
    $jumlah = (int)$_POST['jumlah'];
    $tanggal_masuk = date('Y-m-d H:i:s');

    try {
        $stmt = $pdo->prepare("INSERT INTO barang_masuk (nama_barang, kode_barang, tanggal_masuk, jumlah) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama_barang, $kode_barang, $tanggal_masuk, $jumlah]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal menambah barang masuk: ' . $e->getMessage()]);
    }
} else {
    try {
        $stmt = $pdo->query("SELECT * FROM barang_masuk ORDER BY tanggal_masuk DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal mengambil data: ' . $e->getMessage()]);
    }
}
?>