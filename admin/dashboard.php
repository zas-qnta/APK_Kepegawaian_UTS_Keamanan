<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/layout.php';
require_login();

$priv    = $_SESSION['admin_private_key'];
$total   = 0;
$gagal   = 0;
$terbaru = [];
$dbError = '';

try {
    $rows  = $pdo->query("SELECT * FROM pegawai_terenkripsi ORDER BY id DESC")->fetchAll();
    $total = count($rows);
    foreach ($rows as $r) {
        $d = decrypt_row($r, $priv);
        if ($d === null) {
            $gagal++;
            continue;
        }
        if (count($terbaru) < 5) {
            $terbaru[] = $d;
        }
    }
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

admin_head('Dashboard', 'dashboard');
?>
<div class="card">
    <h1>Dashboard Administrator</h1>
    <p>Selamat datang di panel utama. Semua data pada halaman admin dibuka dengan mendekripsi isi database menggunakan private key Anda.</p>
</div>

<div class="card">
    <h3>Statistik Sistem</h3>
    <?php if ($dbError !== ''): ?>
        <div class="alert danger">Gagal membaca database: <?= e($dbError); ?></div>
    <?php endif; ?>
    <p>Total pegawai tersimpan: <strong class="hl"><?= $total; ?> orang</strong></p>
    <p>Status autentikasi: berhasil masuk menggunakan RSA Private Key.</p>
    <p>Status dekripsi: <?= $total - $gagal; ?> dari <?= $total; ?> data berhasil didekripsi (AES-256-GCM + RSA-OAEP).</p>
</div>

<div class="card">
    <h3>5 Pegawai Terbaru (hasil dekripsi)</h3>
    <div class="table-wrap">
        <table>
            <thead><tr><th>NIP</th><th>Nama</th><th>Jabatan</th></tr></thead>
            <tbody>
            <?php if (count($terbaru) > 0): ?>
                <?php foreach ($terbaru as $p): ?>
                    <tr>
                        <td><?= e($p['nip'] ?? ''); ?></td>
                        <td><?= e($p['nama'] ?? ''); ?></td>
                        <td><?= e($p['jabatan'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">Belum ada data pegawai.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php admin_foot(); ?>