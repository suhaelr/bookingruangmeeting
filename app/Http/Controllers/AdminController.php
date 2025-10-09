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
        $rooms = MeetingRoom::withCount('bookings')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Debug: Log booking counts
        \Log::info('Admin rooms - booking counts:', [
            'rooms' => $rooms->map(function($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                    'bookings_count' => $room->bookings_count
                ];
            })
        ]);
        
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
        try {
            \Log::info('AdminController::updateBookingStatus called', [
                'booking_id' => $id,
                'request_data' => $request->all()
            ]);

            $booking = Booking::findOrFail($id);
            
            $request->validate([
                'status' => 'required|in:pending,confirmed,cancelled',
                'reason' => 'nullable|string|max:255'
            ]);

            \Log::info('Validation passed, updating booking status', [
                'booking_id' => $booking->id,
                'old_status' => $booking->status,
                'new_status' => $request->status
            ]);

            $booking->updateStatus($request->status, $request->reason);

            \Log::info('Booking status updated successfully');

            // Send notification to admin about status change
            $this->notifyAdmin('Booking Status Updated', "Booking '{$booking->title}' status changed to {$request->status}");

            return response()->json([
                'success' => true,
                'message' => 'Status booking berhasil diupdate!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in updateBookingStatus: ' . $e->getMessage(), [
                'booking_id' => $id,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status: ' . $e->getMessage()
            ], 500);
        }
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

    private function notifyAdmin($title, $message)
    {
        // In a real application, this would send notifications via:
        // - Database notifications
        // - Real-time websockets
        // - Email notifications
        // - Push notifications
        
        // For now, we'll store in session for demo purposes
        $notifications = session('admin_notifications', []);
        $notifications[] = [
            'id' => uniqid(),
            'title' => $title,
            'message' => $message,
            'time' => now()->format('Y-m-d H:i:s'),
            'read' => false,
            'type' => $title === 'Booking Status Updated' ? 'info' : 'success'
        ];
        session(['admin_notifications' => $notifications]);
    }

    public function getNotifications()
    {
        $notifications = session('admin_notifications', []);
        
        // Add some default notifications if none exist
        if (empty($notifications)) {
            $notifications = [
                [
                    'id' => 1,
                    'title' => 'Booking Updated',
                    'message' => 'User John Doe updated their booking "Team Meeting" for tomorrow',
                    'time' => '5 minutes ago',
                    'read' => false,
                    'type' => 'info'
                ],
                [
                    'id' => 2,
                    'title' => 'Booking Cancelled',
                    'message' => 'User Jane Smith cancelled their booking "Project Review"',
                    'time' => '15 minutes ago',
                    'read' => false,
                    'type' => 'warning'
                ],
                [
                    'id' => 3,
                    'title' => 'New Booking Request',
                    'message' => 'User Mike Johnson requested a new booking for Conference Room A',
                    'time' => '1 hour ago',
                    'read' => true,
                    'type' => 'success'
                ],
                [
                    'id' => 4,
                    'title' => 'Room Conflict Detected',
                    'message' => 'Potential schedule conflict detected for Meeting Room B at 2:00 PM',
                    'time' => '2 hours ago',
                    'read' => false,
                    'type' => 'error'
                ]
            ];
        }

        return response()->json($notifications);
    }

    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'department' => 'nullable|string|max:100',
            ]);

            $user->update([
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'department' => $request->department,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateRoom(Request $request, $id)
    {
        try {
            $room = MeetingRoom::findOrFail($id);
            
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'capacity' => 'required|integer|min:1',
                'location' => 'required|string|max:255',
                'hourly_rate' => 'required|numeric|min:0',
                'is_active' => 'required|boolean',
                'amenities' => 'nullable|string'
            ]);

            $amenities = $request->amenities ? 
                array_map('trim', explode(',', $request->amenities)) : [];

            $room->update([
                'name' => $request->name,
                'description' => $request->description,
                'capacity' => $request->capacity,
                'location' => $request->location,
                'hourly_rate' => $request->hourly_rate,
                'is_active' => $request->is_active,
                'amenities' => $amenities
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Room berhasil diupdate!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate room: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteRoom($id)
    {
        try {
            $room = MeetingRoom::findOrFail($id);
            
            // Check if room has bookings
            if ($room->bookings()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus room yang memiliki bookings. Hapus bookings terlebih dahulu.'
                ], 400);
            }

            $room->delete();

            return response()->json([
                'success' => true,
                'message' => 'Room berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus room: ' . $e->getMessage()
            ], 500);
        }
    }
}
