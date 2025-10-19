<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeetingRoom;
use App\Models\Booking;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = session('user_data');
        
        // Validate user exists in database
        try {
            $userModel = User::find($user['id']);
            if (!$userModel) {
                \Log::error('User not found in database for dashboard', [
                    'session_user_id' => $user['id'],
                    'session_user_data' => $user
                ]);
                return redirect()->route('login')->with('error', 'User session invalid. Please login again.');
            }
        } catch (\Exception $e) {
            \Log::error('Database error during user validation in dashboard', [
                'error' => $e->getMessage(),
                'session_user_id' => $user['id'],
                'session_user_data' => $user
            ]);
            return redirect()->route('login')->with('error', 'Database error. Please login again.');
        }
        
        // Statistik booking user
        $stats = [
            'total_bookings' => Booking::where('user_id', $userModel->id)->count(),
            'pending_bookings' => Booking::where('user_id', $userModel->id)
                ->where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('user_id', $userModel->id)
                ->where('status', 'confirmed')->count(),
            'cancelled_bookings' => Booking::where('user_id', $userModel->id)
                ->where('status', 'cancelled')->count(),
            'this_month' => Booking::where('user_id', $userModel->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        // Booking aktif user
        $activeBookings = Booking::with('meetingRoom')
            ->where('user_id', $userModel->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_time', '>=', now())
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        // Booking hari ini
        $todayBookings = Booking::with('meetingRoom')
            ->where('user_id', $userModel->id)
            ->whereDate('start_time', today())
            ->orderBy('start_time')
            ->get();

        // Ruang meeting tersedia
        $availableRooms = MeetingRoom::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get user notifications
        $userModel = User::find($user['id']);
        $notifications = collect([]);
        
        if ($userModel) {
            $notifications = $userModel->notifications()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        // Get room availability grid data
        $roomAvailabilityGrid = $this->getRoomAvailabilityGrid();

        return view('user.dashboard', compact(
            'stats',
            'activeBookings',
            'todayBookings',
            'availableRooms',
            'notifications',
            'roomAvailabilityGrid'
        ));
    }

    public function profile()
    {
        $user = session('user_data');
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
        ]);

        // Update session data
        $userData = session('user_data');
        $userData['full_name'] = $request->full_name;
        $userData['email'] = $request->email;
        $userData['phone'] = $request->phone;
        $userData['department'] = $request->department;
        session(['user_data' => $userData]);

        return back()->with('success', 'Profile berhasil diupdate!');
    }

    public function bookings()
    {
        $user = session('user_data');
        
        // Validate user exists in database
        $userModel = User::find($user['id']);
        if (!$userModel) {
            \Log::error('User not found in database for bookings', [
                'session_user_id' => $user['id'],
                'session_user_data' => $user
            ]);
            return redirect()->route('login')->with('error', 'User session invalid. Please login again.');
        }
        
        $bookings = Booking::with('meetingRoom')
            ->where('user_id', $userModel->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        \Log::info('User bookings retrieved', [
            'user_id' => $userModel->id,
            'bookings_count' => $bookings->count()
        ]);
        
        return view('user.bookings', compact('bookings'));
    }

    public function createBooking()
    {
        $rooms = MeetingRoom::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Check if no rooms are available
        if ($rooms->count() === 0) {
            return view('user.create-booking', compact('rooms'))
                ->with('warning', 'Saat ini tidak ada ruang meeting yang tersedia. Silakan hubungi administrator untuk informasi lebih lanjut.');
        }
        
        return view('user.create-booking', compact('rooms'));
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:meeting_rooms,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $room = MeetingRoom::findOrFail($request->room_id);
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        
        $conflictingBookings = $this->getConflictingBookings($room->id, $startTime, $endTime);
        
        if ($conflictingBookings->count() > 0) {
            $conflictDetails = $this->formatConflictDetails($conflictingBookings, $room);
            return response()->json([
                'available' => false,
                'message' => $conflictDetails,
                'conflicts' => $conflictingBookings->map(function($booking) {
                    return [
                        'title' => $booking->title,
                        'user' => $booking->user->full_name,
                        'start_time' => $booking->start_time->format('d M Y H:i'),
                        'end_time' => $booking->end_time->format('H:i'),
                    ];
                })
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Ruang tersedia untuk waktu yang dipilih!'
        ]);
    }

    public function storeBooking(Request $request)
    {
        $user = session('user_data');
        
        $request->validate([
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'attendees_count' => 'required|integer|min:1',
            'attendees' => 'nullable|string',
            'special_requirements' => 'nullable|string',
            'unit_kerja' => 'required|string|max:255',
            'dokumen_perizinan' => 'required|file|mimes:pdf|max:2048',
        ]);

        $room = MeetingRoom::findOrFail($request->meeting_room_id);
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        
        // Check capacity
        if ($request->attendees_count > $room->capacity) {
            return back()->withErrors([
                'attendees_count' => "Jumlah peserta ({$request->attendees_count}) melebihi kapasitas ruangan ({$room->capacity} kursi)."
            ])->withInput();
        }
        
        // Check availability with detailed feedback
        $conflictingBookings = $this->getConflictingBookings($room->id, $startTime, $endTime);
        
        if ($conflictingBookings->count() > 0) {
            $conflictDetails = $this->formatConflictDetails($conflictingBookings, $room);
            return back()->withErrors([
                'start_time' => $conflictDetails
            ])->withInput();
        }

        // Handle file upload
        $dokumenPerizinanPath = null;
        if ($request->hasFile('dokumen_perizinan')) {
            $file = $request->file('dokumen_perizinan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $dokumenPerizinanPath = $file->storeAs('dokumen_perizinan', $filename, 'public');
        }

        // Process attendees - convert string to array
        $attendees = [];
        if ($request->attendees) {
            $attendees = array_filter(array_map('trim', explode(',', $request->attendees)));
        }

        // Validate user exists in database
        $userModel = User::find($user['id']);
        if (!$userModel) {
            \Log::error('User not found in database', [
                'session_user_id' => $user['id'],
                'session_user_data' => $user
            ]);
            return redirect()->route('login')->with('error', 'User session invalid. Please login again.');
        }

        // Log user validation for debugging
        \Log::info('User validation successful', [
            'user_id' => $userModel->id,
            'username' => $userModel->username,
            'email' => $userModel->email
        ]);

        try {
            $booking = Booking::create([
                'user_id' => $userModel->id, // Use the actual user model ID
                'meeting_room_id' => $request->meeting_room_id,
                'title' => $request->title,
                'description' => $request->description,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'attendees_count' => $request->attendees_count,
                'attendees' => $attendees,
                'special_requirements' => $request->special_requirements,
                'unit_kerja' => $request->unit_kerja,
                'dokumen_perizinan' => $dokumenPerizinanPath,
                'total_cost' => 0, // Set to 0 since we removed pricing
            ]);
        } catch (\Exception $e) {
            \Log::error('Booking creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $userModel->id,
                'request_data' => $request->all()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat membuat booking. Silakan coba lagi.')
                ->withInput();
        }

        // Send notification to admin
        $this->notifyAdmin('New Booking Request', "User {$user['full_name']} has requested a new booking: {$booking->title}");

        \Log::info('New booking created', [
            'booking_id' => $booking->id,
            'user_id' => $user['id'],
            'title' => $booking->title,
            'unit_kerja' => $booking->unit_kerja
        ]);

        return redirect()->route('user.dashboard')
            ->with('success', 'Booking berhasil dibuat! Menunggu konfirmasi admin.');
    }

    public function updateBooking(Request $request, $id)
    {
        try {
            $user = session('user_data');
            $booking = Booking::where('id', $id)
                ->where('user_id', $user['id'])
                ->firstOrFail();

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'required|date|after:now',
                'end_time' => 'required|date|after:start_time',
                'special_requirements' => 'nullable|string',
            ]);

            $startTime = Carbon::parse($request->start_time);
            $endTime = Carbon::parse($request->end_time);
            
            // Check for conflicts excluding current booking
            $conflictingBookings = $this->getConflictingBookings($booking->meeting_room_id, $startTime, $endTime, $id);
            
            if ($conflictingBookings->count() > 0) {
                $conflictDetails = $this->formatConflictDetails($conflictingBookings, $booking->meetingRoom);
                return response()->json([
                    'success' => false,
                    'message' => $conflictDetails
                ], 422);
            }

            // Recalculate total cost (hourly_rate removed, set to 0)
            $totalCost = 0.00;

            // Update booking
            $booking->update([
                'title' => $request->title,
                'description' => $request->description,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'special_requirements' => $request->special_requirements,
                'total_cost' => $totalCost,
            ]);

            // Send notification to admin
            $this->notifyAdmin('Booking Updated', "User {$user['full_name']} updated their booking: {$booking->title}");

            \Log::info('Booking updated successfully', [
                'booking_id' => $booking->id,
                'user_id' => $user['id'],
                'updated_fields' => $request->only(['title', 'description', 'start_time', 'end_time', 'special_requirements'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil diupdate!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in updateBooking', [
                'errors' => $e->errors(),
                'booking_id' => $id,
                'user_id' => session('user_data')['id'] ?? null,
                'input' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', array_flatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in updateBooking', [
                'message' => $e->getMessage(),
                'booking_id' => $id,
                'user_id' => session('user_data')['id'] ?? null,
                'input' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancelBooking(Request $request, $id)
    {
        try {
            $user = session('user_data');
            $booking = Booking::where('id', $id)
                ->where('user_id', $user['id'])
                ->firstOrFail();

            // Check if booking can be cancelled
            if (!$booking->canBeCancelled()) {
                return back()->with('error', 'Booking tidak dapat dibatalkan pada waktu ini.');
            }

            // Update booking status
            $booking->updateStatus('cancelled', 'Cancelled by user');

            // Send notification to admin
            $this->notifyAdmin('Booking Cancelled', "User {$user['full_name']} cancelled their booking: {$booking->title}");

            \Log::info('Booking cancelled successfully', [
                'booking_id' => $booking->id,
                'user_id' => $user['id']
            ]);

            return back()->with('success', 'Booking berhasil dibatalkan.');
        } catch (\Exception $e) {
            \Log::error('Error in cancelBooking', [
                'message' => $e->getMessage(),
                'booking_id' => $id,
                'user_id' => session('user_data')['id'] ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal membatalkan booking: ' . $e->getMessage());
        }
    }

    private function notifyAdmin($title, $message)
    {
        // Store notification in session for admin
        $notifications = session('admin_notifications', []);
        $notifications[] = [
            'id' => uniqid(),
            'title' => $title,
            'message' => $message,
            'time' => now()->format('Y-m-d H:i:s'),
            'read' => false,
            'type' => 'success'
        ];
        session(['admin_notifications' => $notifications]);
        
        \Log::info('Admin notification sent', [
            'title' => $title,
            'message' => $message
        ]);
    }

    private function getConflictingBookings($roomId, $startTime, $endTime, $excludeId = null)
    {
        $query = Booking::with('user')
            ->where('meeting_room_id', $roomId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_time', '>=', now()) // Only check future bookings
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime->subSecond()])
                      ->orWhereBetween('end_time', [$startTime->addSecond(), $endTime])
                      ->orWhere(function ($query) use ($startTime, $endTime) {
                          $query->where('start_time', '<=', $startTime)
                                ->where('end_time', '>=', $endTime);
                      });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }

    private function formatConflictDetails($conflictingBookings, $room)
    {
        $conflicts = [];
        $suggestedTimes = [];
        
        foreach ($conflictingBookings as $booking) {
            $conflicts[] = "• {$booking->title} oleh {$booking->user->full_name} ({$booking->start_time->format('d M Y H:i')} - {$booking->end_time->format('H:i')})";
        }
        
        // Find next available slots
        $nextAvailable = $this->findNextAvailableSlots($room->id, $conflictingBookings);
        
        $message = "❌ Ruang meeting tidak tersedia pada waktu yang dipilih.\n\n";
        $message .= "📅 Sudah dibooking oleh:\n" . implode("\n", $conflicts) . "\n\n";
        
        if (count($nextAvailable) > 0) {
            $message .= "💡 Saran waktu kosong terdekat:\n";
            foreach ($nextAvailable as $slot) {
                $message .= "• {$slot['date']} {$slot['time']} ({$slot['duration']} jam)\n";
            }
        } else {
            $message .= "⚠️ Tidak ada slot kosong dalam 7 hari ke depan untuk ruangan ini.";
        }
        
        return $message;
    }

    private function findNextAvailableSlots($roomId, $existingBookings)
    {
        $suggestions = [];
        $today = Carbon::today();
        
        // Check next 7 days
        for ($i = 1; $i <= 7; $i++) {
            $date = $today->copy()->addDays($i);
            
            // Check morning slot (9:00-12:00)
            $morningStart = $date->copy()->setTime(9, 0);
            $morningEnd = $date->copy()->setTime(12, 0);
            
            if ($this->isTimeSlotAvailable($roomId, $morningStart, $morningEnd, $existingBookings)) {
                $suggestions[] = [
                    'date' => $date->format('d M Y'),
                    'time' => '09:00 - 12:00',
                    'duration' => '3'
                ];
            }
            
            // Check afternoon slot (13:00-17:00)
            $afternoonStart = $date->copy()->setTime(13, 0);
            $afternoonEnd = $date->copy()->setTime(17, 0);
            
            if ($this->isTimeSlotAvailable($roomId, $afternoonStart, $afternoonEnd, $existingBookings)) {
                $suggestions[] = [
                    'date' => $date->format('d M Y'),
                    'time' => '13:00 - 17:00',
                    'duration' => '4'
                ];
            }
            
            // Limit to 3 suggestions
            if (count($suggestions) >= 3) break;
        }
        
        return $suggestions;
    }

    private function isTimeSlotAvailable($roomId, $startTime, $endTime, $existingBookings)
    {
        foreach ($existingBookings as $booking) {
            if ($booking->meeting_room_id == $roomId && 
                in_array($booking->status, ['pending', 'confirmed']) &&
                $booking->start_time >= now() && // Only check future bookings
                $this->timesOverlap($startTime, $endTime, $booking->start_time, $booking->end_time)) {
                return false;
            }
        }
        return true;
    }

    private function timesOverlap($start1, $end1, $start2, $end2)
    {
        return $start1->lt($end2) && $end1->gt($start2);
    }

    private function getRoomAvailabilityGrid()
    {
        $rooms = MeetingRoom::where('is_active', true)
            ->orderBy('name')
            ->get();

        \Log::info('Room availability grid - rooms found', [
            'room_count' => $rooms->count(),
            'rooms' => $rooms->pluck('name', 'id')->toArray()
        ]);

        $timeSlots = [];
        $currentTime = now()->startOfDay();
        
        // Generate time slots for today (every 30 minutes from 8:00 to 18:00)
        for ($hour = 8; $hour <= 18; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) {
                $timeSlots[] = $currentTime->copy()->setTime($hour, $minute);
            }
        }

        \Log::info('Room availability grid - time slots generated', [
            'time_slot_count' => count($timeSlots),
            'first_slot' => $timeSlots[0]->format('H:i'),
            'last_slot' => end($timeSlots)->format('H:i')
        ]);

        $grid = [];
        
        foreach ($rooms as $room) {
            $roomData = [
                'id' => $room->id,
                'name' => $room->name,
                'location' => $room->location,
                'capacity' => $room->capacity,
                'timeSlots' => []
            ];

            $availableSlots = 0;
            $totalSlots = 0;
            
            foreach ($timeSlots as $timeSlot) {
                $endTime = $timeSlot->copy()->addMinutes(30);
                $totalSlots++;
                
                // Check if this time slot has already passed
                $isPastTime = $timeSlot->isPast();
                
                // Check if room is available for this time slot
                $conflictingBooking = Booking::where('meeting_room_id', $room->id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $timeSlot)
                    ->where('start_time', '>=', now()) // Only check future bookings
                    ->with('user')
                    ->first();

                // Check if room was used before (completed bookings) - only for past bookings
                $previousBooking = null;
                if ($isPastTime) {
                    $previousBooking = Booking::where('meeting_room_id', $room->id)
                        ->where('status', 'completed')
                        ->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $timeSlot)
                        ->with('user')
                        ->first();
                }

                $isAvailable = !$conflictingBooking && !$isPastTime;
                if ($isAvailable) {
                    $availableSlots++;
                }
                
                $slotData = [
                    'time' => $timeSlot->format('H:i'),
                    'datetime' => $timeSlot->format('Y-m-d H:i:s'),
                    'isAvailable' => $isAvailable,
                    'isPastTime' => $isPastTime,
                    'wasUsed' => $previousBooking ? true : false,
                    'booking' => null,
                    'previousBooking' => null
                ];

                if ($conflictingBooking) {
                    $slotData['booking'] = [
                        'id' => $conflictingBooking->id,
                        'title' => $conflictingBooking->title,
                        'user_name' => $conflictingBooking->user->full_name,
                        'unit_kerja' => $conflictingBooking->unit_kerja,
                        'start_time' => $conflictingBooking->start_time->format('H:i'),
                        'end_time' => $conflictingBooking->end_time->format('H:i'),
                        'status' => $conflictingBooking->status
                    ];
                }

                if ($previousBooking) {
                    $slotData['previousBooking'] = [
                        'id' => $previousBooking->id,
                        'title' => $previousBooking->title,
                        'user_name' => $previousBooking->user->full_name,
                        'unit_kerja' => $previousBooking->unit_kerja,
                        'start_time' => $previousBooking->start_time->format('H:i'),
                        'end_time' => $previousBooking->end_time->format('H:i'),
                        'status' => $previousBooking->status
                    ];
                }

                $roomData['timeSlots'][] = $slotData;
            }

            \Log::info('Room availability grid - room processed', [
                'room_id' => $room->id,
                'room_name' => $room->name,
                'total_slots' => $totalSlots,
                'available_slots' => $availableSlots,
                'past_slots' => $totalSlots - $availableSlots
            ]);

            $grid[] = $roomData;
        }

        \Log::info('Room availability grid - completed', [
            'total_rooms' => count($grid),
            'total_slots_per_room' => $totalSlots
        ]);

        return $grid;
    }


    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = session('user_data');
        
        // Check if user exists in database
        $dbUser = User::find($user['id']);
        
        if ($dbUser) {
            // Verify current password
            if (!Hash::check($request->current_password, $dbUser->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect.'
                ], 400);
            }
            
            // Update password
            $dbUser->password = Hash::make($request->new_password);
            $dbUser->save();
        } else {
            // For hardcoded users, just update session
            if ($user['username'] === 'admin' && $request->current_password === 'admin') {
                // Update session data
                $userData = session('user_data');
                $userData['password_changed'] = true;
                session(['user_data' => $userData]);
            } elseif ($user['username'] === 'user' && $request->current_password === 'user') {
                // Update session data
                $userData = session('user_data');
                $userData['password_changed'] = true;
                session(['user_data' => $userData]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect.'
                ], 400);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully!'
        ]);
    }

    public function updateNotificationSettings(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'daily_reminders' => 'boolean',
            'booking_confirmations' => 'boolean',
        ]);

        $user = session('user_data');
        
        // Update notification settings in session
        $userData = session('user_data');
        $userData['notification_settings'] = [
            'email_notifications' => $request->has('email_notifications'),
            'daily_reminders' => $request->has('daily_reminders'),
            'booking_confirmations' => $request->has('booking_confirmations'),
        ];
        session(['user_data' => $userData]);

        return response()->json([
            'success' => true,
            'message' => 'Notification settings saved successfully!'
        ]);
    }

    public function notifications()
    {
        $user = session('user_data');
        $userModel = User::find($user['id']);
        
        $notifications = $userModel->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('user.notifications', compact('notifications'));
    }

    public function markNotificationRead(Request $request, $id)
    {
        try {
            $user = session('user_data');
            $notification = \App\Models\UserNotification::where('id', $id)
                ->where('user_id', $user['id'])
                ->firstOrFail();
            
            $notification->markAsRead();
            
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    public function markAllNotificationsRead(Request $request)
    {
        try {
            $user = session('user_data');
            $userModel = User::find($user['id']);
            
            $userModel->notifications()
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);
            
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read'
            ], 500);
        }
    }
}
