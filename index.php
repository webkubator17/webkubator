<?php
require_once __DIR__ . '/dashboard/data.php';
$siteData = dashboard_load_data();
$homeSettings = $siteData['home'];
$brandSettings = $siteData['brand'];
$siteUrl = 'https://webkubator.com/';
$logoUrl = $siteUrl . ltrim((string) $brandSettings['logo'], '/');
$heroImageUrl = $siteUrl . ltrim((string) $homeSettings['hero_image'], '/');
$structuredData = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Organization',
            '@id' => $siteUrl . '#organization',
            'name' => $brandSettings['site_name'],
            'url' => $siteUrl,
            'logo' => $logoUrl,
            'description' => 'Webkubator menyediakan jasa pembuatan website profesional dan jasa website cepat untuk UMKM serta perusahaan.',
            'email' => 'webkubator@gmail.com',
            'telephone' => '+6287753719307',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'sales',
                'telephone' => '+6287753719307',
                'availableLanguage' => ['Indonesian'],
            ],
        ],
        [
            '@type' => 'WebSite',
            '@id' => $siteUrl . '#website',
            'url' => $siteUrl,
            'name' => $brandSettings['site_name'],
            'inLanguage' => 'id-ID',
            'publisher' => ['@id' => $siteUrl . '#organization'],
        ],
        [
            '@type' => 'WebPage',
            '@id' => $siteUrl . '#webpage',
            'url' => $siteUrl,
            'name' => 'Jasa Pembuatan Website Profesional | ' . $brandSettings['site_name'],
            'description' => 'Jasa pembuatan website profesional dan jasa website untuk UMKM dan perusahaan. Modern, cepat, responsif, dan siap online dalam 5 hari.',
            'inLanguage' => 'id-ID',
            'isPartOf' => ['@id' => $siteUrl . '#website'],
            'about' => ['@id' => $siteUrl . '#organization'],
        ],
        [
            '@type' => 'Service',
            '@id' => $siteUrl . '#website-service',
            'name' => 'Jasa Pembuatan Website Profesional',
            'serviceType' => 'Jasa pembuatan website',
            'description' => 'Jasa website modern, cepat, responsif, dan siap online untuk UMKM serta perusahaan di Indonesia.',
            'url' => $siteUrl,
            'provider' => ['@id' => $siteUrl . '#organization'],
            'areaServed' => ['@type' => 'Country', 'name' => 'Indonesia'],
        ],
    ],
];
$services = [
    ['image' => 'service-website.webp', 'title' => 'Pembuatan Website', 'text' => 'Kami membuat website profesional yang responsif dengan desain terbaik.'],
    ['image' => 'service-seo.webp', 'title' => 'Optimasi SEO', 'text' => 'Optimasi SEO untuk meningkatkan peringkat dan visibilitas website Anda di mesin pencari.'],
    ['image' => 'service-hosting.webp', 'title' => 'Layanan Hosting', 'text' => 'Webkubator menyediakan hosting cepat dan handal untuk memastikan website Anda selalu online.'],
    ['image' => 'service-maintenance.webp', 'title' => 'Pemeliharaan Website', 'text' => 'Layanan pemeliharaan untuk menjaga performa dan keamanan website Anda.'],
];
$partners = $siteData['partners'];
$projects = array_values(array_filter($siteData['projects'], static fn (array $project): bool => !empty($project['visible'])));
require_once __DIR__ . '/pricing-data.php';
$pricingCatalog = pricing_catalog();
$defaultPricingKey = 'landing-page';
$defaultPricing = $pricingCatalog[$defaultPricingKey];
$testimonials = [
    ['image' => 'avatar-hendry.webp', 'name' => 'Hendry', 'company' => 'Nusa Jaya Steel', 'text' => 'Pengerjaan nya bagus dan cepet, admin nya juga sopan dan baik 🙏'],
    ['image' => 'avatar-ldoats.webp', 'name' => 'L.doats', 'company' => 'Warmie Tengah', 'text' => 'Keren bangettt, harganya murah tapi bagus. Adminnya juga okk, bisa tanya ini itu dan ngasih rekomendasi buat aku yang gak ngerti web'],
    ['image' => 'avatar-alphascent.webp', 'name' => 'Alphascent', 'company' => 'Alphascent Official', 'text' => 'Thank you. Sangat membatu untuk aku yang males ribet ini hehehe'],
];
$reasons = [
    ['title' => '5 Hari Selesai', 'text' => 'Website bisnis Anda siap online dalam 5 hari dengan proses yang jelas, cepat, dan terarah.', 'icon' => 'clock'],
    ['title' => 'UI/UX Modern', 'text' => 'Tampilan modern dan mudah digunakan untuk memberi pengalaman terbaik bagi pengunjung di setiap perangkat.', 'icon' => 'palette'],
    ['title' => 'Cepat dan Responsif', 'text' => 'Website dioptimalkan agar cepat dibuka dan nyaman digunakan di desktop, tablet, maupun mobile.', 'icon' => 'bolt'],
    ['title' => 'Full Garansi', 'text' => 'Kami mendampingi dan membantu memastikan website tetap berjalan baik setelah selesai dibuat.', 'icon' => 'shield'],
];
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function wa(string $message = 'Halo Webkubator, saya ingin konsultasi website.'): string { return 'https://wa.me/6287753719307?text=' . rawurlencode($message); }
function icon(string $name): string
{
    $paths = [
        'coins' => '<path d="M256 0C150 0 64 28.7 64 64s86 64 192 64 192-28.7 192-64S362 0 256 0Zm-192 128v48c0 35.3 86 64 192 64s192-28.7 192-64v-48c-41.3 34-116.9 51.6-192 51.6S105.3 162 64 128Zm0 112v48c0 35.3 86 64 192 64s192-28.7 192-64v-48c-41.3 34-116.9 51.6-192 51.6S105.3 274 64 240Zm0 112v48c0 35.3 86 64 192 64s192-28.7 192-64v-48c-41.3 34-116.9 51.6-192 51.6S105.3 386 64 352Z"/>',
        'server' => '<path d="M480 32H32C14.3 32 0 46.3 0 64v64c0 17.7 14.3 32 32 32h448c17.7 0 32-14.3 32-32V64c0-17.7-14.3-32-32-32ZM96 120a24 24 0 1 1 0-48 24 24 0 0 1 0 48Zm64 0a24 24 0 1 1 0-48 24 24 0 0 1 0 48Zm320 72H32c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32h448c17.7 0 32-14.3 32-32v-64c0-17.7-14.3-32-32-32ZM96 280a24 24 0 1 1 0-48 24 24 0 0 1 0 48Zm64 0a24 24 0 1 1 0-48 24 24 0 0 1 0 48Zm320 72H32c-17.7 0-32 14.3-32 32v64c0 17.7 14.3 32 32 32h448c17.7 0 32-14.3 32-32v-64c0-17.7-14.3-32-32-32ZM96 440a24 24 0 1 1 0-48 24 24 0 0 1 0 48Zm64 0a24 24 0 1 1 0-48 24 24 0 0 1 0 48Z"/>',
        'headset' => '<path d="M256 0C113.2 0 4.6 118.8 0 256v16c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16v-16c0-105.9 86.1-192 192-192s192 86.1 192 192h-.1c.1 2.4.1 165.7.1 165.7 0 14.5-11.8 26.3-26.3 26.3H320c0-26.5-21.5-48-48-48h-32c-26.5 0-48 21.5-48 48s21.5 48 48 48h181.7c49.9 0 90.3-40.4 90.3-90.3V256C507.4 118.8 398.8 0 256 0Zm-96 176h-16c-35.3 0-64 28.7-64 64v48c0 35.3 28.7 64 64 64h16c17.7 0 32-14.3 32-32V208c0-17.7-14.3-32-32-32Zm208 0h-16c-17.7 0-32 14.3-32 32v112c0 17.7 14.3 32 32 32h16c35.3 0 64-28.7 64-64v-48c0-35.3-28.7-64-64-64Z"/>',
        'clock' => '<path d="M256 0a256 256 0 1 0 0 512 256 256 0 1 0 0-512Zm0 464a208 208 0 1 1 0-416 208 208 0 0 1 0 416Zm24-208V128h-48v176l128 76 24-41-104-63Z"/>',
        'shield' => '<path d="M256 0 32 80v128c0 132 95 255 224 304 129-49 224-172 224-304V80L256 0Zm0 464C159 420 80 318 80 208v-94l176-63 176 63v94c0 110-79 212-176 256Z"/>',
        'bolt' => '<path d="M288 0 48 304h160l-32 208 240-304H256L288 0Z"/>',
        'palette' => '<path d="M256 0C115 0 0 115 0 256s115 256 256 256h32c18 0 32-14 32-32 0-14-9-26-21-30-8-3-11-14-7-22 5-9 15-14 25-14h51c82 0 148-66 148-148C516 119 399 0 256 0Zm0 464c-115 0-208-93-208-208S141 48 256 48s212 95 212 218c0 56-44 100-100 100h-51c-28 0-54 14-68 38-10 17-8 38 4 54h3ZM160 208a32 32 0 1 0 0-64 32 32 0 0 0 0 64Zm96-96a32 32 0 1 0 0-64 32 32 0 0 0 0 64Zm96 96a32 32 0 1 0 0-64 32 32 0 0 0 0 64ZM160 352a32 32 0 1 0 0-64 32 32 0 0 0 0 64Z"/>',
    ];
    return '<svg viewBox="0 0 512 512" aria-hidden="true" focusable="false">' . ($paths[$name] ?? '') . '</svg>';
}
$assetVersion = (string) max((int) @filemtime(__DIR__ . '/styles.css'), (int) @filemtime(__DIR__ . '/script.js'), (int) @filemtime(__DIR__ . '/pricing/pricing.css'));
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#00032D">
    <meta name="description" content="Jasa pembuatan website profesional dan jasa website untuk UMKM dan perusahaan. Webkubator membuat website modern, cepat, responsif, dan siap online dalam 5 hari."><meta name="robots" content="index,follow,max-image-preview:large"><meta property="og:type" content="website"><meta property="og:locale" content="id_ID"><meta property="og:title" content="Jasa Pembuatan Website Profesional | <?= e($brandSettings['site_name']) ?>"><meta property="og:description" content="Jasa pembuatan website profesional dan jasa website untuk UMKM dan perusahaan. Modern, cepat, responsif, dan siap online dalam 5 hari."><meta property="og:url" content="<?= e($siteUrl) ?>"><meta property="og:image" content="<?= e($heroImageUrl) ?>"><meta property="og:image:alt" content="<?= e($homeSettings['hero_alt']) ?>"><link rel="canonical" href="<?= e($siteUrl) ?>"><link rel="icon" type="image/png" href="<?= e($brandSettings['favicon']) ?>?v=<?= e($assetVersion) ?>"><link rel="apple-touch-icon" href="<?= e($brandSettings['favicon']) ?>?v=<?= e($assetVersion) ?>">
    <title>Jasa Pembuatan Website Profesional | <?= e($brandSettings['site_name']) ?></title>
    <script type="application/ld+json"><?= json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="preconnect" href="https://cdn-uicons.flaticon.com"><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500;700&family=Ubuntu:wght@400;500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="styles.css?v=<?= e($assetVersion) ?>">
    <link rel="stylesheet" href="pricing/pricing.css?v=<?= e($assetVersion) ?>">
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js" defer></script>
</head>
<body>
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    <header class="site-header" data-header><div class="container header-inner"><div class="header-left"><a class="brand" href="#top" aria-label="<?= e($brandSettings['site_name']) ?>, kembali ke halaman utama"><img src="<?= e($brandSettings['logo']) ?>" alt="Logo <?= e($brandSettings['site_name']) ?>" width="48" height="48"><span><?= e($brandSettings['site_name']) ?></span></a></div><nav class="site-nav" id="site-nav" aria-label="Navigasi utama"><button class="menu-close" type="button" aria-label="Tutup menu navigasi">×</button><a href="#service">Service</a><a href="#pricing">Pricing</a><a href="/about/">About</a><a href="/portofolio/">Portfolio</a><a href="#contactus">Contact Us</a></nav><a class="header-contact button button-primary" href="<?= e(wa('Halo Webkubator, saya ingin memesan website.')) ?>" target="_blank" rel="noopener"><i class="fi fi-rr-paper-plane" aria-hidden="true"></i> Hubungi Kami</a><button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="Buka menu navigasi"><span></span><span></span><span></span></button></div></header>
    <main id="main-content">
        <section class="hero section" id="top"><div class="container hero-grid"><div class="hero-copy reveal"><p class="eyebrow"><span class="eyebrow-dot"></span> <?= e($homeSettings['eyebrow']) ?></p><h1><?= e($homeSettings['title']) ?></h1><p class="hero-lead"><?= e($homeSettings['description']) ?></p><div class="hero-actions"><a class="button button-outline" href="#pricing"><i class="fi fi-rr-briefcase" aria-hidden="true"></i><span><?= e($homeSettings['cta_primary']) ?></span></a><a class="button button-primary" href="<?= e(wa('Halo Webkubator, saya ingin konsultasi gratis tentang website.')) ?>" target="_blank" rel="noopener"><i class="fi fi-rr-paper-plane" aria-hidden="true"></i><span><?= e($homeSettings['cta_secondary']) ?></span></a></div></div><div class="hero-visual hero-visual-composite reveal reveal-delay-1"><figure class="hero-composite-frame"><img src="<?= e($homeSettings['hero_image']) ?>" alt="<?= e($homeSettings['hero_alt']) ?>" width="1373" height="1145" fetchpriority="high"><figcaption class="sr-only"><?= e($homeSettings['hero_alt']) ?></figcaption></figure></div></div></section>
        <section class="trust-strip" aria-labelledby="trust-title"><div class="container trust-heading"><h2 id="trust-title">Dipercaya <strong>Ratusan</strong> Klien</h2></div><div class="logo-marquee" data-logo-marquee role="region" aria-label="Logo bisnis dan mitra Webkubator"><div class="logo-marquee-track" data-logo-marquee-track><div class="logo-marquee-group"><?php foreach ($partners as $partner): ?><?php if (!empty($partner['visible'])): ?><div class="logo-marquee-item<?= strtolower(trim((string) ($partner['alt'] ?? ''))) === 'migranpreneur' ? ' logo-marquee-item-dark' : '' ?>"><img src="<?= e($partner['image']) ?>" alt="<?= e($partner['alt']) ?>" width="190" height="80" loading="lazy" draggable="false"></div><?php endif; ?><?php endforeach; ?></div><div class="logo-marquee-group" aria-hidden="true"><?php foreach ($partners as $partner): ?><?php if (!empty($partner['visible'])): ?><div class="logo-marquee-item<?= strtolower(trim((string) ($partner['alt'] ?? ''))) === 'migranpreneur' ? ' logo-marquee-item-dark' : '' ?>"><img src="<?= e($partner['image']) ?>" alt="" width="190" height="80" loading="lazy" draggable="false"></div><?php endif; ?><?php endforeach; ?></div></div></div></section>
        <section class="section service-section" id="service"><div class="container"><div class="section-heading reveal"><p class="eyebrow">Jasa Website untuk Bisnis</p><h2>Layanan jasa pembuatan website <span>profesional</span></h2></div><div class="service-grid"><?php foreach ($services as $index => $service): ?><article class="service-card reveal reveal-delay-<?= min($index, 3) ?>"><div class="service-image"><img src="assets/images/<?= e($service['image']) ?>" alt="" width="300" height="300" loading="lazy"></div><h3><?= e($service['title']) ?></h3><p><?= e($service['text']) ?></p></article><?php endforeach; ?></div></div></section>
        <section class="section portfolio-section" id="portfolio"><div class="container"><div class="section-heading reveal"><p class="eyebrow">Portfolio jasa website</p><h2>Website yang <span>berbicara</span></h2></div><div class="portfolio-grid"><?php foreach ($projects as $index => $project): ?><article class="project-card reveal <?= $index % 2 ? 'project-offset' : '' ?>"><a class="project-image" href="<?= e($project['url']) ?>" target="_blank" rel="noopener"><img src="<?= e($project['image']) ?>" alt="Preview hero section website <?= e($project['name']) ?>" width="1280" height="753" loading="lazy"><span class="project-arrow" aria-hidden="true">↗</span></a><div class="project-meta"><h3><?= e($project['name']) ?></h3><a href="<?= e($project['url']) ?>" target="_blank" rel="noopener">Visit Website <span aria-hidden="true">↗</span></a></div></article><?php endforeach; ?></div></div></section>
        <section class="section pricing-section" id="pricing">
            <span id="pricelist" class="anchor-target"></span>
            <div class="container">
                <div class="section-heading reveal"><p class="eyebrow">Harga jasa website</p><h2>Pilih paket jasa pembuatan <span>website</span></h2></div>
                <div class="pricing-selector reveal"><span class="pricing-selector-label">Jenis website</span><div class="pricing-type-list" role="group" aria-label="Pilih jenis website"><?php foreach ($pricingCatalog as $key => $category): ?><button class="pricing-type-option <?= $key === $defaultPricingKey ? 'is-active' : '' ?>" type="button" data-pricing-option="<?= e($key) ?>" aria-pressed="<?= $key === $defaultPricingKey ? 'true' : 'false' ?>"><?= e($category['label']) ?></button><?php endforeach; ?></div></div>
                <div class="pricing-grid" id="pricing-grid" data-pricing-grid>
                    <?php foreach ($defaultPricing['plans'] as $index => $plan): ?>
                        <?php $planFeatures = (array) $plan['features']; ?>
                        <article class="price-card <?= $index === 1 ? 'price-card-featured' : '' ?> reveal reveal-delay-<?= min($index, 2) ?>">
                            <div class="price-header"><h3><?= e($plan['name']) ?></h3></div>
                            <strong class="price">Rp<?= e($plan['price']) ?></strong>
                            <p class="renewal"><?= e($plan['renewal']) ?></p>
                            <ul>
                                <?php foreach (array_slice($planFeatures, 0, 5) as $feature): ?><li><i class="fi <?= e(pricing_feature_icon($feature)) ?>" aria-hidden="true"></i><span><?= e($feature) ?></span></li><?php endforeach; ?>
                                <?php if (count($planFeatures) > 5): ?><li class="price-more-wrap"><details class="price-more"><summary><i class="fi fi-rr-plus" aria-hidden="true"></i><span>Lihat lebih banyak</span></summary><ul><?php foreach (array_slice($planFeatures, 5) as $feature): ?><li><i class="fi <?= e(pricing_feature_icon($feature)) ?>" aria-hidden="true"></i><span><?= e($feature) ?></span></li><?php endforeach; ?></ul></details></li><?php endif; ?>
                            </ul>
                            <a class="button <?= $index === 1 ? 'button-primary' : 'button-outline' ?>" href="/pricing/?category=<?= e($defaultPricingKey) ?>&amp;plan=<?= e(pricing_plan_slug($plan['name'])) ?>">Pilih Paket <i class="fi fi-rr-arrow-right" aria-hidden="true"></i></a>
                        </article>
                    <?php endforeach; ?>
                </div>
                <script id="pricing-catalog" type="application/json"><?= json_encode($pricingCatalog, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
            </div>
        </section>
        <section class="section testimonial-section"><div class="container"><div class="section-heading reveal"><p class="eyebrow">Cerita dari klien</p><h2>Testi<span>moni</span></h2></div><div class="testimonial-grid"><?php foreach ($testimonials as $index => $testimonial): ?><article class="testimonial-card reveal reveal-delay-<?= min($index, 2) ?>"><div class="stars" aria-label="5 dari 5 bintang">★★★★★</div><p>“<?= e($testimonial['text']) ?>”</p><strong><?= e($testimonial['name']) ?></strong><span><?= e($testimonial['company']) ?></span></article><?php endforeach; ?></div></div></section>
        <section class="section reasons-section"><div class="container reasons-grid"><div class="reasons-copy reveal"><p class="eyebrow">Kenapa Webkubator?</p><h2>Kenapa memilih <span>kami</span></h2><p>Bangun kehadiran online yang dapat dipercaya dengan partner yang memahami kebutuhan bisnis Anda.</p><div class="tech-visual reasons-lottie" aria-label="Animasi visual dari website WordPress lama"><lottie-player src="https://assets8.lottiefiles.com/private_files/lf30_qwz0gbhf.json" background="transparent" speed="1" loop autoplay></lottie-player></div></div><div class="reason-list"><?php foreach ($reasons as $index => $reason): ?><article class="reason-card reveal reveal-delay-<?= min($index, 2) ?>"><span class="reason-icon"><?= icon($reason['icon']) ?></span><div><h3><?= e($reason['title']) ?></h3><p><?= e($reason['text']) ?></p></div></article><?php endforeach; ?></div></div></section>
        <section class="section contact-section" id="contactus"><div class="container contact-shell reveal"><div><p class="eyebrow">Siap memulai?</p><h2>Hubungi <span>kami</span></h2><p>Diskusikan kebutuhan website Anda bersama Webkubator.</p></div><div class="contact-list"><a class="contact-card" href="https://wa.me/6287753719307" target="_blank" rel="noopener"><span class="contact-icon">☎</span><span><small>Hubungi Kami</small><strong>(+62)87753719307</strong></span><b aria-hidden="true">↗</b></a><a class="contact-card" href="mailto:webkubator@gmail.com"><span class="contact-icon">@</span><span><small>Email</small><strong>webkubator@gmail.com</strong></span><b aria-hidden="true">↗</b></a></div></div></section>
    </main>
    <footer class="site-footer" aria-label="Informasi Webkubator"><div class="container footer-inner"><div class="footer-grid"><div class="footer-brand"><a class="brand footer-brand-link" href="#top" aria-label="<?= e($brandSettings['site_name']) ?>, kembali ke halaman utama"><img src="<?= e($brandSettings['logo']) ?>" alt="Logo <?= e($brandSettings['site_name']) ?>" width="52" height="52"><span><?= e($brandSettings['site_name']) ?></span></a><p class="footer-description">Webkubator menyediakan jasa pembuatan website profesional dan jasa website modern, cepat, serta responsif untuk bisnis Anda.</p><a class="button button-primary footer-cta" href="<?= e(wa('Halo Webkubator, saya ingin konsultasi gratis tentang website.')) ?>" target="_blank" rel="noopener"><i class="fi fi-rr-paper-plane" aria-hidden="true"></i> Konsultasi Gratis</a></div><nav class="footer-nav" aria-label="Navigasi footer"><h2 class="footer-heading">Jelajahi</h2><ul class="footer-links"><li><a href="#service">Layanan Website</a></li><li><a href="#pricing">Paket Website</a></li><li><a href="#portfolio">Portofolio</a></li><li><a href="/about/">Tentang Webkubator</a></li><li><a href="#contactus">Kontak</a></li></ul></nav><div class="footer-contact"><h2 class="footer-heading">Hubungi Kami</h2><address class="footer-address"><a href="https://wa.me/6287753719307" target="_blank" rel="noopener"><span class="footer-contact-icon"><i class="fi fi-rr-phone-call" aria-hidden="true"></i></span><span><small>WhatsApp</small><strong>+62 877-5371-9307</strong></span></a><a href="mailto:webkubator@gmail.com"><span class="footer-contact-icon"><i class="fi fi-rr-envelope" aria-hidden="true"></i></span><span><small>Email</small><strong>webkubator@gmail.com</strong></span></a></address></div></div><div class="footer-bottom"><p>© <?= date('Y') ?> <?= e($brandSettings['site_name']) ?>. Semua hak dilindungi.</p><div class="footer-legal"><a href="/about/">Tentang Kami</a><span aria-hidden="true">•</span><a href="#top">Kembali ke atas <span aria-hidden="true">↑</span></a></div></div></div></footer>
    <a class="whatsapp-float" href="https://wa.me/6287753719307" target="_blank" rel="noopener" aria-label="Hubungi Webkubator melalui WhatsApp"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 3.5A11.8 11.8 0 0 0 12.08 0C5.55 0 .24 5.3.24 11.83c0 2.08.54 4.11 1.57 5.9L.14 23.85l6.27-1.64a11.8 11.8 0 0 0 5.66 1.44h.01c6.52 0 11.83-5.3 11.83-11.82 0-3.16-1.23-6.13-3.41-8.33ZM12.08 21.6h-.01a9.77 9.77 0 0 1-4.98-1.36l-.36-.22-3.72.98 1-3.62-.24-.37a9.77 9.77 0 0 1-1.5-5.18c0-5.38 4.38-9.76 9.77-9.76a9.7 9.7 0 0 1 6.91 2.87 9.7 9.7 0 0 1 2.86 6.92c0 5.38-4.38 9.75-9.75 9.75Zm5.35-7.3c-.29-.15-1.72-.85-1.99-.94-.27-.1-.46-.15-.65.15-.2.29-.75.94-.92 1.13-.17.2-.34.22-.63.08-.29-.15-1.24-.46-2.36-1.46a8.9 8.9 0 0 1-1.64-2.03c-.17-.29-.02-.45.13-.6.13-.13.29-.34.44-.51.15-.17.2-.29.3-.49.1-.2.05-.37-.02-.52-.08-.15-.65-1.57-.89-2.15-.23-.56-.47-.49-.65-.5h-.55c-.2 0-.52.07-.8.37-.27.29-1.04 1.02-1.04 2.49 0 1.47 1.07 2.89 1.21 3.09.15.2 2.1 3.2 5.1 4.49.71.31 1.27.5 1.7.64.71.23 1.36.2 1.87.12.57-.08 1.72-.7 1.96-1.38.24-.68.24-1.26.17-1.38-.07-.12-.27-.2-.56-.34Z"/></svg></a>
    <script src="script.js?v=<?= e($assetVersion) ?>" defer></script>
</body>
</html>
