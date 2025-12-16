<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeetingRoom;
use App\Models\Booking;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function dashboard(Request $request)
    {
        $user = session('user_data');

        // Validate user exists in database
        try {
            $userModel = User::find($user['id']);
            if (!$userModel) {
                Log::error('User not found in database for dashboard', [
                    'session_user_id' => $user['id'],
                    'session_user_data' => $user
                ]);
                return redirect()->route('login')->with('error', 'User session invalid. Please login again.');
            }
        } catch (\Exception $e) {
            Log::error('Database error during user validation in dashboard', [
                'error' => $e->getMessage(),
                'session_user_id' => $user['id'],
                'session_user_data' => $user
            ]);
            return redirect()->route('login')->with('error', 'Database error. Please login again.');
        }

        // Statistik booking user - optimized single query
        $bookingsQuery = Booking::where('user_id', $userModel->id);
        $allBookings = $bookingsQuery->get();

        $stats = [
            'total_bookings' => $allBookings->count(),
            'pending_bookings' => $allBookings->where('status', 'pending')->count(),
            'confirmed_bookings' => $allBookings->where('status', 'confirmed')->count(),
            'cancelled_bookings' => $allBookings->where('status', 'cancelled')->count(),
            'this_month' => $allBookings->filter(function ($booking) {
                return $booking->created_at->month === now()->month
                    && $booking->created_at->year === now()->year;
            })->count(),
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

        // Get user notifications (reuse existing $userModel)
        $notifications = $userModel->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Calendar month selection (date param takes precedence over month)
        $monthParam = $request->input('month'); // format YYYY-MM
        $dateParam = $request->input('date');   // format YYYY-MM-DD
        if (!empty($dateParam)) {
            try {
                $calendarAnchor = Carbon::parse($dateParam)->startOfMonth();
            } catch (\Throwable $e) {
                $calendarAnchor = now()->startOfMonth();
            }
        } elseif (!empty($monthParam)) {
            try {
                // Normalize month string to YYYY-MM and build a safe date
                $monthKey = substr($monthParam, 0, 7);
                $calendarAnchor = Carbon::parse($monthKey . '-01')->startOfMonth();
            } catch (\Throwable $e) {
                $calendarAnchor = now()->startOfMonth();
            }
        } else {
            $calendarAnchor = now()->startOfMonth();
        }

        // Build calendar days for current anchor month
        $startOfMonth = $calendarAnchor->copy()->startOfMonth();
        $endOfMonth = $calendarAnchor->copy()->endOfMonth();

        // Fetch all confirmed bookings in the displayed month
        $monthlyConfirmed = Booking::with(['meetingRoom', 'user', 'invitations.pic'])
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

                // Check if user is in the invited PICs list (check by pic_id in invitations)
                $isInvitedPic = false;
                if ($booking->invitations && $booking->invitations->count() > 0) {
                    $isInvitedPic = $booking->invitations->contains(function ($inv) use ($userModel) {
                        return $inv && $inv->pic_id && (int)$inv->pic_id === (int)$userModel->id;
                    });
                }

                $canSeeDescription = false;
                if ($isAdmin || $isOwner) {
                    // Admin and owner always can see
                    $canSeeDescription = true;
                } elseif ($booking->description_visibility === 'public') {
                    // Public visibility: everyone can see
                    $canSeeDescription = true;
                } elseif ($booking->description_visibility === 'invited_pics_only') {
                    // Invited PICs only: only invited PICs can see (checked via checkbox)
                    $canSeeDescription = $isInvitedPic;
                }

                // Debug logging
                Log::info('Calendar item visibility check', [
                    'booking_id' => $booking->id,
                    'booking_title' => $booking->title,
                    'user_id' => $userModel->id,
                    'booking_user_id' => $booking->user_id,
                    'description_visibility' => $booking->description_visibility,
                    'is_admin' => $isAdmin,
                    'is_owner' => $isOwner,
                    'is_invited_pic' => $isInvitedPic,
                    'invitations_count' => $booking->invitations ? $booking->invitations->count() : 0,
                    'invited_pic_ids' => $booking->invitations ? $booking->invitations->pluck('pic_id')->toArray() : [],
                    'can_see_description' => $canSeeDescription,
                ]);

                $invitedPics = $booking->invitations->map(function ($inv) {
                    return [
                        'id' => $inv->pic_id,
                        'name' => $inv->pic?->full_name,
                        'unit_kerja' => $inv->pic?->unit_kerja,
                    ];
                })->values();

                // Document visibility: same logic as description visibility
                $hasDocument = !empty($booking->dokumen_perizinan);
                $canSeeDocument = false;
                $documentUrl = null;

                if ($hasDocument) {
                    // Use same visibility logic as description
                    if ($isAdmin || $isOwner) {
                        // Admin and owner always can see
                        $canSeeDocument = true;
                    } elseif ($booking->description_visibility === 'public') {
                        // Public visibility: everyone can see
                        $canSeeDocument = true;
                    } elseif ($booking->description_visibility === 'invited_pics_only') {
                        // Invited PICs only: only invited PICs can see (checked via checkbox)
                        $canSeeDocument = $isInvitedPic;
                    }

                    if ($canSeeDocument) {
                        // Use absolute URL to ensure iframe can load it
                        $documentUrl = url(route('user.bookings.document', $booking->id, false));
                    }
                }

                // Get attendance status for calendar color (only for booking owner)
                $attendanceStatus = null;
                $attendanceStatusColor = 'blue'; // default
                $attendanceStatusText = '';

                if ($isOwner && $booking->invitations && $booking->invitations->count() > 0) {
                    // Check all PIC attendance statuses
                    $allConfirmed = true;
                    $hasDeclined = false;
                    $hasPending = false;
                    $hasAbsent = false;

                    foreach ($booking->invitations as $invitation) {
                        if ($invitation->attendance_status === 'confirmed') {
                            // At least one confirmed
                        } elseif ($invitation->attendance_status === 'declined' || $invitation->attendance_status === 'absent') {
                            $hasDeclined = true;
                            $allConfirmed = false;
                            if ($invitation->attendance_status === 'absent') {
                                $hasAbsent = true;
                            }
                        } elseif ($invitation->attendance_status === 'pending') {
                            $hasPending = true;
                            $allConfirmed = false;
                        }
                    }

                    // Determine color and text
                    if ($allConfirmed && !$hasPending && !$hasDeclined) {
                        $attendanceStatus = 'all_confirmed';
                        $attendanceStatusColor = 'green';
                        $attendanceStatusText = 'Dikonfirmasi akan hadir';
                    } elseif ($hasDeclined || $hasAbsent) {
                        $attendanceStatus = 'has_declined';
                        $attendanceStatusColor = 'red';
                        $attendanceStatusText = $hasAbsent ? 'Tidak hadir' : 'Belum bisa hadir';
                    } elseif ($hasPending) {
                        $attendanceStatus = 'has_pending';
                        $attendanceStatusColor = 'yellow';
                        $attendanceStatusText = 'Belum ada respon';
                    }
                }

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
                    'invited_pics' => $invitedPics,
                    'has_document' => $hasDocument,
                    'can_see_document' => $canSeeDocument,
                    'document_url' => $documentUrl,
                    'attendance_status' => $attendanceStatus,
                    'attendance_status_color' => $attendanceStatusColor,
                    'attendance_status_text' => $attendanceStatusText,
                    'is_owner' => $isOwner,
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
        $bookingController = new BookingController();
        $roomAvailabilityGrid = $bookingController->getRoomAvailabilityGrid($selectedDate);

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

    /**
     * Display the user profile page.
     */
    public function profile()
    {
        $user = session('user_data');
        $unitKerjaOptions = config('unit_kerja.options', []);
        return view('user.profile', compact('user', 'unitKerjaOptions'));
    }

    /**
     * Update the user profile.
     */
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

    /**
     * Change user password.
     */
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

    /**
     * Update user notification settings.
     */
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
}
