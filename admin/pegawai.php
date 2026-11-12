<?php
session_start();
if (!isset($_SESSION['admin_logged']) && !isset($_SESSION['admin_logged_in'])) {
    header('Location: ../auth/login.php');
    exit;
}
require_once '../config/database.php';

$success = '';
$error = '';

// PROSES TAMBAH / EDIT PEGAWAI
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $nip = trim($_POST['nip'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');

    if ($action === 'add') {
        if (!empty($nip) && !empty($nama)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO pegawai (nip, nama, jabatan, email, telepon) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nip, $nama, $jabatan, $email, $telepon]);
                $success = "Data pegawai berhasil ditambahkan!";
            } catch (Exception $e) {
                $error = "Gagal menambah data: " . $e->getMessage();
            }
        } else {
            $error = "NIP dan Nama wajib diisi!";
        }
    }
}

// PROSES HAPUS PEGAWAI
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM pegawai WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: pegawai.php');
        exit;
    } catch (Exception $e) {
        $error = "Gagal menghapus data: " . $e->getMessage();
    }
}

// Ambil data pegawai
try {
    $stmt = $pdo->query("SELECT * FROM pegawai ORDER BY id DESC");
    $listPegawai = $stmt->fetchAll();
} catch (Exception $e) {
    $listPegawai = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Pegawai - Sistem Kepegawaian</title>
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
        p { color: #94a3b8; font-size: 14px; margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.08); font-size: 14px; }
        th { background-color: rgba(0, 0, 0, 0.2); color: #7ee7df; font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
        td { color: #cbd5e1; }
        tr:hover td { background-color: rgba(255, 255, 255, 0.02); }

        .btn { padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; cursor: pointer; border: none; display: inline-block; transition: all 0.2s; }
        .btn-primary { background-color: #7ee7df; color: #112220; }
        .btn-primary:hover { background-color: #5bcaca; transform: translateY(-1px); }
        .btn-danger { background-color: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
        .btn-danger:hover { background-color: #ef4444; color: white; }
        
        .alert-success { background-color: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; }
        .alert-danger { background-color: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; }
        
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group.full { grid-column: span 2; }
        label { display: block; font-weight: 500; font-size: 13px; color: #cbd5e1; margin-bottom: 6px; }
        input[type="text"], input[type="email"] { 
            width: 100%; 
            padding: 10px 14px; 
            background: rgba(15, 32, 29, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.12); 
            border-radius: 8px; 
            color: #ffffff;
            font-size: 14px; 
            transition: all 0.2s;
        }
        input[type="text"]:focus, input[type="email"]:focus { 
            outline: none; 
            border-color: #7ee7df; 
            box-shadow: 0 0 0 3px rgba(126, 231, 223, 0.15); 
        }

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
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="pegawai.php" class="active">👥 Data Pegawai</a>
    <a href="../auth/logout.php" class="btn-logout">Keluar Sistem</a>
</div>
<div class="main-content">
    <div class="card">
        <h1>Manajemen Data Pegawai</h1>
        <p>Sistem Pengelolaan Informasi Kepegawaian Eksekutif</p>
        
        <?php if (!empty($success)): ?>
            <div class="alert-success"><?= $success; ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert-danger"><?= $error; ?></div>
        <?php endif; ?>

        <h3 style="margin-top: 25px;">+ Tambah Pegawai Baru</h3>
        <form method="POST" style="margin-top: 15px;">
            <input type="hidden" name="action" value="add">
            <div class="form-grid">
                <div class="form-group">
                    <label>NIP Pegawai:</label>
                    <input type="text" name="nip" required placeholder="Contoh: 19900101...">
                </div>
                <div class="form-group">
                    <label>Nama Lengkap & Gelar:</label>
                    <input type="text" name="nama" required placeholder="Contoh: Zaskia Qanita, S.Kom.">
                </div>
                <div class="form-group">
                    <label>Jabatan:</label>
                    <input type="text" name="jabatan" placeholder="Contoh: Lead Developer">
                </div>
                <div class="form-group">
                    <label>Alamat Email:</label>
                    <input type="email" name="email" placeholder="Contoh: email@instansi.go.id">
                </div>
                <div class="form-group full">
                    <label>Nomor Telepon / WhatsApp:</label>
                    <input type="text" name="telepon" placeholder="Contoh: 08123456789">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">💾 Simpan Data Baru</button>
        </form>
    </div>

    <div class="card">
        <h3>Daftar Pegawai Terdaftar</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIP</th>
                    <th>Nama Lengkap</th>
                    <th>Jabatan</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($listPegawai) > 0): ?>
                    <?php $no = 1; foreach ($listPegawai as $p): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($p['nip']); ?></td>
                        <td><?= htmlspecialchars($p['nama']); ?></td>
                        <td><?= htmlspecialchars($p['jabatan']); ?></td>
                        <td><?= htmlspecialchars($p['email']); ?></td>
                        <td><?= htmlspecialchars($p['telepon']); ?></td>
                        <td>
                            <a href="pegawai.php?delete=<?= $p['id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #64748b; padding: 30px;">Belum ada data pegawai di dalam database.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
