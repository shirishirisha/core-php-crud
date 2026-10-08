<?php
session_start();
require_once __DIR__ . '/../config/database.php';

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function is_admin(): bool { return (current_user()['role'] ?? '') === 'admin'; }
function redirect(string $path): never { header('Location: ' . BASE_URL . $path); exit; }

function require_login(): void { if (!current_user()) redirect('login.php'); }
function require_role(string $role): void {
    require_login();
    if (current_user()['role'] !== $role) { http_response_code(403); exit('403 - You do not have access to this page.'); }
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function csrf_check(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Session expired. Go back and try again.'); }
}

function flash(?string $msg = null, string $type = 'success') {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return null; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}

function money(float $n): string {
    if ($n >= 10000000) return '₹' . round($n / 10000000, 2) . ' Cr';
    if ($n >= 100000)   return '₹' . round($n / 100000, 2) . ' L';
    return '₹' . number_format($n);
}
