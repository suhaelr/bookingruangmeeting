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
    public function dashboard(Request $request)
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

        // Calendar month selection
        $monthParam = $request->input('month'); // format YYYY-MM
        $calendarAnchor = $monthParam ? Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth() : now()->startOfMonth();

        // Build calendar days for current anchor month
        $startOfMonth = $calendarAnchor->copy()->startOfMonth();
        $endOfMonth = $calendarAnchor->copy()->endOfMonth();

        // Fetch all confirmed bookings in the displayed month
        $monthlyConfirmed = Booking::with(['meetingRoom', 'user', 'invitations'])
            ->where('status', 'confirmed')
            ->whereDate('start_time', '>=', $startOfMonth->toDateString())
            ->whereDate('start_time', '<=', $endOfMonth->toDateString())
            ->orderBy('start_time')
            ->get()
            ->groupBy(function ($b) {
                return $b->start_time->toDateString();
            });

        $calendarDays = [];
        $cursor = $startOfMonth->copy();
        while ($cursor->lte($endOfMonth)) {
            $dateKey = $cursor->toDateString();
            $items = [];
            foreach ($monthlyConfirmed->get($dateKey, collect([])) as $booking) {
                // Strict visibility: only admin, owner, or invited PIC may see description when visibility is invited_pics_only
                $isAdmin = $userModel->role === 'admin';
                $isOwner = (int)$booking->user_id === (int)$userModel->id;
                $isInvitedPic = $booking->invitations->contains(function($inv) use ($userModel) {
                    return (int)$inv->pic_id === (int)$userModel->id;
                });
                $canSeeDescription = false;
                if ($isAdmin || $isOwner) {
                    $canSeeDescription = true;
                } elseif ($booking->description_visibility === 'public') {
                    $canSeeDescription = true;
                } elseif ($booking->description_visibility === 'invited_pics_only' && $isInvitedPic) {
                    $canSeeDescription = true;
                }
                
                // Debug logging
                \Log::info('Calendar item debug', [
                    'booking_id' => $booking->id,
                    'booking_title' => $booking->title,
                    'user_id' => $userModel->id,
                    'booking_user_id' => $booking->user_id,
                    'description_visibility' => $booking->description_visibility,
                    'has_description' => !empty($booking->description),
                    'can_see_description' => $canSeeDescription,
                    'is_invited_pic' => $isInvitedPic,
                    'invitations_count' => $booking->invitations->count(),
                ]);
                
                $items[] = [
                    'id' => $booking->id,
                    'title' => $booking->title,
                    'start_time' => $booking->start_time->format('H:i'),
                    'end_time' => $booking->end_time->format('H:i'),
                    'room' => $booking->meetingRoom?->name,
                    'pic_name' => $booking->user?->full_name ?? $booking->user?->name,
                    'unit_kerja' => $booking->unit_kerja ?? ($booking->user?->unit_kerja ?? $booking->user?->department),
                    'description' => $canSeeDescription ? $booking->description : null,
                    'can_see_description' => $canSeeDescription,
                    'is_invited_pic' => $isInvitedPic,
                ];
            }
            $calendarDays[] = [
                'date' => $dateKey,
                'day' => (int) $cursor->format('j'),
                'isToday' => $cursor->isToday(),
                'items' => $items,
            ];
            $cursor->addDay();
        }

        // Get selected date from request, default to today (for legacy grid)
        $selectedDate = $request->has('date') ? Carbon::parse($request->input('date')) : now();

        // Get room availability grid data (kept for other sections that still use it)
        $roomAvailabilityGrid = $this->getRoomAvailabilityGrid($selectedDate);

        return view('user.dashboard', compact(
            'stats',
            'activeBookings',
            'todayBookings',
            'availableRooms',
            'notifications',
            'roomAvailabilityGrid',
            'selectedDate',
            'calendarDays',
            'calendarAnchor'
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
            'unit_kerja' => 'nullable|string|max:255',
        ]);

        // Update database and session
        $userData = session('user_data');
        $userModel = User::find($userData['id']);

        if ($userModel) {
            // Persist to DB (mirror department to unit_kerja for admin listing)
            $userModel->full_name = $request->full_name;
            $userModel->name = $request->full_name;
            $userModel->email = $request->email;
            $userModel->phone = $request->phone;
            // Prefer the unit_kerja input; if not provided, fallback to department
            $unitKerjaInput = $request->unit_kerja ?? $request->department;
            $userModel->department = $request->department ?? $unitKerjaInput;
            $userModel->unit_kerja = $unitKerjaInput;
            $userModel->save();
        }

        // Refresh session data from DB to ensure consistency
        $userData['full_name'] = $userModel->full_name ?? $request->full_name;
        $userData['email'] = $userModel->email ?? $request->email;
        $userData['phone'] = $userModel->phone ?? $request->phone;
        $userData['department'] = $userModel->department ?? ($request->department ?? $request->unit_kerja);
        $userData['unit_kerja'] = $userModel->unit_kerja ?? $request->unit_kerja;
        session(['user_data' => $userData]);

        return back()->with('success', 'Profil berhasil disimpan.');
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
        
        $bookings = Booking::with(['meetingRoom', 'invitations.pic'])
            ->where('user_id', $userModel->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // All PICs for edit modal (checkbox list)
        $allPics = User::where('role', 'user')
            ->orderBy('full_name')
            ->get(['id','full_name','unit_kerja']);
        
        \Log::info('User bookings retrieved', [
            'user_id' => $userModel->id,
            'bookings_count' => $bookings->count()
        ]);
        
        return view('user.bookings', compact('bookings','allPics'));
    }

    public function createBooking()
    {
        $rooms = MeetingRoom::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Get all PICs (users) for invitation
        $allPics = User::where('role', 'user')
            ->orderBy('full_name')
            ->get();
        
        // Check if no rooms are available
        if ($rooms->count() === 0) {
            return view('user.create-booking', compact('rooms', 'allPics'))
                ->with('warning', 'Saat ini tidak ada ruang meeting yang tersedia. Silakan hubungi administrator untuk informasi lebih lanjut.');
        }
        
        return view('user.create-booking', compact('rooms', 'allPics'));
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:meeting_rooms,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'exclude_booking_id' => 'nullable|integer|exists:bookings,id',
        ]);

        $user = session('user_data');
        $userModel = User::find($user['id']);
        if (!$userModel) {
            return response()->json([
                'available' => false,
                'message' => 'User session invalid. Please login again.'
            ], 401);
        }

        $room = MeetingRoom::findOrFail($request->room_id);
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $excludeBookingId = $request->input('exclude_booking_id');
        
        $conflictingBookings = $this->getConflictingBookings($room->id, $startTime, $endTime, $excludeBookingId);
        
        // Debug logging
        \Log::info('Availability check debug', [
            'user_id' => $userModel->id,
            'room_id' => $room->id,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'conflicting_bookings_count' => $conflictingBookings->count(),
            'conflicting_bookings' => $conflictingBookings->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'title' => $booking->title,
                    'start_time' => $booking->start_time->toDateTimeString(),
                    'end_time' => $booking->end_time->toDateTimeString(),
                ];
            })
        ]);
        
        if ($conflictingBookings->count() > 0) {
            // Build UI message with all conflicts (including same user) to BLOCK booking
            $conflictDetails = $this->formatConflictDetails($conflictingBookings, $room);

            // For preempt UI purposes, only return conflicts owned by other users
            $otherUserConflicts = $conflictingBookings->filter(function($booking) use ($userModel) {
                return $booking->user_id !== $userModel->id;
            });

            return response()->json([
                'available' => false,
                'message' => $conflictDetails,
                'conflicts' => $otherUserConflicts->map(function($booking) {
                    return [
                        'id' => $booking->id,
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
        
        // Debug timezone info
        \Log::info('Booking validation timezone debug', [
            'current_time' => now()->format('Y-m-d H:i:s'),
            'timezone' => config('app.timezone'),
            'start_time_request' => $request->start_time,
            'end_time_request' => $request->end_time
        ]);

        $request->validate([
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_visibility' => 'required|in:public,invited_pics_only',
            'invited_pics' => 'nullable|array',
            'invited_pics.*' => 'exists:users,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'attendees_count' => 'required|integer|min:1',
            'attendees' => 'nullable|string',
            'special_requirements' => 'nullable|string',
            'unit_kerja' => 'required|string|max:255',
            // Make PDF optional
            'dokumen_perizinan' => 'nullable|file|mimes:pdf|max:2048',
            'dokumen_perizinan_data' => 'nullable|string',
            'captcha_answer' => 'required|integer',
        ]);

        // Only validate logical time constraints (no time restrictions)
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        
        if ($startTime->gte($endTime)) {
            return back()->withErrors([
                'end_time' => 'Waktu selesai harus setelah waktu mulai.'
            ])->withInput();
        }

        // Verify captcha
        $userAnswer = $request->input('captcha_answer');
        $correctAnswer = session('captcha_answer');
        
        if ($userAnswer != $correctAnswer) {
            return back()->withErrors([
                'captcha_answer' => 'Jawaban captcha salah. Silakan coba lagi.'
            ])->withInput();
        }
        
        // Clear captcha from session after successful verification
        session()->forget(['captcha_answer', 'captcha_question', 'captcha_verified']);

        // Validate user exists in database first
        $userModel = User::find($user['id']);
        if (!$userModel) {
            \Log::error('User not found in database', [
                'session_user_id' => $user['id'],
                'session_user_data' => $user
            ]);
            return redirect()->route('login')->with('error', 'User session invalid. Please login again.');
        }

        $room = MeetingRoom::findOrFail($request->meeting_room_id);
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        
        // Check capacity
        if ($request->attendees_count > $room->capacity) {
            return back()->withErrors([
                'attendees_count' => "Jumlah peserta ({$request->attendees_count}) melebihi kapasitas ruangan ({$room->capacity} kursi)."
            ])->withInput();
        }
        
        // Check availability with detailed feedback (BLOCK regardless of owner)
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
                'description_visibility' => $request->description_visibility,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'attendees_count' => $request->attendees_count,
                'attendees' => $attendees,
                'special_requirements' => $request->special_requirements,
                'unit_kerja' => $request->unit_kerja,
                'dokumen_perizinan' => $dokumenPerizinanPath,
                'total_cost' => 0, // Set to 0 since we removed pricing
            ]);

            // Create PIC invitation records
            if ($request->has('invited_pics')) {
                foreach ($request->invited_pics as $picId) {
                    \App\Models\MeetingInvitation::create([
                        'booking_id' => $booking->id,
                        'pic_id' => $picId,
                        'invited_by_pic_id' => $userModel->id,
                        'status' => 'invited'
                    ]);
                    
                    // Send notification to invited PIC
                    \App\Models\UserNotification::createNotification(
                        $picId,
                        'info',
                        'Undangan Meeting dari PIC',
                        "PIC {$userModel->full_name} mengundang Anda ke meeting '{$booking->title}'",
                        $booking->id
                    );
                }
            }
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
        $this->notifyAdmin('New Booking Request', "User {$user['full_name']} has requested a new booking: {$booking->title}", 'success', $booking->id);

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

            // Check if booking is already confirmed by admin
            if ($booking->status === 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking tidak dapat diedit karena sudah dikonfirmasi oleh admin.'
                ], 403);
            }

            // Only validate fields that are actually being updated
            $validationRules = [];
            $updateData = [];

            // Title validation - only if provided and different from current
            if ($request->filled('title') && $request->title !== $booking->title) {
                $validationRules['title'] = 'required|string|max:255';
                $updateData['title'] = $request->title;
            }

            // Description validation - only if provided and different from current
            if ($request->has('description') && $request->description !== $booking->description) {
                $validationRules['description'] = 'nullable|string';
                $updateData['description'] = $request->description;
            }

            // Start time validation - only if provided and different from current
            if ($request->filled('start_time') && $request->start_time !== $booking->start_time->format('Y-m-d\TH:i')) {
                $validationRules['start_time'] = 'required|date';
                $updateData['start_time'] = $request->start_time;
            }

            // End time validation - only if provided and different from current
            if ($request->filled('end_time') && $request->end_time !== $booking->end_time->format('Y-m-d\TH:i')) {
                $validationRules['end_time'] = 'required|date';
                $updateData['end_time'] = $request->end_time;
            }

            // Special requirements validation - only if provided and different from current
            if ($request->has('special_requirements') && $request->special_requirements !== $booking->special_requirements) {
                $validationRules['special_requirements'] = 'nullable|string';
                $updateData['special_requirements'] = $request->special_requirements;
            }

            // Description visibility
            if ($request->filled('description_visibility') && $request->description_visibility !== $booking->description_visibility) {
                $validationRules['description_visibility'] = 'required|in:public,invited_pics_only';
                $updateData['description_visibility'] = $request->description_visibility;
            }

            // It's valid to update only invited PICs without changing booking fields, so do not return error here

            // Validate only the fields being updated
            $request->validate($validationRules);

            // Time validation logic
            $startTime = isset($updateData['start_time']) ? Carbon::parse($updateData['start_time']) : Carbon::parse($booking->start_time);
            $endTime = isset($updateData['end_time']) ? Carbon::parse($updateData['end_time']) : Carbon::parse($booking->end_time);
            
            // Validate end time is after start time
            if ($endTime <= $startTime) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu selesai harus setelah waktu mulai.'
                ], 422);
            }
            
            // Only validate future time if the time has actually changed
            $originalStartTime = Carbon::parse($booking->start_time);
            $originalEndTime = Carbon::parse($booking->end_time);
            
            if ($startTime->ne($originalStartTime) && $startTime->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu mulai tidak boleh di masa lalu jika diubah.'
                ], 422);
            }
            
            if ($endTime->ne($originalEndTime) && $endTime->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu selesai tidak boleh di masa lalu jika diubah.'
                ], 422);
            }
            
            // Check for conflicts only if time is being changed
            if (isset($updateData['start_time']) || isset($updateData['end_time'])) {
                $conflictingBookings = $this->getConflictingBookings($booking->meeting_room_id, $startTime, $endTime, $id);
                
                if ($conflictingBookings->count() > 0) {
                    $conflictDetails = $this->formatConflictDetails($conflictingBookings, $booking->meetingRoom);
                    return response()->json([
                        'success' => false,
                        'message' => $conflictDetails
                    ], 422);
                }
            }

            // Recalculate total cost (hourly_rate removed, set to 0) only if there are changes
            if (!empty($updateData)) {
                $updateData['total_cost'] = 0.00;
                // Update booking with only changed fields
                $booking->update($updateData);
            }

            // Sync invited PICs if provided
            if ($request->has('invited_pics')) {
                $request->validate([
                    'invited_pics' => 'array',
                    'invited_pics.*' => 'integer|exists:users,id'
                ]);

                $newPicIds = collect($request->invited_pics)->map(fn($v) => (int)$v)->unique()->values();
                $currentPicIds = $booking->invitations()->pluck('pic_id');

                $toAdd = $newPicIds->diff($currentPicIds);
                $toDelete = $currentPicIds->diff($newPicIds);

                if ($toDelete->count() > 0) {
                    $booking->invitations()->whereIn('pic_id', $toDelete)->delete();
                }
                foreach ($toAdd as $picId) {
                    \App\Models\MeetingInvitation::create([
                        'booking_id' => $booking->id,
                        'pic_id' => $picId,
                        'invited_by_pic_id' => $user['id'],
                        'status' => 'invited'
                    ]);
                }
            }

            // Send notification to admin
            $this->notifyAdmin('Booking Updated', "User {$user['full_name']} updated their booking: {$booking->title}");

            \Log::info('Booking updated successfully', [
                'booking_id' => $booking->id,
                'user_id' => $user['id'],
                'updated_fields' => array_keys($updateData)
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
                'message' => 'Validation error: ' . implode(', ', array_merge(...array_values($e->errors()))),
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
            $this->notifyAdmin('Booking Cancelled', "User {$user['full_name']} cancelled their booking: {$booking->title}", 'warning', $booking->id);

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

    public function requestPreempt(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $user = session('user_data');
        $requesterId = $user['id'] ?? null;

        try {
            $target = Booking::with('user')->findOrFail($id);

            // Cannot preempt own booking
            if ($target->user_id === $requesterId) {
                return response()->json(['success' => false, 'message' => 'Tidak dapat meminta didahulukan pada booking milik sendiri.'], 400);
            }

            // If already pending, do nothing (idempotent)
            if ($target->preempt_status === 'pending') {
                return response()->json(['success' => true, 'message' => 'Permintaan sudah dalam status menunggu tanggapan.']);
            }

            // Compute SLA
            $now = now();
            $minutes = 60;
            if ($target->start_time && $target->start_time->diffInHours($now, false) > -2) {
                // < 2 hours to start
                $minutes = 15;
            }
            $deadline = $now->copy()->addMinutes($minutes);

            // Start preempt
            $target->startPreempt($requesterId, $deadline, $request->input('reason'));

            \Log::info('Preempt requested', [
                'booking_id' => $target->id,
                'requested_by' => $requesterId,
                'deadline_at' => $deadline->toDateTimeString(),
            ]);

            // Notify owner (simple DB notification)
            try {
                \App\Models\UserNotification::createNotification(
                    $target->user_id,
                    'info',
                    'Permintaan Didahulukan',
                    'Booking Anda diminta untuk didahulukan oleh pengguna lain. Mohon tanggapi segera.',
                    $target->id
                );
            } catch (\Throwable $e) {
                \Log::error('Failed to create preempt notification to owner', ['error' => $e->getMessage()]);
            }

            return response()->json(['success' => true, 'message' => 'Permintaan dikirim. Menunggu tanggapan pemilik booking.']);
        } catch (\Exception $e) {
            \Log::error('Error in requestPreempt', [
                'error' => $e->getMessage(),
                'booking_id' => $id,
                'user_id' => $requesterId,
            ]);
            return response()->json(['success' => false, 'message' => 'Gagal mengirim permintaan.'], 500);
        }
    }

    public function respondPreempt(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:accept_cancel',
        ]);

        $user = session('user_data');
        $ownerId = $user['id'] ?? null;

        try {
            $booking = Booking::findOrFail($id);

            if ($booking->user_id !== $ownerId) {
                return response()->json(['success' => false, 'message' => 'Anda bukan pemilik booking ini.'], 403);
            }

            if ($booking->preempt_status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Tidak ada permintaan yang perlu ditanggapi.'], 400);
            }

            $action = $request->input('action');

            if ($action === 'accept_cancel') {
                $requesterId = $booking->preempt_requested_by;

                try {
                    \DB::beginTransaction();
                    // 1) Batalkan booking lama & tutup preempt
                    $booking->updateStatus('cancelled', 'Cancelled due to preempt request');
                    $booking->closePreempt();

                    // 2) Auto-create & confirm booking baru untuk peminta pada slot yang sama
                    if ($requesterId) {
                        $requester = \App\Models\User::find($requesterId);
                        if ($requester) {
                            $new = new \App\Models\Booking();
                            $new->user_id = $requester->id;
                            $new->meeting_room_id = $booking->meeting_room_id;
                            $new->title = '[Didahulukan] ' . ($booking->title ?? 'Meeting');
                            $new->description = 'Dibuat otomatis setelah disetujui didahulukan.';
                            $new->description_visibility = $booking->description_visibility ?: 'invited_pics_only';
                            $new->start_time = $booking->start_time;
                            $new->end_time = $booking->end_time;
                            $new->status = 'confirmed';
                            $new->attendees_count = max(1, (int)($booking->attendees_count ?? 1));
                            $new->attendees = $booking->attendees ?? [];
                            $new->special_requirements = $booking->special_requirements;
                            // Ensure unit_kerja is never null (DB may enforce NOT NULL on some envs)
                            $fallbackUnit = $booking->unit_kerja
                                ?? $requester->unit_kerja
                                ?? $requester->department
                                ?? 'Tidak diketahui';
                            $new->unit_kerja = $fallbackUnit;
                            $new->total_cost = 0;
                            $new->save();

                            // Notifikasi ke peminta
                            \App\Models\UserNotification::createNotification(
                                $requester->id,
                                'success',
                                'Booking Anda Otomatis Dikonfirmasi',
                                'Permintaan didahulukan disetujui. Booking baru telah dibuat dan dikonfirmasi pada slot tersebut.',
                                $new->id
                            );

                            // Notifikasi ke admin: gunakan user admin pertama jika kolom user_id NOT NULL
                            try {
                                $adminUser = \App\Models\User::where('role', 'admin')->orderBy('id')->first();
                                if ($adminUser) {
                                    \App\Models\UserNotification::createNotification(
                                        $adminUser->id,
                                        'info',
                                        'Auto-Confirm Setelah Didahulukan',
                                        "Booking #{$booking->id} dibatalkan oleh pemilik; booking baru #{$new->id} untuk peminta telah dikonfirmasi.",
                                        $new->id
                                    );
                                }
                            } catch (\Throwable $e) { \Log::error('Notify admin auto-confirm failed', ['e'=>$e->getMessage()]); }
                        }
                    }
                    \DB::commit();
                } catch (\Throwable $t) {
                    \DB::rollBack();
                    \Log::error('respondPreempt transaction failed', [
                        'booking_id' => $booking->id,
                        'requester_id' => $requesterId,
                        'error' => $t->getMessage(),
                    ]);
                    throw $t;
                }

                \Log::info('Preempt accepted with cancel (auto-confirm created for requester if possible)', [
                    'booking_id' => $booking->id,
                    'owner_id' => $ownerId,
                    'requester_id' => $booking->preempt_requested_by
                ]);
                return response()->json(['success' => true, 'message' => 'Booking dibatalkan. Permintaan didahulukan disetujui dan booking peminta dikonfirmasi otomatis.']);
            }

            return response()->json(['success' => false, 'message' => 'Aksi tidak dikenal.'], 400);
        } catch (\Exception $e) {
            \Log::error('Error in respondPreempt', [
                'error' => $e->getMessage(),
                'booking_id' => $id,
                'user_id' => $ownerId,
            ]);
            return response()->json(['success' => false, 'message' => 'Gagal memproses tanggapan.'], 500);
        }
    }

    private function canPicSeeDescription($booking, $picId)
    {
        // PIC yang membuat booking selalu bisa melihat deskripsi
        if ($booking->user_id == $picId) {
            return true;
        }
        
        // Jika visibility public, semua PIC bisa melihat
        if ($booking->description_visibility === 'public') {
            return true;
        }
        
        // Jika visibility invited_pics_only, hanya PIC yang diundang yang bisa melihat
        if ($booking->description_visibility === 'invited_pics_only') {
            return $booking->invitations->contains('pic_id', $picId);
        }
        
        return false;
    }

    private function notifyAdmin($title, $message, $type = 'info', $bookingId = null)
    {
        // Create admin notification in database
        try {
            \App\Models\UserNotification::create([
                'user_id' => null, // Admin notifications don't belong to specific user
                'booking_id' => $bookingId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'is_read' => false,
            ]);
            
            \Log::info('Admin notification created from UserController', [
                'title' => $title,
                'type' => $type,
                'booking_id' => $bookingId
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create admin notification from UserController', [
                'title' => $title,
                'error' => $e->getMessage()
            ]);
        }
    }




    private function getRoomAvailabilityGrid($selectedDate = null)
    {
        // If no date is provided, default to today
        if (is_null($selectedDate)) {
            $selectedDate = now()->startOfDay();
        }

        $rooms = MeetingRoom::where('is_active', true)
            ->orderBy('name')
            ->get();

        \Log::info('Room availability grid - rooms found', [
            'room_count' => $rooms->count(),
            'rooms' => $rooms->pluck('name', 'id')->toArray(),
            'selected_date' => $selectedDate->format('Y-m-d')
        ]);

        $timeSlots = [];
        $startHour = 8;
        $endHour = 18; // Up to 18:30
        
        // Generate all slots from 8 AM to 6:30 PM for the selected day
        for ($hour = $startHour; $hour <= $endHour; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) {
                $timeSlots[] = $selectedDate->copy()->setTime($hour, $minute);
            }
        }
        // Add 18:30 slot explicitly if not already added
        $lastSlot = $selectedDate->copy()->setTime(18, 30);
        if (!in_array($lastSlot, $timeSlots)) {
            $timeSlots[] = $lastSlot;
        }

        \Log::info('Room availability grid - time slots generated', [
            'time_slot_count' => count($timeSlots),
            'first_slot' => count($timeSlots) > 0 ? $timeSlots[0]->format('H:i') : 'N/A',
            'last_slot' => count($timeSlots) > 0 ? end($timeSlots)->format('H:i') : 'N/A',
            'current_time' => now()->format('H:i'),
            'target_date' => $selectedDate->format('Y-m-d')
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
                
                // Check if this time slot has already passed (only for the selected date if it's today)
                $isPastTime = $timeSlot->isPast() && $selectedDate->isSameDay(now());
                
                // Check if room is available for this time slot
                $conflictingBooking = Booking::where('meeting_room_id', $room->id)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->whereDate('start_time', $selectedDate) // Filter by selected date
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $timeSlot)
                    ->with('user')
                    ->first();

                // Check if room was used before (completed bookings) - only for past bookings
                $previousBooking = null;
                if ($isPastTime) {
                    $previousBooking = Booking::where('meeting_room_id', $room->id)
                        ->where('status', 'completed')
                        ->whereDate('start_time', $selectedDate) // Filter by selected date
                        ->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $timeSlot)
                        ->with('user')
                        ->first();
                }

                $isAvailable = !$conflictingBooking && !$isPastTime;
                if ($isAvailable) {
                    $availableSlots++;
                }
                
                // Debug logging for first few slots or if there's a booking/previous booking
                if ($totalSlots <= 5 || $conflictingBooking || $previousBooking) {
                    \Log::info('Room availability grid - slot debug', [
                        'room_id' => $room->id,
                        'room_name' => $room->name,
                        'slot_time' => $timeSlot->format('H:i'),
                        'slot_datetime' => $timeSlot->format('Y-m-d H:i:s'),
                        'is_past_time' => $isPastTime,
                        'has_conflicting_booking' => $conflictingBooking ? true : false,
                        'conflicting_booking_id' => $conflictingBooking ? $conflictingBooking->id : null,
                        'has_previous_booking' => $previousBooking ? true : false,
                        'previous_booking_id' => $previousBooking ? $previousBooking->id : null,
                        'is_available' => $isAvailable,
                        'current_time' => now()->format('H:i'),
                        'slot_is_today' => $timeSlot->isToday(),
                        'selected_date_is_today' => $selectedDate->isSameDay(now())
                    ]);
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
                'past_slots' => $selectedDate->isSameDay(now()) ? ($totalSlots - $availableSlots) : 0 // Only count past slots for today
            ]);

            $grid[] = $roomData;
        }

        \Log::info('Room availability grid - completed', [
            'total_rooms' => count($grid),
            'total_slots_per_room' => count($timeSlots),
            'final_grid_data' => $grid // Log the final grid structure for verification
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

    public function getUserNotifications()
    {
        $user = session('user_data');
        $userModel = User::find($user['id']);
        
        $notifications = $userModel->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'time' => $notification->created_at->diffForHumans(),
                    'read' => $notification->is_read,
                    'type' => $notification->type,
                    'booking_id' => $notification->booking_id,
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s')
                ];
            });

        return response()->json($notifications);
    }

    public function markNotificationRead(Request $request, $id)
    {
        try {
            $user = session('user_data');
            $notification = \App\Models\UserNotification::where('id', $id)
                ->where('user_id', $user['id'])
                ->firstOrFail();
            
            $notification->markAsRead();
            
            \Log::info('Notification marked as read', [
                'notification_id' => $id,
                'user_id' => $user['id']
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to mark notification as read', [
                'notification_id' => $id,
                'user_id' => $user['id'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAllNotificationsRead(Request $request)
    {
        try {
            $user = session('user_data');
            $userModel = User::find($user['id']);
            
            if (!$userModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
            
            $updatedCount = $userModel->notifications()
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);
            
            \Log::info('Mark all notifications as read', [
                'user_id' => $user['id'],
                'updated_count' => $updatedCount
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to mark all notifications as read', [
                'user_id' => $user['id'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getConflictingBookings($roomId, $startTime, $endTime, $excludeBookingId = null)
    {
        $query = Booking::where('meeting_room_id', $roomId)
            ->where(function ($q) {
                $q->whereIn('status', ['pending', 'confirmed'])
                  ->orWhere('preempt_status', 'pending');
            })
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->with('user');

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->get();
    }

    private function formatConflictDetails($conflictingBookings, $room)
    {
        if ($conflictingBookings->count() === 0) {
            return 'Ruang tersedia untuk waktu yang dipilih.';
        }

        $conflictList = $conflictingBookings->map(function($booking) {
            return "• {$booking->title} oleh {$booking->user->full_name} ({$booking->start_time->format('d M Y H:i')} - {$booking->end_time->format('H:i')})";
        })->join("\n");

        return "Ruang {$room->name} tidak tersedia untuk waktu yang dipilih karena ada konflik dengan booking berikut:\n\n{$conflictList}\n\nSilakan pilih waktu yang berbeda.";
    }
}

