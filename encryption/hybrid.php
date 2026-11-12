<?php


// Lokasi penyimpanan pasangan kunci RSA
if (!defined('KEY_DIR')) {
    define('KEY_DIR', __DIR__ . '/../keys');
}
function ensure_key_pair(): void
{
    if (!is_dir(KEY_DIR)) {
        mkdir(KEY_DIR, 0700, true);
    }

    $privatePath = KEY_DIR . '/private.pem';
    $publicPath  = KEY_DIR . '/public.pem';

    if (file_exists($privatePath) && file_exists($publicPath)) {
        return;
    }

    // Lokasi konfigurasi OpenSSL pada Laragon
    $configPath = 'D:/laragon/bin/apache/httpd-2.4.54-win64-VS16/conf/openssl.cnf';

    if (!file_exists($configPath)) {
        throw new RuntimeException('File openssl.cnf tidak ditemukan: ' . $configPath);
    }

    $config = [
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
        'config' => $configPath
    ];

    $res = openssl_pkey_new($config);

    if (!$res) {
        $errors = [];
        while ($msg = openssl_error_string()) {
            $errors[] = $msg;
        }

        throw new RuntimeException(
            'Gagal membuat kunci RSA: ' . implode(' | ', $errors)
        );
    }

    if (!openssl_pkey_export($res, $privatePem, null, $config)) {
        throw new RuntimeException('Gagal mengekspor private key.');
    }

    $details = openssl_pkey_get_details($res);

    if (!$details || empty($details['key'])) {
        throw new RuntimeException('Gagal mengambil public key.');
    }

    file_put_contents($privatePath, $privatePem, LOCK_EX);
    file_put_contents($publicPath, $details['key'], LOCK_EX);

    @chmod($privatePath, 0600);
}


function encrypt_content(string $plaintext): array
{
    ensure_key_pair();

    $publicPath = KEY_DIR . '/public.pem';
    $publicKey = openssl_pkey_get_public(file_get_contents($publicPath));

    if (!$publicKey) {
        throw new RuntimeException('Public key tidak dapat dibaca.');
    }

    $aesKey = random_bytes(32);
    $iv = random_bytes(12);
    $tag = '';

    $ciphertext = openssl_encrypt(
        $plaintext,
        'aes-256-gcm',
        $aesKey,
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );

    if ($ciphertext === false) {
        throw new RuntimeException('Enkripsi AES gagal.');
    }

    if (!openssl_public_encrypt(
        $aesKey,
        $encryptedKey,
        $publicKey,
        OPENSSL_PKCS1_OAEP_PADDING
    )) {
        throw new RuntimeException('Enkripsi RSA gagal.');
    }

    return [
        'ciphertext' => base64_encode($ciphertext),
        'iv' => base64_encode($iv),
        'tag' => base64_encode($tag),
        'wrapped_key' => base64_encode($encryptedKey),
        'encrypted_key' => base64_encode($encryptedKey)
    ];
}

function decrypt_content(array $record): string
{
    $privatePath = KEY_DIR . '/private.pem';

    if (!file_exists($privatePath)) {
        throw new RuntimeException('Private key RSA tidak ditemukan.');
    }

    $decode = function (string $field) use ($record): string {
        if (!isset($record[$field]) || $record[$field] === '') {
            throw new RuntimeException('Kolom ' . $field . ' kosong.');
        }

        $value = base64_decode((string)$record[$field], true);

        if ($value === false) {
            throw new RuntimeException('Format Base64 kolom ' . $field . ' tidak valid.');
        }

        return $value;
    };

    $ciphertext = $decode('ciphertext');
    $iv = $decode('iv');
    $tag = $decode('tag');

    $wrappedKeyField = isset($record['wrapped_key'])
        ? 'wrapped_key'
        : 'encrypted_key';

    $wrappedKey = $decode($wrappedKeyField);

    $privateKey = openssl_pkey_get_private(
        file_get_contents($privatePath)
    );

    if (!$privateKey) {
        throw new RuntimeException('Private key RSA tidak dapat dibaca.');
    }

    $aesKey = '';

    if (!openssl_private_decrypt(
        $wrappedKey,
        $aesKey,
        $privateKey,
        OPENSSL_PKCS1_OAEP_PADDING
    )) {
        throw new RuntimeException('Gagal membuka kunci AES dengan RSA-OAEP.');
    }

    if (strlen($aesKey) !== 32) {
        throw new RuntimeException('Panjang kunci AES tidak valid.');
    }

    $plaintext = openssl_decrypt(
        $ciphertext,
        'aes-256-gcm',
        $aesKey,
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );

    if ($plaintext === false) {
        throw new RuntimeException('Dekripsi AES-256-GCM gagal.');
    }

    return $plaintext;
}