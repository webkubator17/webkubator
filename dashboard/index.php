<?php
declare(strict_types=1);

require_once __DIR__ . '/data.php';
require_once __DIR__ . '/auth.php';

session_set_cookie_params([
    'httponly' => true,
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'samesite' => 'Lax',
]);
session_start();

function dashboard_h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function dashboard_asset_url(string $path): string
{
    $clean = dashboard_clean_asset_path($path);
    return $clean === '' ? '' : '../' . $clean;
}

function dashboard_icon(string $name): string
{
    $paths = [
        'home' => '<path d="M3 10.7 12 3l9 7.7v8.8a1.5 1.5 0 0 1-1.5 1.5h-5v-6h-5v6h-5A1.5 1.5 0 0 1 3 19.5v-8.8Z"/><path d="m2 11 10-8.5L22 11"/>',
        'logo' => '<circle cx="12" cy="12" r="8.5"/><path d="m8 10 2.5 2L8 14m8-4-2.5 2 2.5 2m-5.5 2 3-8"/>',
        'portfolio' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M8 5V3h8v2M3 10h18M10 10v2h4v-2"/>',
        'settings' => '<path d="M12 8.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z"/><path d="m19.4 15 .1.1 1.4 1.1-1.8 3.1-1.7-.7a8.3 8.3 0 0 1-1.8 1l-.3 1.8h-3.6l-.3-1.8a8.3 8.3 0 0 1-1.8-1l-1.7.7-1.8-3.1 1.5-1.1a8.2 8.2 0 0 1 0-2L5.6 12l1.8-3.1 1.7.7a8.3 8.3 0 0 1 1.8-1l.3-1.8h3.6l.3 1.8a8.3 8.3 0 0 1 1.8 1l1.7-.7 1.8 3.1-1.4 1.1a8.2 8.2 0 0 1 0 1.9Z"/>',
        'logout' => '<path d="M10 4H5.5A1.5 1.5 0 0 0 4 5.5v13A1.5 1.5 0 0 0 5.5 20H10M14 8l4 4-4 4m4-4H9"/>',
        'save' => '<path d="M5 3h11l3 3v15H5V3Z"/><path d="M8 3v6h8V3M8 21v-7h8v7"/>',
        'upload' => '<path d="M12 16V4m0 0L8 8m4-4 4 4M5 14v5h14v-5"/>',
        'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/>',
    ];
    return '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . ($paths[$name] ?? '') . '</svg>';
}

function dashboard_csrf_token(): string
{
    if (empty($_SESSION['dashboard_csrf'])) {
        $_SESSION['dashboard_csrf'] = bin2hex(random_bytes(24));
    }
    return (string) $_SESSION['dashboard_csrf'];
}

function dashboard_valid_csrf(): bool
{
    return isset($_POST['csrf'], $_SESSION['dashboard_csrf']) && hash_equals((string) $_SESSION['dashboard_csrf'], (string) $_POST['csrf']);
}

function dashboard_set_flash(string $type, string $message): void
{
    $_SESSION['dashboard_flash'] = ['type' => $type, 'message' => $message];
}

function dashboard_redirect(string $page = 'home'): void
{
    header('Location: /dashboard/?page=' . rawurlencode($page));
    exit;
}

function dashboard_store_image(array $file, string $prefix): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > 8 * 1024 * 1024) {
        return null;
    }
    $tmp = (string) ($file['tmp_name'] ?? '');
    $size = @getimagesize($tmp);
    if ($tmp === '' || $size === false) {
        return null;
    }
    $mime = (string) ($size['mime'] ?? '');
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    if (!isset($extensions[$mime])) {
        return null;
    }
    $directory = __DIR__ . '/../assets/images/uploads';
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        return null;
    }
    $filename = preg_replace('/[^a-z0-9-]+/i', '-', $prefix) . '-' . bin2hex(random_bytes(5)) . '.' . $extensions[$mime];
    $destination = $directory . '/' . $filename;
    if (!move_uploaded_file($tmp, $destination)) {
        return null;
    }
    return 'assets/images/uploads/' . $filename;
}

