<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use App\Models\MeetingRoom;
use App\Models\Booking;
use App\Models\User;

class SeoService
{
    /**
     * Generate dynamic SEO data for any page
     */
    public function generatePageSeo($pageType, $data = [])
    {
        $cacheKey = "seo_data_{$pageType}_" . md5(serialize($data));
        
        return Cache::remember($cacheKey, 3600, function () use ($pageType, $data) {
            $seoConfig = config('seo');
            $defaultConfig = $seoConfig['default'];
            $pageConfig = $seoConfig['pages'][$pageType] ?? [];
            
            // Merge configurations
            $seoData = array_merge($pageConfig, $data);
            
            // Generate dynamic content
            $seoData = $this->enhanceSeoData($pageType, $seoData, $data);
            
            return $seoData;
        });
    }
    
    /**
     * Enhance SEO data with dynamic content
     */
    private function enhanceSeoData($pageType, $seoData, $data)
    {
        switch ($pageType) {
            case 'user_dashboard':
                return $this->enhanceUserDashboardSeo($seoData, $data);
            case 'admin_dashboard':
                return $this->enhanceAdminDashboardSeo($seoData, $data);
            case 'user_bookings':
                return $this->enhanceUserBookingsSeo($seoData, $data);
            case 'create_booking':
                return $this->enhanceCreateBookingSeo($seoData, $data);
            case 'admin_users':
                return $this->enhanceAdminUsersSeo($seoData, $data);
            case 'admin_rooms':
                return $this->enhanceAdminRoomsSeo($seoData, $data);
            case 'admin_bookings':
                return $this->enhanceAdminBookingsSeo($seoData, $data);
            default:
                return $seoData;
        }
    }
    
    /**
     * Enhance user dashboard SEO
     */
    private function enhanceUserDashboardSeo($seoData, $data)
    {
        $user = session('user_data');
        if (!$user) {
            return $seoData;
        }
        
        $userName = $user['full_name'] ?? 'Pengguna';
        $totalBookings = $data['total_bookings'] ?? 0;
        $pendingBookings = $data['pending_bookings'] ?? 0;
        
        return array_merge($seoData, [
            'title' => "Dashboard {$userName} - " . config('seo.default.site_name'),
            'description' => "Dashboard pribadi {$userName} dengan {$totalBookings} pemesanan ruang meeting. {$pendingBookings} menunggu konfirmasi.",
            'keywords' => "dashboard {$userName}, pemesanan ruang meeting, {$totalBookings} booking, {$pendingBookings} pending",
            'og_title' => "Dashboard {$userName}",
            'og_description' => "Kelola {$totalBookings} pemesanan ruang meeting Anda",
            'structured_data' => $this->generateUserDashboardStructuredData($user, $data)
        ]);
    }
    
    /**
     * Enhance admin dashboard SEO
     */
    private function enhanceAdminDashboardSeo($seoData, $data)
    {
        $admin = session('user_data');
        if (!$admin) {
            return $seoData;
        }
        
        $adminName = $admin['full_name'] ?? 'Administrator';
        $totalUsers = $data['total_users'] ?? 0;
        $totalRooms = $data['total_rooms'] ?? 0;
        $totalBookings = $data['total_bookings'] ?? 0;
        
        return array_merge($seoData, [
            'title' => "Dashboard Admin {$adminName} - " . config('seo.default.site_name'),
            'description' => "Dashboard administrator {$adminName} mengelola {$totalUsers} pengguna, {$totalRooms} ruang meeting, dan {$totalBookings} pemesanan.",
            'keywords' => "dashboard admin {$adminName}, {$totalUsers} pengguna, {$totalRooms} ruang, {$totalBookings} pemesanan",
            'og_title' => "Dashboard Admin {$adminName}",
            'og_description' => "Kelola sistem dengan {$totalUsers} pengguna dan {$totalBookings} pemesanan",
            'structured_data' => $this->generateAdminDashboardStructuredData($admin, $data)
        ]);
    }
    
    /**
     * Enhance user bookings SEO
     */
    private function enhanceUserBookingsSeo($seoData, $data)
    {
        $user = session('user_data');
        if (!$user) {
            return $seoData;
        }
        
        $userName = $user['full_name'] ?? 'Pengguna';
        $totalBookings = $data['total_bookings'] ?? 0;
        $bookings = $data['bookings'] ?? [];
        
        return array_merge($seoData, [
            'title' => "Pemesanan {$userName} ({$totalBookings}) - " . config('seo.default.site_name'),
            'description' => "Kelola {$totalBookings} pemesanan ruang meeting {$userName}. Lihat status, edit, atau batalkan pemesanan yang sudah dibuat.",
            'keywords' => "pemesanan {$userName}, {$totalBookings} booking, jadwal meeting, status pemesanan",
            'og_title' => "Pemesanan {$userName}",
            'og_description' => "Kelola {$totalBookings} pemesanan ruang meeting Anda",
            'structured_data' => $this->generateUserBookingsStructuredData($user, $bookings)
        ]);
    }
    
