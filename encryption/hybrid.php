<?php
// Hybrid cryptosystem: AES-256-GCM (simetris) + RSA-OAEP (asimetris)
// Server hanya memegang PUBLIC key. Dekripsi butuh PRIVATE key milik admin.

const PUBLIC_KEY_FILE = __DIR__ . '/../keys/public.pem';

// Merapikan PEM hasil paste (baris baru hilang, CRLF, spasi berlebih)
function normalize_private_key(string $input): string
{
    $input = trim($input);
    if ($input === '') {
        return '';
    }
    if (!preg_match('/-----BEGIN ([A-Z ]*PRIVATE KEY)-----(.+?)-----END \1-----/s', $input, $m)) {
        return '';
    }
    $body = preg_replace('/\s+/', '', $m[2]);
    return "-----BEGIN {$m[1]}-----\n" . chunk_split($body, 64, "\n") . "-----END {$m[1]}-----\n";
}

// Bukti kepemilikan: data acak dienkripsi public key sistem, harus bisa dibuka private key yang diinput
function verify_private_key(string $privatePem): bool
{
    if (!is_file(PUBLIC_KEY_FILE)) {
        return false;
    }
    $priv = @openssl_pkey_get_private($privatePem);
    $pub  = @openssl_pkey_get_public(file_get_contents(PUBLIC_KEY_FILE));
    if (!$priv || !$pub) {
        return false;
    }
    $probe = random_bytes(32);
    if (!openssl_public_encrypt($probe, $enc, $pub, OPENSSL_PKCS1_OAEP_PADDING)) {
        return false;
    }
    if (!@openssl_private_decrypt($enc, $dec, $priv, OPENSSL_PKCS1_OAEP_PADDING)) {
        return false;
    }
    return hash_equals($probe, $dec);
}

function encrypt_content(string $plaintext): array
{
    if (!is_file(PUBLIC_KEY_FILE)) {
        throw new RuntimeException('Public key tidak ditemukan (keys/public.pem).');
    }
    $publicKey = openssl_pkey_get_public(file_get_contents(PUBLIC_KEY_FILE));
    if (!$publicKey) {
        throw new RuntimeException('Public key tidak dapat dibaca.');
    }

    $aesKey = random_bytes(32);
    $iv     = random_bytes(12);
    $tag    = '';

    $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $aesKey, OPENSSL_RAW_DATA, $iv, $tag, '', 16);
    if ($ciphertext === false) {
        throw new RuntimeException('Enkripsi AES-256-GCM gagal.');
    }

    if (!openssl_public_encrypt($aesKey, $wrappedKey, $publicKey, OPENSSL_PKCS1_OAEP_PADDING)) {
        throw new RuntimeException('Enkripsi kunci AES dengan RSA-OAEP gagal.');
    }

    return [
        'ciphertext'  => base64_encode($ciphertext),
        'iv'          => base64_encode($iv),
        'tag'         => base64_encode($tag),
        'wrapped_key' => base64_encode($wrappedKey),
    ];
}

function decrypt_content(array $record, string $privatePem): string
{
    $decode = function (string $field) use ($record): string {
        $value = base64_decode((string)($record[$field] ?? ''), true);
        if ($value === false || $value === '') {
            throw new RuntimeException('Kolom ' . $field . ' kosong atau tidak valid.');
        }
        return $value;
    };

    $privateKey = openssl_pkey_get_private($privatePem);
    if (!$privateKey) {
        throw new RuntimeException('Private key tidak dapat dibaca.');
    }

    if (!openssl_private_decrypt($decode('wrapped_key'), $aesKey, $privateKey, OPENSSL_PKCS1_OAEP_PADDING)) {
        throw new RuntimeException('Gagal membuka kunci AES dengan RSA-OAEP.');
    }

    $plaintext = openssl_decrypt($decode('ciphertext'), 'aes-256-gcm', $aesKey, OPENSSL_RAW_DATA, $decode('iv'), $decode('tag'));
    if ($plaintext === false) {
        throw new RuntimeException('Dekripsi AES-256-GCM gagal (data berubah atau kunci salah).');
    }
    return $plaintext;
}

// Dekripsi satu baris database menjadi array data pegawai (null jika gagal)
function decrypt_row(array $row, string $privatePem): ?array
{
    try {
        $data = json_decode(decrypt_content($row, $privatePem), true);
        return is_array($data) ? $data : null;
    } catch (Throwable $e) {
        return null;
    }
}