function dashboard_indexed_upload(string $field, int $index, string $prefix): ?string
{
    if (!isset($_FILES[$field]['name'][$index])) {
        return null;
    }
    $file = [
        'name' => $_FILES[$field]['name'][$index],
        'type' => $_FILES[$field]['type'][$index] ?? '',
        'tmp_name' => $_FILES[$field]['tmp_name'][$index] ?? '',
        'error' => $_FILES[$field]['error'][$index] ?? UPLOAD_ERR_NO_FILE,
        'size' => $_FILES[$field]['size'][$index] ?? 0,
    ];
    return dashboard_store_image($file, $prefix);
}

function dashboard_post_value(array $source, string $key, string $fallback = ''): string
{
    return trim((string) ($source[$key] ?? $fallback));
}

if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }
    session_destroy();
    header('Location: /dashboard/');
    exit;
}

$loginError = '';
if (!isset($_SESSION['dashboard_user']) && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $username = dashboard_post_value($_POST, 'username');
    $password = (string) ($_POST['password'] ?? '');
    if (dashboard_verify_password($username, $password)) {
        session_regenerate_id(true);
        $_SESSION['dashboard_user'] = $username;
        dashboard_csrf_token();
        dashboard_redirect('home');
    }
    $loginError = 'Username atau password tidak sesuai.';
}

if (!isset($_SESSION['dashboard_user'])):
?><!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Dashboard | Webkubator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;500;600;700&family=Fira+Code:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body class="login-page">
    <main class="login-shell">
        <section class="login-card" aria-labelledby="login-title">
            <div class="login-mark"><?= dashboard_icon('logo') ?></div>
            <p class="eyebrow">Webkubator control room</p>
            <h1 id="login-title">Masuk ke Dashboard</h1>
            <p class="login-copy">Kelola konten website utama dengan aman dari satu tempat.</p>
            <?php if ($loginError !== ''): ?><div class="alert alert-error" role="alert"><?= dashboard_h($loginError) ?></div><?php endif; ?>
            <form method="post" class="stack-form" autocomplete="on">
                <input type="hidden" name="action" value="login">
                <label for="username">Username</label>
                <input id="username" name="username" type="text" autocomplete="username" required autofocus>
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
                <button class="button button-primary button-wide" type="submit">Masuk <?= dashboard_icon('logout') ?></button>
            </form>
            <p class="login-hint">Gunakan kredensial admin yang diberikan saat instalasi.</p>
        </section>
    </main>
</body>
</html>
<?php exit; endif;

