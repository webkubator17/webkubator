<?php
$sent = filter_input(INPUT_GET, 'sent', FILTER_VALIDATE_INT);
$projects = [
    ['name' => 'PT Altiga Falindo', 'type' => 'Company profile · Industri', 'image' => 'assets/images/altiga.webp', 'url' => 'https://altigabengkel.com/'],
    ['name' => 'Kazeem Vokasi', 'type' => 'Education · Company profile', 'image' => 'assets/images/kazeem.webp', 'url' => 'https://kazeemvocint.com/'],
    ['name' => 'Sewa Boat Batam', 'type' => 'Travel · Booking', 'image' => 'assets/images/boat.webp', 'url' => 'https://sewaboatbatam.com/'],
    ['name' => 'FME Indonesia', 'type' => 'Organization · Information', 'image' => 'assets/images/fmei.webp', 'url' => 'https://fmeindonesia.com/'],
    ['name' => 'Desa Cipayung', 'type' => 'Government · Public service', 'image' => 'assets/images/cipayung.webp', 'url' => 'https://desacipayung.id/'],
    ['name' => 'Desa Talang Tinggi', 'type' => 'Government · Public service', 'image' => 'assets/images/talang-tinggi.webp', 'url' => 'https://desatalangtinggiulumanna.com/'],
];
$plans = [
    ['name' => 'Starter', 'price' => '1,45 jt', 'description' => 'Untuk landing page dan bisnis yang baru mulai serius online.', 'features' => ['Landing page / company profile', 'Domain .COM atau .WEB.ID', 'Hosting 1 GB SSD', 'SSL dan 3 email domain', 'Pengerjaan hingga 7 hari']],
    ['name' => 'Business', 'price' => '1,75 jt', 'description' => 'Pilihan seimbang untuk bisnis yang ingin terlihat profesional.', 'features' => ['Company profile hingga 5 menu', 'Domain .COM atau .ID', 'Hosting 2 GB SSD', 'SSL dan 5 email domain', 'CTA WhatsApp dan form kontak']],
    ['name' => 'Commerce', 'price' => '2,5 jt', 'description' => 'Untuk katalog produk, direct WhatsApp, dan kebutuhan yang lebih lengkap.', 'features' => ['Katalog produk dan toko online direct WA', 'Domain .COM, .ID, atau .SHOP', 'Hosting 3 GB SSD', 'SSL dan 7 email domain', 'Pengerjaan 10–15 hari']],
];
function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function wa(string $message): string { return 'https://wa.me/6287753719307?text=' . rawurlencode($message); }
$defaultMessage = 'Halo Webkubator, saya ingin konsultasi website.';
$assetVersion = (string) max((int) @filemtime(__DIR__ . '/styles.css'), (int) @filemtime(__DIR__ . '/script.js'));
header('Cache-Control: no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#080d2b">
    <meta name="description" content="Webkubator membantu bisnis, organisasi, dan UMKM membangun website yang cepat, meyakinkan, dan siap menghasilkan kontak baru.">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="id_ID">
    <meta property="og:title" content="Webkubator — Website yang Membuat Bisnis Terlihat Serius">
    <meta property="og:description" content="Jasa pembuatan website profesional dengan proses jelas, desain modern, dan dukungan yang manusiawi.">
    <meta property="og:url" content="https://webkubator.com/">
    <link rel="canonical" href="https://webkubator.com/">
    <title>Webkubator — Jasa Pembuatan Website Profesional</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=<?= e($assetVersion) ?>">
</head>
<body>
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    <header class="site-header" data-header>
        <div class="container header-inner">
            <a class="brand" href="#top" aria-label="Webkubator, kembali ke halaman utama">
                <img src="assets/images/logo.webp" alt="Logo Webkubator" width="160" height="25">
            </a>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="Buka menu navigasi">
                <span></span><span></span><span></span>
            </button>
            <nav class="site-nav" id="site-nav" aria-label="Navigasi utama">
                <a href="#layanan">Layanan</a>
                <a href="#portfolio">Portfolio</a>
                <a href="#harga">Harga</a>
                <a href="#faq">FAQ</a>
                <a class="nav-cta" href="<?= e(wa($defaultMessage)) ?>" target="_blank" rel="noopener">Konsultasi gratis <span aria-hidden="true">↗</span></a>
            </nav>
        </div>
    </header>

    <main id="main-content">
        <?php if ($sent === 1): ?><div class="notice notice-success" role="status">Terima kasih. Pesan Anda sudah dikirim ke Webkubator.</div><?php endif; ?>
        <?php if ($sent === 0): ?><div class="notice notice-error" role="alert">Pesan belum terkirim. Silakan hubungi kami melalui WhatsApp.</div><?php endif; ?>
        <section class="hero section" id="top">
            <div class="container hero-grid">
                <div class="hero-copy reveal">
                    <p class="eyebrow"><span class="eyebrow-dot"></span> Digital partner untuk bisnis Indonesia</p>
                    <h1>Website yang membuat bisnis terlihat <em>serius.</em></h1>
                    <p class="hero-lead">Kami merancang website yang bukan hanya bagus dilihat, tetapi juga membantu calon pelanggan percaya, memahami, lalu menghubungi bisnis Anda.</p>
                    <div class="hero-actions">
                        <a class="button button-primary" href="#contact">Mulai konsultasi <span aria-hidden="true">↗</span></a>
                        <a class="button button-ghost" href="#portfolio">Lihat hasil kerja <span aria-hidden="true">↓</span></a>
                    </div>
                    <div class="trust-row" aria-label="Pencapaian Webkubator">
                        <div><strong>5+</strong><span>Tahun pengalaman</span></div>
                        <div><strong>52</strong><span>Website diluncurkan</span></div>
                        <div><strong>25+</strong><span>Klien bertumbuh</span></div>
                    </div>
                </div>
                <div class="hero-visual reveal reveal-delay-1">
                    <div class="hero-orbit orbit-one"></div><div class="hero-orbit orbit-two"></div>
                    <div class="hero-card">
                        <div class="hero-card-top"><span class="status-dot"></span><span>WEBKUBATOR / 2026</span><span class="hero-card-menu">•••</span></div>
                        <img src="assets/images/hero.webp" alt="Ilustrasi tim membangun website bisnis" width="1024" height="683">
                        <div class="hero-card-caption"><span>Build with purpose</span><strong>01 — 04</strong></div>
                    </div>
                    <div class="floating-note note-top"><span class="note-icon">↗</span><span><b>Fast to launch</b><small>Mulai online lebih cepat</small></span></div>
                    <div class="floating-note note-bottom"><span class="note-icon note-icon-lime">✦</span><span><b>Made for growth</b><small>Siap dikembangkan</small></span></div>
                </div>
            </div>
        </section>

        <section class="marquee-band" aria-label="Keunggulan Webkubator"><div class="marquee-track"><span>Strategy</span><i>✦</i><span>Design</span><i>✦</i><span>Development</span><i>✦</i><span>SEO ready</span><i>✦</i><span>Strategy</span><i>✦</i><span>Design</span><i>✦</i><span>Development</span><i>✦</i><span>SEO ready</span></div></section>

        <section class="section section-light" id="layanan">
            <div class="container">
                <div class="section-heading reveal"><div><p class="eyebrow eyebrow-dark">Apa yang kami kerjakan</p><h2>Fondasi digital yang terasa <span>solid.</span></h2></div><p class="section-intro">Dari halaman pertama hingga website yang siap berkembang, kami bantu menyederhanakan prosesnya.</p></div>
                <div class="service-grid">
                    <article class="service-card reveal"><div class="service-number">01</div><div class="line-icon">↗</div><h3>Website bisnis</h3><p>Company profile, landing page, dan katalog yang menjelaskan nilai bisnis Anda dalam hitungan detik.</p><a href="#contact">Pelajari layanan <span aria-hidden="true">→</span></a></article>
                    <article class="service-card reveal reveal-delay-1"><div class="service-number">02</div><div class="line-icon">⌁</div><h3>SEO dasar</h3><p>Struktur konten dan teknis yang lebih siap ditemukan mesin pencari sejak website diluncurkan.</p><a href="#contact">Pelajari layanan <span aria-hidden="true">→</span></a></article>
                    <article class="service-card reveal reveal-delay-2"><div class="service-number">03</div><div class="line-icon">◌</div><h3>Hosting & domain</h3><p>Setup hosting, domain, SSL, dan email bisnis agar semuanya siap dipakai tanpa drama teknis.</p><a href="#contact">Pelajari layanan <span aria-hidden="true">→</span></a></article>
                    <article class="service-card service-card-dark reveal reveal-delay-3"><div class="service-number">04</div><div class="line-icon">✦</div><h3>Maintenance</h3><p>Pendampingan setelah launch untuk menjaga website tetap aman, relevan, dan terawat.</p><a href="#contact">Bicarakan kebutuhan <span aria-hidden="true">→</span></a></article>
                </div>
            </div>
        </section>

        <section class="section section-ink" id="portfolio">
            <div class="container">
                <div class="section-heading section-heading-light reveal"><div><p class="eyebrow">Dipercaya oleh berbagai bidang</p><h2>Beberapa karya yang <span>kami banggakan.</span></h2></div><a class="text-link" href="#contact">Ingin jadi berikutnya? <span aria-hidden="true">↗</span></a></div>
                <div class="portfolio-grid">
                    <?php foreach ($projects as $index => $project): ?>
                        <article class="project-card reveal <?= $index % 2 ? 'project-offset' : '' ?>">
                            <a href="<?= e($project['url']) ?>" target="_blank" rel="noopener" class="project-image"><img src="<?= e($project['image']) ?>" alt="Preview website <?= e($project['name']) ?>" loading="lazy"><span class="project-arrow" aria-hidden="true">↗</span></a>
                            <div class="project-meta"><div><h3><?= e($project['name']) ?></h3><p><?= e($project['type']) ?></p></div><span class="project-index">0<?= $index + 1 ?></span></div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section section-paper" id="proses">
            <div class="container process-grid">
                <div class="process-intro reveal"><p class="eyebrow eyebrow-dark">Cara kerja</p><h2>Jelas dari awal.<br><span>Tenang sampai akhir.</span></h2><p>Kami menjaga proses tetap sederhana agar Anda bisa fokus pada bisnis, bukan mengejar kabar tentang website.</p><a class="button button-dark" href="<?= e(wa('Halo Webkubator, saya ingin tahu proses pembuatan website.')) ?>" target="_blank" rel="noopener">Tanya prosesnya <span aria-hidden="true">↗</span></a></div>
                <div class="steps-list">
                    <div class="step reveal"><span class="step-number">01</span><div><h3>Kenali kebutuhan</h3><p>Kita mulai dari tujuan, audiens, dan apa yang ingin website capai.</p></div><span class="step-mark">↗</span></div>
                    <div class="step reveal reveal-delay-1"><span class="step-number">02</span><div><h3>Rancang & bangun</h3><p>Struktur, desain, dan konten disusun menjadi pengalaman yang mudah dipahami.</p></div><span class="step-mark">↗</span></div>
                    <div class="step reveal reveal-delay-2"><span class="step-number">03</span><div><h3>Launch & dampingi</h3><p>Website online, terukur, dan tetap punya tempat untuk berkembang.</p></div><span class="step-mark">↗</span></div>
                </div>
            </div>
        </section>

        <section class="section section-light pricing-section" id="harga">
            <div class="container">
                <div class="section-heading reveal"><div><p class="eyebrow eyebrow-dark">Paket yang transparan</p><h2>Mulai dari kebutuhan,<br><span>bukan jargon.</span></h2></div><p class="section-intro">Semua paket dibuat sederhana. Jika kebutuhan Anda berbeda, kami bantu susun penawaran yang lebih tepat.</p></div>
                <div class="pricing-grid">
                    <?php foreach ($plans as $index => $plan): ?>
                        <article class="price-card <?= $index === 1 ? 'price-card-featured' : '' ?> reveal reveal-delay-<?= $index ?>">
                            <?php if ($index === 1): ?><span class="popular-label">Paling populer</span><?php endif; ?>
                            <div class="price-top"><span class="price-index">0<?= $index + 1 ?></span><h3><?= e($plan['name']) ?></h3></div>
                            <p class="price-description"><?= e($plan['description']) ?></p><div class="price-value"><small>mulai</small><strong>Rp<?= e($plan['price']) ?></strong></div>
                            <ul><?php foreach ($plan['features'] as $feature): ?><li><span aria-hidden="true">✓</span><?= e($feature) ?></li><?php endforeach; ?></ul>
                            <a class="button <?= $index === 1 ? 'button-primary' : 'button-outline' ?>" href="<?= e(wa('Halo Webkubator, saya tertarik dengan paket ' . $plan['name'] . '.')) ?>" target="_blank" rel="noopener">Pilih paket <span aria-hidden="true">↗</span></a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section testimonial-section">
            <div class="container"><div class="testimonial-heading reveal"><p class="eyebrow eyebrow-dark">Kata mereka</p><h2>Partner yang enak<br><span>diajak tumbuh.</span></h2></div><div class="testimonial-grid"><blockquote class="quote-card reveal"><div class="quote-mark">“</div><p>Pengerjaannya bagus dan cepat. Adminnya sopan, komunikatif, dan membantu memberi rekomendasi.</p><footer><strong>Hendry</strong><span>Nusa Jaya Steel</span></footer></blockquote><blockquote class="quote-card quote-card-accent reveal reveal-delay-1"><div class="quote-mark">“</div><p>Harganya masuk akal, hasilnya bagus, dan saya bisa tanya banyak hal tanpa merasa merepotkan.</p><footer><strong>L.doats</strong><span>Warmie Tengah</span></footer></blockquote><blockquote class="quote-card reveal reveal-delay-2"><div class="quote-mark">“</div><p>Sangat membantu untuk yang ingin punya website tanpa harus ribet mengurus semuanya sendiri.</p><footer><strong>Alphascent</strong><span>Official Store</span></footer></blockquote></div></div>
        </section>

        <section class="section faq-section" id="faq"><div class="container faq-grid"><div class="faq-intro reveal"><p class="eyebrow eyebrow-dark">Pertanyaan umum</p><h2>Masih ada yang ingin<br><span>ditanyakan?</span></h2><p>Kalau pertanyaan Anda belum ada di sini, langsung kirim pesan. Kami jawab dengan bahasa manusia.</p><a class="text-link text-link-dark" href="<?= e(wa('Halo Webkubator, saya ingin bertanya tentang layanan website.')) ?>" target="_blank" rel="noopener">Tanya langsung <span aria-hidden="true">↗</span></a></div><div class="faq-list reveal reveal-delay-1"><details open><summary>Berapa lama proses pembuatan website?</summary><p>Landing page dan company profile biasanya selesai dalam 7 hari kerja. Paket dengan katalog atau kebutuhan lebih kompleks membutuhkan sekitar 10–15 hari kerja.</p></details><details><summary>Apakah domain dan hosting sudah termasuk?</summary><p>Ya, setiap paket memiliki fasilitas domain, hosting, SSL, dan email sesuai detail paket yang dipilih.</p></details><details><summary>Apakah bisa request desain sendiri?</summary><p>Bisa. Kami dapat menyesuaikan arah visual dengan identitas brand, referensi, dan kebutuhan audiens Anda.</p></details><details><summary>Bagaimana setelah website online?</summary><p>Kami tetap bisa membantu maintenance, perubahan konten, dan pengembangan fitur sesuai kebutuhan berikutnya.</p></details></div></div></section>

        <section class="section contact-section" id="contact"><div class="container contact-shell reveal"><div class="contact-copy"><p class="eyebrow">Mari mulai percakapan</p><h2>Punya ide?<br><span>Kita wujudkan.</span></h2><p>Ceritakan bisnis dan kebutuhan Anda. Tidak harus sudah tahu semuanya—kami bantu merapikannya.</p><div class="contact-links"><a href="https://wa.me/6287753719307" target="_blank" rel="noopener"><span>WhatsApp</span><strong>+62 877 5371 9307 ↗</strong></a><a href="mailto:webkubator@gmail.com"><span>Email</span><strong>webkubator@gmail.com ↗</strong></a></div></div><form class="contact-form" action="contact.php" method="post"><input class="honeypot" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true"><label for="name">Nama lengkap</label><input id="name" name="name" type="text" placeholder="Nama Anda" required><label for="email">Email</label><input id="email" name="email" type="email" placeholder="nama@email.com" required><label for="message">Ceritakan kebutuhan Anda</label><textarea id="message" name="message" rows="4" placeholder="Saya ingin membuat website untuk..." required></textarea><button class="button button-lime" type="submit">Kirim pesan <span aria-hidden="true">↗</span></button><p class="form-note">Atau langsung chat via WhatsApp untuk respons lebih cepat.</p></form></div></section>
    </main>

    <footer class="site-footer"><div class="container footer-inner"><a class="brand" href="#top" aria-label="Webkubator, kembali ke atas"><img src="assets/images/logo.webp" alt="Webkubator" width="160" height="25"></a><p>Website yang bekerja lebih keras untuk bisnis Anda.</p><div class="footer-bottom"><span>© <?= date('Y') ?> Webkubator</span><span>Dibuat dengan niat baik di Indonesia.</span><a href="#top">Kembali ke atas ↑</a></div></div></footer>
    <a class="whatsapp-float" href="https://wa.me/6287753719307" target="_blank" rel="noopener" aria-label="Hubungi Webkubator melalui WhatsApp"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 3.5A11.8 11.8 0 0 0 12.08 0C5.55 0 .24 5.3.24 11.83c0 2.08.54 4.11 1.57 5.9L.14 23.85l6.27-1.64a11.8 11.8 0 0 0 5.66 1.44h.01c6.52 0 11.83-5.3 11.83-11.82 0-3.16-1.23-6.13-3.41-8.33ZM12.08 21.6h-.01a9.77 9.77 0 0 1-4.98-1.36l-.36-.22-3.72.98 1-3.62-.24-.37a9.77 9.77 0 0 1-1.5-5.18c0-5.38 4.38-9.76 9.77-9.76a9.7 9.7 0 0 1 6.91 2.87 9.7 9.7 0 0 1 2.86 6.92c0 5.38-4.38 9.75-9.75 9.75Zm5.35-7.3c-.29-.15-1.72-.85-1.99-.94-.27-.1-.46-.15-.65.15-.2.29-.75.94-.92 1.13-.17.2-.34.22-.63.08-.29-.15-1.24-.46-2.36-1.46a8.9 8.9 0 0 1-1.64-2.03c-.17-.29-.02-.45.13-.6.13-.13.29-.34.44-.51.15-.17.2-.29.3-.49.1-.2.05-.37-.02-.52-.08-.15-.65-1.57-.89-2.15-.23-.56-.47-.49-.65-.5h-.55c-.2 0-.52.07-.8.37-.27.29-1.04 1.02-1.04 2.49 0 1.47 1.07 2.89 1.21 3.09.15.2 2.1 3.2 5.1 4.49.71.31 1.27.5 1.7.64.71.23 1.36.2 1.87.12.57-.08 1.72-.7 1.96-1.38.24-.68.24-1.26.17-1.38-.07-.12-.27-.2-.56-.34Z"/></svg></a>
    <script src="script.js?v=<?= e($assetVersion) ?>" defer></script>
</body>
</html>

