<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/escape.php';

// Memastikan user sudah login
requireLogin();

$success = '';
$error = '';

// Mengambil daftar produk untuk pilihan dropdown
$stmtProducts = $pdo->query("SELECT * FROM products ORDER BY name ASC");
$products = $stmtProducts->fetchAll();

// Proses Simpan Transaksi Grosir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    $type       = $_POST['type'] ?? ''; // 'in' untuk masuk, 'out' untuk keluar
    $quantity   = intval($_POST['quantity'] ?? 0);
    $notes      = trim($_POST['notes'] ?? '');

    if (!empty($product_id) && !empty($type) && $quantity > 0) {
        try {
            $pdo->beginTransaction();

            // 1. Simpan ke tabel riwayat/histori transaksi jika ada
            // (Menggunakan prepared statement untuk keamanan)
            $stmtInsert = $pdo->prepare("INSERT INTO stock_transactions (product_id, type, quantity, notes, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmtInsert->execute([$product_id, $type, $quantity, $notes]);

            // 2. Update stok di tabel products
            if ($type === 'in') {
                $stmtUpdate = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
            } else {
                $stmtUpdate = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            }
            $stmtUpdate->execute([$quantity, $product_id]);

            $pdo->commit();
            $success = "Transaksi grosir berhasil disimpan!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Gagal menyimpan transaksi: " . $e->getMessage();
        }
    } else {
        $error = "Harap pilih produk, jenis transaksi, dan jumlah barang dengan benar!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Grosir - Inventory Grosir</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f0eb;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(92, 61, 46, 0.12);
            border-top: 5px solid #6f4e37;
        }

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0e6dd;
        }

        .header-flex h2 {
            color: #4a2c11;
            margin: 0;
            font-size: 22px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #5c3818;
            font-size: 14px;
            font-weight: bold;
        }

        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d3c4b2;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
            outline: none;
            background-color: #faf8f5;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus, 
        .form-group select:focus, 
        .form-group textarea:focus {
            border-color: #8b5a2b;
            box-shadow: 0 0 6px rgba(139, 90, 43, 0.3);
        }

        /* TOMBOL UTAMA WARNA COKELAT ELEGAN */
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #8b5a2b; /* Warna cokelat kayu utama */
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.2s, transform 0.1s;
        }

        .btn-submit:hover {
            background-color: #6e441f; /* Cokelat lebih pekat saat kursor diarahkan */
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        .btn-secondary {
            padding: 8px 14px;
            background-color: #d7ccc8;
            color: #4a2c11;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-secondary:hover {
            background-color: #c5b7b1;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-flex">
        <h2>Input Transaksi Grosir</h2>
        <a href="dashboard.php" class="btn-secondary">Kembali</a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="product_id">Pilih Produk Grosir</label>
            <select name="product_id" id="product_id" required>
                <option value="">-- Pilih Produk --</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= htmlspecialchars($p['name']) ?> (Stok: <?= $p['stock'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="type">Jenis Transaksi</label>
            <select name="type" id="type" required>
                <option value="in">Barang Masuk (Tambah Stok)</option>
                <option value="out">Barang Keluar / Penjualan (Kurangi Stok)</option>
            </select>
        </div>

        <div class="form-group">
            <label for="quantity">Jumlah (Qty)</label>
            <input type="number" name="quantity" id="quantity" min="1" required placeholder="Masukkan jumlah barang">
        </div>

        <div class="form-group">
            <label for="notes">Catatan Transaksi (Opsional)</label>
            <textarea name="notes" id="notes" rows="3" placeholder="Contoh: Pembelian dari Supplier A / Penjualan Grosir Toko B"></textarea>
        </div>

        <!-- Tombol Simpan Cokelat -->
        <button type="submit" class="btn-submit">Simpan Transaksi Grosir</button>
    </form>
</div>

</body>
</html>