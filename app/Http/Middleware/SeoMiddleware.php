<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\SeoController;

class SeoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip SEO processing for POST requests to avoid CSRF issues
        if ($request->isMethod('POST')) {
            return $next($request);
        }
        
        // Get current route name
        $routeName = $request->route()?->getName();
        
        // Determine page type based on route
        $pageType = $this->determinePageType($routeName, $request);
        
        // Generate SEO data only for GET requests
        try {
            $seoController = new SeoController();
            $seoData = $seoController->generateSeoData($pageType, $this->getPageData($request));
            
            // Share SEO data with all views
            View::share('seoData', $seoData);
            View::share('pageType', $pageType);
        } catch (\Exception $e) {
            // If SEO data generation fails, continue without it
            \Log::warning('SEO data generation failed: ' . $e->getMessage());
        }
        
        // Add performance headers
        $response = $next($request);
        
        // Add SEO-related headers only for successful responses
        if ($response->getStatusCode() === 200) {
            $this->addSeoHeaders($response, $seoData ?? []);
        }
        
        return $response;
    }
    
    /**
     * Determine page type based on route name
     */
    private function determinePageType($routeName, $request)
    {
        if (!$routeName) {
            return 'default';
        }
        
        // Map route names to page types
        $routeMap = [
            'login' => 'login',
            'register' => 'register',
            'user.dashboard' => 'user_dashboard',
            'admin.dashboard' => 'admin_dashboard',
            'user.bookings' => 'user_bookings',
            'user.bookings.create' => 'create_booking',
            'admin.users' => 'admin_users',
            'admin.rooms' => 'admin_rooms',
            'admin.bookings' => 'admin_bookings',
            'privacy.policy' => 'privacy_policy',
            'terms.service' => 'terms_of_service',
        ];
        
        return $routeMap[$routeName] ?? 'default';
    }
    
    /**
     * Get page-specific data for SEO
     */
    private function getPageData($request)
    {
        $data = [];
        
        // Skip database queries for auth pages to avoid CSRF issues
        $routeName = $request->route()?->getName();
        $authRoutes = ['login', 'register', 'password.request', 'password.reset', 'email.verify'];
        
        if (in_array($routeName, $authRoutes)) {
            return $data;
        }
        
        // Get user data if logged in
        if (session('user_logged_in')) {
            $data['user'] = session('user_data');
        }
        
        // Add route-specific data only for non-auth routes
        switch ($routeName) {
            case 'user.dashboard':
                $data = array_merge($data, $this->getUserDashboardData());
                break;
            case 'admin.dashboard':
                $data = array_merge($data, $this->getAdminDashboardData());
                break;
            case 'user.bookings':
                $data = array_merge($data, $this->getUserBookingsData());
                break;
            case 'user.bookings.create':
                $data = array_merge($data, $this->getCreateBookingData());
                break;
            case 'admin.users':
                $data = array_merge($data, $this->getAdminUsersData());
                break;
            case 'admin.rooms':
                $data = array_merge($data, $this->getAdminRoomsData());
                break;
            case 'admin.bookings':
                $data = array_merge($data, $this->getAdminBookingsData());
                break;
        }
        
        return $data;
    }
    
    /**
     * Get user dashboard data
     */
    private function getUserDashboardData()
    {
        try {
            $user = session('user_data');
            if (!$user) {
                return [];
            }
            
            // Get user's bookings count with error handling
            $totalBookings = \App\Models\Booking::where('user_id', $user['id'])->count();
            $pendingBookings = \App\Models\Booking::where('user_id', $user['id'])
                ->where('status', 'pending')->count();
            $confirmedBookings = \App\Models\Booking::where('user_id', $user['id'])
                ->where('status', 'confirmed')->count();
            
            return [
                'total_bookings' => $totalBookings,
                'pending_bookings' => $pendingBookings,
                'confirmed_bookings' => $confirmedBookings,
            ];
        } catch (\Exception $e) {
            \Log::warning('Failed to get user dashboard data: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get admin dashboard data
     */
    private function getAdminDashboardData()
    {
        try {
            $totalUsers = \App\Models\User::count();
            $totalRooms = \App\Models\MeetingRoom::count();
            $totalBookings = \App\Models\Booking::count();
            $confirmedBookings = \App\Models\Booking::where('status', 'confirmed')->count();
            
            return [
                'total_users' => $totalUsers,
                'total_rooms' => $totalRooms,
                'total_bookings' => $totalBookings,
                'confirmed_bookings' => $confirmedBookings,
            ];
        } catch (\Exception $e) {
            \Log::warning('Failed to get admin dashboard data: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get user bookings data
     */
    private function getUserBookingsData()
    {
        try {
            $user = session('user_data');
            if (!$user) {
                return [];
            }
            
            $bookings = \App\Models\Booking::where('user_id', $user['id'])
                ->with(['meetingRoom', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            return [
                'total_bookings' => \App\Models\Booking::where('user_id', $user['id'])->count(),
                'bookings' => $bookings,
            ];
        } catch (\Exception $e) {
            \Log::warning('Failed to get user bookings data: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get create booking data
     */
    private function getCreateBookingData()
    {
        try {
            $rooms = \App\Models\MeetingRoom::where('is_active', true)->get();
            
            return [
                'available_rooms' => $rooms,
            ];
        } catch (\Exception $e) {
            \Log::warning('Failed to get create booking data: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get admin users data
     */
    private function getAdminUsersData()
    {
        try {
            $users = \App\Models\User::orderBy('created_at', 'desc')->limit(10)->get();
            $totalUsers = \App\Models\User::count();
            $activeUsers = \App\Models\User::whereNotNull('last_login_at')->count();
            
            return [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'users' => $users,
            ];
        } catch (\Exception $e) {
            \Log::warning('Failed to get admin users data: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get admin rooms data
     */
    private function getAdminRoomsData()
    {
        try {
            $rooms = \App\Models\MeetingRoom::orderBy('created_at', 'desc')->limit(10)->get();
            $totalRooms = \App\Models\MeetingRoom::count();
            $availableRooms = \App\Models\MeetingRoom::where('is_active', true)->count();
            
            return [
                'total_rooms' => $totalRooms,
                'available_rooms' => $availableRooms,
                'rooms' => $rooms,
            ];
        } catch (\Exception $e) {
            \Log::warning('Failed to get admin rooms data: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get admin bookings data
     */
    private function getAdminBookingsData()
    {
        try {
            $bookings = \App\Models\Booking::with(['meetingRoom', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            $totalBookings = \App\Models\Booking::count();
            $pendingBookings = \App\Models\Booking::where('status', 'pending')->count();
            $confirmedBookings = \App\Models\Booking::where('status', 'confirmed')->count();
            
            return [
                'total_bookings' => $totalBookings,
                'pending_bookings' => $pendingBookings,
                'confirmed_bookings' => $confirmedBookings,
                'bookings' => $bookings,
            ];
        } catch (\Exception $e) {
            \Log::warning('Failed to get admin bookings data: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Add SEO-related headers to response
     */
    private function addSeoHeaders($response, $seoData = [])
    {
        try {
            // Add cache headers for better performance
            $response->headers->set('Cache-Control', 'public, max-age=3600');
            $response->headers->set('Vary', 'Accept-Encoding, User-Agent');
            
            // Add security headers
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'DENY');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
            
            // Add performance headers
            $response->headers->set('X-DNS-Prefetch-Control', 'on');
        } catch (\Exception $e) {
            \Log::warning('Failed to add SEO headers: ' . $e->getMessage());
        }
        
        return $response;
    }
}
