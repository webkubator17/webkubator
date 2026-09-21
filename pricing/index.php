<?php
declare(strict_types=1);

require_once __DIR__ . '/../dashboard/data.php';
require_once __DIR__ . '/../pricing-data.php';

$siteData = dashboard_load_data();
$homeSettings = $siteData['home'];
$brandSettings = $siteData['brand'];
$catalog = pricing_catalog();
$categoryKey = strtolower(trim((string) ($_GET['category'] ?? 'landing-page')));
$category = $catalog[$categoryKey] ?? $catalog['landing-page'];
$categoryKey = array_search($category, $catalog, true) ?: 'landing-page';
$requestedPlan = strtolower(trim((string) ($_GET['plan'] ?? '')));
$plan = $category['plans'][0];
foreach ($category['plans'] as $candidate) {
    if (pricing_plan_slug($candidate['name']) === $requestedPlan) {
        $plan = $candidate;
        break;
    }
}
$planIndex = array_search($plan, $category['plans'], true);
$planIndex = $planIndex === false ? 0 : (int) $planIndex;
$freeDomain = pricing_domain_from_plan($plan);
$basePrice = (int) str_replace('.', '', (string) $plan['price']);
$renewalPrice = (int) preg_replace('/\D+/', '', (string) $plan['renewal']);
$assetVersion = (string) max(
    (int) @filemtime(__DIR__ . '/pricing.css'),
    (int) @filemtime(__DIR__ . '/pricing.js'),
    (int) @filemtime(__DIR__ . '/../styles.css')
);

function pricing_page_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function pricing_page_wa(string $message): string
{
    return 'https://wa.me/6287753719307?text=' . rawurlencode($message);
}

$siteName = (string) ($brandSettings['site_name'] ?? 'Webkubator');
$logo = '../' . ltrim((string) ($brandSettings['logo'] ?? 'assets/images/logo-white.webp'), '/');
$initialOrderMessage = sprintf(
    'Halo Webkubator, saya ingin memesan %s paket %s. Durasi: 1 tahun. Domain: belum diisi. Total: Rp%s.',
    $category['label'],
    $plan['name'],
    number_format($basePrice, 0, ',', '.')
);
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#00032d">
    <meta name="description" content="Rincian paket <?= pricing_page_e($category['label']) ?> <?= pricing_page_e($plan['name']) ?> dari Webkubator, lengkap dengan pilihan durasi, domain, dan pemesanan WhatsApp.">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="https://webkubator.com/pricing/">
    <link rel="icon" type="image/png" href="../assets/images/favicon.png?v=<?= pricing_page_e($assetVersion) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn-uicons.flaticon.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Roboto:wght@400;500;700&family=Ubuntu:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="../styles.css?v=<?= pricing_page_e($assetVersion) ?>">
    <link rel="stylesheet" href="pricing.css?v=<?= pricing_page_e($assetVersion) ?>">
    <title><?= pricing_page_e($category['label']) ?> <?= pricing_page_e($plan['name']) ?> | <?= pricing_page_e($siteName) ?></title>
