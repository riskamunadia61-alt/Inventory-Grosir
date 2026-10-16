<?php require_once __DIR__ . '/../helpers/auth.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Inventory Grosir</title>
    <style>
        /* Palette Warna: Coklat & Cream */
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 20px; 
            background: #fdfbf7; /* Cream Muda Warm */
            color: #3e2723; /* Coklat Gelap Teks */
        }
        
        nav { 
            background: #4a3525; /* Coklat Tuai/Espresso */
            padding: 14px 22px; 
            color: #f5ebe0; 
            margin-bottom: 20px; 
            border-radius: 8px; 
            box-shadow: 0 4px 6px rgba(74, 53, 37, 0.15);
        }
        
        nav a { 
            color: #f5ebe0; 
            margin-right: 18px; 
            text-decoration: none; 
            font-weight: 600; 
            transition: color 0.2s;
        }
        
        nav a:hover {
            color: #d7ccc8; /* Cream/Light Beige saat Hover */
        }
        
        .container { 
            background: #fffdfa; /* Cream Sangat Soft */
            padding: 25px; 
            border-radius: 10px; 
            border: 1px solid #e7d8c9;
            box-shadow: 0 4px 12px rgba(62, 39, 35, 0.05); 
        }

        h2, h3 {
            color: #3e2723;
            border-bottom: 2px solid #d7ccc8;
            padding-bottom: 8px;
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
            background: #ffffff;
            border-radius: 6px;
            overflow: hidden;
        }
        
        table, th, td { 
            border: 1px solid #e7d8c9; 
            padding: 12px; 
            text-align: left; 
        }
        
        th { 
            background: #ebd9c8; /* Cream Coklat Header Tabel */
            color: #3e2723; 
            font-weight: 700;
        }

        tr:nth-child(even) {
            background-color: #faf6f0; /* Pattern Cream Belang Tabel */
        }
        
        .alert { 
            padding: 12px; 
            background: #f8d7da; 
            color: #721c24; 
            margin-bottom: 15px; 
            border-radius: 6px; 
            border: 1px solid #f5c6cb;
        }
        
        .success { 
            padding: 12px; 
            background: #e2f0d9; 
            color: #2d572c; 
            margin-bottom: 15px; 
            border-radius: 6px; 
            border: 1px solid #c3e6cb;
        }
        
        .warning-row { 
            background: #fff3cd !important; 
            font-weight: bold; 
        }
        
        .badge { 
            padding: 4px 10px; 
            border-radius: 12px; 
            font-size: 12px; 
            color: #fff; 
            font-weight: bold;
        }
        
        .badge-in { background: #5d7052; } /* Hijau Olive Soft */
        .badge-out { background: #a94442; }

        /* Style untuk Tombol & Input */
        button, .btn {
            background: #7f5539; /* Coklat Medium */
            color: #fff;
            border: none;
            padding: 9px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background 0.2s;
        }

        button:hover, .btn:hover {
            background: #9c6644;
        }

        input[type="text"], input[type="number"], select {
            border: 1px solid #c6ac8f;
            border-radius: 5px;
            padding: 8px 10px;
            background-color: #fffdfa;
            color: #3e2723;
        }

        input[type="text"]:focus, input[type="number"]:focus, select:focus {
            outline: none;
            border-color: #7f5539;
            box-shadow: 0 0 5px rgba(127, 85, 57, 0.3);
        }
    </style>
</head>
<body>
    <nav>
        <strong>[Grosir App] Inventory Gudang</strong> | 
        <?php if (isLoggedIn()): ?>
            <a href="/inventory-grosir/public/dashboard.php">Dashboard</a>
            <a href="/inventory-grosir/public/products.php">Katalog Produk</a>
            <a href="/inventory-grosir/public/suppliers.php">Distributor/Supplier</a>
            <a href="/inventory-grosir/public/stock_transaction.php">Barang Masuk/Keluar</a>
            <span style="float:right;">
                User: <strong><?= e($_SESSION['user']['username']); ?> (<?= e($_SESSION['user']['role']); ?>)</strong> | 
                <a href="/inventory-grosir/public/logout.php" style="color:#e6ccb2;">Logout</a>
            </span>
        <?php endif; ?>
    </nav>
    <div class="container">