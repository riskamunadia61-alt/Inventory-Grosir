<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/escape.php';

requireAdmin();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id   = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $supplier_id  = filter_input(INPUT_POST, 'supplier_id', FILTER_VALIDATE_INT);
    $supply_price = filter_input(INPUT_POST, 'supply_price', FILTER_VALIDATE_FLOAT);

    if (!$product_id) $errors[] = 'Produk wajib dipilih.';
    if (!$supplier_id) $errors[] = 'Supplier wajib dipilih.';
    if ($supply_price === false || $supply_price < 0) $errors[] = 'Harga pasokan tidak valid.';

    if (empty($errors)) {
        try {
            // Hapus data lama jika ada
            $sqlDelete = 'DELETE FROM product_suppliers WHERE product_id = :p_id AND supplier_id = :s_id';
            $stmtDel = $pdo->prepare($sqlDelete);
            $stmtDel->execute([
                'p_id' => $product_id,
                's_id' => $supplier_id
            ]);

            // Insert data baru
            $sqlInsert = 'INSERT INTO product_suppliers (product_id, supplier_id, supply_price) 
                          VALUES (:p_id, :s_id, :price)';
            $stmtIns = $pdo->prepare($sqlInsert);
            $stmtIns->execute([
                'p_id'  => $product_id,
                's_id'  => $supplier_id,
                'price' => $supply_price
            ]);
            
            header('Location: suppliers.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Gagal menghubungkan produk ke supplier: ' . $e->getMessage();
        }
    }
}

// Ambil daftar produk dan supplier untuk dropdown
$products  = $pdo->query('SELECT id, sku, name FROM products ORDER BY name ASC')->fetchAll();
$suppliers = $pdo->query('SELECT id, name FROM suppliers ORDER BY name ASC')->fetchAll();

require_once __DIR__ . '/../views/header.php';
?>

<h2>Hubungkan Produk ke Supplier / Distributor</h2>

<?php if (!empty($errors)): ?>
    <div class="alert">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= e($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" style="background:#fffdfa; padding:20px; border:1px solid #e7d8c9; max-width:600px; border-radius:8px;">
    <p>
        <label><strong>Pilih Produk Grosir:</strong></label><br>
        <select name="product_id" required style="width:100%; padding:8px; margin-top:5px;">
            <option value="">-- Pilih Produk --</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= $p['id']; ?>">
                    [<?= e($p['sku']); ?>] <?= e($p['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label><strong>Pilih Distributor / Supplier:</strong></label><br>
        <select name="supplier_id" required style="width:100%; padding:8px; margin-top:5px;">
            <option value="">-- Pilih Supplier --</option>
            <?php foreach ($suppliers as $s): ?>
                <option value="<?= $s['id']; ?>">
                    <?= e($s['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label><strong>Harga Pasokan dari Supplier (Rp):</strong></label><br>
        <input type="number" step="0.01" name="supply_price" placeholder="Contoh: 150000" required style="width:100%; padding:8px; margin-top:5px;">
    </p>

    <button type="submit" style="padding:10px 20px; background:#7f5539; color:#fff; border:none; border-radius:5px; cursor:pointer; font-weight:bold;">
        Simpan Relasi Supplier
    </button>
    <a href="suppliers.php" style="margin-left:10px; color:#3e2723; text-decoration:none;">Batal</a>
</form>

<?php require_once __DIR__ . '/../views/footer.php'; ?>