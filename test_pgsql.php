<?php
try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=dw_outdoor', 'postgres', '0987654321');
    echo "✅ Koneksi BERHASIL!";
} catch (PDOException $e) {
    echo "❌ Gagal: " . $e->getMessage();
}