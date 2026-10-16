<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /inventory-grosir/public/login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if ($_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        echo "<div style='padding:20px; color:red; font-family:sans-serif;'>
                <h2>403 Forbidden: Akses Ditolak</h2>
                <p>Fitur Administrasi Master Data ini hanya dapat diakses oleh Admin.</p>
                <a href='/inventory-grosir/public/dashboard.php'>Kembali ke Dashboard</a>
              </div>";
        exit;
    }
}