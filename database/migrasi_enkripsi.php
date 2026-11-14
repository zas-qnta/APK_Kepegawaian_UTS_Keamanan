<?php
// Jalankan sekali lewat terminal: php database\migrasi_enkripsi.php
// Membuat tabel terenkripsi dan memindahkan data plaintext lama (jika ada) dalam bentuk terenkripsi.
if (PHP_SAPI !== 'cli') {
    exit('Jalankan lewat terminal.');
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../encryption/hybrid.php';

$pdo->exec("CREATE TABLE IF NOT EXISTS pegawai_terenkripsi (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    ciphertext  MEDIUMTEXT   NOT NULL,
    iv          VARCHAR(64)  NOT NULL,
    tag         VARCHAR(64)  NOT NULL,
    wrapped_key TEXT         NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
echo "Tabel pegawai_terenkripsi siap.\n";

if (!$pdo->query("SHOW TABLES LIKE 'pegawai'")->fetchColumn()) {
    echo "Tabel lama 'pegawai' tidak ada, tidak ada data yang dimigrasi.\n";
    exit(0);
}

$rows = $pdo->query("SELECT * FROM pegawai ORDER BY id ASC")->fetchAll();
$ins  = $pdo->prepare("INSERT INTO pegawai_terenkripsi (ciphertext, iv, tag, wrapped_key) VALUES (?, ?, ?, ?)");

$pdo->beginTransaction();
$ok = 0;
foreach ($rows as $r) {
    $data = [
        'nip'     => (string)($r['nip'] ?? ''),
        'nama'    => (string)($r['nama'] ?? ''),
        'jabatan' => (string)($r['jabatan'] ?? ''),
        'email'   => (string)($r['email'] ?? ''),
        'telepon' => (string)($r['telepon'] ?? ''),
    ];
    $enc = encrypt_content((string)json_encode($data, JSON_UNESCAPED_UNICODE));
    $ins->execute([$enc['ciphertext'], $enc['iv'], $enc['tag'], $enc['wrapped_key']]);
    $ok++;
}
$pdo->commit();
echo "$ok dari " . count($rows) . " baris berhasil dienkripsi.\n";

if ($ok === count($rows)) {
    $pdo->exec("DROP TABLE pegawai");
    echo "Tabel lama 'pegawai' (plaintext) sudah dihapus.\n";
}