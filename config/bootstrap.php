<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../encryption/hybrid.php';

function require_login(): void {
    if (empty($_SESSION['user'])) {
        header('Location: ../auth/login.php');
        exit;
    }
}
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
