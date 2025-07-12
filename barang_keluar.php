<?php
header('Content-Type: application/json');
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = $_POST['nama_barang'];
    $kode_barang = $_POST['kode_barang'];
    $jumlah = (int)$_POST['jumlah'];
    $tanggal_keluar = date('Y-m-d H:i:s');

    try {
        $stmt = $pdo->prepare("INSERT INTO barang_keluar (nama_barang, kode_barang, tanggal_keluar, jumlah) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama_barang, $kode_barang, $tanggal_keluar, $jumlah]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal menambah barang keluar: ' . $e->getMessage()]);
    }
} else {
    try {
        $stmt = $pdo->query("SELECT * FROM barang_keluar ORDER BY tanggal_keluar DESC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Gagal mengambil data: ' . $e->getMessage()]);
    }
}
?>