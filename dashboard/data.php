<?php
declare(strict_types=1);

function dashboard_storage_dir(): string
{
    return __DIR__ . '/storage';
}

function dashboard_data_path(): string
{
    return dashboard_storage_dir() . '/site-data.json';
}

function dashboard_default_data(): array
{
    return [
        'home' => [
            'eyebrow' => 'Jasa Website Cepat untuk Bisnis Modern',
            'title' => 'Jasa Pembuatan Website Profesional untuk Bisnis Anda',
            'description' => 'Bangun website bisnis yang modern, cepat, dan responsif bersama Webkubator, jasa pembuatan website profesional untuk UMKM dan perusahaan. Dapatkan jasa website yang membantu bisnis tampil lebih profesional dan siap online dalam 5 hari.',
            'hero_image' => 'assets/images/hero-image-v3.webp',
            'hero_alt' => 'Pria tersenyum membawa laptop dengan highlight layanan Webkubator.',
            'cta_primary' => 'Cek Paket Website',
            'cta_secondary' => 'Konsultasi Gratis',
        ],
        'brand' => [
            'site_name' => 'Webkubator',
            'logo' => 'assets/images/logo-white.webp',
            'favicon' => 'assets/images/favicon.png',
        ],
        'partners' => [
            ['image' => 'assets/images/partner-ofc.webp', 'alt' => 'Our Five Coco', 'visible' => true],
            ['image' => 'assets/images/partner-danuzkuy.webp', 'alt' => 'Danuzkuy', 'visible' => true],
            ['image' => 'assets/images/client-logos/pkbm-siloam.png', 'alt' => 'PKBM Siloam', 'visible' => true],
            ['image' => 'assets/images/client-logos/tautku.png', 'alt' => 'Tautku.id', 'visible' => true],
            ['image' => 'assets/images/client-logos/sraya-bali.png', 'alt' => 'Sraya Bali Wellness', 'visible' => true],
            ['image' => 'assets/images/client-logos/tanajava.png', 'alt' => 'Tanajava Essential Oil', 'visible' => true],
            ['image' => 'assets/images/client-logos/nusa-jaya-steel.png', 'alt' => 'Nusa Jaya Steel', 'visible' => true],
            ['image' => 'assets/images/client-logos/matrix-welding-school.png', 'alt' => 'Matrix Welding School', 'visible' => true],
            ['image' => 'assets/images/client-logos/capunglam.png', 'alt' => 'Capunglam', 'visible' => true],
            ['image' => 'assets/images/client-logos/sakuta-dewandaru-mada.png', 'alt' => 'Sakuta Dewandaru Mada', 'visible' => true],
        ],
        'projects' => [
            ['name' => 'Edukasi Berkendara', 'image' => 'assets/images/edukasi.webp', 'preview_url' => '', 'url' => 'https://edukasiberkendara.id/', 'traffic' => 0, 'visible' => true],
            ['name' => 'Kazeem Vokasi', 'image' => 'assets/images/kazeem.webp', 'preview_url' => '', 'url' => 'https://kazeemvocint.com/', 'traffic' => 0, 'visible' => true],
            ['name' => 'Fikri Hamdani', 'image' => 'assets/images/fikri.webp', 'preview_url' => '', 'url' => 'https://fikrihamdani.my.id/', 'traffic' => 0, 'visible' => true],
            ['name' => 'Sraya Bali Wellness', 'image' => '', 'preview_url' => 'https://srayabaliwellness.com', 'url' => 'https://srayabaliwellness.com', 'traffic' => 0, 'visible' => true],
            ['name' => 'Tanajava Essential Oil', 'image' => '', 'preview_url' => 'https://tanajava.my.id', 'url' => 'https://tanajava.my.id', 'traffic' => 0, 'visible' => true],
            ['name' => 'Nusa Jaya Steel', 'image' => '', 'preview_url' => 'https://nusajayasteel.com', 'url' => 'https://nusajayasteel.com', 'traffic' => 0, 'visible' => true],
            ['name' => 'Matrix Welding School', 'image' => '', 'preview_url' => 'https://matrixweldingschool.com', 'url' => 'https://matrixweldingschool.com', 'traffic' => 0, 'visible' => true],
            ['name' => 'Capunglam', 'image' => '', 'preview_url' => 'https://capunglam.com', 'url' => 'https://capunglam.com', 'traffic' => 0, 'visible' => true],
            ['name' => 'Sakuta Dewandaru Mada', 'image' => '', 'preview_url' => 'https://sakutadewandarumada.web.id', 'url' => 'https://sakutadewandarumada.web.id', 'traffic' => 0, 'visible' => true],
        ],
    ];
}

