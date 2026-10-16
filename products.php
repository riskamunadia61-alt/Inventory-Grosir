<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/escape.php';

// Memastikan user sudah login
requireLogin();

// Mengambil kata kunci pencarian jika ada
$search = trim($_GET['search'] ?? '');

try {
    // Mencoba query dengan JOIN ke tabel categories (jika ada relasi foreign key)
    if ($search !== '') {
        $sql = "SELECT p.*, c.name AS category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.name LIKE ? OR c.name LIKE ? 
                ORDER BY p.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["%$search%", "%$search%"]);
    } else {
        $sql = "SELECT p.*, c.name AS category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }
    $products = $stmt->fetchAll();
} catch (Exception $e) {
    // Jika tabel categories tidak ada, gunakan query biasa dari tabel products
    if ($search !== '') {
        $sql = "SELECT * FROM products WHERE name LIKE ? ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["%$search%"]);
    } else {
        $sql = "SELECT * FROM products ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }
    $products = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk - Inventory Grosir</title>
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
            padding: 25px;
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
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .search-box input[type="text"] {
            padding: 9px 12px;
            border: 1px solid #d3c4b2;
            border-radius: 6px;
            width: 260px;
            outline: none;
            background-color: #faf8f5;
        }
        .search-box input[type="text"]:focus {
            border-color: #8b5a2b;
            box-shadow: 0 0 5px rgba(139, 90, 43, 0.3);
        }
        .btn {
            padding: 8px 14px;
            border: 1px solid transparent;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
            transition: all 0.2s;
        }
        .btn-primary { background-color: #e6d7c3; color: #4a2c11; border-color: #c8b49c; }
        .btn-primary:hover { background-color: #d8c3aa; }
        .btn-warning { background-color: #f3e9dc; color: #5c3818; border-color: #d7ccc8; }
        .btn-warning:hover { background-color: #e6d5c3; }
        .btn-danger { background-color: #f7e2e2; color: #7a2e2e; border-color: #e3b8b8; }
        .btn-danger:hover { background-color: #ebd0d0; }
        .btn-success { background-color: #8b5a2b; color: white; }
        .btn-success:hover { background-color: #6e441f; }
        .btn-secondary { background-color: #d7ccc8; color: #4a2c11; }
        .btn-secondary:hover { background-color: #c5b7b1; }

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
        table th { background-color: #4a2c11; color: white; font-weight: 600; }
        table tr:nth-child(even) { background-color: #faf7f4; }
        table tr:hover { background-color: #f2ebe4; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-flex">
        <h2>Daftar Produk</h2>
        <div>
            <a href="dashboard.php" class="btn btn-secondary">Ke Dashboard</a>
            <a href="product_form.php" class="btn btn-success">+ Tambah Produk</a>
        </div>
    </div>

    <!-- Form Pencarian -->
    <form action="products.php" method="GET" class="search-box">
        <input type="text" name="search" placeholder="Cari nama produk..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary">Cari</button>
        <?php if ($search !== ''): ?>
            <a href="products.php" class="btn btn-secondary">Reset</a>
        <?php endif; ?>
    </form>

    <!-- Tabel Data Produk -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $index => $item): 
                    $harga = $item['price_per_unit'] ?? $item['unit_price'] ?? $item['harga'] ?? $item['price'] ?? 0;
                    // Mengecek fleksibilitas nama kolom kategori
                    $kategori = $item['category_name'] ?? $item['category'] ?? $item['kategori'] ?? '-';
                ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= e($item['name'] ?? '-') ?></td>
                        <td><?= e($kategori) ?></td>
                        <td><?= e($item['stock'] ?? 0) ?></td>
                        <td>Rp <?= number_format($harga, 0, ',', '.') ?></td>
                        <td>
                            <a href="product_form.php?id=<?= $item['id'] ?>" class="btn btn-warning">Edit</a>
                            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                                <a href="product_delete.php?id=<?= $item['id'] ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Data produk tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>