    /**
     * Enhance create booking SEO
     */
    private function enhanceCreateBookingSeo($seoData, $data)
    {
        $user = session('user_data');
        $userName = $user['full_name'] ?? 'Pengguna';
        $availableRooms = $data['available_rooms'] ?? [];
        $roomCount = count($availableRooms);
        
        return array_merge($seoData, [
            'title' => "Buat Pemesanan Baru - " . config('seo.default.site_name'),
            'description' => "Buat pemesanan ruang meeting baru untuk {$userName}. Pilih dari {$roomCount} ruang meeting yang tersedia dengan fasilitas lengkap.",
            'keywords' => "buat pemesanan, booking baru, {$roomCount} ruang tersedia, jadwal meeting",
            'og_title' => "Buat Pemesanan Baru",
            'og_description' => "Pilih dari {$roomCount} ruang meeting yang tersedia",
            'structured_data' => $this->generateCreateBookingStructuredData($availableRooms)
        ]);
    }
    
    /**
     * Enhance admin users SEO
     */
    private function enhanceAdminUsersSeo($seoData, $data)
    {
        $totalUsers = $data['total_users'] ?? 0;
        $activeUsers = $data['active_users'] ?? 0;
        $users = $data['users'] ?? [];
        
        return array_merge($seoData, [
            'title' => "Kelola {$totalUsers} Pengguna - Admin Panel",
            'description' => "Kelola {$totalUsers} pengguna sistem pemesanan ruang meeting. {$activeUsers} pengguna aktif dengan akses penuh ke sistem.",
            'keywords' => "kelola pengguna, {$totalUsers} pengguna, {$activeUsers} aktif, admin panel",
            'og_title' => "Kelola Pengguna",
            'og_description' => "Kelola {$totalUsers} pengguna sistem",
            'structured_data' => $this->generateAdminUsersStructuredData($users)
        ]);
    }
    
    /**
     * Enhance admin rooms SEO
     */
    private function enhanceAdminRoomsSeo($seoData, $data)
    {
        $totalRooms = $data['total_rooms'] ?? 0;
        $availableRooms = $data['available_rooms'] ?? 0;
        $rooms = $data['rooms'] ?? [];
        
        return array_merge($seoData, [
            'title' => "Kelola {$totalRooms} Ruang Meeting - Admin Panel",
            'description' => "Kelola {$totalRooms} ruang meeting dengan {$availableRooms} ruang tersedia. Tambah, edit, atau hapus ruang meeting sesuai kebutuhan.",
            'keywords' => "kelola ruang meeting, {$totalRooms} ruang, {$availableRooms} tersedia, admin panel",
            'og_title' => "Kelola Ruang Meeting",
            'og_description' => "Kelola {$totalRooms} ruang meeting yang tersedia",
            'structured_data' => $this->generateAdminRoomsStructuredData($rooms)
        ]);
    }
    
    /**
     * Enhance admin bookings SEO
     */
    private function enhanceAdminBookingsSeo($seoData, $data)
    {
        $totalBookings = $data['total_bookings'] ?? 0;
        $pendingBookings = $data['pending_bookings'] ?? 0;
        $confirmedBookings = $data['confirmed_bookings'] ?? 0;
        $bookings = $data['bookings'] ?? [];
        
        return array_merge($seoData, [
            'title' => "Kelola {$totalBookings} Pemesanan - Admin Panel",
            'description' => "Kelola {$totalBookings} pemesanan ruang meeting. {$pendingBookings} menunggu konfirmasi, {$confirmedBookings} sudah dikonfirmasi.",
            'keywords' => "kelola pemesanan, {$totalBookings} pemesanan, {$pendingBookings} pending, {$confirmedBookings} confirmed",
            'og_title' => "Kelola Pemesanan",
            'og_description' => "Kelola {$totalBookings} pemesanan ruang meeting",
            'structured_data' => $this->generateAdminBookingsStructuredData($bookings)
        ]);
    }
    