$page = in_array($_GET['page'] ?? 'home', ['home', 'logo', 'portfolio'], true) ? (string) $_GET['page'] : 'home';
$data = dashboard_load_data();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!dashboard_valid_csrf()) {
        $errors[] = 'Sesi formulir sudah kedaluwarsa. Silakan muat ulang halaman dan coba lagi.';
    } else {
        $action = (string) ($_POST['action'] ?? '');
        if ($action === 'save_home') {
            $home = $data['home'];
            foreach (['eyebrow', 'title', 'description', 'hero_alt', 'cta_primary', 'cta_secondary'] as $field) {
                $home[$field] = dashboard_post_value($_POST, $field, $home[$field]);
            }
            if ($home['title'] === '' || $home['description'] === '') {
                $errors[] = 'Judul dan deskripsi beranda wajib diisi.';
            }
            $upload = dashboard_store_image($_FILES['hero_image_upload'] ?? [], 'hero');
            if ($upload !== null) {
                $home['hero_image'] = $upload;
            }
            $data['home'] = $home;
            if (!$errors && dashboard_save_data($data)) {
                dashboard_set_flash('success', 'Pengaturan beranda berhasil disimpan.');
                dashboard_redirect('home');
            }
        } elseif ($action === 'save_logo') {
            $data['brand']['site_name'] = dashboard_post_value($_POST, 'site_name', $data['brand']['site_name']);
            $logoUpload = dashboard_store_image($_FILES['logo_upload'] ?? [], 'logo');
            $faviconUpload = dashboard_store_image($_FILES['favicon_upload'] ?? [], 'favicon');
            if ($logoUpload !== null) {
                $data['brand']['logo'] = $logoUpload;
            }
            if ($faviconUpload !== null) {
                $data['brand']['favicon'] = $faviconUpload;
            }
            $partners = [];
            foreach ((array) ($_POST['partners'] ?? []) as $index => $partner) {
                if (!is_array($partner)) {
                    continue;
                }
                $alt = dashboard_post_value($partner, 'alt');
                if ($alt === '') {
                    continue;
                }
                $image = dashboard_clean_asset_path(dashboard_post_value($partner, 'image'));
                $partnerUpload = dashboard_indexed_upload('partner_image', (int) $index, 'partner');
                if ($partnerUpload !== null) {
                    $image = $partnerUpload;
                }
                if ($image === '') {
                    continue;
                }
                $partners[] = ['image' => $image, 'alt' => $alt, 'visible' => isset($partner['visible'])];
            }
            $data['partners'] = $partners;
            if (!$errors && dashboard_save_data($data)) {
                dashboard_set_flash('success', 'Logo dan daftar klien berhasil disimpan.');
                dashboard_redirect('logo');
            }
        } elseif ($action === 'save_portfolio') {
            $projects = [];
            foreach ((array) ($_POST['projects'] ?? []) as $index => $project) {
                if (!is_array($project)) {
                    continue;
                }
                $name = dashboard_post_value($project, 'name');
                $url = dashboard_post_value($project, 'url');
                if ($name === '' || $url === '') {
                    continue;
                }
                $image = dashboard_clean_asset_path(dashboard_post_value($project, 'image'));
                $projectUpload = dashboard_indexed_upload('project_image', (int) $index, 'portfolio');
                if ($projectUpload !== null) {
                    $image = $projectUpload;
                }
                $projects[] = [
                    'name' => $name,
                    'image' => $image,
                    'preview_url' => dashboard_post_value($project, 'preview_url'),
                    'url' => $url,
                    'traffic' => max(0, (int) ($project['traffic'] ?? 0)),
                    'visible' => isset($project['visible']),
                    'order' => count($projects),
                ];
            }
            $data['projects'] = $projects;
            if (!$errors && dashboard_save_data($data)) {
                dashboard_set_flash('success', 'Portfolio berhasil disimpan sesuai urutan yang Anda atur.');
                dashboard_redirect('portfolio');
            }
        } elseif ($action === 'change_password') {
            $currentPassword = (string) ($_POST['current_password'] ?? '');
            $newPassword = (string) ($_POST['new_password'] ?? '');
            if (!dashboard_verify_password((string) $_SESSION['dashboard_user'], $currentPassword)) {
                $errors[] = 'Password saat ini tidak sesuai.';
            } elseif (strlen($newPassword) < 12) {
                $errors[] = 'Password baru minimal 12 karakter.';
            } elseif (!dashboard_set_password((string) $_SESSION['dashboard_user'], $newPassword)) {
                $errors[] = 'Password gagal disimpan. Pastikan folder dashboard/storage dapat ditulis.';
            } else {
                dashboard_set_flash('success', 'Password dashboard berhasil diganti.');
                dashboard_redirect('home');
            }
        }
    }
}

