<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Display a listing of the user's bookings.
     */
    public function index()
    {
        $user = session('user_data');

        // Validate user exists in database
        $userModel = User::find($user['id']);
        if (!$userModel) {
            Log::error('User not found in database for bookings', [
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
            ->get(['id', 'full_name', 'unit_kerja']);

        Log::info('User bookings retrieved', [
            'user_id' => $userModel->id,
            'bookings_count' => $bookings->count()
        ]);

        return view('user.bookings.index', compact('bookings', 'allPics'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        $rooms = MeetingRoom::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get all PICs (users) for invitation
        $allPics = User::where('role', 'user')
            ->orderBy('full_name')
            ->get();

        // Get current user's unit_kerja for auto-fill
        $user = session('user_data');
        $userModel = User::find($user['id'] ?? null);
        $userUnitKerja = $userModel ? ($userModel->unit_kerja ?? null) : null;
        $unitKerjaOptions = config('unit_kerja.options', []);

        // Check if no rooms are available
        if ($rooms->count() === 0) {
            return view('user.bookings.create', compact('rooms', 'allPics', 'userUnitKerja', 'unitKerjaOptions'))
                ->with('warning', 'Saat ini tidak ada ruang meeting yang tersedia. Silakan hubungi administrator untuk informasi lebih lanjut.');
        }

        return view('user.bookings.create', compact('rooms', 'allPics', 'userUnitKerja', 'unitKerjaOptions'));
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request)
    {
        $user = session('user_data');

        // Debug timezone info
        Log::info('Booking validation timezone debug', [
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
            'unit_kerja' => 'required|string|max:255',
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
            Log::error('User not found in database', [
                'session_user_id' => $user['id'],
                'session_user_data' => $user
            ]);
            return redirect()->route('login')->with('error', 'User session invalid. Please login again.');
        }

        $room = MeetingRoom::findOrFail($request->meeting_room_id);
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);

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

        // Log user validation for debugging
        Log::info('User validation successful', [
            'user_id' => $userModel->id,
            'username' => $userModel->username,
            'email' => $userModel->email
        ]);

        try {
            $booking = Booking::create([
                'user_id' => $userModel->id,
                'meeting_room_id' => $request->meeting_room_id,
                'title' => $request->title,
                'description' => $request->description,
                'description_visibility' => $request->description_visibility,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'attendees_count' => 1,
                'attendees' => [],
                'special_requirements' => null,
                'unit_kerja' => $request->unit_kerja,
                'dokumen_perizinan' => $dokumenPerizinanPath,
                'total_cost' => 0,
            ]);

            // Create PIC invitation records
            if ($request->has('invited_pics')) {
                foreach ($request->invited_pics as $picId) {
                    \App\Models\MeetingInvitation::create([
                        'booking_id' => $booking->id,
                        'pic_id' => $picId,
                        'invited_by_pic_id' => $userModel->id,
                        'status' => 'invited',
                        'attendance_status' => 'pending'
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
            Log::error('Booking creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $userModel->id,
                'request_data' => $request->all()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat membuat booking. Silakan coba lagi.')
                ->withInput();
        }

        // Send notification to admin
        $this->notifyAdmin('Permintaan Booking Baru', "User {$user['full_name']} telah membuat permintaan booking baru: {$booking->title}", 'success', $booking->id);

        Log::info('New booking created', [
            'booking_id' => $booking->id,
            'user_id' => $user['id'],
            'title' => $booking->title,
            'unit_kerja' => $booking->unit_kerja
        ]);

        return redirect()->route('user.dashboard')
            ->with('success', 'Booking berhasil dibuat! Menunggu konfirmasi admin.');
    }

    /**
     * Update the specified booking.
     */
    public function update(Request $request, $id)
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

            // Title validation
            if ($request->filled('title') && $request->title !== $booking->title) {
                $validationRules['title'] = 'required|string|max:255';
                $updateData['title'] = $request->title;
            }

            // Description validation
            if ($request->has('description') && $request->description !== $booking->description) {
                $validationRules['description'] = 'nullable|string';
                $updateData['description'] = $request->description;
            }

            // Start time validation
            if ($request->filled('start_time') && $request->start_time !== $booking->start_time->format('Y-m-d\TH:i')) {
                $validationRules['start_time'] = 'required|date';
                $updateData['start_time'] = $request->start_time;
            }

            // End time validation
            if ($request->filled('end_time') && $request->end_time !== $booking->end_time->format('Y-m-d\TH:i')) {
                $validationRules['end_time'] = 'required|date';
                $updateData['end_time'] = $request->end_time;
            }

            // Special requirements validation
            if ($request->has('special_requirements') && $request->special_requirements !== $booking->special_requirements) {
                $validationRules['special_requirements'] = 'nullable|string';
                $updateData['special_requirements'] = $request->special_requirements;
            }

            // Description visibility
            if ($request->filled('description_visibility') && $request->description_visibility !== $booking->description_visibility) {
                $validationRules['description_visibility'] = 'required|in:public,invited_pics_only';
                $updateData['description_visibility'] = $request->description_visibility;
            }

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
            $this->notifyAdmin('Booking Diperbarui', "User {$user['full_name']} telah memperbarui booking mereka: {$booking->title}");

            Log::info('Booking updated successfully', [
                'booking_id' => $booking->id,
                'user_id' => $user['id'],
                'updated_fields' => array_keys($updateData)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil diupdate!'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in updateBooking', [
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
            Log::error('Error in updateBooking', [
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

    /**
     * Cancel the specified booking.
     */
    public function cancel(Request $request, $id)
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
            $booking->updateStatus('cancelled', 'Dibatalkan oleh user');

            // Send notification to admin
            $this->notifyAdmin('Booking Dibatalkan', "User {$user['full_name']} telah membatalkan booking mereka: {$booking->title}", 'warning', $booking->id);

            Log::info('Booking cancelled successfully', [
                'booking_id' => $booking->id,
                'user_id' => $user['id']
            ]);

            return back()->with('success', 'Booking berhasil dibatalkan.');
        } catch (\Exception $e) {
            Log::error('Error in cancelBooking', [
                'message' => $e->getMessage(),
                'booking_id' => $id,
                'user_id' => session('user_data')['id'] ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Gagal membatalkan booking: ' . $e->getMessage());
        }
    }

    /**
     * Check room availability for the given time slot.
     */
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
        Log::info('Availability check debug', [
            'user_id' => $userModel->id,
            'room_id' => $room->id,
            'start_time' => $startTime->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString(),
            'conflicting_bookings_count' => $conflictingBookings->count(),
            'conflicting_bookings' => $conflictingBookings->map(function ($booking) {
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
            $conflictDetails = $this->formatConflictDetails($conflictingBookings, $room);

            return response()->json([
                'available' => false,
                'message' => $conflictDetails
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Ruang tersedia untuk waktu yang dipilih!'
        ]);
    }

    /**
     * Display the booking document.
     */
    public function viewDocument($id)
    {
        try {
            $user = session('user_data');
            $userModel = User::find($user['id']);

            if (!$userModel) {
                return response()->json([
                    'success' => false,
                    'message' => 'User session invalid'
                ], 401);
            }

            $booking = Booking::with('invitations.pic')->findOrFail($id);

            if (!$booking->dokumen_perizinan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dokumen tidak ditemukan'
                ], 404);
            }

            // Check document visibility: same logic as description visibility
            $isAdmin = $userModel->role === 'admin';
            $isOwner = (int)$booking->user_id === (int)$userModel->id;

            // Check if user is in the invited PICs list
            $isInvitedPic = false;
            if ($booking->invitations && $booking->invitations->count() > 0) {
                $isInvitedPic = $booking->invitations->contains(function ($inv) use ($userModel) {
                    return $inv && $inv->pic_id && (int)$inv->pic_id === (int)$userModel->id;
                });
            }

            $canSeeDocument = false;
            if ($isAdmin || $isOwner) {
                $canSeeDocument = true;
            } elseif ($booking->description_visibility === 'public') {
                $canSeeDocument = true;
            } elseif ($booking->description_visibility === 'invited_pics_only') {
                $canSeeDocument = $isInvitedPic;
            }

            Log::info('Document visibility check', [
                'booking_id' => $id,
                'user_id' => $userModel->id,
                'is_admin' => $isAdmin,
                'is_owner' => $isOwner,
                'is_invited_pic' => $isInvitedPic,
                'description_visibility' => $booking->description_visibility,
                'invitations_count' => $booking->invitations ? $booking->invitations->count() : 0,
                'invited_pic_ids' => $booking->invitations ? $booking->invitations->pluck('pic_id')->toArray() : [],
                'can_see_document' => $canSeeDocument,
            ]);

            if (!$canSeeDocument) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk melihat dokumen ini'
                ], 403);
            }

            $filePath = storage_path('app/public/' . $booking->dokumen_perizinan);

            if (!file_exists($filePath)) {
                Log::error('PDF file not found', [
                    'booking_id' => $id,
                    'file_path' => $filePath,
                    'dokumen_perizinan' => $booking->dokumen_perizinan
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'File dokumen tidak ditemukan'
                ], 404);
            }

            // Return PDF with proper headers for iframe embedding
            $response = response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="dokumen_booking_' . $id . '.pdf"',
                'Cache-Control' => 'public, max-age=3600',
                'X-Content-Type-Options' => 'nosniff',
            ]);

            // Allow iframe embedding from same origin
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

            // Override CSP to allow iframe embedding
            $response->headers->remove('Content-Security-Policy');
            $response->headers->set('Content-Security-Policy', "frame-ancestors 'self'");

            return $response;
        } catch (\Exception $e) {
            Log::error('Error viewing document: ' . $e->getMessage(), [
                'booking_id' => $id,
                'user_id' => $user['id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat dokumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get room availability grid for a selected date.
     * Made public so it can be called from UserController dashboard.
     */
    public function getRoomAvailabilityGrid($selectedDate = null)
    {
        // If no date is provided, default to today
        if (is_null($selectedDate)) {
            $selectedDate = now()->startOfDay();
        }

        $rooms = MeetingRoom::where('is_active', true)
            ->orderBy('name')
            ->get();

        Log::info('Room availability grid - rooms found', [
            'room_count' => $rooms->count(),
            'rooms' => $rooms->pluck('name', 'id')->toArray(),
            'selected_date' => $selectedDate->format('Y-m-d')
        ]);

        $timeSlots = [];
        $startHour = 8;
        $endHour = 18;

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

        Log::info('Room availability grid - time slots generated', [
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
                'capacity' => $room->capacity ?? 0,
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
                    ->whereDate('start_time', $selectedDate)
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $timeSlot)
                    ->with('user')
                    ->first();

                // Check if room was used before (completed bookings) - only for past bookings
                $previousBooking = null;
                if ($isPastTime) {
                    $previousBooking = Booking::where('meeting_room_id', $room->id)
                        ->where('status', 'completed')
                        ->whereDate('start_time', $selectedDate)
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
                    Log::info('Room availability grid - slot debug', [
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

            Log::info('Room availability grid - room processed', [
                'room_id' => $room->id,
                'room_name' => $room->name,
                'total_slots' => $totalSlots,
                'available_slots' => $availableSlots,
                'past_slots' => $selectedDate->isSameDay(now()) ? ($totalSlots - $availableSlots) : 0
            ]);

            $grid[] = $roomData;
        }

        Log::info('Room availability grid - completed', [
            'total_rooms' => count($grid),
            'total_slots_per_room' => count($timeSlots),
            'final_grid_data' => $grid
        ]);

        return $grid;
    }

    /**
     * Get conflicting bookings for a room and time slot.
     */
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

    /**
     * Format conflict details for display.
     */
    private function formatConflictDetails($conflictingBookings, $room)
    {
        if ($conflictingBookings->count() === 0) {
            return 'Ruang tersedia untuk waktu yang dipilih.';
        }

        $conflictList = $conflictingBookings->map(function ($booking) {
            return "• {$booking->title} oleh {$booking->user->full_name} ({$booking->start_time->format('d M Y H:i')} - {$booking->end_time->format('H:i')})";
        })->join("\n");

        return "Ruang {$room->name} tidak tersedia untuk waktu yang dipilih karena ada konflik dengan booking berikut:\n\n{$conflictList}\n\nSilakan pilih waktu yang berbeda.";
    }

    /**
     * Notify admin users about booking events.
     */
    private function notifyAdmin($title, $message, $type = 'info', $bookingId = null)
    {
        try {
            $adminUsers = User::where('role', 'admin')->get();

            if ($adminUsers->isEmpty()) {
                Log::warning('No admin user found to send notification', [
                    'title' => $title,
                    'type' => $type,
                    'booking_id' => $bookingId
                ]);
                return;
            }

            foreach ($adminUsers as $adminUser) {
                \App\Models\UserNotification::createNotification(
                    $adminUser->id,
                    $type,
                    $title,
                    $message,
                    $bookingId
                );
            }

            Log::info('Admin notifications created from BookingController', [
                'title' => $title,
                'type' => $type,
                'booking_id' => $bookingId,
                'admin_count' => $adminUsers->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create admin notification from BookingController', [
                'title' => $title,
                'error' => $e->getMessage()
            ]);
        }
    }
}
