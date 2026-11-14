<?php
require_once __DIR__ . '/config/database.php';

$page = $_GET['page'] ?? 'home';
$rows = [];

if ($page === 'data') {
    try {
        $rows = $pdo->query("SELECT id, ciphertext, iv FROM pegawai_terenkripsi ORDER BY id ASC")->fetchAll();
    } catch (Exception $e) {
        $rows = [];
    }
}
?>
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
            color: #e2e8f0; display: flex; flex-direction: column; min-height: 100vh;
        }
        header {
            background-color: rgba(18, 38, 35, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;
        }
        header h1 { font-size: 20px; color: #7ee7df; letter-spacing: 0.5px; }
        nav { display: flex; gap: 8px; margin-left: auto; margin-right: 10px; }
        nav a { color: #94a3b8; text-decoration: none; padding: 9px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; transition: all 0.2s; }
        nav a:hover { color: #7ee7df; background-color: rgba(126, 231, 223, 0.1); }
        nav a.active { color: #7ee7df; background-color: rgba(126, 231, 223, 0.15); }
        .btn-login { background-color: #7ee7df; color: #112220; padding: 9px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; transition: all 0.2s; }
        .btn-login:hover { background-color: #5bcaca; transform: translateY(-1px); }
        .container {
            max-width: 900px; margin: 50px auto; padding: 40px; background: rgba(27, 59, 54, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            flex: 1; width: 90%;
        }
        h2 { margin-bottom: 15px; color: #ffffff; font-size: 26px; }
        p { color: #94a3b8; margin-bottom: 20px; line-height: 1.6; font-size: 15px; }
        .info-box { background: rgba(15, 32, 29, 0.6); border: 1px solid rgba(255, 255, 255, 0.08); padding: 25px; border-radius: 12px; margin-top: 25px; }
        .info-box h3 { color: #7ee7df; margin-bottom: 10px; font-size: 18px; }
        .info-box p:last-child { margin-bottom: 0; }
        .info-clickable { cursor: pointer; user-select: none; transition: all 0.25s ease; }
        .info-clickable:hover { border-color: rgba(126, 231, 223, 0.5); transform: translateY(-2px); }
        .info-clickable .warn-text { display: none; margin: 12px 0 0 0; }
        .info-clickable.warning { background: rgba(245, 158, 11, 0.18); border: 1px solid #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2), 0 0 24px rgba(245, 158, 11, 0.25); }
        .info-clickable.warning h3 { color: #fbbf24; }
        .info-clickable.warning .warn-text { display: block; color: #fde68a; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.08); font-size: 13px; }
        th { color: #7ee7df; font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        td { color: #cbd5e1; }
        td.cipher { font-family: Consolas, monospace; font-size: 12px; color: #64748b; word-break: break-all; }
        footer { background-color: rgba(18, 38, 35, 0.95); border-top: 1px solid rgba(255, 255, 255, 0.08); text-align: center; padding: 25px 20px; margin-top: auto; }
        footer p { margin: 0; color: #64748b; font-size: 13px; }
    </style>
</head>
<body>
<header>
    <h1>&#10024; Portal Kepegawaian</h1>
    <nav>
        <a href="index.php" class="<?= $page === 'data' ? '' : 'active'; ?>">Beranda</a>
        <a href="index.php?page=data" class="<?= $page === 'data' ? 'active' : ''; ?>">Data Pegawai</a>
    </nav>
    <a href="auth/login.php" class="btn-login">Login Admin</a>
</header>

<div class="container">
<?php if ($page === 'data'): ?>

    <h2>Data Pegawai</h2>
    <p>Data pegawai disimpan dalam bentuk terenkripsi (AES-256-GCM + RSA-OAEP). Isinya hanya dapat dibaca administrator yang memiliki private key.</p>
    <div class="info-box">
        <p>Total data tersimpan: <strong style="color:#7ee7df;"><?= count($rows); ?></strong></p>
        <?php if (count($rows) > 0): ?>
            <table>
                <thead><tr><th>No</th><th>ID</th><th>Ciphertext (cuplikan)</th><th>IV</th></tr></thead>
                <tbody>
                <?php $no = 1; foreach ($rows as $r): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= (int)$r['id']; ?></td>
                        <td class="cipher"><?= htmlspecialchars(substr($r['ciphertext'], 0, 32)); ?>...</td>
                        <td class="cipher"><?= htmlspecialchars($r['iv']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Belum ada data pegawai.</p>
        <?php endif; ?>
    </div>

<?php else: ?>

    <h2>Selamat Datang di Sistem Informasi Kepegawaian</h2>
    <p>Halaman ini merupakan portal utama bagi pengunjung dan pegawai untuk melihat informasi instansi secara terbuka dan transparan.</p>

    <div class="info-box info-clickable" id="infoLayanan" role="button" tabindex="0" aria-pressed="false">
        <h3><span id="infoIcon">&#8505;&#65039;</span> Informasi Layanan</h3>
        <p>Sistem ini dilindungi dengan standar keamanan kriptografi mutakhir untuk menjaga integritas data kepegawaian internal perusahaan/instansi secara aman dan terstruktur.</p>
        <p class="warn-text">&#9888;&#65039; Peringatan: Data kepegawaian bersifat rahasia. Dilarang menyalin, menyebarkan, atau menyalahgunakan informasi tanpa izin instansi.</p>
    </div>

<?php endif; ?>
</div>

<footer>
    <p>&copy; 2026 Sistem Informasi Kepegawaian. Hak Cipta Dilindungi.</p>
</footer>

<?php if ($page !== 'data'): ?>
<script>
    (function () {
        var box  = document.getElementById('infoLayanan');
        var icon = document.getElementById('infoIcon');
        function toggle() {
            var aktif = box.classList.toggle('warning');
            box.setAttribute('aria-pressed', aktif ? 'true' : 'false');
            icon.innerHTML = aktif ? '&#9888;&#65039;' : '&#8505;&#65039;';
        }
        box.addEventListener('click', toggle);
        box.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(); }
        });
    })();
</script>
<?php endif; ?>
</body>
</html>