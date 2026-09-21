<?php
declare(strict_types=1);

function pricing_catalog(): array
{
    return [
        'landing-page' => [
            'label' => 'Landing Page',
            'plans' => [
                ['name' => 'Startup', 'price' => '700.000', 'renewal' => 'Perpanjangan Rp600.000/Tahun', 'features' => ['FREE Domain .WEB.ID', '1 Halaman Landing Page', 'Hosting 800MB', 'Website SSL/HTTPS', 'Unlimited Bandwidth', '5 Email Domain', 'Integrasi Sosial Media', 'Fitur Chat WhatsApp', 'Manual Book', 'Standar Kontak Form', 'Garansi Selamanya']],
                ['name' => 'Bisnis', 'price' => '900.000', 'renewal' => 'Perpanjangan Rp750.000/Tahun', 'features' => ['FREE Domain .COM', '3 Halaman Landing Page', 'Hosting 1,2GB', 'Website SSL/HTTPS', 'Unlimited Bandwidth', '10 Email Domain', 'Integrasi Sosial Media', 'Fitur Chat WhatsApp', 'Manual Book', 'Standar Kontak Form', 'Free Template Premium', 'Garansi Selamanya']],
                ['name' => 'Premium', 'price' => '1.100.000', 'renewal' => 'Perpanjangan Rp900.000/Tahun', 'features' => ['FREE Domain .COM/.ID', '5 Halaman Landing Page', 'Hosting 3GB', 'Website SSL/HTTPS', 'Unlimited Bandwidth', '20 Email Domain', 'Integrasi Sosial Media', 'Fitur Chat WhatsApp', 'Manual Book', 'Standar Kontak Form', 'Free Template Premium', 'Optimasi SEO', 'Free Plugin', 'Garansi Selamanya']],
            ],
        ],
        'company-profile' => [
            'label' => 'Company Profile',
            'plans' => [
                ['name' => 'Startup', 'price' => '850.000', 'renewal' => 'Perpanjangan Rp700.000/Tahun', 'features' => ['FREE Domain .COM', '5 Halaman Website', 'Hosting 1GB', 'Website SSL/HTTPS', 'Profil & Layanan', 'Integrasi WhatsApp', 'Google Maps', 'Form Kontak', 'SEO Dasar', 'Garansi Selamanya']],
                ['name' => 'Bisnis', 'price' => '1.100.000', 'renewal' => 'Perpanjangan Rp900.000/Tahun', 'features' => ['FREE Domain .COM', '10 Halaman Website', 'Hosting 2GB', 'Website SSL/HTTPS', 'Profil, Layanan & Tim', 'Portofolio Proyek', 'Integrasi WhatsApp & Maps', 'Form Kontak', 'SEO Dasar', 'Free Template Premium', 'Garansi Selamanya']],
                ['name' => 'Premium', 'price' => '1.300.000', 'renewal' => 'Perpanjangan Rp1.050.000/Tahun', 'features' => ['FREE Domain .COM/.ID', '15+ Halaman Website', 'Hosting 3GB', 'Website SSL/HTTPS', 'Profil, Tim & Cabang', 'Portofolio Proyek', 'Blog atau Berita', 'Integrasi WhatsApp & Maps', 'Optimasi SEO', 'Free Plugin', 'Garansi Selamanya']],
            ],
        ],
        'toko-online' => [
            'label' => 'Website Instansi',
            'plans' => [
                ['name' => 'Startup', 'price' => '850.000', 'renewal' => 'Perpanjangan Rp700.000/Tahun', 'features' => ['FREE Domain .COM', '5 Halaman Website', 'Hosting 1GB', 'Website SSL/HTTPS', 'Profil Instansi & Layanan', 'Struktur Organisasi', 'Google Maps', 'Form Kontak', 'SEO Dasar', 'Garansi Selamanya']],
                ['name' => 'Bisnis', 'price' => '1.100.000', 'renewal' => 'Perpanjangan Rp900.000/Tahun', 'features' => ['FREE Domain .COM', '10 Halaman Website', 'Hosting 2GB', 'Website SSL/HTTPS', 'Profil, Layanan & Program', 'Struktur Organisasi', 'Informasi Publik', 'Integrasi WhatsApp & Maps', 'SEO Dasar', 'Garansi Selamanya']],
                ['name' => 'Premium', 'price' => '1.300.000', 'renewal' => 'Perpanjangan Rp1.050.000/Tahun', 'features' => ['FREE Domain .COM/.ID', '15+ Halaman Website', 'Hosting 3GB', 'Website SSL/HTTPS', 'Profil, Program & Unit Kerja', 'Struktur Organisasi', 'Berita atau Agenda', 'Form Layanan Publik', 'Optimasi SEO', 'Garansi Selamanya']],
            ],
        ],
        'e-commerce' => [
            'label' => 'E-Commerce',
            'plans' => [
                ['name' => 'Startup', 'price' => '1.000.000', 'renewal' => 'Perpanjangan Rp800.000/Tahun', 'features' => ['FREE Domain .COM', 'Produk hingga 100 Item', 'Hosting 4GB', 'Website SSL/HTTPS', 'Kategori & Variasi Produk', 'Keranjang & Checkout', 'Manajemen Pesanan', 'Integrasi Pembayaran', 'Integrasi WhatsApp', 'Garansi Selamanya']],
                ['name' => 'Bisnis', 'price' => '1.500.000', 'renewal' => 'Perpanjangan Rp1.200.000/Tahun', 'features' => ['FREE Domain .COM', 'Produk Unlimited', 'Hosting 6GB', 'Website SSL/HTTPS', 'Manajemen Stok', 'Voucher dan Promo', 'Integrasi Pembayaran', 'Laporan Penjualan', 'Optimasi SEO', 'Free Plugin Premium', 'Garansi Selamanya']],
                ['name' => 'Premium', 'price' => '1.750.000', 'renewal' => 'Perpanjangan Rp1.400.000/Tahun', 'features' => ['FREE Domain .COM/.ID', 'Produk Unlimited', 'Hosting 10GB', 'Website SSL/HTTPS', 'Fitur Multi-Level', 'Manajemen Stok & Pesanan', 'Integrasi Pembayaran', 'Laporan Penjualan', 'Optimasi SEO Lanjutan', 'Prioritas Support', 'Garansi Selamanya']],
            ],
        ],
        'link-bio' => [
            'label' => 'Link Bio',
            'plans' => [
                ['name' => 'Basic', 'price' => '350.000', 'renewal' => 'Perpanjangan Rp250.000/Tahun', 'features' => ['1 Halaman Link Bio', 'Custom Nama Brand', 'Hingga 8 Tombol Link', 'Integrasi Sosial Media', 'Tombol WhatsApp', 'Responsive Mobile', 'SSL/HTTPS', 'Garansi 3 Bulan']],
                ['name' => 'Pro', 'price' => '650.000', 'renewal' => 'Perpanjangan Rp400.000/Tahun', 'features' => ['1 Halaman Link Bio', 'Custom Domain', 'Hingga 15 Tombol Link', 'Katalog Produk Ringkas', 'Integrasi Sosial Media', 'Tombol WhatsApp', 'Pixel & Analytics Dasar', 'Responsive Mobile', 'Garansi 6 Bulan']],
                ['name' => 'Premium', 'price' => '1.000.000', 'renewal' => 'Perpanjangan Rp600.000/Tahun', 'features' => ['1 Halaman Link Bio', 'Custom Domain', 'Link dan Produk Unlimited', 'Katalog Produk Ringkas', 'Form Kontak', 'Pixel & Analytics', 'Optimasi SEO Dasar', 'Responsive Mobile', 'Prioritas Support', 'Garansi Selamanya']],
            ],
        ],
    ];
}