    /**
     * Generate structured data for user dashboard
     */
    private function generateUserDashboardStructuredData($user, $data)
    {
        return [
            [
                '@type' => 'WebPage',
                'name' => "Dashboard {$user['full_name']}",
                'description' => "Dashboard pribadi untuk mengelola pemesanan ruang meeting",
                'url' => request()->url(),
                'isPartOf' => [
                    '@type' => 'WebSite',
                    'name' => config('seo.default.site_name'),
                    'url' => config('seo.default.site_url')
                ],
                'author' => [
                    '@type' => 'Person',
                    'name' => $user['full_name'],
                    'email' => $user['email']
                ]
            ]
        ];
    }
    
    /**
     * Generate structured data for admin dashboard
     */
    private function generateAdminDashboardStructuredData($admin, $data)
    {
        return [
            [
                '@type' => 'WebPage',
                'name' => "Dashboard Admin {$admin['full_name']}",
                'description' => "Dashboard administrator untuk mengelola sistem",
                'url' => request()->url(),
                'isPartOf' => [
                    '@type' => 'WebSite',
                    'name' => config('seo.default.site_name'),
                    'url' => config('seo.default.site_url')
                ],
                'author' => [
                    '@type' => 'Person',
                    'name' => $admin['full_name'],
                    'email' => $admin['email'],
                    'jobTitle' => 'Administrator'
                ]
            ]
        ];
    }
    
    /**
     * Generate structured data for user bookings
     */
    private function generateUserBookingsStructuredData($user, $bookings)
    {
        $structuredData = [
            [
                '@type' => 'CollectionPage',
                'name' => "Pemesanan {$user['full_name']}",
                'description' => "Koleksi pemesanan ruang meeting pengguna",
                'url' => request()->url(),
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'numberOfItems' => count($bookings),
                    'itemListElement' => []
                ]
            ]
        ];
        
        foreach ($bookings as $index => $booking) {
            $structuredData[0]['mainEntity']['itemListElement'][] = [
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
                    ]
                ]
            ];
        }
        
        return $structuredData;
    }
    
    /**
     * Generate structured data for create booking
     */
    private function generateCreateBookingStructuredData($rooms)
    {
        $structuredData = [
            [
                '@type' => 'WebPage',
                'name' => 'Buat Pemesanan Baru',
                'description' => 'Form untuk membuat pemesanan ruang meeting baru',
                'url' => request()->url(),
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'numberOfItems' => count($rooms),
                    'itemListElement' => []
                ]
            ]
        ];
        
        foreach ($rooms as $index => $room) {
            $structuredData[0]['mainEntity']['itemListElement'][] = [
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
     * Generate structured data for admin users
     */
    private function generateAdminUsersStructuredData($users)
    {
        $structuredData = [
            [
                '@type' => 'WebPage',
                'name' => 'Kelola Pengguna',
                'description' => 'Halaman admin untuk mengelola pengguna sistem',
                'url' => request()->url(),
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'numberOfItems' => count($users),
                    'itemListElement' => []
                ]
            ]
        ];
        
        foreach ($users as $index => $user) {
            $structuredData[0]['mainEntity']['itemListElement'][] = [
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
     * Generate structured data for admin rooms
     */
    private function generateAdminRoomsStructuredData($rooms)
    {
        $structuredData = [
            [
                '@type' => 'WebPage',
                'name' => 'Kelola Ruang Meeting',
                'description' => 'Halaman admin untuk mengelola ruang meeting',
                'url' => request()->url(),
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'numberOfItems' => count($rooms),
                    'itemListElement' => []
                ]
            ]
        ];
        
        foreach ($rooms as $index => $room) {
            $structuredData[0]['mainEntity']['itemListElement'][] = [
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
     * Generate structured data for admin bookings
     */
    private function generateAdminBookingsStructuredData($bookings)
    {
        $structuredData = [
            [
                '@type' => 'WebPage',
                'name' => 'Kelola Pemesanan',
                'description' => 'Halaman admin untuk mengelola pemesanan',
                'url' => request()->url(),
                'mainEntity' => [
                    '@type' => 'ItemList',
                    'numberOfItems' => count($bookings),
                    'itemListElement' => []
                ]
            ]
        ];
        
        foreach ($bookings as $index => $booking) {
            $structuredData[0]['mainEntity']['itemListElement'][] = [
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
     * Clear SEO cache
     */
    public function clearSeoCache()
    {
        Cache::forget('seo_data_*');
    }
    
    /**
     * Get SEO statistics
     */
    public function getSeoStats()
    {
        return [
            'total_pages' => count(config('seo.pages')),
            'cache_enabled' => config('cache.default') !== 'array',
            'last_updated' => now()->toISOString(),
        ];
    }
}