</head>
<body class="pricing-detail-body">
    <a class="skip-link" href="#pricing-detail">Lewati ke konfigurasi paket</a>
    <header class="site-header" data-header>
        <div class="container header-inner">
            <div class="header-left">
                <a class="brand" href="../" aria-label="<?= pricing_page_e($siteName) ?>, kembali ke website utama">
                    <img src="<?= pricing_page_e($logo) ?>" alt="Logo <?= pricing_page_e($siteName) ?>" width="48" height="48">
                    <span><?= pricing_page_e($siteName) ?></span>
                </a>
            </div>
            <nav class="site-nav" id="site-nav" aria-label="Navigasi utama">
                <button class="menu-close" type="button" aria-label="Tutup menu navigasi">×</button>
                <a href="../#service">Service</a>
                <a href="../#pricing">Pricing</a>
                <a href="../about/">About</a>
                <a href="../portofolio/">Portfolio</a>
                <a href="../#contactus">Contact Us</a>
            </nav>
            <a class="header-contact button button-primary" href="<?= pricing_page_e(pricing_page_wa('Halo Webkubator, saya ingin konsultasi paket website.')) ?>" target="_blank" rel="noopener">
                <i class="fi fi-rr-paper-plane" aria-hidden="true"></i> Hubungi Kami
            </a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="Buka menu navigasi"><span></span><span></span><span></span></button>
        </div>
    </header>

    <main id="pricing-detail" class="pricing-detail-page" data-pricing-detail data-category="<?= pricing_page_e($categoryKey) ?>" data-plan="<?= pricing_page_e(pricing_plan_slug($plan['name'])) ?>" data-base-price="<?= pricing_page_e((string) $basePrice) ?>" data-renewal-price="<?= pricing_page_e((string) $renewalPrice) ?>" data-free-domain="<?= pricing_page_e($freeDomain) ?>" data-wa-number="6287753719307">
        <section class="pricing-detail-intro">
            <div class="container">
                <a class="pricing-back-link" href="../#pricing"><i class="fi fi-rr-arrow-left" aria-hidden="true"></i> Kembali ke daftar paket</a>
                <p class="eyebrow"><span class="eyebrow-dot"></span> Konfigurasi paket website</p>
                <h1><?= pricing_page_e($category['label']) ?> <span><?= pricing_page_e($plan['name']) ?></span></h1>
                <p class="pricing-detail-lead">Atur durasi, cek domain, lalu kirim rincian pesanan langsung ke WhatsApp Webkubator.</p>
            </div>
        </section>

        <section class="section pricing-detail-section">
            <div class="container pricing-checkout-layout">
                <section class="pricing-config-card" aria-labelledby="config-title">
                    <div class="pricing-card-heading">
                        <span class="pricing-heading-icon"><i class="fi fi-rr-settings-sliders" aria-hidden="true"></i></span>
                        <div>
                            <p class="pricing-card-kicker">Paket terpilih</p>
                            <h2 id="config-title"><?= pricing_page_e($category['label']) ?> — <?= pricing_page_e($plan['name']) ?></h2>
                        </div>
                    </div>

                    <div class="pricing-config-block">
                        <div class="pricing-block-heading">
                            <div>
                                <p class="pricing-card-kicker">Langkah 1</p>
                                <h3>Durasi website</h3>
                            </div>
                            <span class="pricing-inline-note">Hemat hingga 30%</span>
                        </div>
                        <div class="duration-options" role="group" aria-label="Pilih durasi website">
                            <button class="duration-option is-active" type="button" data-duration="1" aria-pressed="true"><strong>1 Tahun</strong><span>Harga normal</span></button>
                            <button class="duration-option" type="button" data-duration="2" aria-pressed="false"><strong>2 Tahun</strong><span>Diskon 20%</span></button>
                            <button class="duration-option" type="button" data-duration="3" aria-pressed="false"><strong>3 Tahun</strong><span>Diskon 30%</span></button>
                        </div>
                        <div class="duration-breakdown" aria-live="polite">
                            <span><i class="fi fi-rr-calendar" aria-hidden="true"></i> Tahun pertama Rp<?= pricing_page_e(number_format($basePrice, 0, ',', '.')) ?></span>
                            <span><i class="fi fi-rr-refresh" aria-hidden="true"></i> Tahun berikutnya Rp<?= pricing_page_e(number_format($renewalPrice, 0, ',', '.')) ?>/tahun</span>
                        </div>
                    </div>

                    <div class="pricing-config-block domain-block">
                        <div class="pricing-block-heading">
                            <div>
                                <p class="pricing-card-kicker">Langkah 2</p>
                                <h3>Pesan domain</h3>
                            </div>
                            <span class="domain-free-pill"><i class="fi fi-rr-badge-check" aria-hidden="true"></i> Gratis <?= pricing_page_e($freeDomain) ?></span>
                        </div>
                        <label for="domain-input">Nama domain yang ingin dipesan</label>
                        <div class="domain-input-row">
                            <span class="domain-prefix" aria-hidden="true">https://</span>
                            <input id="domain-input" type="text" inputmode="url" autocomplete="url" spellcheck="false" placeholder="namabisnis.com" aria-describedby="domain-help domain-status">
                            <button class="domain-check-button" id="domain-check" type="button"><i class="fi fi-rr-search" aria-hidden="true"></i><span>Periksa</span></button>
                        </div>
                        <p class="domain-help" id="domain-help">Masukkan tanpa https:// dan tanpa www. Ketersediaan diperiksa melalui registry domain.</p>
                        <p class="domain-status" id="domain-status" role="status" aria-live="polite"><i class="fi fi-rr-info" aria-hidden="true"></i><span>Isi domain untuk mulai memeriksa.</span></p>
                    </div>

                    <div class="pricing-benefit-box">
                        <p class="pricing-card-kicker">Yang Anda dapatkan</p>
                        <ul>
                            <li><i class="fi fi-rr-globe" aria-hidden="true"></i><span>Free domain <strong><?= pricing_page_e($freeDomain) ?></strong> selama 1 tahun</span></li>
                            <li><i class="fi fi-rr-server" aria-hidden="true"></i><span>Free hosting sesuai kapasitas paket</span></li>
                            <li><i class="fi fi-rr-shield-check" aria-hidden="true"></i><span>SSL/HTTPS dan dukungan setelah website online</span></li>
                        </ul>
                    </div>
                </section>

                <aside class="order-summary-card" aria-labelledby="summary-title">
                    <div class="summary-topline"><span class="summary-icon"><i class="fi fi-rr-receipt" aria-hidden="true"></i></span><p>Ringkasan pesanan</p></div>
                    <h2 id="summary-title"><?= pricing_page_e($plan['name']) ?></h2>
                    <p class="summary-category"><?= pricing_page_e($category['label']) ?></p>
                    <dl class="summary-list">
                        <div><dt>Durasi</dt><dd id="summary-duration">1 tahun</dd></div>
                        <div><dt>Jasa pembuatan website</dt><dd id="summary-service">Rp<?= pricing_page_e(number_format($basePrice, 0, ',', '.')) ?></dd></div>
                        <div><dt>Free domain <?= pricing_page_e($freeDomain) ?></dt><dd class="summary-free">Rp0</dd></div>
                        <div><dt>Free hosting</dt><dd class="summary-free">Rp0</dd></div>
                        <div class="summary-discount-row" id="summary-discount-row" hidden><dt>Diskon durasi</dt><dd id="summary-discount">-Rp0</dd></div>
                    </dl>
                    <div class="summary-total"><span>Total tanpa pajak</span><strong id="summary-total">Rp<?= pricing_page_e(number_format($basePrice, 0, ',', '.')) ?></strong></div>
                    <div class="summary-domain"><span>Domain</span><strong id="summary-domain">Belum diisi</strong></div>
                    <a class="button button-primary summary-submit is-disabled" id="order-whatsapp" href="<?= pricing_page_e(pricing_page_wa($initialOrderMessage)) ?>" target="_blank" rel="noopener" aria-disabled="true"><i class="fi fi-rr-paper-plane" aria-hidden="true"></i> Pesan Sekarang</a>
                    <p class="summary-footnote"><i class="fi fi-rr-lock" aria-hidden="true"></i> Rincian aman dikirim ke WhatsApp untuk konsultasi dan konfirmasi akhir.</p>
                </aside>
            </div>
        </section>
    </main>

    <footer class="site-footer pricing-footer" aria-label="Informasi Webkubator">
        <div class="container footer-bottom"><p>© <?= date('Y') ?> <?= pricing_page_e($siteName) ?>. Semua hak dilindungi.</p><div class="footer-legal"><a href="../">Kembali ke beranda</a><span aria-hidden="true">•</span><a href="../#contactus">Kontak</a></div></div>
    </footer>
    <script src="../script.js?v=<?= pricing_page_e($assetVersion) ?>" defer></script>
    <script src="pricing.js?v=<?= pricing_page_e($assetVersion) ?>" defer></script>
</body>
</html>