function pricing_feature_icon(string $feature): string
{
    $feature = strtolower($feature);
    $icons = [
        'domain' => 'fi-rr-globe',
        'halaman' => 'fi-rr-browser',
        'produk' => 'fi-rr-box-open',
        'hosting' => 'fi-rr-database',
        'ssl' => 'fi-rr-shield-check',
        'bandwidth' => 'fi-rr-chart-line-up',
        'email' => 'fi-rr-envelope',
        'sosial' => 'fi-rr-share',
        'whatsapp' => 'fi-rr-paper-plane',
        'manual' => 'fi-rr-book-alt',
        'kontak' => 'fi-rr-form',
        'template' => 'fi-rr-palette',
        'seo' => 'fi-rr-search',
        'plugin' => 'fi-rr-puzzle-piece',
        'garansi' => 'fi-rr-badge-check',
        'brand' => 'fi-rr-star',
        'tombol' => 'fi-rr-link',
        'mobile' => 'fi-rr-mobile',
        'katalog' => 'fi-rr-list-check',
        'keranjang' => 'fi-rr-shopping-cart',
        'checkout' => 'fi-rr-credit-card',
        'pesanan' => 'fi-rr-receipt',
        'pembayaran' => 'fi-rr-wallet',
        'stok' => 'fi-rr-boxes',
        'voucher' => 'fi-rr-ticket',
        'laporan' => 'fi-rr-chart-histogram',
        'profil' => 'fi-rr-id-badge',
        'organisasi' => 'fi-rr-users',
        'maps' => 'fi-rr-marker',
        'publik' => 'fi-rr-document',
        'berita' => 'fi-rr-newspaper',
        'program' => 'fi-rr-calendar',
        'analytics' => 'fi-rr-chart-line-up',
        'form' => 'fi-rr-form',
    ];
    foreach ($icons as $keyword => $icon) {
        if (strpos($feature, $keyword) !== false) {
            return $icon;
        }
    }
    return 'fi-rr-check';
}

function pricing_domain_from_plan(array $plan): string
{
    foreach ((array) ($plan['features'] ?? []) as $feature) {
        if (preg_match('/FREE Domain\s+(.+)/i', (string) $feature, $matches)) {
            return trim($matches[1]);
        }
    }
    return '.COM';
}

function pricing_plan_slug(string $name): string
{
    return strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
}
