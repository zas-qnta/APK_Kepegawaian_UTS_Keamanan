<?php
session_start();
if (!isset($_SESSION['admin_logged']) && !isset($_SESSION['admin_logged_in'])) {
    header('Location: ../auth/login.php');
    exit;
}
require_once '../config/database.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pegawai");
    $result = $stmt->fetch();
    $totalPegawai = $result['total'] ?? 0;
} catch (Exception $e) {
    $totalPegawai = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Kepegawaian</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { 
            background-color: #1b3b36; 
            background-image: radial-gradient(circle at 50% 50%, #244c45 0%, #152f2b 100%);
            color: #e2e8f0; 
            display: flex; 
            min-height: 100vh;
        }
        .sidebar { 
            width: 260px; 
            background-color: rgba(18, 38, 35, 0.95); 
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            height: 100vh; 
            padding: 25px 20px; 
            position: fixed; 
            display: flex;
            flex-direction: column;
        }
        .sidebar h2 { font-size: 18px; margin-bottom: 35px; color: #7ee7df; letter-spacing: 0.5px; }
        .sidebar a { display: block; color: #94a3b8; text-decoration: none; padding: 12px 15px; border-radius: 10px; font-size: 14px; margin-bottom: 6px; transition: all 0.2s; }
        .sidebar a:hover, .sidebar a.active { background-color: rgba(126, 231, 223, 0.15); color: #7ee7df; font-weight: 600; }
        
        .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
        
        .card { 
            background: rgba(27, 59, 54, 0.85); 
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 30px; 
            border-radius: 16px; 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); 
            backdrop-filter: blur(10px);
            margin-bottom: 25px; 
        }
        h1 { font-size: 24px; color: #ffffff; margin-bottom: 8px; font-weight: 600; }
        h3 { font-size: 18px; color: #7ee7df; margin-bottom: 15px; }
        p { color: #94a3b8; font-size: 14px; line-height: 1.6; }
        
        .btn-logout { 
            background-color: rgba(239, 68, 68, 0.15); 
            color: #f87171; 
            border: 1px solid rgba(239, 68, 68, 0.25);
            padding: 10px 16px; 
            border-radius: 10px; 
            text-decoration: none; 
            font-size: 13px; 
            font-weight: 600; 
            display: block; 
            margin-top: auto; 
            text-align: center; 
            transition: all 0.2s;
        }
        .btn-logout:hover { background-color: #ef4444; color: white; }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>✨ Admin Panel</h2>
    <a href="dashboard.php" class="active">📊 Dashboard</a>
    <a href="pegawai.php">👥 Data Pegawai</a>
    <a href="../auth/logout.php" class="btn-logout">Keluar Sistem</a>
</div>
<div class="main-content">
    <div class="card">
        <h1>Dashboard Administrator</h1>
        <p>Selamat datang di Panel Utama Sistem Informasi Kepegawaian.</p>
    </div>
    <div class="card">
        <h3>Statistik Sistem</h3>
        <p>Total Pegawai Terdaftar di Database: <strong style="color: #7ee7df; font-size: 18px;"><?= $totalPegawai; ?> Orang</strong></p>
        <p style="margin-top: 10px;">✅ Status Autentikasi: Berhasil masuk menggunakan verifikasi RSA Private Key dengan aman.</p>
    </div>
</div>
</body>
</html>
