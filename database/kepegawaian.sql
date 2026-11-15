CREATE DATABASE IF NOT EXISTS kepegawaian CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kepegawaian;

-- Seluruh data pegawai (NIP, nama, jabatan, email, telepon) disimpan sebagai satu paket terenkripsi
CREATE TABLE IF NOT EXISTS pegawai_terenkripsi (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    ciphertext  MEDIUMTEXT   NOT NULL,   -- data pegawai (JSON) dienkripsi AES-256-GCM, base64
    iv          VARCHAR(64)  NOT NULL,   -- initialization vector GCM, base64
    tag         VARCHAR(64)  NOT NULL,   -- authentication tag GCM, base64
    wrapped_key TEXT         NOT NULL,   -- kunci AES yang dibungkus RSA-OAEP (public key), base64
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data yang boleh tampil di halaman publik (nama dan jabatan). NIP, email, telepon hanya ada di pegawai_terenkripsi.
CREATE TABLE IF NOT EXISTS pegawai_publik (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    pegawai_id INT UNSIGNED NOT NULL UNIQUE,
    nama       VARCHAR(150) NOT NULL,
    jabatan    VARCHAR(150) NOT NULL DEFAULT '',
    FOREIGN KEY (pegawai_id) REFERENCES pegawai_terenkripsi(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;