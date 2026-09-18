<?php
declare(strict_types=1);

require_once __DIR__ . '/../dashboard/data.php';

$siteData = dashboard_load_data();
$brandSettings = $siteData['brand'];
$projects = array_values(array_filter(
    is_array($siteData['projects'] ?? null) ? $siteData['projects'] : [],
    static fn (array $project): bool => !empty($project['visible'])
));
$siteName = (string) ($brandSettings['site_name'] ?? 'Webkubator');
$logo = '../' . ltrim((string) ($brandSettings['logo'] ?? 'assets/images/logo-white.webp'), '/');
$favicon = '../' . ltrim((string) ($brandSettings['favicon'] ?? 'assets/images/favicon.png'), '/');
$pageUrl = 'https://webkubator.com/portofolio/';
$assetVersion = (string) max(
    (int) @filemtime(__DIR__ . '/../styles.css'),
    (int) @filemtime(__DIR__ . '/../script.js'),
    (int) @filemtime(__DIR__ . '/portofolio.css')
);
$itemList = [];
foreach ($projects as $position => $project) {
    $itemList[] = [
        '@type' => 'ListItem',
        'position' => $position + 1,
        'name' => (string) ($project['name'] ?? 'Website Webkubator'),
        'url' => (string) ($project['url'] ?? ''),
    ];
}
$structuredData = [
    '@context' => 'https://schema.org',
    '@type' => 'CollectionPage',
    '@id' => $pageUrl . '#webpage',
    'url' => $pageUrl,
    'name' => 'Portofolio Website Webkubator | Jasa Pembuatan Website',
    'description' => 'Kumpulan website yang dibuat Webkubator untuk UMKM, perusahaan, sekolah, dan berbagai kebutuhan bisnis.',
    'inLanguage' => 'id-ID',
    'isPartOf' => ['@id' => 'https://webkubator.com/#website'],
    'mainEntity' => [
        '@type' => 'ItemList',
        'numberOfItems' => count($itemList),
        'itemListElement' => $itemList,
    ],
];
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function wa(string $message = 'Halo Webkubator, saya ingin konsultasi website.'): string { return 'https://wa.me/6287753719307?text=' . rawurlencode($message); }
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#00032D">
    <meta name="description" content="Portofolio website Webkubator: website modern, cepat, dan responsif untuk UMKM, perusahaan, sekolah, dan bisnis Indonesia."><meta name="robots" content="index,follow,max-image-preview:large">
    <meta property="og:type" content="website"><meta property="og:locale" content="id_ID"><meta property="og:title" content="Portofolio Website Webkubator | Jasa Pembuatan Website"><meta property="og:description" content="Lihat berbagai website yang telah dibuat Webkubator untuk membantu bisnis tampil lebih profesional dan siap online."><meta property="og:url" content="<?= e($pageUrl) ?>"><meta property="og:image" content="https://webkubator.com/assets/images/hero-image-v3.webp"><link rel="canonical" href="<?= e($pageUrl) ?>"><link rel="icon" type="image/png" href="<?= e($favicon) ?>?v=<?= e($assetVersion) ?>"><link rel="apple-touch-icon" href="<?= e($favicon) ?>?v=<?= e($assetVersion) ?>">
    <title>Portofolio Website Webkubator | Jasa Pembuatan Website</title>
    <script type="application/ld+json"><?= json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="preconnect" href="https://cdn-uicons.flaticon.com"><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500;700&family=Ubuntu:wght@400;500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="../styles.css?v=<?= e($assetVersion) ?>"><link rel="stylesheet" href="portofolio.css?v=<?= e($assetVersion) ?>">
