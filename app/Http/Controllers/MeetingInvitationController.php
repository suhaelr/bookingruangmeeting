<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeetingInvitation;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class MeetingInvitationController extends Controller
{
    /**
     * Prepare confirm attendance - accessible without login.
     * Saves invitation_id to session and redirects to login.
     * If user is already logged in, directly shows the confirmation.
     */
    public function prepareConfirmAttendance($invitationId)
    {
        // Validate invitation exists
        $invitation = MeetingInvitation::find($invitationId);

        if (!$invitation) {
            return redirect()->route('login')->with('error', 'Undangan tidak ditemukan.');
        }

        // If user is already logged in, directly process the confirmation
        if (session('user_logged_in') && session('user_data')) {
            return $this->showConfirmAttendance($invitationId);
        }

        // Save invitation_id to session for after login
        session(['pending_attendance_confirmation' => $invitationId]);

        // Redirect to login with intended URL
        return redirect()->route('login')
            ->with('intended', route('user.confirm-attendance', $invitationId));
    }

    /**
     * Show confirm attendance page.
     */
    public function showConfirmAttendance($invitationId)
    {
        $user = session('user_data');
        $userModel = User::find($user['id']);

        $invitation = MeetingInvitation::with(['booking', 'booking.meetingRoom', 'invitedByPic'])
            ->where('id', $invitationId)
            ->where('pic_id', $userModel->id)
            ->firstOrFail();

        $booking = $invitation->booking;

        // Check if meeting has passed
        $isPastMeeting = $booking->start_time < now();

        // If meeting has passed, auto set as absent
        if ($isPastMeeting && $invitation->isAttendancePending()) {
            $invitation->markAsAbsent();

            // Send notification to user who invited
            if ($invitation->invitedByPic) {
                \App\Models\UserNotification::createNotification(
                    $invitation->invited_by_pic_id,
                    'warning',
                    'PIC Tidak Hadir di Meeting',
                    "PIC {$userModel->full_name} tidak hadir di meeting '{$booking->title}' (meeting sudah lewat)",
                    $booking->id
                );
            }
        }

        // Save invitation_id in session for popup after redirect
        session(['pending_attendance_confirmation' => $invitationId]);

        return redirect()->route('user.dashboard')
            ->with('show_attendance_modal', true)
            ->with('invitation_id', $invitationId);
    }

    /**
     * Handle attendance confirmation.
     */
    public function confirmAttendance(Request $request, $invitationId)
    {
        $user = session('user_data');
        $userModel = User::find($user['id']);

        $request->validate([
            'attendance_status' => 'required|in:confirmed,declined'
        ]);

        $invitation = MeetingInvitation::with(['booking', 'booking.meetingRoom', 'invitedByPic'])
            ->where('id', $invitationId)
            ->where('pic_id', $userModel->id)
            ->firstOrFail();

        $booking = $invitation->booking;

        // Check if meeting has passed
        $isPastMeeting = $booking->start_time < now();

        if ($isPastMeeting) {
            // If meeting has passed, auto set as absent
            $invitation->markAsAbsent();

            // Send notification to user who invited
            if ($invitation->invitedByPic) {
                \App\Models\UserNotification::createNotification(
                    $invitation->invited_by_pic_id,
                    'warning',
                    'PIC Tidak Hadir di Meeting',
                    "PIC {$userModel->full_name} tidak hadir di meeting '{$booking->title}' (meeting sudah lewat)",
                    $booking->id
                );
            }

            return response()->json([
                'success' => false,
                'message' => 'Meeting sudah lewat. Status kehadiran Anda otomatis ditandai sebagai tidak hadir.'
            ], 400);
        }

        // Update attendance status
        if ($request->attendance_status === 'confirmed') {
            $invitation->confirmAttendance();
            $statusText = 'akan hadir';
            $notificationType = 'success';
            $notificationTitle = 'PIC Konfirmasi Kehadiran';
        } else {
            $invitation->declineAttendance();
            $statusText = 'tidak bisa hadir';
            $notificationType = 'warning';
            $notificationTitle = 'PIC Tidak Bisa Hadir';
        }

        // Send notification to user who invited
        if ($invitation->invitedByPic) {
            \App\Models\UserNotification::createNotification(
                $invitation->invited_by_pic_id,
                $notificationType,
                $notificationTitle,
                "PIC {$userModel->full_name} mengkonfirmasi {$statusText} di meeting '{$booking->title}'",
                $booking->id
            );
        }

        // Remove session pending_attendance_confirmation
        session()->forget('pending_attendance_confirmation');

        return response()->json([
            'success' => true,
            'message' => $request->attendance_status === 'confirmed'
                ? 'Terima kasih! Kehadiran Anda telah dikonfirmasi.'
                : 'Terima kasih! Status kehadiran Anda telah diperbarui.'
        ]);
    }
}
