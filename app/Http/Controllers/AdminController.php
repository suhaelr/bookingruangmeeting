<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MeetingRoom;
use App\Models\Booking;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Statistik umum
        $stats = [
            'total_users' => User::count(),
            'total_rooms' => MeetingRoom::count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),
            'revenue_this_month' => Booking::where('status', 'confirmed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_cost'),
            'active_rooms' => MeetingRoom::where('is_active', true)->count(),
        ];

        // Booking terbaru
        $recentBookings = Booking::with(['user', 'meetingRoom'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Booking hari ini
        $todayBookings = Booking::with(['user', 'meetingRoom'])
            ->whereDate('start_time', today())
            ->orderBy('start_time')
            ->get();

        // Statistik per bulan (6 bulan terakhir)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $date->format('M Y'),
                'bookings' => Booking::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'revenue' => Booking::where('status', 'confirmed')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->sum('total_cost'),
            ];
        }

        // Ruang meeting dengan booking terbanyak
        $popularRooms = MeetingRoom::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentBookings',
            'todayBookings',
            'monthlyStats',
            'popularRooms'
        ));
    }

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function rooms()
    {
        $rooms = MeetingRoom::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.rooms', compact('rooms'));
    }

    public function bookings()
    {
        $bookings = Booking::with(['user', 'meetingRoom'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.bookings', compact('bookings'));
    }

    public function updateBookingStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
            'reason' => 'nullable|string|max:255'
        ]);

        $booking->updateStatus($request->status, $request->reason);

        return back()->with('success', 'Status booking berhasil diupdate!');
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Prevent deleting admin users
            if ($user->role === 'admin') {
                return back()->with('error', 'Cannot delete admin users.');
            }
            
            // Delete user's bookings first
            $user->bookings()->delete();
            
            // Delete the user
            $user->delete();
            
            return back()->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}
