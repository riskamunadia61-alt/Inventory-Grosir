<?php
require_once __DIR__ . '/../config/database.php';

$username = 'staff_gudang';
$password = 'staff123';
$name     = 'Staff Gudang Utama';
$role     = 'staff';

// Hash password menggunakan standar PHP
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Hapus user lama jika ada
    $stmtDel = $pdo->prepare('DELETE FROM users WHERE username = :username');
    $stmtDel->execute(['username' => $username]);

    // Insert user staff baru
    $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, name, role) VALUES (:username, :password_hash, :name, :role)');
    $stmt->execute([
        'username'      => $username,
        'password_hash' => $password_hash,
        'name'          => $name,
        'role'          => $role
    ]);

    echo "<h3 style='color:green;'>Berhasil membuat akun Staff Gudang!</h3>";
    echo "<p>Username: <b>staff_gudang</b><br>Password: <b>staff123</b></p>";
    echo "<a href='login.php'>Klik di sini untuk Login</a>";
} catch (PDOException $e) {
    echo "<h3 style='color:red;'>Gagal: " . $e->getMessage() . "</h3>";
}