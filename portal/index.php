<?php
declare(strict_types=1);

require_once __DIR__ . '/../dashboard/data.php';

$siteData = dashboard_load_data();
$homeSettings = $siteData['home'];
$brandSettings = $siteData['brand'];
$siteName = (string) ($brandSettings['site_name'] ?? 'Webkubator');
$logo = '../' . ltrim((string) ($brandSettings['logo'] ?? 'assets/images/logo-white.webp'), '/');
$heroImage = '../' . ltrim((string) ($homeSettings['hero_image'] ?? 'assets/images/hero-image-v3.webp'), '/');
$heroAlt = (string) ($homeSettings['hero_alt'] ?? 'Jasa pembuatan website profesional Webkubator.');
$assetVersion = (string) max(
    (int) @filemtime(__DIR__ . '/portal.css'),
    (int) @filemtime(__DIR__ . '/portal.js'),
    (int) @filemtime(__DIR__ . '/../styles.css')
);
$whatsappUrl = 'https://wa.me/6287753719307?text=' . rawurlencode('Halo Webkubator, saya ingin konsultasi gratis tentang website.');
$links = [
    ['label' => 'Website', 'icon' => 'fi-rr-globe', 'href' => '../', 'external' => false],
    ['label' => 'Whatsapp', 'icon' => 'fi-rr-paper-plane', 'href' => $whatsappUrl, 'external' => true],
    ['label' => 'Portofolio', 'icon' => 'fi-rr-briefcase', 'href' => '../#portfolio', 'external' => false],
    ['label' => 'Pricelist Landing Page', 'icon' => 'fi-rr-browser', 'href' => '../?pricing=landing-page#pricing', 'external' => false],
    ['label' => 'Pricelist Company Profile', 'icon' => 'fi-rr-building', 'href' => '../?pricing=company-profile#pricing', 'external' => false],
    ['label' => 'Pricelist Toko Online', 'icon' => 'fi-rr-shopping-bag', 'href' => '../?pricing=toko-online#pricing', 'external' => false],
    ['label' => 'Pricelist E-commerce', 'icon' => 'fi-rr-shopping-cart', 'href' => '../?pricing=e-commerce#pricing', 'external' => false],
    ['label' => 'Pricelist Link Bio', 'icon' => 'fi-rr-link', 'href' => '../?pricing=link-bio#pricing', 'external' => false],
];
function portal_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#00032d">
    <meta name="description" content="Semua link Webkubator: website, WhatsApp, portofolio, dan pricelist jasa pembuatan website.">
    <meta name="robots" content="index,follow">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Link Bio Webkubator | Jasa Pembuatan Website">
    <meta property="og:description" content="Akses website, portofolio, WhatsApp, dan pricelist Webkubator dari satu halaman.">
    <meta property="og:url" content="https://webkubator.com/portal/">
    <meta property="og:image" content="https://webkubator.com/<?= portal_e(ltrim((string) ($homeSettings['hero_image'] ?? 'assets/images/hero-image-v3.webp'), '/')) ?>">
    <link rel="canonical" href="https://webkubator.com/portal/">
    <link rel="icon" type="image/png" href="../assets/images/favicon.png?v=<?= portal_e($assetVersion) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn-uicons.flaticon.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="portal.css?v=<?= portal_e($assetVersion) ?>">
    <title>Link Bio <?= portal_e($siteName) ?> | Jasa Pembuatan Website</title>
</head>
<body>
    <main class="portal-page">
        <header class="portal-header">
            <a class="portal-brand" href="../" aria-label="<?= portal_e($siteName) ?>, kembali ke website utama">
                <span class="portal-logo-shell"><img src="<?= portal_e($logo) ?>" alt="Logo <?= portal_e($siteName) ?>" width="88" height="88"></span>
                <span class="portal-brand-name"><?= portal_e($siteName) ?></span>
            </a>
            <p class="portal-kicker">Jasa Pembuatan Website Profesional</p>
            <h1>Bangun kehadiran online yang <span>lebih kuat.</span></h1>
            <p class="portal-intro">Pilih link yang ingin Anda buka.</p>
        </header>

        <figure class="portal-hero">
            <span class="portal-hero-glow" aria-hidden="true"></span>
            <img src="<?= portal_e($heroImage) ?>" alt="<?= portal_e($heroAlt) ?>" width="1373" height="1145" fetchpriority="high">
        </figure>

        <nav class="portal-links" aria-label="Link Webkubator">
            <?php foreach ($links as $index => $link): ?>
                <a class="portal-link" href="<?= portal_e($link['href']) ?>" style="--delay: <?= portal_e((string) ($index * 55)) ?>ms"<?= $link['external'] ? ' target="_blank" rel="noopener"' : '' ?><?= $link['label'] === 'Pricelist Link Bio' ? ' aria-current="page"' : '' ?>>
                    <span class="portal-link-icon"><i class="<?= portal_e($link['icon']) ?>" aria-hidden="true"></i></span>
                    <span class="portal-link-label"><?= portal_e($link['label']) ?></span>
                    <span class="portal-link-arrow" aria-hidden="true">↗</span>
                </a>
            <?php endforeach; ?>
        </nav>

        <footer class="portal-footer">
            <span>© <?= date('Y') ?> <?= portal_e($siteName) ?></span>
            <span aria-hidden="true">•</span>
            <a href="../">Kembali ke website utama</a>
        </footer>
    </main>
    <script src="portal.js?v=<?= portal_e($assetVersion) ?>" defer></script>
</body>
</html>

