<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';

requireAdmin(); // Khusus Admin!

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        // Hapus relasi supplier terlebih dahulu agar tidak memicu error FK
        $stmtRel = $pdo->prepare('DELETE FROM product_suppliers WHERE product_id = :id');
        $stmtRel->execute(['id' => $id]);

        // Hapus data produk
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
    } catch (PDOException $e) {
        // Abaikan error jika terhalang foreign key transaksi
    }
}

header('Location: products.php');
exit;