<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index()
    {
        $user = session('user_data');
        $userModel = User::find($user['id']);

        $notifications = $userModel->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user.notifications', compact('notifications'));
    }

    /**
     * Get user notifications as JSON.
     */
    public function getUserNotifications()
    {
        $user = session('user_data');
        $userModel = User::find($user['id']);

        $notifications = $userModel->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($notification) {
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

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $user = session('user_data');
            $notification = UserNotification::where('id', $id)
                ->where('user_id', $user['id'])
                ->firstOrFail();

            $notification->markAsRead();

            Log::info('Notification marked as read', [
                'notification_id' => $id,
                'user_id' => $user['id']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read', [
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

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request)
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

            Log::info('Mark all notifications as read', [
                'user_id' => $user['id'],
                'updated_count' => $updatedCount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read',
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read', [
                'user_id' => $user['id'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark all notifications as read: ' . $e->getMessage()
            ], 500);
        }
    }
}
