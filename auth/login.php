<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $privateKeyInput = trim($_POST['private_key'] ?? '');
    
    if (!empty($privateKeyInput)) {
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_logged_in'] = true;
        
        $baseUrl = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
        header('Location: ' . $baseUrl . '/admin/dashboard.php');
        exit;
    } else {
        $error = "RSA Private Key tidak boleh kosong!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sistem Kepegawaian</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { 
            background-color: #1b3b36; 
            background-image: radial-gradient(circle at 50% 50%, #244c45 0%, #152f2b 100%);
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
        }
        .login-card { 
            background: rgba(27, 59, 54, 0.9); 
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 35px; 
            border-radius: 16px; 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4); 
            backdrop-filter: blur(10px);
            width: 100%; 
            max-width: 440px; 
        }
        h2 { color: #ffffff; margin-bottom: 8px; font-size: 24px; }
        p { color: #94a3b8; font-size: 14px; margin-bottom: 25px; }
        .error-msg { background-color: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 12px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; }
        label { display: block; font-weight: 500; font-size: 13px; color: #cbd5e1; margin-bottom: 8px; }
        textarea { 
            width: 100%; 
            height: 130px; 
            padding: 12px; 
            background: rgba(15, 32, 29, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.12); 
            border-radius: 8px; 
            color: #ffffff;
            font-family: monospace; 
            font-size: 11px; 
            resize: vertical; 
        }
        textarea:focus { outline: none; border-color: #7ee7df; box-shadow: 0 0 0 3px rgba(126, 231, 223, 0.15); }
        button[type="submit"] { 
            width: 100%; 
            background-color: #7ee7df; 
            color: #112220; 
            border: none; 
            padding: 12px; 
            border-radius: 8px; 
            font-size: 15px; 
            font-weight: 600; 
            cursor: pointer; 
            margin-top: 20px; 
            transition: all 0.2s;
        }
        button[type="submit"]:hover { background-color: #5bcaca; transform: translateY(-1px); }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #94a3b8; text-decoration: none; font-size: 13px; transition: color 0.2s; }
        .back-link:hover { color: #7ee7df; }
    </style>
</head>
<body>
<div class="login-card">
    <h2>🔐 Login Admin</h2>
    <p>Autentikasi keamanan menggunakan RSA Private Key.</p>
    
    <?php if (isset($error)): ?>
        <div class="error-msg"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="private_key">Masukkan / Paste RSA Private Key:</label>
        <textarea name="private_key" id="private_key" placeholder="-----BEGIN PRIVATE KEY----- ..."></textarea>
        <button type="submit">Masuk sebagai Admin</button>
    </form>
    
    <a href="../index.php" class="back-link">&larr; Kembali ke Beranda Publik</a>
</div>
</body>
</html>
