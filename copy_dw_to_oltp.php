<?php
// copy_dw_to_oltp.php
// Script untuk menyalin data cabang dan produk dari Data Warehouse ke OLTP

// Konfigurasi koneksi (sesuaikan password jika ada)
$dw_host = 'localhost';
$dw_port = '5432';
$dw_db = 'dw_outdoor';
$dw_user = 'postgres';
$dw_pass = ''; // Ganti dengan password Anda jika ada (misal '0987654321')

$oltp_host = 'localhost';
$oltp_port = '5432';
$oltp_db = 'oltp_rental';
$oltp_user = 'postgres';
$oltp_pass = ''; // Ganti dengan password yang sama

try {
    // Koneksi ke Data Warehouse
    $dw_pdo = new PDO("pgsql:host=$dw_host;port=$dw_port;dbname=$dw_db", $dw_user, $dw_pass);
    $dw_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Koneksi ke OLTP
    $oltp_pdo = new PDO("pgsql:host=$oltp_host;port=$oltp_port;dbname=$oltp_db", $oltp_user, $oltp_pass);
    $oltp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Koneksi berhasil.\n";

    // 1. Copy cabang
    $stmt = $dw_pdo->query("SELECT kode_cabang, nama_kota FROM dim_cabang");
    $cabangs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $insertCabang = $oltp_pdo->prepare("INSERT INTO cabang (kode_cabang, nama_kota) VALUES (?, ?)");
    $countCabang = 0;
    foreach ($cabangs as $cabang) {
        try {
            $insertCabang->execute([$cabang['kode_cabang'], $cabang['nama_kota']]);
            $countCabang++;
        } catch (PDOException $e) {
            // skip jika duplicate (misal sudah ada)
            if ($e->getCode() != '23505') throw $e; // 23505 = unique violation
        }
    }
    echo "✅ $countCabang cabang berhasil disalin (skip duplicate).\n";

    // 2. Copy produk (hanya yang aktif)
    $stmt = $dw_pdo->query("SELECT kode_produk, nama_produk, harga_sewa_dasar FROM dim_produk WHERE is_current = true");
    $produks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $insertProduk = $oltp_pdo->prepare("INSERT INTO produk (kode_produk, nama_produk, harga_sewa) VALUES (?, ?, ?)");
    $countProduk = 0;
    foreach ($produks as $produk) {
        try {
            $insertProduk->execute([$produk['kode_produk'], $produk['nama_produk'], $produk['harga_sewa_dasar']]);
            $countProduk++;
        } catch (PDOException $e) {
            if ($e->getCode() != '23505') throw $e;
        }
    }
    echo "✅ $countProduk produk berhasil disalin (skip duplicate).\n";

    // 3. (Opsional) Buat user superadmin dan admin cabang jika belum ada
    // Cek apakah superadmin sudah ada
    $checkSuper = $oltp_pdo->query("SELECT COUNT(*) FROM users WHERE username = 'superadmin'")->fetchColumn();
    if (!$checkSuper) {
        $oltp_pdo->prepare("INSERT INTO users (username, password, role, cabang_id) VALUES ('superadmin', md5('123456'), 'superadmin', NULL)")->execute();
        echo "✅ Superadmin ditambahkan.\n";
    } else {
        echo "⚠ Superadmin sudah ada, skip.\n";
    }

    // Cek apakah admin cabang sudah ada (minimal 1)
    $checkAdmin = $oltp_pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin_cabang'")->fetchColumn();
    if ($checkAdmin == 0) {
        // Ambil semua cabang untuk membuat admin cabang
        $cabangsForAdmin = $oltp_pdo->query("SELECT id_cabang, nama_kota FROM cabang")->fetchAll(PDO::FETCH_ASSOC);
        $insertAdmin = $oltp_pdo->prepare("INSERT INTO users (username, password, role, cabang_id) VALUES (?, md5('123456'), 'admin_cabang', ?)");
        $countAdmin = 0;
        foreach ($cabangsForAdmin as $cabang) {
            $username = 'adm_' . strtolower(str_replace(' ', '', $cabang['nama_kota']));
            try {
                $insertAdmin->execute([$username, $cabang['id_cabang']]);
                $countAdmin++;
            } catch (PDOException $e) {
                if ($e->getCode() != '23505') throw $e;
            }
        }
        echo "✅ $countAdmin admin cabang ditambahkan.\n";
    } else {
        echo "⚠ Admin cabang sudah ada, skip.\n";
    }

    echo "\n🎉 Semua data berhasil disalin.\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>