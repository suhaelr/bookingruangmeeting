<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SEO Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the SEO configuration for the application.
    | You can customize meta tags, Open Graph, Twitter Cards, and other SEO elements.
    |
    */

    'default' => [
        'site_name' => env('SEO_SITE_NAME', 'Sistem Pemesanan Ruang Meeting'),
        'site_description' => env('SEO_SITE_DESCRIPTION', 'Sistem pemesanan ruang meeting yang modern dan efisien untuk organisasi Anda. Kelola jadwal meeting dengan mudah dan praktis.'),
        'site_keywords' => env('SEO_SITE_KEYWORDS', 'pemesanan ruang meeting, sistem booking, manajemen meeting, jadwal meeting, ruang rapat'),
        'site_url' => env('SEO_SITE_URL', 'https://pusdatinbgn.web.id'),
        'site_logo' => env('SEO_SITE_LOGO', '/logo-bgn.png'),
        'site_author' => env('SEO_SITE_AUTHOR', 'PUSDATIN BGN'),
        'site_language' => env('SEO_SITE_LANGUAGE', 'id'),
        'site_region' => env('SEO_SITE_REGION', 'ID'),
        'site_currency' => env('SEO_SITE_CURRENCY', 'IDR'),
    ],

    'pages' => [
        'login' => [
            'title' => 'Masuk - Sistem Pemesanan Ruang Meeting',
            'description' => 'Masuk ke sistem pemesanan ruang meeting untuk mengelola jadwal meeting Anda. Akses mudah dan aman dengan berbagai metode login.',
            'keywords' => 'login, masuk, sistem pemesanan, ruang meeting, autentikasi',
            'canonical' => '/login',
            'robots' => 'noindex, nofollow',
        ],
        'register' => [
            'title' => 'Daftar - Sistem Pemesanan Ruang Meeting',
            'description' => 'Daftar akun baru untuk mengakses sistem pemesanan ruang meeting. Proses pendaftaran mudah dan cepat.',
            'keywords' => 'daftar, registrasi, akun baru, sistem pemesanan, ruang meeting',
            'canonical' => '/register',
            'robots' => 'noindex, nofollow',
        ],
        'user_dashboard' => [
            'title' => 'Dashboard Pengguna - Sistem Pemesanan Ruang Meeting',
            'description' => 'Dashboard pengguna untuk mengelola pemesanan ruang meeting. Lihat jadwal, statistik, dan kelola booking Anda.',
            'keywords' => 'dashboard pengguna, pemesanan ruang meeting, jadwal meeting, statistik booking',
            'canonical' => '/user/dashboard',
            'robots' => 'noindex, nofollow',
        ],
        'admin_dashboard' => [
            'title' => 'Dashboard Admin - Sistem Pemesanan Ruang Meeting',
            'description' => 'Dashboard admin untuk mengelola sistem pemesanan ruang meeting. Kelola pengguna, ruang meeting, dan pemesanan.',
            'keywords' => 'dashboard admin, manajemen sistem, pengguna, ruang meeting, pemesanan',
            'canonical' => '/admin/dashboard',
            'robots' => 'noindex, nofollow',
        ],
        'user_bookings' => [
            'title' => 'Pemesanan Saya - Sistem Pemesanan Ruang Meeting',
            'description' => 'Kelola semua pemesanan ruang meeting Anda. Lihat status, edit, atau batalkan pemesanan yang sudah dibuat.',
            'keywords' => 'pemesanan saya, booking, jadwal meeting, status pemesanan',
            'canonical' => '/user/bookings',
            'robots' => 'noindex, nofollow',
        ],
        'create_booking' => [
            'title' => 'Buat Pemesanan - Sistem Pemesanan Ruang Meeting',
            'description' => 'Buat pemesanan ruang meeting baru. Pilih ruang, tanggal, dan waktu yang sesuai untuk meeting Anda.',
            'keywords' => 'buat pemesanan, booking baru, ruang meeting, jadwal meeting',
            'canonical' => '/user/bookings/create',
            'robots' => 'noindex, nofollow',
        ],
        'admin_users' => [
            'title' => 'Kelola Pengguna - Admin Panel',
            'description' => 'Kelola pengguna sistem pemesanan ruang meeting. Tambah, edit, atau hapus akun pengguna.',
            'keywords' => 'kelola pengguna, admin panel, manajemen user, sistem pemesanan',
            'canonical' => '/admin/users',
            'robots' => 'noindex, nofollow',
        ],
        'admin_rooms' => [
            'title' => 'Kelola Ruang Meeting - Admin Panel',
            'description' => 'Kelola ruang meeting yang tersedia. Tambah, edit, atau hapus ruang meeting.',
            'keywords' => 'kelola ruang meeting, admin panel, manajemen ruang, sistem pemesanan',
            'canonical' => '/admin/rooms',
            'robots' => 'noindex, nofollow',
        ],
        'admin_bookings' => [
            'title' => 'Kelola Pemesanan - Admin Panel',
            'description' => 'Kelola semua pemesanan ruang meeting. Lihat, konfirmasi, atau batalkan pemesanan.',
            'keywords' => 'kelola pemesanan, admin panel, manajemen booking, sistem pemesanan',
            'canonical' => '/admin/bookings',
            'robots' => 'noindex, nofollow',
        ],
        'privacy_policy' => [
            'title' => 'Kebijakan Privasi - Sistem Pemesanan Ruang Meeting',
            'description' => 'Kebijakan privasi sistem pemesanan ruang meeting. Informasi tentang pengumpulan, penggunaan, dan perlindungan data pribadi.',
            'keywords' => 'kebijakan privasi, perlindungan data, privasi pengguna, sistem pemesanan',
            'canonical' => '/privacy-policy',
            'robots' => 'index, follow',
        ],
        'terms_of_service' => [
            'title' => 'Syarat dan Ketentuan - Sistem Pemesanan Ruang Meeting',
            'description' => 'Syarat dan ketentuan penggunaan sistem pemesanan ruang meeting. Aturan dan ketentuan yang berlaku.',
            'keywords' => 'syarat ketentuan, aturan penggunaan, terms of service, sistem pemesanan',
            'canonical' => '/terms-of-service',
            'robots' => 'index, follow',
        ],
    ],

    'open_graph' => [
        'type' => 'website',
        'locale' => 'id_ID',
        'site_name' => env('SEO_SITE_NAME', 'Sistem Pemesanan Ruang Meeting'),
    ],

    'twitter' => [
        'card' => 'summary_large_image',
        'site' => env('TWITTER_SITE', '@pusdatinbgn'),
        'creator' => env('TWITTER_CREATOR', '@pusdatinbgn'),
    ],

    'structured_data' => [
        'organization' => [
            '@type' => 'Organization',
            'name' => env('SEO_SITE_NAME', 'Sistem Pemesanan Ruang Meeting'),
            'url' => env('SEO_SITE_URL', 'https://pusdatinbgn.web.id'),
            'logo' => env('SEO_SITE_URL', 'https://pusdatinbgn.web.id') . '/logo-bgn.png',
            'description' => env('SEO_SITE_DESCRIPTION', 'Sistem pemesanan ruang meeting yang modern dan efisien'),
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'ID',
                'addressRegion' => 'Indonesia',
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => env('CONTACT_PHONE', '+62-xxx-xxx-xxxx'),
                'contactType' => 'customer service',
                'availableLanguage' => ['Indonesian', 'English'],
            ],
        ],
        'web_site' => [
            '@type' => 'WebSite',
            'name' => env('SEO_SITE_NAME', 'Sistem Pemesanan Ruang Meeting'),
            'url' => env('SEO_SITE_URL', 'https://pusdatinbgn.web.id'),
            'description' => env('SEO_SITE_DESCRIPTION', 'Sistem pemesanan ruang meeting yang modern dan efisien'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => env('SEO_SITE_URL', 'https://pusdatinbgn.web.id') . '/search?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ],
        'web_application' => [
            '@type' => 'WebApplication',
            'name' => env('SEO_SITE_NAME', 'Sistem Pemesanan Ruang Meeting'),
            'url' => env('SEO_SITE_URL', 'https://pusdatinbgn.web.id'),
            'description' => env('SEO_SITE_DESCRIPTION', 'Sistem pemesanan ruang meeting yang modern dan efisien'),
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web Browser',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'IDR',
            ],
        ],
    ],

    'sitemap' => [
        'enabled' => true,
        'priority' => [
            'home' => 1.0,
            'privacy_policy' => 0.8,
            'terms_of_service' => 0.8,
            'login' => 0.6,
            'register' => 0.6,
        ],
        'changefreq' => [
            'home' => 'daily',
            'privacy_policy' => 'monthly',
            'terms_of_service' => 'monthly',
            'login' => 'weekly',
            'register' => 'weekly',
        ],
    ],

    'analytics' => [
        'google_analytics' => env('GOOGLE_ANALYTICS_ID'),
        'google_tag_manager' => env('GOOGLE_TAG_MANAGER_ID'),
        'facebook_pixel' => env('FACEBOOK_PIXEL_ID'),
    ],

    'performance' => [
        'preload_critical_resources' => true,
        'lazy_load_images' => true,
        'minify_html' => env('MINIFY_HTML', false),
        'compress_assets' => env('COMPRESS_ASSETS', true),
    ],
];