function dashboard_normalize_data(array $data): array
{
    $defaults = dashboard_default_data();
    $normalized = $defaults;

    if (isset($data['home']) && is_array($data['home'])) {
        $normalized['home'] = array_merge($defaults['home'], $data['home']);
    }
    if (isset($data['brand']) && is_array($data['brand'])) {
        $normalized['brand'] = array_merge($defaults['brand'], $data['brand']);
    }

    if (isset($data['partners']) && is_array($data['partners'])) {
        $normalized['partners'] = [];
        foreach ($data['partners'] as $partner) {
            if (!is_array($partner) || trim((string) ($partner['alt'] ?? '')) === '') {
                continue;
            }
            $normalized['partners'][] = [
                'image' => dashboard_clean_asset_path((string) ($partner['image'] ?? '')),
                'alt' => trim((string) $partner['alt']),
                'visible' => (bool) ($partner['visible'] ?? false),
            ];
        }

        // Keep the two historical client logos when an older dashboard save
        // contains only the newer portfolio/client entries.
        $legacyPartners = [
            ['image' => 'assets/images/partner-ofc.webp', 'alt' => 'Our Five Coco', 'visible' => true],
            ['image' => 'assets/images/partner-danuzkuy.webp', 'alt' => 'Danuzkuy', 'visible' => true],
        ];
        $storedImages = array_column($normalized['partners'], 'image');
        foreach (array_reverse($legacyPartners) as $legacyPartner) {
            if (!in_array($legacyPartner['image'], $storedImages, true)) {
                array_unshift($normalized['partners'], $legacyPartner);
            }
        }
    }

    if (isset($data['projects']) && is_array($data['projects'])) {
        $normalized['projects'] = [];
        $usesManualOrder = $data['projects'] !== [];
        foreach ($data['projects'] as $position => $project) {
            if (!is_array($project) || trim((string) ($project['name'] ?? '')) === '') {
                continue;
            }
            $hasOrder = array_key_exists('order', $project) && is_numeric($project['order']);
            if (!$hasOrder) {
                $usesManualOrder = false;
            }
            $normalized['projects'][] = [
                'name' => trim((string) $project['name']),
                'image' => dashboard_clean_asset_path((string) ($project['image'] ?? '')),
                'preview_url' => trim((string) ($project['preview_url'] ?? '')),
                'url' => trim((string) ($project['url'] ?? '')),
                'traffic' => max(0, (int) ($project['traffic'] ?? 0)),
                'visible' => (bool) ($project['visible'] ?? false),
                'order' => $hasOrder ? (int) $project['order'] : (int) $position,
            ];
        }

        $withPosition = [];
        foreach ($normalized['projects'] as $position => $project) {
            $project['_position'] = $position;
            $withPosition[] = $project;
        }
        usort($withPosition, static function (array $a, array $b) use ($usesManualOrder): int {
            if ($usesManualOrder) {
                $order = ((int) $a['order']) <=> ((int) $b['order']);
                return $order !== 0 ? $order : ($a['_position'] <=> $b['_position']);
            }
            $traffic = ((int) $b['traffic']) <=> ((int) $a['traffic']);
            return $traffic !== 0 ? $traffic : ($a['_position'] <=> $b['_position']);
        });
        foreach ($withPosition as $position => &$project) {
            $project['order'] = $position;
            unset($project['_position']);
        }
        unset($project);
        $normalized['projects'] = $withPosition;
    }

    foreach (['eyebrow', 'title', 'description', 'hero_alt', 'cta_primary', 'cta_secondary'] as $key) {
        $normalized['home'][$key] = trim((string) ($normalized['home'][$key] ?? $defaults['home'][$key]));
    }
    $normalized['home']['hero_image'] = dashboard_clean_asset_path((string) ($normalized['home']['hero_image'] ?? $defaults['home']['hero_image']));
    $normalized['brand']['site_name'] = trim((string) ($normalized['brand']['site_name'] ?? $defaults['brand']['site_name']));
    $normalized['brand']['logo'] = dashboard_clean_asset_path((string) ($normalized['brand']['logo'] ?? $defaults['brand']['logo']));
    $normalized['brand']['favicon'] = dashboard_clean_asset_path((string) ($normalized['brand']['favicon'] ?? $defaults['brand']['favicon']));

    return $normalized;
}

function dashboard_clean_asset_path(string $path): string
{
    $path = str_replace('\\', '/', trim($path));
    if ($path === '' || strpos($path, '..') !== false) {
        return '';
    }
    if (strncmp($path, 'assets/images/', 14) !== 0) {
        $path = 'assets/images/' . ltrim($path, '/');
    }
    if (strpos($path, 'assets/images/assets/images/') === 0) {
        $path = substr($path, strlen('assets/images/'));
    }
    return ltrim($path, '/');
}

function dashboard_load_data(): array
{
    $path = dashboard_data_path();
    if (!is_file($path)) {
        return dashboard_default_data();
    }
    $decoded = json_decode((string) file_get_contents($path), true);
    return is_array($decoded) ? dashboard_normalize_data($decoded) : dashboard_default_data();
}

function dashboard_save_data(array $data): bool
{
    $directory = dashboard_storage_dir();
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        return false;
    }
    $json = json_encode(dashboard_normalize_data($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }
    $temporary = $directory . '/site-data-' . bin2hex(random_bytes(6)) . '.tmp';
    if (file_put_contents($temporary, $json, LOCK_EX) === false) {
        return false;
    }
    return rename($temporary, dashboard_data_path());
}

