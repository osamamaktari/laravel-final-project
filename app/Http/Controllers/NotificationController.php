<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return response()->json($user->notifications);
    }

    public function markAsRead(string $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where("id", $id)->firstOrFail();
        $notification->markAsRead();

        return response()->json(["message" => "Notification marked as read."]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return response()->json(["message" => "All notifications marked as read."]);
    }

    public function unreadCount()
    {
        $user = Auth::user();
        return response()->json(["unread_count" => $user->unreadNotifications->count()]);
    }
}