</head>
<body>
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    <header class="site-header" data-header><div class="container header-inner"><div class="header-left"><a class="brand" href="../" aria-label="<?= e($siteName) ?>, kembali ke halaman utama"><img src="<?= e($logo) ?>" alt="Logo <?= e($siteName) ?>" width="48" height="48"><span><?= e($siteName) ?></span></a></div><nav class="site-nav" id="site-nav" aria-label="Navigasi utama"><button class="menu-close" type="button" aria-label="Tutup menu navigasi">×</button><a href="../#service">Service</a><a href="../#pricing">Pricing</a><a href="../about/">About</a><a href="./" aria-current="page">Portfolio</a><a href="../#contactus">Contact Us</a></nav><a class="header-contact button button-primary" href="<?= e(wa('Halo Webkubator, saya ingin memesan website.')) ?>" target="_blank" rel="noopener"><i class="fi fi-rr-paper-plane" aria-hidden="true"></i> Hubungi Kami</a><button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="Buka menu navigasi"><span></span><span></span><span></span></button></div></header>
    <main id="main-content" class="portfolio-page">
        <section class="portfolio-page-hero" aria-labelledby="portfolio-title"><div class="container"><p class="eyebrow"><span class="eyebrow-dot" aria-hidden="true"></span>Portfolio jasa website</p><h1 id="portfolio-title" class="portfolio-page-title">Website yang <span>berbicara</span></h1><p class="portfolio-page-lead">Kumpulan website yang kami bangun untuk membantu bisnis tampil lebih profesional, dipercaya pelanggan, dan siap berkembang secara online.</p><div class="portfolio-page-actions"><a class="button button-primary" href="../#contactus"><i class="fi fi-rr-paper-plane" aria-hidden="true"></i> Konsultasi Gratis</a><a class="button button-outline" href="../#pricing"><i class="fi fi-rr-tags" aria-hidden="true"></i> Lihat Paket Website</a></div></div></section>
        <section class="section portfolio-section portfolio-page-section" aria-labelledby="portfolio-grid-title"><div class="container"><div class="section-heading portfolio-page-heading reveal"><p class="eyebrow">Karya pilihan Webkubator</p><h2 id="portfolio-grid-title">Website untuk berbagai <span>kebutuhan bisnis</span></h2><p class="portfolio-page-summary">Dari company profile hingga website layanan dan edukasi, setiap proyek dirancang dengan fokus pada tampilan, performa, dan pengalaman pengguna.</p></div><div class="portfolio-grid portfolio-page-grid"><?php foreach ($projects as $index => $project): ?><?php $projectName = (string) ($project['name'] ?? 'Website Webkubator'); $projectUrl = (string) ($project['url'] ?? '#'); $previewUrl = (string) ($project['preview_url'] ?? ''); $image = !empty($project['image']) ? '../' . ltrim((string) $project['image'], '/') : '../assets/images/hero-image-v3.webp'; ?><article class="project-card reveal reveal-delay-<?= min($index % 3, 2) ?>"><?php if ($previewUrl !== ''): ?><div class="project-image project-preview"><iframe src="<?= e($previewUrl) ?>" title="Preview hero website <?= e($projectName) ?>" loading="lazy" scrolling="no" referrerpolicy="strict-origin-when-cross-origin"></iframe><a class="project-preview-link" href="<?= e($projectUrl) ?>" target="_blank" rel="noopener">Buka Website <span aria-hidden="true">↗</span></a></div><?php else: ?><a class="project-image" href="<?= e($projectUrl) ?>" target="_blank" rel="noopener" aria-label="Buka website <?= e($projectName) ?>"><img src="<?= e($image) ?>" alt="Preview website <?= e($projectName) ?>" loading="lazy"><span class="project-arrow" aria-hidden="true">↗</span></a><?php endif; ?><div class="project-meta"><div><h3><?= e($projectName) ?></h3><p>Website profesional oleh Webkubator</p></div><a href="<?= e($projectUrl) ?>" target="_blank" rel="noopener">Visit Website <span aria-hidden="true">↗</span></a></div></article><?php endforeach; ?></div><?php if ($projects === []): ?><p class="portfolio-empty">Portofolio sedang diperbarui. Silakan kembali lagi dalam beberapa saat.</p><?php endif; ?></div></section>
        <section class="portfolio-page-cta" aria-labelledby="portfolio-cta-title"><div class="container"><div><p class="eyebrow">Siap membuat website?</p><h2 id="portfolio-cta-title">Bangun website profesional untuk <span>bisnis Anda</span></h2></div><a class="button button-primary" href="../#contactus"><i class="fi fi-rr-paper-plane" aria-hidden="true"></i> Hubungi Kami</a></div></section>
    </main>
    <footer class="site-footer" aria-label="Informasi Webkubator"><div class="container footer-inner"><div class="footer-grid"><div class="footer-brand"><a class="brand footer-brand-link" href="../" aria-label="<?= e($siteName) ?>, kembali ke halaman utama"><img src="<?= e($logo) ?>" alt="Logo <?= e($siteName) ?>" width="52" height="52"><span><?= e($siteName) ?></span></a><p class="footer-description">Webkubator menyediakan jasa pembuatan website profesional dan jasa website modern, cepat, serta responsif untuk bisnis Anda.</p><a class="button button-primary footer-cta" href="<?= e(wa('Halo Webkubator, saya ingin konsultasi gratis tentang website.')) ?>" target="_blank" rel="noopener"><i class="fi fi-rr-paper-plane" aria-hidden="true"></i> Konsultasi Gratis</a></div><nav class="footer-nav" aria-label="Navigasi footer"><h2 class="footer-heading">Jelajahi</h2><ul class="footer-links"><li><a href="../#service">Layanan Website</a></li><li><a href="../#pricing">Paket Website</a></li><li><a href="./">Portofolio</a></li><li><a href="../about/">Tentang Webkubator</a></li><li><a href="../#contactus">Kontak</a></li></ul></nav><div class="footer-contact"><h2 class="footer-heading">Hubungi Kami</h2><address class="footer-address"><a href="https://wa.me/6287753719307" target="_blank" rel="noopener"><span class="footer-contact-icon"><i class="fi fi-rr-phone-call" aria-hidden="true"></i></span><span><small>WhatsApp</small><strong>+62 877-5371-9307</strong></span></a><a href="mailto:webkubator@gmail.com"><span class="footer-contact-icon"><i class="fi fi-rr-envelope" aria-hidden="true"></i></span><span><small>Email</small><strong>webkubator@gmail.com</strong></span></a></address></div></div><div class="footer-bottom"><p>© <?= date('Y') ?> <?= e($siteName) ?>. Semua hak dilindungi.</p><div class="footer-legal"><a href="../about/">Tentang Kami</a><span aria-hidden="true">•</span><a href="./">Kembali ke atas <span aria-hidden="true">↑</span></a></div></div></div></footer>
    <a class="whatsapp-float" href="https://wa.me/6287753719307" target="_blank" rel="noopener" aria-label="Hubungi Webkubator melalui WhatsApp"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 3.5A11.8 11.8 0 0 0 12.08 0C5.55 0 .24 5.3.24 11.83c0 2.08.54 4.11 1.57 5.9L.14 23.85l6.27-1.64a11.8 11.8 0 0 0 5.66 1.44h.01c6.52 0 11.83-5.3 11.83-11.82 0-3.16-1.23-6.13-3.41-8.33ZM12.08 21.6h-.01a9.77 9.77 0 0 1-4.98-1.36l-.36-.22-3.72.98 1-3.62-.24-.37a9.77 9.77 0 0 1-1.5-5.18c0-5.38 4.38-9.76 9.77-9.76a9.7 9.7 0 0 1 6.91 2.87 9.7 9.7 0 0 1 2.86 6.92c0 5.38-4.38 9.75-9.75 9.75Zm5.35-7.3c-.29-.15-1.72-.85-1.99-.94-.27-.1-.46-.15-.65.15-.2.29-.75.94-.92 1.13-.17.2-.34.22-.63.08-.29-.15-1.24-.46-2.36-1.46a8.9 8.9 0 0 1-1.64-2.03c-.17-.29-.02-.45.13-.6.13-.13.29-.34.44-.51.15-.17.2-.29.3-.49.1-.2.05-.37-.02-.52-.08-.15-.65-1.57-.89-2.15-.23-.56-.47-.49-.65-.5h-.55c-.2 0-.52.07-.8.37-.27.29-1.04 1.02-1.04 2.49 0 1.47 1.07 2.89 1.21 3.09.15.2 2.1 3.2 5.1 4.49.71.31 1.27.5 1.7.64.71.23 1.36.2 1.87.12.57-.08 1.72-.7 1.96-1.38.24-.68.24-1.26.17-1.38-.07-.12-.27-.2-.56-.34Z"/></svg></a>
    <script src="../script.js?v=<?= e($assetVersion) ?>" defer></script>
</body>
</html>
