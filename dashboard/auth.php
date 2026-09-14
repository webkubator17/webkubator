<?php
declare(strict_types=1);

function dashboard_auth_path(): string
{
    return __DIR__ . '/storage/auth.json';
}

function dashboard_default_auth(): array
{
    return [
        'username' => 'admin',
        'algorithm' => 'sha256',
        'salt' => '9d0cbb3c1dd0d6e34d3bb8706ed1a5d6',
        'hash' => '05c99a9a606bfb34b201f473066cb8bbe6f930d3d85eb0b005fc6e8a9e9fc49c',
    ];
}

function dashboard_load_auth(): array
{
    $path = dashboard_auth_path();
    if (!is_file($path)) {
        return dashboard_default_auth();
    }
    $data = json_decode((string) file_get_contents($path), true);
    return is_array($data) ? array_merge(dashboard_default_auth(), $data) : dashboard_default_auth();
}

function dashboard_password_digest(string $password, string $salt): string
{
    return hash('sha256', $salt . "\0" . $password);
}

function dashboard_verify_password(string $username, string $password): bool
{
    $auth = dashboard_load_auth();
    if (!hash_equals((string) $auth['username'], $username)) {
        return false;
    }
    if (($auth['algorithm'] ?? 'sha256') === 'password') {
        return password_verify($password, (string) $auth['hash']);
    }
    return hash_equals((string) $auth['hash'], dashboard_password_digest($password, (string) $auth['salt']));
}

function dashboard_set_password(string $username, string $password): bool
{
    $directory = dashboard_storage_dir();
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        return false;
    }
    if (function_exists('password_hash')) {
        $auth = ['username' => $username, 'algorithm' => 'password', 'salt' => '', 'hash' => password_hash($password, PASSWORD_DEFAULT)];
    } else {
        $salt = bin2hex(random_bytes(16));
        $auth = ['username' => $username, 'algorithm' => 'sha256', 'salt' => $salt, 'hash' => dashboard_password_digest($password, $salt)];
    }
    return file_put_contents(dashboard_auth_path(), json_encode($auth, JSON_PRETTY_PRINT), LOCK_EX) !== false;
}

