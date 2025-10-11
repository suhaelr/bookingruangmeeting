<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use App\Models\MeetingRoom;
use App\Models\Booking;
use App\Models\User;

class SeoController extends Controller
{
    /**
     * Generate dynamic SEO data for pages
     */
    public function generateSeoData($page = null, $data = [])
    {
        $seoConfig = config('seo');
        $defaultConfig = $seoConfig['default'];
        
        // Get page-specific config
        $pageConfig = $seoConfig['pages'][$page] ?? [];
        
        // Merge with dynamic data
        $seoData = array_merge($pageConfig, $data);
        
        // Generate dynamic content based on page type
        switch ($page) {
            case 'user_dashboard':
                return $this->generateUserDashboardSeo($seoData, $data);
            case 'admin_dashboard':
                return $this->generateAdminDashboardSeo($seoData, $data);
            case 'user_bookings':
                return $this->generateUserBookingsSeo($seoData, $data);
            case 'create_booking':
                return $this->generateCreateBookingSeo($seoData, $data);
            case 'admin_users':
                return $this->generateAdminUsersSeo($seoData, $data);
            case 'admin_rooms':
                return $this->generateAdminRoomsSeo($seoData, $data);
            case 'admin_bookings':
                return $this->generateAdminBookingsSeo($seoData, $data);
            default:
                return $this->generateDefaultSeo($seoData, $data);
        }
    }
    
