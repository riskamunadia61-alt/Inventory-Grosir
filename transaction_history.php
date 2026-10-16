<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/escape.php';

// Memastikan user sudah login
requireLogin();

// Ambil riwayat transaksi dari database beserta nama produknya
$sql = "SELECT st.*, p.name AS product_name, p.sku 
        FROM stock_transactions st 
        JOIN products p ON st.product_id = p.id 
        ORDER BY st.created_at DESC";

try {
    $stmt = $pdo->query($sql);
    $history = $stmt->fetchAll();
} catch (Exception $e) {
    $history = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Riwayat Transaksi - Inventory Grosir</title>
    <style>
        * { box-sizing: border-box; }
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
            max-width: 1000px;
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(92, 61, 46, 0.12);
            border-top: 5px solid #6f4e37;
        }
        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0e6dd;
        }
        .header-flex h2 { color: #4a2c11; margin: 0; font-size: 22px; }
        .btn-secondary {
            padding: 8px 14px;
            background-color: #d7ccc8;
            color: #4a2c11;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        .btn-secondary:hover { background-color: #c5b7b1; }
        
        /* Badge Jenis Transaksi */
        .badge {
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
        }
        .badge-in { background-color: #d4edda; color: #155724; }
        .badge-out { background-color: #f8d7da; color: #721c24; }

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
        table tr:nth-child(even) { background-color: #faf7f4; }
        table tr:hover { background-color: #f2ebe4; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-flex">
        <h2>Laporan Riwayat Transaksi Stok</h2>
        <div>
            <a href="stock_transaction.php" class="btn-secondary">+ Input Transaksi</a>
            <a href="dashboard.php" class="btn-secondary">Ke Dashboard</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal & Waktu</th>
                <th>Nama Produk</th>
                <th>Jenis Transaksi</th>
                <th>Jumlah (Qty)</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($history) > 0): ?>
                <?php foreach ($history as $index => $row): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                        <td>
                            <?php if ($row['type'] === 'in'): ?>
                                <span class="badge badge-in">Barang Masuk</span>
                            <?php else: ?>
                                <span class="badge badge-out">Barang Keluar</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= number_format($row['quantity']) ?></strong></td>
                        <td><?= htmlspecialchars($row['notes'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Belum ada riwayat transaksi stok.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>