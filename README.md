# Sistem Informasi Kepegawaian

## Deskripsi

Sistem Informasi Kepegawaian merupakan aplikasi berbasis web yang digunakan untuk mengelola dan menyimpan data pegawai secara lebih terstruktur dengan menerapkan teknologi kriptografi untuk menjaga kerahasiaan informasi.

Aplikasi ini dikembangkan menggunakan PHP Native dan MySQL. Sistem keamanan data menerapkan metode Hybrid Cryptosystem yang menggabungkan algoritma AES-256-GCM dan RSA-OAEP.

Selain itu, autentikasi administrator menggunakan RSA Private Key dengan mekanisme tanda tangan digital (Digital Signature).

## Fitur Utama

1. Login administrator menggunakan RSA Private Key.
2. Autentikasi menggunakan tanda tangan digital RSA-SHA256.
3. Pengelolaan data pegawai.
4. Enkripsi data menggunakan AES-256-GCM.
5. Pengamanan kunci AES menggunakan RSA-OAEP.
6. Dekripsi data pegawai oleh sistem yang memiliki private key RSA.
7. Penyimpanan data terenkripsi ke dalam database MySQL.

## Teknologi yang Digunakan

| Teknologi | Keterangan |
|---|---|
| PHP Native | Bahasa pemrograman backend |
| MySQL | Database |
| HTML | Struktur halaman web |
| CSS | Tampilan antarmuka |
| Bootstrap 5 | Framework antarmuka |
| JavaScript Web Crypto API | Pembuatan tanda tangan digital |
| OpenSSL | Implementasi kriptografi |
| Laragon | Lingkungan pengembangan lokal |

## Algoritma Kriptografi

### 1. AES-256-GCM

AES-256-GCM digunakan untuk mengenkripsi data pegawai.

- AES menggunakan kunci sepanjang 256 bit.
- Mode GCM memberikan kerahasiaan dan autentikasi integritas data.
- Setiap proses enkripsi menggunakan kunci dan IV acak.
- Hasil enkripsi menghasilkan ciphertext dan authentication tag.

### 2. RSA-OAEP

RSA-OAEP digunakan untuk mengamankan kunci AES.

- Sistem membuat kunci AES secara acak.
- Kunci AES dienkripsi menggunakan RSA Public Key.
- RSA Private Key digunakan untuk membuka kembali kunci AES.
- Kunci AES yang berhasil dibuka digunakan untuk mendekripsi data pegawai.

### 3. Digital Signature RSA-SHA256

RSA juga digunakan untuk autentikasi administrator.

- Administrator memilih file RSA Private Key.
- Browser membuat tanda tangan digital dari challenge yang diberikan server.
- Server memverifikasi tanda tangan menggunakan RSA Public Key.
- Jika valid, administrator mendapatkan akses ke dashboard.

## Struktur Folder

```text
sistem-kepegawaian/
|
|-- admin/
|   |-- dashboard.php
|   |-- detail_pegawai.php
|   |-- pegawai.php
|
|-- auth/
|   |-- login.php
|   |-- logout.php
|   |-- auth_keys/
|       |-- admin_public.pem
|
|-- config/
|   |-- bootstrap.php
|   |-- database.php
|
|-- database/
|   |-- kepegawaian.sql
|
|-- encryption/
|   |-- hybrid.php
|
|-- keys/
|   |-- private.pem
|   |-- public.pem
|
|-- backups/
|
|-- index.php
|-- README.md
```

## Persyaratan Sistem

Sebelum menjalankan aplikasi, pastikan perangkat telah memiliki:

- Laragon.
- PHP 8.x.
- MySQL atau MariaDB.
- Ekstensi OpenSSL PHP aktif.
- Browser modern yang mendukung Web Crypto API.

## Cara Instalasi

### 1. Menyiapkan Proyek

Salin folder proyek ke direktori:

```text
D:\laragon\www\
```

### 2. Menyiapkan Database

1. Jalankan Laragon.
2. Aktifkan Apache dan MySQL.
3. Buka phpMyAdmin.
4. Import file `database/kepegawaian.sql`.
5. Pastikan database `kepegawaian` berhasil dibuat.

### 3. Konfigurasi Database

Buka file:

```text
config/database.php
```

Sesuaikan konfigurasi database dengan lingkungan komputer:

```php
$host = 'localhost';
$db   = 'kepegawaian';
$user = 'root';
$pass = '';
```

### 4. Menyiapkan Kunci RSA

Sistem menggunakan dua pasangan kunci RSA dengan fungsi berbeda:

**Kunci enkripsi data pegawai:**
- `keys/public.pem`
- `keys/private.pem`

**Kunci autentikasi administrator:**
- `auth/auth_keys/admin_public.pem`
- `admin-login-private.pem` yang disimpan secara terpisah di komputer administrator.

Jangan menghapus, mengganti, atau membagikan private key yang sudah digunakan.

### 5. Menjalankan Aplikasi

Buka browser dan akses:

```text
http://localhost/sistem-kepegawaian/
```

Halaman login administrator dapat diakses melalui:

```text
http://localhost/sistem-kepegawaian/auth/login.php
```

Pilih file `admin-login-private.pem` untuk melakukan autentikasi.

## Alur Sistem Enkripsi

```text
Data Pegawai
     |
     v
Membuat Kunci AES-256
     |
     v
Enkripsi Data dengan AES-256-GCM
     |
     v
Enkripsi Kunci AES dengan RSA Public Key
     |
     v
Simpan Ciphertext, IV, Tag, dan Wrapped Key
     |
     v
Database MySQL
     |
     v
RSA Private Key Membuka Kunci AES
     |
     v
Dekripsi AES-256-GCM
     |
     v
Data Pegawai Asli
```

## Catatan Keamanan

- Private key tidak boleh dibagikan kepada pihak lain.
- Private key enkripsi data pegawai harus disimpan secara aman.
- Lakukan pencadangan database dan pasangan kunci secara berkala.
- Jangan menyimpan private key di direktori publik website.
- Gunakan HTTPS ketika aplikasi diterapkan pada server produksi.
- Sistem login menggunakan challenge agar tanda tangan digital tidak dapat digunakan kembali untuk sesi login berikutnya.
- Jangan menghapus kolom `password_hash` dari database tanpa melakukan migrasi skema yang terencana.

## Tujuan Pengembangan

Aplikasi ini dikembangkan sebagai implementasi penerapan ilmu kriptografi dalam pengembangan sistem informasi berbasis web, khususnya untuk menjaga kerahasiaan dan integritas data pegawai serta menerapkan autentikasi administrator berbasis pasangan kunci RSA.