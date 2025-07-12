<?php
header('Content-Type: application/json');
require 'config.php';

try {
    $stmt = $pdo->query("SELECT * FROM view_sisa_stok ORDER BY kode_barang");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal mengambil data: ' . $e->getMessage()]);
}
?>