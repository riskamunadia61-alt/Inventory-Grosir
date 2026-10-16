<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/escape.php';

// Memastikan user sudah login
requireLogin();

$id = $_GET['id'] ?? null;
$isEdit = !empty($id);

$errors = [];
$category = '';
$sku = '';
$name = '';
$unit = 'Karton';
$price = '';
$stock = '';

// Ambil data jika sedang mode Edit
if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        $category = $product['category'] ?? '';
        $sku = $product['sku'] ?? '';
        $name = $product['name'] ?? '';
        $unit = $product['unit'] ?? 'Karton';
        $price = $product['price_per_unit'] ?? $product['unit_price'] ?? $product['harga'] ?? $product['price'] ?? '';
        $stock = $product['stock'] ?? '';
    }
}

// Proses simpan data (Tambah / Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = trim($_POST['category'] ?? '');
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $unit = trim($_POST['unit'] ?? 'Karton');
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? 0;

    if (empty($name)) {
        $errors[] = "Nama produk wajib diisi.";
    }

    if (empty($errors)) {
        try {
            if ($isEdit) {
                // Query Update Data
                $sql = "UPDATE products SET category = ?, sku = ?, name = ?, unit = ?, price_per_unit = ?, stock = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$category, $sku, $name, $unit, $price, $stock, $id]);
            } else {
                // Query Insert Data Baru
                $sql = "INSERT INTO products (category, sku, name, unit, price_per_unit, stock) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$category, $sku, $name, $unit, $price, $stock]);
            }

            header("Location: products.php");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = "Gagal menyimpan data: Kode SKU '$sku' sudah digunakan. Silakan pakai SKU lain.";
            } else {
                $errors[] = "Terjadi kesalahan database: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit Produk' : 'Tambah Produk Grosir Baru' ?> - Inventory Grosir</title>
    <style>
        * {
            box-sizing: border-box;
        }

        /* Mengatur posisi halaman tepat di tengah layar */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f0eb;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center; /* Tengah horizontal */
            align-items: center;     /* Tengah vertikal */
        }

        .container {
            width: 100%;
            max-width: 650px;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(92, 61, 46, 0.12);
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

        .alert-danger {
            background-color: #f7e2e2;
            color: #7a2e2e;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #e3b8b8;
            font-size: 14px;
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
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d3c4b2;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            background-color: #faf8f5;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus, 
        .form-group select:focus {
            border-color: #8b5a2b;
            box-shadow: 0 0 6px rgba(139, 90, 43, 0.3);
        }

        /* Tombol Cokelat Elegan */
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #8b5a2b;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: #6e441f;
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
        <h2><?= $isEdit ? 'Edit Produk Grosir' : 'Tambah Produk Grosir Baru' ?></h2>
        <a href="products.php" class="btn-secondary">Kembali</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label for="category">Kategori:</label>
            <select name="category" id="category">
                <option value="">-- Pilih Kategori --</option>
                <option value="Minuman" <?= $category === 'Minuman' ? 'selected' : '' ?>>Minuman</option>
                <option value="Sembako" <?= $category === 'Sembako' ? 'selected' : '' ?>>Sembako</option>
                <option value="Bumbu Dapur" <?= $category === 'Bumbu Dapur' ? 'selected' : '' ?>>Bumbu Dapur</option>
                <option value="Makanan Ringan" <?= $category === 'Makanan Ringan' ? 'selected' : '' ?>>Makanan Ringan</option>
            </select>
        </div>

        <div class="form-group">
            <label for="sku">Kode SKU (Harus Unik):</label>
            <input type="text" name="sku" id="sku" value="<?= htmlspecialchars($sku) ?>" placeholder="Contoh: GROS-AMDK-01">
        </div>

        <div class="form-group">
            <label for="name">Nama Produk Grosir:</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>" required placeholder="Masukkan nama produk">
        </div>

        <div class="form-group">
            <label for="unit">Satuan Kemasan (Contoh: Karton / Dus / Bal):</label>
            <input type="text" name="unit" id="unit" value="<?= htmlspecialchars($unit) ?>" placeholder="Karton">
        </div>

        <div class="form-group">
            <label for="price">Harga per Kemasan (Rp):</label>
            <input type="number" name="price" id="price" value="<?= htmlspecialchars($price) ?>" placeholder="Contoh: 50000">
        </div>

        <div class="form-group">
            <label for="stock">Stok Awal (Jumlah Kemasan):</label>
            <input type="number" name="stock" id="stock" value="<?= htmlspecialchars($stock) ?>" placeholder="Contoh: 100">
        </div>

        <button type="submit" class="btn-submit"><?= $isEdit ? 'Update Produk' : 'Simpan Produk Baru' ?></button>
    </form>
</div>

</body>
</html>