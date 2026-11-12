<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Kepegawaian</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { 
            background-color: #1b3b36; 
            background-image: radial-gradient(circle at 50% 50%, #244c45 0%, #152f2b 100%);
            color: #e2e8f0; 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh; 
        }
        header { 
            background-color: rgba(18, 38, 35, 0.95); 
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 20px 40px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        header h1 { font-size: 20px; color: #7ee7df; letter-spacing: 0.5px; }
        .btn-login { 
            background-color: #7ee7df; 
            color: #112220; 
            padding: 9px 18px; 
            border-radius: 8px; 
            text-decoration: none; 
            font-weight: 600; 
            font-size: 14px; 
            transition: all 0.2s; 
        }
        .btn-login:hover { background-color: #5bcaca; transform: translateY(-1px); }
        .container { 
            max-width: 900px; 
            margin: 50px auto; 
            padding: 40px; 
            background: rgba(27, 59, 54, 0.85); 
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px; 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); 
            backdrop-filter: blur(10px);
            flex: 1; 
            width: 90%; 
        }
        h2 { margin-bottom: 15px; color: #ffffff; font-size: 26px; }
        p { color: #94a3b8; margin-bottom: 20px; line-height: 1.6; font-size: 15px; }
        .info-box { 
            background: rgba(15, 32, 29, 0.6); 
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 25px; 
            border-radius: 12px; 
            margin-top: 25px; 
        }
        .info-box h3 { color: #7ee7df; margin-bottom: 10px; font-size: 18px; }
        footer { 
            background-color: rgba(18, 38, 35, 0.95); 
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            color: #64748b; 
            text-align: center; 
            padding: 25px 20px; 
            margin-top: auto; 
            font-size: 13px; 
        }
    </style>
</head>
<body>
<header>
    <h1>✨ Portal Kepegawaian</h1>
    <a href="auth/login.php" class="btn-login">Login Admin</a>
</header>
<div class="container">
    <h2>Selamat Datang di Sistem Informasi Kepegawaian</h2>
    <p>Halaman ini merupakan portal utama bagi pengunjung dan pegawai untuk melihat informasi instansi secara terbuka dan transparan.</p>
    <div class="info-box">
        <h3>Informasi Layanan</h3>
        <p>Sistem ini dilindungi dengan standar keamanan kriptografi mutakhir untuk menjaga integritas data kepegawaian internal perusahaan/instansi secara aman dan terstruktur.</p>
    </div>
</div>
<footer>
    <p>&copy; 2026 Sistem Informasi Kepegawaian. Hak Cipta Dilindungi.</p>
</footer>
</body>
</html>
