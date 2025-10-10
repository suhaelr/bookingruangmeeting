<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MeetingRoom;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|max:255|unique:users',
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'phone' => 'nullable|string|max:20',
                'department' => 'nullable|string|max:100',
                'role' => 'required|in:admin,user'
            ]);

            $user = User::create([
                'username' => $request->username,
                'name' => $request->full_name,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
                'department' => $request->department,
                'role' => $request->role,
                'email_verified_at' => now(),
            ]);

            \Log::info('User created successfully', [
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role
            ]);

            return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan!');
        } catch (ValidationException $e) {
            \Log::error('Validation error in storeUser', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error in storeUser', [
                'message' => $e->getMessage(),
                'input' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal menambahkan user: ' . $e->getMessage())->withInput();
        }
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

    public function createRoom()
    {
        return view('admin.rooms.create');
    }

    public function storeRoom(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'capacity' => 'required|numeric|min:1',
                'location' => 'required|string|max:255',
                'is_active' => 'required|string|in:0,1,true,false,on,off,',
                'amenities' => 'nullable|string'
            ]);

            $amenities = $request->amenities ? 
                array_map('trim', explode(',', $request->amenities)) : [];

            // Handle is_active conversion from string to boolean
            $isActiveValue = $request->input('is_active');
            $isActive = false;
            if (in_array($isActiveValue, ['1', 'true', 'on', 1, true])) {
                $isActive = true;
            } elseif (in_array($isActiveValue, ['0', 'false', 'off', 0, false])) {
                $isActive = false;
            }

            $room = MeetingRoom::create([
                'name' => $request->name,
                'description' => $request->description,
                'capacity' => (int)$request->capacity,
                'location' => $request->location,
                'is_active' => $isActive,
                'amenities' => $amenities
            ]);

            return redirect()->route('admin.rooms')->with('success', 'Room berhasil ditambahkan!');
        } catch (ValidationException $e) {
            \Log::warning('Validation error in storeRoom', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Validasi gagal, periksa kembali data yang dimasukkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan room: ' . $e->getMessage())->withInput();
        }
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
                'status' => 'required|in:pending,confirmed,cancelled,completed',
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
        } catch (ValidationException $e) {
            \Log::warning('Validation error in updateBookingStatus', [
                'errors' => $e->errors(),
                'booking_id' => $id,
                'input' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
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

    public function clearAllNotifications()
    {
        try {
            // Clear all notifications from session
            session()->forget('admin_notifications');
            
            return response()->json([
                'success' => true,
                'message' => 'All notifications cleared successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear notifications: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'department' => 'nullable|string|max:100',
                'role' => 'required|in:admin,user'
            ]);

            $user->update([
                'full_name' => $request->full_name,
                'name' => $request->full_name, // Update name field too
                'email' => $request->email,
                'phone' => $request->phone,
                'department' => $request->department,
                'role' => $request->role,
            ]);

            \Log::info('User updated successfully', [
                'user_id' => $user->id,
                'updated_fields' => $request->only(['full_name', 'email', 'phone', 'department', 'role'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate!'
            ]);
        } catch (ValidationException $e) {
            \Log::error('Validation error in updateUser', [
                'errors' => $e->errors(),
                'user_id' => $id,
                'input' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $e->errors()['email'] ?? []),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in updateUser', [
                'message' => $e->getMessage(),
                'user_id' => $id,
                'input' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateRoom(Request $request, $id)
    {
        try {
            \Log::info('updateRoom called', [
                'room_id' => $id,
                'payload' => $request->all(),
                'json_payload' => $request->json()->all(),
                'headers' => $request->headers->all(),
                'method' => $request->method(),
                'url' => $request->url(),
                'content_type' => $request->header('Content-Type'),
                'timestamp' => now()
            ]);

            $room = MeetingRoom::findOrFail($id);
            
            // Get data from JSON or form data
            $data = $request->json()->all() ?: $request->all();
            
            // Debug: Log individual field values
            \Log::info('Field values received', [
                'name' => $data['name'] ?? null,
                'capacity' => $data['capacity'] ?? null,
                'description' => $data['description'] ?? null,
                'location' => $data['location'] ?? null,
                'is_active' => $data['is_active'] ?? null,
                'amenities' => $data['amenities'] ?? null,
                'name_type' => gettype($data['name'] ?? null),
                'capacity_type' => gettype($data['capacity'] ?? null),
                'location_type' => gettype($data['location'] ?? null)
            ]);
            
            // Validate the data
            $validator = \Illuminate\Support\Facades\Validator::make($data, [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'capacity' => 'required|numeric|min:1',
                'location' => 'required|string|max:255',
                'is_active' => 'nullable|string|in:0,1,true,false,on,off,',
                'amenities' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $amenities = isset($data['amenities']) && $data['amenities'] ? 
                array_map('trim', explode(',', $data['amenities'])) : [];
            $amenities = array_values(array_filter($amenities, fn($item) => $item !== ''));

            // Handle is_active conversion from string to boolean
            $isActive = $room->is_active; // Default to current value
            if (isset($data['is_active'])) {
                $isActiveValue = $data['is_active'];
                \Log::info('is_active value received', [
                    'value' => $isActiveValue,
                    'type' => gettype($isActiveValue)
                ]);
                
                if (in_array($isActiveValue, ['1', 'true', 'on', 1, true])) {
                    $isActive = true;
                } elseif (in_array($isActiveValue, ['0', 'false', 'off', 0, false])) {
                    $isActive = false;
                }
            }

            $room->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'capacity' => (int)$data['capacity'],
                'location' => $data['location'],
                'is_active' => $isActive,
                'amenities' => $amenities
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Room berhasil diupdate!'
            ]);
        } catch (ModelNotFoundException $e) {
            \Log::warning('Room not found in updateRoom', [
                'room_id' => $id,
                'input' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Room tidak ditemukan'
            ], 404);
        } catch (ValidationException $e) {
            \Log::error('Validation error in updateRoom', [
                'errors' => $e->errors(),
                'room_id' => $id,
                'input' => $request->all(),
                'validation_rules' => [
                    'name' => 'required|string|max:255',
                    'description' => 'nullable|string',
                    'capacity' => 'required|integer|min:1',
                    'location' => 'required|string|max:255',
                    'is_active' => 'nullable|string|in:0,1,true,false,on,off,',
                    'amenities' => 'nullable|string'
                ]
            ]);
            // Flatten errors manually for compatibility
            $flattenedErrors = [];
            foreach ($e->errors() as $field => $messages) {
                $flattenedErrors = array_merge($flattenedErrors, $messages);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', $flattenedErrors),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in updateRoom', [
                'message' => $e->getMessage(),
                'room_id' => $id,
                'input' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
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

    public function downloadDokumenPerizinan($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            
            if (!$booking->dokumen_perizinan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen perizinan tidak ditemukan'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $booking->dokumen_perizinan);
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File dokumen tidak ditemukan'
                ], 404);
            }

            return response()->download($filePath, 'dokumen_perizinan_' . $booking->id . '.pdf');
        } catch (\Exception $e) {
            \Log::error('Error downloading dokumen perizinan: ' . $e->getMessage(), [
                'booking_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunduh dokumen: ' . $e->getMessage()
            ], 500);
        }
    }
}
