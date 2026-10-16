<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/escape.php';

requireLogin();

// Query Banyak Supplier & Produk terkait (JOIN Many-to-Many)
$sql = "SELECT p.name AS product_name, p.sku, s.name AS supplier_name, s.phone, ps.supply_price
        FROM product_suppliers ps
        JOIN products p ON p.id = ps.product_id
        JOIN suppliers s ON s.id = ps.supplier_id
        ORDER BY p.name ASC";
$suppliersData = $pdo->query($sql)->fetchAll();

require_once __DIR__ . '/../views/header.php';
?>

<h2>Relasi Supplier & Distributor Grosir (Many-to-Many)</h2>

<!-- Tombol Tambah Relasi khusus Admin -->
<?php if ($_SESSION['user']['role'] === 'admin'): ?>
    <div style="margin-bottom: 15px;">
        <a href="product_supplier_form.php" style="padding: 10px 18px; background: #7f5539; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            + Hubungkan Produk ke Supplier Baru
        </a>
    </div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Produk Grosir</th>
            <th>SKU</th>
            <th>Distributor / Supplier</th>
            <th>Kontak Supplier</th>
            <th>Harga Pasokan</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($suppliersData)): ?>
            <tr><td colspan="5">Belum ada pemetaan supplier ke produk.</td></tr>
        <?php else: ?>
            <?php foreach ($suppliersData as $row): ?>
                <tr>
                    <td><?= e($row['product_name']); ?></td>
                    <td><code><?= e($row['sku']); ?></code></td>
                    <td><?= e($row['supplier_name']); ?></td>
                    <td><?= e($row['phone']); ?></td>
                    <td>Rp <?= number_format($row['supply_price'], 0, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../views/footer.php'; ?>