$flash = $_SESSION['dashboard_flash'] ?? null;
unset($_SESSION['dashboard_flash']);
$pageMeta = [
    'home' => ['label' => 'Beranda', 'title' => 'Pengaturan beranda', 'description' => 'Atur copywriting dan visual utama yang tampil di hero section.', 'icon' => 'home'],
    'logo' => ['label' => 'Logo', 'title' => 'Logo & identitas', 'description' => 'Kelola logo header, favicon, dan logo klien pada marquee.', 'icon' => 'logo'],
    'portfolio' => ['label' => 'Portofolio', 'title' => 'Portofolio website', 'description' => 'Atur website yang ditampilkan dan visitor untuk menentukan urutan otomatis.', 'icon' => 'portfolio'],
];
?><!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= dashboard_h($pageMeta[$page]['title']) ?> | Webkubator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;500;600;700&family=Fira+Code:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body class="dashboard-page">
    <div class="dashboard-shell">
        <aside class="dashboard-sidebar" id="dashboard-sidebar">
            <div class="dashboard-brand"><span class="brand-symbol"><?= dashboard_icon('logo') ?></span><span><strong>Webkubator</strong><small>Control room</small></span></div>
            <nav class="dashboard-nav" aria-label="Menu dashboard">
                <?php foreach ($pageMeta as $key => $meta): ?><a class="dashboard-nav-link <?= $page === $key ? 'is-active' : '' ?>" href="?page=<?= dashboard_h($key) ?>" <?= $page === $key ? 'aria-current="page"' : '' ?>><?= dashboard_icon($meta['icon']) ?><span><?= dashboard_h($meta['label']) ?></span></a><?php endforeach; ?>
            </nav>
            <div class="sidebar-footer"><span class="status-dot"></span><span>Mode aman aktif</span></div>
        </aside>
        <main class="dashboard-main" id="main-content">
            <header class="dashboard-topbar"><button class="sidebar-toggle" type="button" aria-expanded="false" aria-controls="dashboard-sidebar" aria-label="Buka menu dashboard"><span></span><span></span><span></span></button><div><p class="topbar-kicker">Webkubator / <?= dashboard_h($pageMeta[$page]['label']) ?></p><h1><?= dashboard_h($pageMeta[$page]['title']) ?></h1></div><a class="logout-link" href="?logout=1"><?= dashboard_icon('logout') ?><span>Keluar</span></a></header>
            <div class="dashboard-content">
                <?php if ($flash): ?><div class="alert alert-<?= dashboard_h($flash['type']) ?>" role="status"><?= dashboard_h($flash['message']) ?></div><?php endif; ?>
                <?php foreach ($errors as $error): ?><div class="alert alert-error" role="alert"><?= dashboard_h($error) ?></div><?php endforeach; ?>
                <div class="page-intro"><div><p class="eyebrow">Control panel</p><p><?= dashboard_h($pageMeta[$page]['description']) ?></p></div><span class="live-badge"><span class="status-dot"></span>Live</span></div>

                <?php if ($page === 'home'): ?>
                <form class="dashboard-form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?= dashboard_h(dashboard_csrf_token()) ?>"><input type="hidden" name="action" value="save_home">
                    <section class="panel-card"><div class="panel-heading"><div><span class="panel-icon"><?= dashboard_icon('home') ?></span><div><h2>Hero section</h2><p>Konten ini langsung tampil di halaman utama.</p></div></div><button class="button button-primary" type="submit"><?= dashboard_icon('save') ?> Simpan perubahan</button></div>
                        <div class="form-grid form-grid-two"><div class="field"><label for="eyebrow">Sub judul kecil</label><input id="eyebrow" name="eyebrow" type="text" value="<?= dashboard_h($data['home']['eyebrow']) ?>" maxlength="90" required></div><div class="field"><label for="title">Judul utama / H1</label><input id="title" name="title" type="text" value="<?= dashboard_h($data['home']['title']) ?>" maxlength="140" required></div></div>
                        <div class="field"><label for="description">Deskripsi</label><textarea id="description" name="description" rows="5" maxlength="500" required><?= dashboard_h($data['home']['description']) ?></textarea><small>Gunakan kalimat biasa tanpa HTML.</small></div>
                        <div class="form-grid form-grid-two"><div class="field"><label for="cta_primary">Tombol utama</label><input id="cta_primary" name="cta_primary" type="text" value="<?= dashboard_h($data['home']['cta_primary']) ?>" maxlength="40"></div><div class="field"><label for="cta_secondary">Tombol kedua</label><input id="cta_secondary" name="cta_secondary" type="text" value="<?= dashboard_h($data['home']['cta_secondary']) ?>" maxlength="40"></div></div>
                        <div class="form-grid form-grid-two"><div class="field"><label for="hero_alt">Alt text gambar hero</label><input id="hero_alt" name="hero_alt" type="text" value="<?= dashboard_h($data['home']['hero_alt']) ?>" maxlength="180"></div><div class="field"><label for="hero_image_upload">Ganti gambar hero</label><input id="hero_image_upload" name="hero_image_upload" type="file" accept="image/jpeg,image/png,image/webp,image/gif"><small>Format JPG, PNG, WebP, atau GIF. Maksimal 8 MB.</small></div></div>
                        <div class="asset-preview hero-preview"><img src="<?= dashboard_h(dashboard_asset_url($data['home']['hero_image'])) ?>" alt="Preview gambar hero"></div>
                    </section>
                </form>
                <section class="panel-card security-card"><div class="panel-heading"><div><span class="panel-icon"><?= dashboard_icon('settings') ?></span><div><h2>Keamanan dashboard</h2><p>Ganti password awal setelah login pertama.</p></div></div></div><form method="post" class="form-grid form-grid-three"><input type="hidden" name="csrf" value="<?= dashboard_h(dashboard_csrf_token()) ?>"><input type="hidden" name="action" value="change_password"><div class="field"><label for="current_password">Password saat ini</label><input id="current_password" name="current_password" type="password" autocomplete="current-password" required></div><div class="field"><label for="new_password">Password baru</label><input id="new_password" name="new_password" type="password" minlength="12" autocomplete="new-password" required><small>Minimal 12 karakter.</small></div><div class="field field-action"><button class="button button-secondary" type="submit">Ganti password</button></div></form></section>

                <?php elseif ($page === 'logo'): ?>
                <form class="dashboard-form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?= dashboard_h(dashboard_csrf_token()) ?>"><input type="hidden" name="action" value="save_logo">
                    <section class="panel-card"><div class="panel-heading"><div><span class="panel-icon"><?= dashboard_icon('logo') ?></span><div><h2>Identitas website</h2><p>Logo header dan favicon website utama.</p></div></div><button class="button button-primary" type="submit"><?= dashboard_icon('save') ?> Simpan perubahan</button></div>
                        <div class="form-grid form-grid-two"><div class="field"><label for="site_name">Nama website</label><input id="site_name" name="site_name" type="text" value="<?= dashboard_h($data['brand']['site_name']) ?>" maxlength="70" required></div><div class="asset-preview logo-preview"><img src="<?= dashboard_h(dashboard_asset_url($data['brand']['logo'])) ?>" alt="Logo saat ini"></div></div>
                        <div class="form-grid form-grid-two"><div class="field"><label for="logo_upload">Upload logo header</label><input id="logo_upload" name="logo_upload" type="file" accept="image/jpeg,image/png,image/webp,image/gif"><small>Logo transparan direkomendasikan.</small></div><div class="field"><label for="favicon_upload">Upload favicon</label><input id="favicon_upload" name="favicon_upload" type="file" accept="image/png,image/jpeg,image/webp"><small>Gunakan gambar persegi agar hasil tetap bulat di browser.</small></div></div>
                    </section>
                    <section class="panel-card"><div class="panel-heading"><div><span class="panel-icon"><?= dashboard_icon('logo') ?></span><div><h2>Logo klien</h2><p>Logo yang tampil pada section “Dipercaya Ratusan Klien”.</p></div></div><button class="button button-ghost" type="button" data-add-partner><?= dashboard_icon('upload') ?> Tambah logo</button></div>
                        <div class="editor-list" id="partner-list">
                        <?php foreach ($data['partners'] as $index => $partner): ?><div class="mini-editor" data-partner-editor><div class="editor-number">0<?= $index + 1 ?></div><div class="field"><label for="partner-alt-<?= $index ?>">Nama / alt text</label><input id="partner-alt-<?= $index ?>" name="partners[<?= $index ?>][alt]" type="text" value="<?= dashboard_h($partner['alt']) ?>" required></div><div class="field"><label for="partner-image-<?= $index ?>">Path gambar</label><input id="partner-image-<?= $index ?>" name="partners[<?= $index ?>][image]" type="text" value="<?= dashboard_h($partner['image']) ?>"><input name="partner_image[<?= $index ?>]" type="file" accept="image/jpeg,image/png,image/webp,image/gif"></div><label class="check-field"><input name="partners[<?= $index ?>][visible]" type="checkbox" value="1" <?= !empty($partner['visible']) ? 'checked' : '' ?>> Tampilkan</label><button class="icon-button danger" type="button" data-remove-editor aria-label="Hapus baris logo">×</button></div><?php endforeach; ?>
                        </div>
                        <template id="partner-template"><div class="mini-editor" data-partner-editor><div class="editor-number">NEW</div><div class="field"><label>Nama / alt text</label><input name="partners[__INDEX__][alt]" type="text"></div><div class="field"><label>Path gambar</label><input name="partners[__INDEX__][image]" type="text"><input name="partner_image[__INDEX__]" type="file" accept="image/jpeg,image/png,image/webp,image/gif"></div><label class="check-field"><input name="partners[__INDEX__][visible]" type="checkbox" value="1" checked> Tampilkan</label><button class="icon-button danger" type="button" data-remove-editor aria-label="Hapus baris logo">×</button></div></template>
                    </section>
                </form>

                <?php else: ?>
                <form class="dashboard-form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?= dashboard_h(dashboard_csrf_token()) ?>"><input type="hidden" name="action" value="save_portfolio">
                    <section class="panel-card"><div class="panel-heading"><div><span class="panel-icon"><?= dashboard_icon('portfolio') ?></span><div><h2>Daftar portfolio</h2><p>Atur urutan kartu portfolio dengan tombol naik dan turun.</p></div></div><div class="heading-actions"><button class="button button-ghost" type="button" data-add-project><?= dashboard_icon('portfolio') ?> Tambah website</button><button class="button button-primary" type="submit"><?= dashboard_icon('save') ?> Simpan perubahan</button></div></div><div class="info-note"><?= dashboard_icon('info') ?><span>Gunakan tombol ↑ untuk menaikkan website dan ↓ untuk menurunkannya. Urutan ini akan dipakai di halaman utama.</span></div>
                        <div class="editor-list" id="project-list">
                        <?php foreach ($data['projects'] as $index => $project): ?><article class="project-editor" data-project-editor><div class="editor-number">0<?= $index + 1 ?></div><div class="editor-reorder" aria-label="Atur urutan <?= dashboard_h($project['name']) ?>"><button class="icon-button reorder-button" type="button" data-move-up aria-label="Naikkan <?= dashboard_h($project['name']) ?>">↑</button><button class="icon-button reorder-button" type="button" data-move-down aria-label="Turunkan <?= dashboard_h($project['name']) ?>">↓</button></div><div class="field"><label for="project-name-<?= $index ?>">Nama website</label><input id="project-name-<?= $index ?>" name="projects[<?= $index ?>][name]" type="text" value="<?= dashboard_h($project['name']) ?>" required></div><div class="field"><label for="project-url-<?= $index ?>">URL website</label><input id="project-url-<?= $index ?>" name="projects[<?= $index ?>][url]" type="url" value="<?= dashboard_h($project['url']) ?>" required></div><div class="field"><label for="project-preview-<?= $index ?>">URL preview iframe <span>(opsional)</span></label><input id="project-preview-<?= $index ?>" name="projects[<?= $index ?>][preview_url]" type="url" value="<?= dashboard_h($project['preview_url']) ?>"></div><div class="field"><label for="project-traffic-<?= $index ?>">Visitor / bulan</label><input id="project-traffic-<?= $index ?>" name="projects[<?= $index ?>][traffic]" type="number" min="0" step="1" value="<?= (int) $project['traffic'] ?>"></div><div class="field"><label for="project-image-<?= $index ?>">Path gambar fallback</label><input id="project-image-<?= $index ?>" name="projects[<?= $index ?>][image]" type="text" value="<?= dashboard_h($project['image']) ?>"><input name="project_image[<?= $index ?>]" type="file" accept="image/jpeg,image/png,image/webp,image/gif"></div><label class="check-field"><input name="projects[<?= $index ?>][visible]" type="checkbox" value="1" <?= !empty($project['visible']) ? 'checked' : '' ?>> Tampilkan di website</label><button class="icon-button danger" type="button" data-remove-editor aria-label="Hapus baris portfolio">×</button></article><?php endforeach; ?>
                        </div>
                        <template id="project-template"><article class="project-editor" data-project-editor><div class="editor-number">NEW</div><div class="editor-reorder" aria-label="Atur urutan website"><button class="icon-button reorder-button" type="button" data-move-up aria-label="Naikkan website">↑</button><button class="icon-button reorder-button" type="button" data-move-down aria-label="Turunkan website">↓</button></div><div class="field"><label>Nama website</label><input name="projects[__INDEX__][name]" type="text"></div><div class="field"><label>URL website</label><input name="projects[__INDEX__][url]" type="url"></div><div class="field"><label>URL preview iframe <span>(opsional)</span></label><input name="projects[__INDEX__][preview_url]" type="url"></div><div class="field"><label>Visitor / bulan</label><input name="projects[__INDEX__][traffic]" type="number" min="0" step="1" value="0"></div><div class="field"><label>Path gambar fallback</label><input name="projects[__INDEX__][image]" type="text"><input name="project_image[__INDEX__]" type="file" accept="image/jpeg,image/png,image/webp,image/gif"></div><label class="check-field"><input name="projects[__INDEX__][visible]" type="checkbox" value="1" checked> Tampilkan di website</label><button class="icon-button danger" type="button" data-remove-editor aria-label="Hapus baris portfolio">×</button></article></template>
                    </section>
                </form>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="dashboard.js" defer></script>
</body>
</html>