    /**
     * Generate SEO data for user dashboard
     */
    private function generateUserDashboardSeo($seoData, $data)
    {
        $user = session('user_data');
        $userName = $user['full_name'] ?? 'Pengguna';
        
        return array_merge($seoData, [
            'title' => "Dashboard {$userName} - " . config('seo.default.site_name'),
            'description' => "Dashboard pribadi {$userName} untuk mengelola pemesanan ruang meeting. Lihat statistik, jadwal, dan kelola booking Anda.",
            'keywords' => "dashboard pengguna, {$userName}, pemesanan ruang meeting, jadwal meeting, statistik booking",
            'og_title' => "Dashboard {$userName}",
            'og_description' => "Kelola pemesanan ruang meeting Anda dengan mudah",
            'structured_data' => [
                [
                    '@type' => 'WebPage',
                    'name' => "Dashboard {$userName}",
                    'description' => "Dashboard pribadi untuk mengelola pemesanan ruang meeting",
                    'url' => request()->url(),
                    'isPartOf' => [
                        '@type' => 'WebSite',
                        'name' => config('seo.default.site_name'),
                        'url' => config('seo.default.site_url')
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * Generate SEO data for admin dashboard
     */
    private function generateAdminDashboardSeo($seoData, $data)
    {
        $admin = session('user_data');
        $adminName = $admin['full_name'] ?? 'Administrator';
        
        return array_merge($seoData, [
            'title' => "Dashboard Admin {$adminName} - " . config('seo.default.site_name'),
            'description' => "Dashboard administrator {$adminName} untuk mengelola sistem pemesanan ruang meeting. Kelola pengguna, ruang meeting, dan pemesanan.",
            'keywords' => "dashboard admin, {$adminName}, manajemen sistem, pengguna, ruang meeting, pemesanan",
            'og_title' => "Dashboard Admin {$adminName}",
            'og_description' => "Kelola sistem pemesanan ruang meeting",
            'structured_data' => [
                [
                    '@type' => 'WebPage',
                    'name' => "Dashboard Admin {$adminName}",
                    'description' => "Dashboard administrator untuk mengelola sistem",
                    'url' => request()->url(),
                    'isPartOf' => [
                        '@type' => 'WebSite',
                        'name' => config('seo.default.site_name'),
                        'url' => config('seo.default.site_url')
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * Generate SEO data for user bookings page
     */
    private function generateUserBookingsSeo($seoData, $data)
    {
        $user = session('user_data');
        $userName = $user['full_name'] ?? 'Pengguna';
        $totalBookings = $data['total_bookings'] ?? 0;
        
        return array_merge($seoData, [
            'title' => "Pemesanan {$userName} - " . config('seo.default.site_name'),
            'description' => "Kelola {$totalBookings} pemesanan ruang meeting {$userName}. Lihat status, edit, atau batalkan pemesanan yang sudah dibuat.",
            'keywords' => "pemesanan {$userName}, booking, jadwal meeting, status pemesanan, {$totalBookings} pemesanan",
            'og_title' => "Pemesanan {$userName}",
            'og_description' => "Kelola semua pemesanan ruang meeting Anda",
            'structured_data' => [
                [
                    '@type' => 'CollectionPage',
                    'name' => "Pemesanan {$userName}",
                    'description' => "Koleksi pemesanan ruang meeting pengguna",
                    'url' => request()->url(),
                    'mainEntity' => [
                        '@type' => 'ItemList',
                        'numberOfItems' => $totalBookings,
                        'itemListElement' => $this->generateBookingStructuredData($data['bookings'] ?? [])
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * Generate SEO data for create booking page
     */
    private function generateCreateBookingSeo($seoData, $data)
    {
        $user = session('user_data');
        $userName = $user['full_name'] ?? 'Pengguna';
        $availableRooms = $data['available_rooms'] ?? [];
        $roomCount = count($availableRooms);
        
        return array_merge($seoData, [
            'title' => "Buat Pemesanan Baru - " . config('seo.default.site_name'),
            'description' => "Buat pemesanan ruang meeting baru untuk {$userName}. Pilih dari {$roomCount} ruang meeting yang tersedia dengan fasilitas lengkap.",
            'keywords' => "buat pemesanan, booking baru, ruang meeting, jadwal meeting, {$roomCount} ruang tersedia",
            'og_title' => "Buat Pemesanan Baru",
            'og_description' => "Pilih ruang meeting yang sesuai untuk meeting Anda",
            'structured_data' => [
                [
                    '@type' => 'WebPage',
                    'name' => "Buat Pemesanan Baru",
                    'description' => "Form untuk membuat pemesanan ruang meeting baru",
                    'url' => request()->url(),
                    'mainEntity' => [
                        '@type' => 'ItemList',
                        'numberOfItems' => $roomCount,
                        'itemListElement' => $this->generateRoomStructuredData($availableRooms)
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * Generate SEO data for admin users page
     */
    private function generateAdminUsersSeo($seoData, $data)
    {
        $totalUsers = $data['total_users'] ?? 0;
        $activeUsers = $data['active_users'] ?? 0;
        
        return array_merge($seoData, [
            'title' => "Kelola {$totalUsers} Pengguna - Admin Panel",
            'description' => "Kelola {$totalUsers} pengguna sistem pemesanan ruang meeting. {$activeUsers} pengguna aktif dengan akses penuh ke sistem.",
            'keywords' => "kelola pengguna, admin panel, {$totalUsers} pengguna, {$activeUsers} aktif, manajemen user",
            'og_title' => "Kelola Pengguna",
            'og_description' => "Kelola semua pengguna sistem",
            'structured_data' => [
                [
                    '@type' => 'WebPage',
                    'name' => "Kelola Pengguna",
                    'description' => "Halaman admin untuk mengelola pengguna sistem",
                    'url' => request()->url(),
                    'mainEntity' => [
                        '@type' => 'ItemList',
                        'numberOfItems' => $totalUsers,
                        'itemListElement' => $this->generateUserStructuredData($data['users'] ?? [])
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * Generate SEO data for admin rooms page
     */
    private function generateAdminRoomsSeo($seoData, $data)
    {
        $totalRooms = $data['total_rooms'] ?? 0;
        $availableRooms = $data['available_rooms'] ?? 0;
        
        return array_merge($seoData, [
            'title' => "Kelola {$totalRooms} Ruang Meeting - Admin Panel",
            'description' => "Kelola {$totalRooms} ruang meeting dengan {$availableRooms} ruang tersedia. Tambah, edit, atau hapus ruang meeting sesuai kebutuhan.",
            'keywords' => "kelola ruang meeting, admin panel, {$totalRooms} ruang, {$availableRooms} tersedia, manajemen ruang",
            'og_title' => "Kelola Ruang Meeting",
            'og_description' => "Kelola semua ruang meeting yang tersedia",
            'structured_data' => [
                [
                    '@type' => 'WebPage',
                    'name' => "Kelola Ruang Meeting",
                    'description' => "Halaman admin untuk mengelola ruang meeting",
                    'url' => request()->url(),
                    'mainEntity' => [
                        '@type' => 'ItemList',
                        'numberOfItems' => $totalRooms,
                        'itemListElement' => $this->generateRoomStructuredData($data['rooms'] ?? [])
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * Generate SEO data for admin bookings page
     */
    private function generateAdminBookingsSeo($seoData, $data)
    {
        $totalBookings = $data['total_bookings'] ?? 0;
        $pendingBookings = $data['pending_bookings'] ?? 0;
        $confirmedBookings = $data['confirmed_bookings'] ?? 0;
        
        return array_merge($seoData, [
            'title' => "Kelola {$totalBookings} Pemesanan - Admin Panel",
            'description' => "Kelola {$totalBookings} pemesanan ruang meeting. {$pendingBookings} menunggu konfirmasi, {$confirmedBookings} sudah dikonfirmasi.",
            'keywords' => "kelola pemesanan, admin panel, {$totalBookings} pemesanan, {$pendingBookings} pending, {$confirmedBookings} confirmed",
            'og_title' => "Kelola Pemesanan",
            'og_description' => "Kelola semua pemesanan ruang meeting",
            'structured_data' => [
                [
                    '@type' => 'WebPage',
                    'name' => "Kelola Pemesanan",
                    'description' => "Halaman admin untuk mengelola pemesanan",
                    'url' => request()->url(),
                    'mainEntity' => [
                        '@type' => 'ItemList',
                        'numberOfItems' => $totalBookings,
                        'itemListElement' => $this->generateBookingStructuredData($data['bookings'] ?? [])
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * Generate default SEO data
     */
    private function generateDefaultSeo($seoData, $data)
    {
        return array_merge($seoData, [
            'structured_data' => []
        ]);
    }
    
    /**
     * Generate structured data for bookings
     */
    private function generateBookingStructuredData($bookings)
    {
        $structuredData = [];
        
        foreach ($bookings as $index => $booking) {
            $structuredData[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'Event',
                    'name' => $booking->title ?? 'Meeting',
                    'description' => $booking->description ?? 'Pemesanan ruang meeting',
                    'startDate' => $booking->start_time ? $booking->start_time->toISOString() : null,
                    'endDate' => $booking->end_time ? $booking->end_time->toISOString() : null,
                    'location' => [
                        '@type' => 'Place',
                        'name' => $booking->meetingRoom->name ?? 'Ruang Meeting',
                        'address' => $booking->meetingRoom->location ?? 'Lokasi tidak tersedia'
                    ],
                    'organizer' => [
                        '@type' => 'Person',
                        'name' => $booking->user->full_name ?? 'Pengguna'
                    ]
                ]
            ];
        }
        
        return $structuredData;
    }
    
    /**
     * Generate structured data for rooms
     */
    private function generateRoomStructuredData($rooms)
    {
        $structuredData = [];
        
        foreach ($rooms as $index => $room) {
            $structuredData[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'Place',
                    'name' => $room->name ?? 'Ruang Meeting',
                    'description' => $room->description ?? 'Ruang meeting dengan fasilitas lengkap',
                    'address' => $room->location ?? 'Lokasi tidak tersedia',
                    'amenityFeature' => array_map(function($amenity) {
                        return [
                            '@type' => 'LocationFeatureSpecification',
                            'name' => $amenity
                        ];
                    }, $room->getAmenitiesList() ?? [])
                ]
            ];
        }
        
        return $structuredData;
    }
    
    /**
     * Generate structured data for users
     */
    private function generateUserStructuredData($users)
    {
        $structuredData = [];
        
        foreach ($users as $index => $user) {
            $structuredData[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'Person',
                    'name' => $user->full_name ?? $user->name ?? 'Pengguna',
                    'email' => $user->email ?? null,
                    'jobTitle' => $user->department ?? 'Pengguna',
                    'worksFor' => [
                        '@type' => 'Organization',
                        'name' => config('seo.default.site_name')
                    ]
                ]
            ];
        }
        
        return $structuredData;
    }
    
    /**
     * Generate XML sitemap
     */
    public function generateSitemap()
    {
        $seoConfig = config('seo');
        $sitemapConfig = $seoConfig['sitemap'];
        
        if (!$sitemapConfig['enabled']) {
            abort(404);
        }
        
        $urls = [];
        
        // Add static pages
        $staticPages = [
            'home' => '/',
            'privacy_policy' => '/privacy-policy',
            'terms_of_service' => '/terms-of-service',
            'login' => '/login',
            'register' => '/register',
        ];
        
        foreach ($staticPages as $page => $url) {
            $urls[] = [
                'loc' => config('seo.default.site_url') . $url,
                'lastmod' => now()->toISOString(),
                'changefreq' => $sitemapConfig['changefreq'][$page] ?? 'weekly',
                'priority' => $sitemapConfig['priority'][$page] ?? 0.5,
            ];
        }
        
        // Add dynamic pages (if needed)
        // You can add dynamic URLs here based on your content
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($urls as $url) {
            $xml .= '<url>';
            $xml .= '<loc>' . htmlspecialchars($url['loc']) . '</loc>';
            $xml .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
            $xml .= '<changefreq>' . $url['changefreq'] . '</changefreq>';
            $xml .= '<priority>' . $url['priority'] . '</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
    
    /**
     * Generate robots.txt
     */
    public function generateRobots()
    {
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /user/\n";
        $robots .= "Disallow: /debug/\n";
        $robots .= "Disallow: /test/\n";
        $robots .= "Disallow: /oauth/\n";
        $robots .= "\n";
        $robots .= "Sitemap: " . config('seo.default.site_url') . "/sitemap.xml\n";
        
        return response($robots, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
