<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/escape.php';

// Memastikan user sudah login
requireLogin();

// 1. Hitung Total SKU / Barang
$stmtTotal = $pdo->query("SELECT COUNT(*) AS total_sku FROM products");
$totalSku = $stmtTotal->fetch()['total_sku'] ?? 0;

// 2. Hitung Nilai Total Aset Gudang (stok * harga)
$stmtAll = $pdo->query("SELECT * FROM products");
$allProducts = $stmtAll->fetchAll();

$totalAset = 0;
foreach ($allProducts as $p) {
    $stok = $p['stock'] ?? 0;
    $harga = $p['price_per_unit'] ?? $p['unit_price'] ?? $p['harga'] ?? $p['price'] ?? 0;
    $totalAset += ($stok * $harga);
}

// 3. Hitung Peringatan Stok Kritis (misal stok <= 10)
$stmtKritis = $pdo->query("SELECT COUNT(*) AS total_kritis FROM products WHERE stock <= 10");
$totalKritis = $stmtKritis->fetch()['total_kritis'] ?? 0;

// 4. Ambil 5 Produk Terbaru (Dengan JOIN ke tabel categories jika ada)
try {
    $stmtRecent = $pdo->query("
        SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id DESC LIMIT 5
    ");
    $recentProducts = $stmtRecent->fetchAll();
} catch (Exception $e) {
    // Jika tabel categories tidak terhubung dengan category_id, ambil dari tabel products
    $stmtRecent = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 5");
    $recentProducts = $stmtRecent->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Inventory Grosir</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f0eb;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 100%;
            max-width: 1100px;
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(92, 61, 46, 0.12);
            border-top: 5px solid #6f4e37;
        }

        /* NAVBAR & HEADER */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0e6dd;
        }

        .navbar h1 {
            color: #4a2c11;
            margin: 0;
            font-size: 22px;
        }

        .user-info {
            font-size: 14px;
            color: #5c3818;
        }

        /* MENU TOMBOL NAVIGASI CEPAT (ALL FEATURES) */
        .nav-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 25px;
            background-color: #faf7f4;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ebdcd0;
        }

        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        /* Variasi Warna Cokelat & Krem */
        .btn-brown { 
            background-color: #8b5a2b; 
            color: white; 
        }
        .btn-brown:hover { 
            background-color: #6e441f; 
        }

        .btn-cream { 
            background-color: #e6d7c3; 
            color: #4a2c11; 
            border: 1px solid #c8b49c;
        }
        .btn-cream:hover { 
            background-color: #d8c3aa; 
        }

        .btn-light { 
            background-color: #f3e9dc; 
            color: #5c3818; 
            border: 1px solid #d7ccc8;
        }
        .btn-light:hover { 
            background-color: #e6d5c3; 
        }

        .btn-danger { 
            background-color: #f7e2e2; 
            color: #7a2e2e; 
            border: 1px solid #e3b8b8; 
            margin-left: auto;
        }
        .btn-danger:hover { 
            background-color: #ebd0d0; 
        }

        /* KARTU RINGKASAN RINGKAS */
        .card-grid {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            flex: 1;
            background-color: #fdfaf7;
            border: 1px solid #e6d7c3;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(92, 61, 46, 0.06);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .card h3 {
            margin-top: 0;
            margin-bottom: 12px;
            color: #5c3818;
            font-size: 15px;
            font-weight: 600;
            border-bottom: 1px solid #ebdcd0;
            padding-bottom: 8px;
        }

        .card .value {
            font-size: 24px;
            font-weight: bold;
            color: #4a2c11;
            margin: 0;
        }

        /* TABEL DASHBOARD */
        .section-title {
            color: #4a2c11;
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th, table td {
            border: 1px solid #e2d7cd;
            padding: 12px;
            text-align: left;
        }

        table th {
            background-color: #4a2c11;
            color: white;
            font-weight: 600;
        }

        table tr:nth-child(even) {
            background-color: #faf7f4;
        }

        table tr:hover {
            background-color: #f2ebe4;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Navbar / Header -->
    <div class="navbar">
        <h1>Dashboard Inventaris Grosir</h1>
        <div class="user-info">
            Selamat datang, <strong><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></strong> (<?= htmlspecialchars($_SESSION['role'] ?? 'Staff') ?>)
        </div>
    </div>

    <!-- Navigasi Menu Lengkap -->
    <div class="nav-buttons">
        <a href="products.php" class="btn btn-brown">📦 Daftar & Kelola Produk</a>
        <a href="product_form.php" class="btn btn-cream">➕ Tambah Produk Baru</a>
        <a href="stock_transaction.php" class="btn btn-light">📝 Input Transaksi Stok</a>
        <a href="transaction_history.php" class="btn btn-cream">📊 Laporan Riwayat Transaksi</a>
        <a href="logout.php" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin keluar?')">🚪 Logout</a>
    </div>

    <!-- Kotak Ringkasan Statistik -->
    <div class="card-grid">
        <div class="card">
            <h3>Total SKU Grosir</h3>
            <div class="value"><?= number_format($totalSku) ?> Barang</div>
        </div>

        <div class="card">
            <h3>Nilai Total Aset Gudang</h3>
            <div class="value">Rp <?= number_format($totalAset, 0, ',', '.') ?></div>
        </div>

        <div class="card">
            <h3>Peringatan Stok Kritis</h3>
            <div class="value"><?= number_format($totalKritis) ?> SKU</div>
        </div>
    </div>

    <!-- Ringkasan Produk Terbaru -->
    <h3 class="section-title">Ringkasan Produk Terbaru Ditambahkan</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($recentProducts) > 0): ?>
                <?php foreach ($recentProducts as $index => $item): 
                    $harga = $item['price_per_unit'] ?? $item['unit_price'] ?? $item['harga'] ?? $item['price'] ?? 0;
                    
                    // Fleksibilitas pengecekan nama kolom kategori
                    $kategori = '-';
                    if (!empty($item['category_name'])) {
                        $kategori = $item['category_name'];
                    } elseif (!empty($item['category'])) {
                        $kategori = $item['category'];
                    } elseif (!empty($item['kategori'])) {
                        $kategori = $item['kategori'];
                    }
                ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= e($item['name'] ?? '-') ?></td>
                        <td><?= e($kategori) ?></td>
                        <td><?= e($item['stock'] ?? 0) ?></td>
                        <td>Rp <?= number_format($harga, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Belum ada data produk.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>