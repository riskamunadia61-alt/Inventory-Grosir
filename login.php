<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // Verifikasi password (teks biasa / hash)
        if ($user && ($password === $user['password_hash'] || password_verify($password, $user['password_hash']))) {
            
            // Menyimpan session lengkap agar sesuai dengan requireLogin()
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['user']     = $user; // Menyimpan data user lengkap
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            // Pindah ke dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Harap isi semua kolom!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory Grosir</title>
    <style>
        /* Mengubah latar belakang seluruh layar menjadi warna cokelat hangat */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #4a2c11; /* Warna latar belakang cokelat tua / kayu */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Kotak kartu login di tengah layar */
        .login-card {
            background-color: #ffffff;
            padding: 35px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 360px;
            box-sizing: border-box;
            border-top: 6px solid #8b5a2b; /* Aksen warna cokelat elegan */
        }

        .login-card h2 {
            margin-top: 0;
            margin-bottom: 25px;
            text-align: center;
            color: #4a2c11;
            font-size: 22px;
            font-weight: 700;
        }

        /* Pesan peringatan error */
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
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
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 11px;
            border: 1px solid #d3c4b2;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        /* Efek fokus saat kolom input diklik */
        .form-group input:focus {
            border-color: #8b5a2b;
            box-shadow: 0 0 6px rgba(139, 90, 43, 0.4);
        }

        /* Tombol login cokelat */
        .btn-login {
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
            transition: background-color 0.2s, transform 0.1s;
        }

        .btn-login:hover {
            background-color: #6e441f;
        }

        .btn-login:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <h2>Login Sistem</h2>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required placeholder="Masukkan username">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required placeholder="Masukkan password">
            </div>

            <button type="submit" class="btn-login">Login</button>
        </form>
    </div>

</body>
</html>